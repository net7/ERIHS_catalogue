<?php

namespace App\Mail;

use App\Models\User;
use Spatie\MailTemplates\TemplateMailable;

class NewUserRegistered extends TemplateMailable
{
    /** @var string */
    public $user;

    /** @var string */
    public $email;

    public $logo_url;

    public function __construct($user)
    {
        $this->user = $user->name . ' ' . $user->surname;
        $this->email = $user->email ?? '';
        $this->logo_url = asset('images/erihs_logo.png');
        }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
