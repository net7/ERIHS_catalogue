<?php

namespace App\Mail;

use App\Models\User;
use Spatie\MailTemplates\TemplateMailable;

class WelcomeMail extends TemplateMailable
{
    /** @var string */
    public $name;

    /** @var string */
    public $email;

    public function __construct(User $user)
    {
        $this->name = $user->name;
        $this->email = $user->email ?? '';
    }
}
