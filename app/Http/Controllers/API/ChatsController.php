<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Mail\MessageResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Chats;
use App\User;
use Illuminate\Support\Facades\Mail;
//use App\Mail;

use Validator;

use App\Http\Resources\Chats as ChatsResource;

class ChatsController extends BaseController
{
     
     /**
     * Validate the request token.
     *
     */
    public function __construct()
    {
      $this->middleware('auth:sanctum');
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chats = $this->getAllChats();
        return $this->sendResponse(ChatsResource::collection($chats), 'Chats retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'details' => 'required'
        ]);
        $input['user_id'] = $user->id;
        $input['status'] = 0; //status 0 = not answered, 1  = In Progress, 2 = answered , 4= spam
        //print_r($input); die();
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        //in case of chat reply check if the user is chat owner
        if(!empty($input['chat_id'])) {
            $is_owner = $this->validateChatOwner($input['chat_id']);
            if(!$is_owner) {
                unset($input['chat_id']);
                return $this->sendResponse(array('Unauthorized action'), 'This Action can not be performed');
            }
        }
           
        if($chat = Chats::create($input)) {
            $message =  'Chat created successfully.';
            if(!empty($input['chat_id'])){
                $chat = $this->changeChatStatus($input['chat_id'],1);
                $this->sendEmailNotification($chat);
                $message =  'Chat respons submitted successfully.';
            }
        }
        return $this->sendResponse(new ChatsResource($chat), $message);
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chat = Chats::find($id);
        
        if (is_null($chat)) {
            return $this->sendError('Chat not found.');
        }
        //get all chat responses 
        $responses = $this->getAllChatResponses($id);
        $chat = new ChatsResource($chat);
        $chat['responses'] = new ChatsResource($responses);
        return $this->sendResponse($chat, 'Chat retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chats $chat)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'details' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $chat->title = $input['title'];
        $chat->details = $input['details'];
        $chat->save();
   
        return $this->sendResponse(new ChatsResource($chat), 'Chat updated successfully.');
    }
     /*
    * Load all messages responses
    * Params: parent_chat_id
    */
    function getAllChatResponses($id) {
        $chats = Chats::where('chat_id','=',$id)->get();
        return $chats;
    }
     /*
    * Get All Chats based on user role
    */
    function getAllChats() {
        $user = auth()->user();
        if($user->role == 'admin')
            $chats = Chats::all();
        else
            $chats = Chats::where('user_id','=',$user->id)->get();
           
        return $chats;
    }
     /*
    * Change Chat Status
    * Params: chat_id, status
    */
    public function changeChatStatus($id,$status) {
        $chat = Chats::find($id);
        $chat->status = $status;
        $chat->save();
        return $chat;
    }
    /*
    * Set Chat Status
    * Params: Request
    */
    public function setChatStatus(Request $request) {
        $user = auth()->user();
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'chat_id' => 'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if($user->role == 'admin'){
            $chat = $this->changeChatStatus($input['chat_id'],$input['status']);
            return $this->sendResponse(new ChatsResource($chat), 'Chat status has been updated successfully.');
        }
        else {
            return $this->sendResponse(array('error'=>'Unauthrized action'), 'This action can not be performed.');
        }
    }
    /*
    * Validate Chat owner
    * Params: chat_id
    */
    function validateChatOwner($id) {
        $user = auth()->user();
        $chat = Chats::find($id);
        if($user->role != 'admin') {
            
            if($chat->user_id != $user->id)
                return false;
        }
        return true;
        
    }

    function sendEmailNotification($chat) {
        $chatOwner = $this->getChatOwner($chat->chat_id);
        $email = $chatOwner->email;
        $name = $chatOwner->name;
        Mail::to($email)->send(new MessageResponse($chatOwner,$chat));
    }

    function getChatOwner($chat_id) {
        $chat = Chats::find($chat_id);
        $user_id = $chat->user_id;
        $user = User::find($user_id);
        return $user;
    }

    function searchChats(Request $request) {
        $user = auth()->user();
         if($user->role != 'admin'){
           
            return $this->sendResponse(array('error'=>'Unauthrized action'), 'This action can not be performed.');
        }
        $input = $request->all();
        $query = $input['query'];
        $chats = Chats::search($query)->get();
        return $this->sendResponse(new ChatsResource($chats), 'Chat found successfully.');
    }
}