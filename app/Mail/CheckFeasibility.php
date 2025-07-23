<?php

namespace App\Mail;

use App\Filament\Resources\MyProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class CheckFeasibility extends TemplateMailable
{
    /** @var string */
    public string $url;
    public string $user;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal, $user)
    {
        $this->user = $user->full_name ?? '';
        $this->url = MyProposalResource::getUrl('general-info', ['record' => $proposal->getKey()]);
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
