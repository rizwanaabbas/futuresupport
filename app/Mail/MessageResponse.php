<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Chats;
use App\User;

class MessageResponse extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $chat;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Chats $chat)
    {
        $this->user = $user;
        $this->chat = $chat;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $user = $this->user;
        $chat = $this->chat;
        $host = $request->getHttpHost();
        return $this->from('no-reply@ridetothefuture.com', 'Mailtrap')
            ->subject('You have recieved new response')
            ->markdown('mail')
            ->with([
                'name' => $user->name,
                'link' => $host.'/',
            ]);
        //return $this->view('view.mail');
    }
}
