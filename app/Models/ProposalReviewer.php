<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalReviewer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'proposal_reviewer';

    public function proposal(){
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

}
