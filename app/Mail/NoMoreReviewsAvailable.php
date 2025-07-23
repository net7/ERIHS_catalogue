<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class NoMoreReviewsAvailable extends TemplateMailable
{
    /** @var string */
    public string $url;

    /** @var string */
    public string $logo_url;

    public function __construct()
    {
        $this->url = env('APP_URL').'/dashboard/profile';
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
