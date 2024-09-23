<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvited extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'welcome@sunlit.in'))
            ->to($this->user->email)
            ->subject(trans('email.invite_subject'))
            ->markdown('emails.userInvited')->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'subject' => trans('email.invite_subject'),
                'token' => $this->user->invite_token,
                'url' => route('registration.store', [$this->user->email, $this->user->invite_token]),
            ]);
    }
}
