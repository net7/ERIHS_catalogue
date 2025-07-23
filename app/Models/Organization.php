<?php

namespace App\Models;

use App\Services\OrganizationService;
use App\Traits\CordraInterface;
use App\Traits\HasCordra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class Organization extends Model implements CordraInterface
{
    use HasFactory;
    use HasTags;
    use HasCordra;

    protected $guarded = ['id'];

    protected $casts = [
        'external_pid' => 'array',
        'organization_type' => 'array',
        'part_of_organisations' => 'array',
        'webpages' => 'array',
        'research_disciplines_narrower' => 'array',
        'research_disciplines' => 'array',
        'research_references' => 'array',
    ];

    public function address()
    {
        return $this->hasOne('App\Models\Address');
    }

    public function toCordraJson(): array
    {
        return OrganizationService::createJsonToSend($this);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_user', 'organization_id', 'user_id');
    }

    public function getCountries()
    {
        $result = [];
        $countries = $this->tagsWithType('country')->all();
        foreach ($countries as $county) {
            $result[] = $county->name;
        }
        return $result;
    }


    public function countries()
    {
        return $this->tags()->where('type', 'country');
    }

    public function getOrganizationTypes()
    {
        $result = [];
        $types = $this->tagsWithType('organisation_type')->all();
        foreach ($types as $type) {
            $result[] = $type->name;
        }
        return $result;
    }   

    public function getResearchDisciplines()
    {
        $result = [];
        $disciplines = $this->tagsWithType('research_disciplines')->all();
        foreach ($disciplines as $discipline) {
            $result[] = $discipline->name;
        }
        return $result;
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
    // Definiamo una relazione per i methods attraverso services
    public function methods()
    {
        return $this->hasManyThrough(Method::class, Service::class);
    }

    // Definiamo una relazione per i methods attraverso services
    public function tools()
    {
        return $this->hasMany(Tool::class);
    }
}
