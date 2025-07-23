<?php

namespace App\ProposalStatusActivities;

use App\Models\User;

interface StatusActivityInterface
{
    public function getType();

    public function getViewData(User $user = null, $viewType = null);

    public function toArray();

    public function getName();

}
