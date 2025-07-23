<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'closing_procedures_carried_out' => 'boolean'
    ];
    public function getLabelAttribute(): string
    {
        return '(' . Carbon::parse($this->start_date)->format('Y/m/d') . ' - ' . Carbon::parse($this->end_date)->format('Y/m/d') . ') ' . $this->name;
    }

    public function proposals(){
        return $this->hasMany(Proposal::class);
    }

    public function isOpen(){
        return (Carbon::today() >= Carbon::create($this->start_date) && Carbon::today() <= Carbon::create($this->end_date));
    }

    public function isClosed(){
        return Carbon::today() > Carbon::create($this->end_date);
    }

    public function isProcessed(){
        return $this->closing_procedures_carried_out;
    }

    public function isClosedAndProcessed(){
        return $this->isClosed() && $this->isProcessed();
    }

    public function setIsProcessed(){
        $this->update(['closing_procedures_carried_out' => true]);
    }
}
