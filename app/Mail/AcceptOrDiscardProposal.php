<?php

namespace App\Mail;

use App\Filament\Resources\ProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class AcceptOrDiscardProposal extends TemplateMailable
{
    /** @var string */
    public string $url;
    public string $proposalName;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal)
    {
        $this->url = ProposalResource::getUrl('general-info', ['record' => $proposal->getKey()]);
        $this->proposalName = $proposal->name;
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
