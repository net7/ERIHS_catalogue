<?php

namespace App\Mail;

use App\Filament\Resources\MyProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class UpdateFilesReminder extends TemplateMailable
{
    /** @var string */
    public string $url;
    public string $proposalName;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal)
    {
        $this->url = MyProposalResource::getUrl('update-files', ['record' => $proposal->getKey()]);
        $this->proposalName = $proposal->name;
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
