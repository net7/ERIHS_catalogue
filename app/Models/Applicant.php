<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'surname', 'nationality', 'birth_year', 'gender', 'home_institution', 'institution_address', 'institution_city', 'institution_status_code',
        'institution_country', 'job', 'academic_background', 'position', 'office_phone', 'mobile_phone', 'email'
    ];

    public function proposals(){
        return $this->hasMany(ApplicantProposal::class);
    }
}

