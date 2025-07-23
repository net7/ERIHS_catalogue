<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalService extends Model
{
    use HasFactory;

    const TO_BE_DEFINED = 'to_be_defined';
    const FEASIBLE = 'feasible';
    const NOT_FEASIBLE = 'not_feasible';

    const ACCESS_SCHEDULED = 'scheduled';
    const ACCESS_CARRIED_OUT = 'carried_out';


    protected $table = 'proposal_service';

    protected $guarded = ['id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function hasFeasibility()
    {
        return ($this->feasible != self::TO_BE_DEFINED && $this->feasible != null);
    }

    public function isNotFeasible()
    {
        return ($this->feasible == self::NOT_FEASIBLE);
    }

    public function isFeasible()
    {
        return ($this->feasible == self::FEASIBLE);
    }
}
