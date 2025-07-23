<?php

namespace App\Mail;

use App\Filament\Resources\ProposalResource;
use Spatie\MailTemplates\TemplateMailable;

class NewSubmission extends TemplateMailable
{
    /** @var string */
    public string $user;
    public string $proposalName;
    public string $url;

    /** @var string */
    public string $logo_url;

    public function __construct($user, $proposal)
    {
        $this->user = $user->full_name ?? '';
        $this->proposalName = $proposal->name;
        $this->logo_url = asset('images/erihs_logo.png');
        $this->url = ProposalResource::getUrl('general-info', ['record' => $proposal->getKey()]);
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
