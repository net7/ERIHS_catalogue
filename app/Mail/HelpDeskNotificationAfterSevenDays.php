<?php

namespace App\Mail;

use App\Filament\Resources\ProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class HelpDeskNotificationAfterSevenDays extends TemplateMailable
{
    /** @var string */
    public string $reviewer;
    public string $proposalName;
    public string $url;

    /** @var string */
    public string $logo_url;

    public function __construct($reviewer, $proposal)
    {
        $this->reviewer = $reviewer->full_name ?? '';
        $this->proposalName = $proposal->name;
        $this->url = ProposalResource::getUrl('general-info', ['record' => $proposal->getKey()]);
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
