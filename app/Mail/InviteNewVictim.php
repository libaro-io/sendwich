<?php

namespace App\Mail;

use App\Models\Company;
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
        return $this->subject("You're invited to Sendwich by " .$this->company->name)
            ->view('emails.invitationMail');

    }
}
