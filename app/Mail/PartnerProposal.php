<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class PartnerProposal extends TemplateMailable
{
    /** @var string */
    public string $proposalName;
    public string $leader;

    /** @var string */
    public string $logo_url;

    public function __construct($proposal)
    {

        $this->proposalName = $proposal->name;
        $leader = $proposal->leader->first();
        $this->leader = $leader->name. ' '. $leader->surname . ', '. $leader->email;
        $this->logo_url = asset('images/erihs_logo.png');
    }

    public function getHtmlLayout(): ?string
    {
        $pathToLayout = storage_path('mail-layouts/header-email.blade.php');
        return file_get_contents($pathToLayout);
    }
}
