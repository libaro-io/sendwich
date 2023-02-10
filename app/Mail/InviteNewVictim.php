<?php

namespace App\Mail;

use App\Models\InvitedUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteNewVictim extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public InvitedUser $invitee;
    public string $signedUrl;

    public function __construct($invitee , $signedUrl)
    {
        $this->invitee = $invitee;
        $this->signedUrl = $signedUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invitationMail');

    }
}
