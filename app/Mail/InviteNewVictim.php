<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\InvitedUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
    public Company $company;

    public function __construct(InvitedUser $invitee , $signedUrl)
    {
        $this->invitee = $invitee;
        $this->signedUrl = $signedUrl;
        $this->company = Company::query()->findOrFail($invitee->company_id)->first();

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('sending invitation mail');

        $mail =  $this->subject("You're Invited to Join {$this->company->name} on Sendwich! ")
            ->view('emails.invitationMail');

        Log::info('invitation mail sent');
        
        return $mail;
    }
}
