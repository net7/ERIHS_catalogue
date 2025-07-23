<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ApplicantProposal extends Pivot
{
    use HasFactory;

    protected $primaryKey = 'applicant_id';
    protected $foreignKey = 'proposal_id';

    public function applicant(){
        return $this->belongsTo(User::class);
    }

    public function proposal(){
        return $this->belongsTo(Proposal::class);
    }

}
