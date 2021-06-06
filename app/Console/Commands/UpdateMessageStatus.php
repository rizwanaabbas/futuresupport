<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Chats;
use Carbon\Carbon;
use App\Http\Resources\Chats as ChatsResource;
class UpdateMessageStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateMessageStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //get all chats with status 1
        $chats = Chats::where('status',1)->get();
        if(!empty($chats)) {
            foreach($chats as $chat) {
                $chat_id = $chat->id;
                //find the latest responses more than 245 hours old with status 1
                $lastResponse = Chats::where("created_at","<",Carbon::now()->subDay())->where("created_at","<",Carbon::now())
                    ->where('chat_id',$chat_id)
                    ->orderBy('id','DESC')
                    ->offset(0)
                    ->limit(1)
                    ->first();
                if(!empty($lastResponse)) {
                    $chat->status = 2;
                    $chat->save();
                }
            }
        }
        return 0;
    }
}
