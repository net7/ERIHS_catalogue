<?php

namespace App\Mail;

use App\Filament\Resources\ProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class ReviewerConflicts extends TemplateMailable
{
    /** @var string */
    public string $url;
    public string $proposalName;
    public string $reviewer;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal, $reviewer)
    {
        $this->url = ProposalResource::getUrl('manage-reviewers', ['record' => $proposal->getKey()]);
        $this->proposalName = $proposal->name;
        $this->reviewer = $reviewer->full_name ?? '';
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
