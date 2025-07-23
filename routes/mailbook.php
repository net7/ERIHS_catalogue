<?php

use App\Mail\ApplicationFeasible;
use App\Mail\ApplicationInMainList;
use App\Mail\ApplicationRejected;
use App\Mail\ApplicationReviewOutcome;
use App\Mail\ApplicationSubmitted;
use App\Mail\CloseProcess;
use App\Mail\CloseProposal;
use App\Mail\MailbookMail;
use App\Mail\NewReviewerSelection;
use App\Mail\NewSubmission;
use App\Mail\NewUserRegistered;
use App\Mail\ProposalEvaluationUpdate;
use App\Mail\ReviewerConflicts;
use App\Mail\ReviewerSelected;
use App\Mail\ReviewerCheck;
use App\Mail\SolictUser;
use App\Mail\WeeklyReminder;
use App\Models\User;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(MailbookMail::class);

// Use a closure to customize the parameters of the mail instance
Mailbook::add(function (): NewUserRegistered {
    // $user = User::factory()->make();
    // $newUser = $_POST['name'];
    // $newUserEmail = $_POST['email'];
    $a = new NewUserRegistered('utente', 'p@p.it');
    return $a;
});

Mailbook::add(function (): ApplicationSubmitted {
    return new \App\Mail\ApplicationSubmitted(1);
});

Mailbook::add(function (): NewSubmission {
    return new NewSubmission('email@email.com', 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ApplicationFeasible {
    return new ApplicationFeasible(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ReviewerSelected {
    return new ReviewerSelected(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ReviewerCheck {
    return new ReviewerCheck(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): NewReviewerSelection {
    return new NewReviewerSelection(1, 'NOME DELLA PROPOSTA', 'email@email.it');
});

Mailbook::add(function (): ReviewerConflicts {
    return new ReviewerConflicts(1, 'NOME DELLA PROPOSTA', 'email@email.it');
});

Mailbook::add(function (): WeeklyReminder {
    return new WeeklyReminder(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ProposalEvaluationUpdate {
    return new ProposalEvaluationUpdate(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ApplicationReviewOutcome {
    return new ApplicationReviewOutcome('email@email.it', 1, 'NOME DELLA PROPOSTA', 'rejected');
});

Mailbook::add(function (): ApplicationRejected {
    return new ApplicationRejected(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): ApplicationInMainList {
    return new ApplicationInMainList(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): SolictUser {
    return new SolictUser(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): CloseProposal {
    return new CloseProposal(1, 'NOME DELLA PROPOSTA');
});

Mailbook::add(function (): CloseProcess {
    return new CloseProcess(1, 'NOME DELLA PROPOSTA');
});
