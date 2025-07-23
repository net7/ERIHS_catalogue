<?php

namespace App\Mail;

use App\Filament\Resources\MyProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class CloseAccess extends TemplateMailable
{
    /** @var string */
    public string $proposalName;
    public string $url;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal)
    {
        $this->proposalName = $proposal->name;
        $this->url = MyProposalResource::getUrl('service-access', ['record' => $proposal->getKey()]);
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
