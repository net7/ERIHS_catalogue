<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class ProposalDiscardedFromHd extends TemplateMailable
{
    /** @var string */
    public string $url;
    public string $proposalName;
    public string $motivation;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal, $type)
    {
        $typeClass = "\App\Filament\Resources\\{$type}";
        $this->url = $typeClass::getUrl('general-info', ['record' => $proposal->getKey()]);
        $this->motivation = $proposal->proposal_notes ?? '';
        $this->proposalName = $proposal->name;
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
