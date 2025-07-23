<?php

namespace App\Models;

use App\Services\ServiceService;
use App\Traits\CordraInterface;
use App\Traits\HasCordra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Support\Collection;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class Service extends Model implements CordraInterface
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use Searchable;
    use HasTags;
    use HasCordra;

    protected $guarded = ['id'];
    protected $with = ['methodServiceTool', 'organization', 'serviceManagers'];
    protected $casts = [
        'contacts' => 'array',
        'categories' => 'array',
        'functions' => 'array',
        'measurable_properties' => 'array',
        'resources' => 'array',
        'links' => 'array',
    ];

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }


    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'method_service_tool');
    }

    public function methods()
    {
        return $this->belongsToMany(Method::class, 'method_service_tool');
    }

    public function serviceManagers(){
        return $this->belongsToMany(User::class, 'service_manager_service');
    }

    public function methodServiceTool()
    {
        return $this->hasMany(MethodServiceTool::class, 'service_id');
    }

    public function shouldBeSearchable(): bool
    {
        return count($this->toSearchableArray()) > 0;
    }

    public function searchableAs(): string
    {
        return config('app.elastic_index');
    }

    public function techniques()
    {
        $result = [];
        $techniques = $this->tagsWithType('technique')->all();
        if (isset($techniques)) {
            foreach ($techniques as $technique) {
                $result[] = $technique->name;
            }
        }
        return $result;
    }

    public function materials()
    {
        $result = [];
        $measurable_properties = $this->measurable_properties;
        if (isset($measurable_properties)) {
            foreach ($measurable_properties as $property) {
                if (isset($property['materials_tag_field'])) {
                    foreach (collect($property['materials_tag_field']) as $material_id) {
                        $result[] = Tag::find($material_id)->name;
                    }
                }
            }
        }
        return $result;
    }

    public function researchDisciplines()
    {
        $result = [];
        $research_disciplines = $this->tagsWithType('research_disciplines')->all();
        if (isset($research_disciplines)) {
            foreach ($research_disciplines as $discipline) {
                $result[] = $discipline->name;
            }
        }
        return $result;
    }

    //Used in catalogue to sho tools
    public function getTools(): array
    {
        $tools = [];
        foreach ($this->methodServiceTool as $serviceTool) {
            $toolName = Tool::find($serviceTool->tool_id)?->name;
            if ($toolName && !in_array($toolName, $tools)) {
                $tools[] = $toolName;
            }
        }
        return $tools;
    }

    public function toSearchableArray(): array
    {
        $with = [
            'methodServiceTool',
            'methodServiceTool.method',
            'methodServiceTool.tool',
            'organization',
            'tags',
        ];
        $this->loadMissing($with);

        $array = $this->toArray();

        array_walk_recursive($array, function (&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        $array['organization']['country'] = $this->getOrganizationCountries();
        $array['techniques'] = $this->techniques();
        $array['materials'] = $this->materials();
        $array['research_disciplines'] = $this->researchDisciplines();
        $array['platforms'] = $this->getPlatforms()->toArray();

        unset($array['tags']);

        foreach ($array['method_service_tool'] as $id => $data) {
            unset($array['method_service_tool'][$id]['method']['method_parameter']);
        }

        return $array;
    }

    public function getOrganizationCountries()
    {
        $result = [];
        $organization = $this->organization;
        if (isset($organization)) {
            $organizationCountry = $organization->tagsWithType('country');
            foreach ($organizationCountry as $country) {
                $result[] = $country->name;
            }
        }

        return $result;
    }

    public function getProviderRoles()
    {
        $result = [];
        $providerRole = $this->tagsWithType('provider_role');
        foreach ($providerRole as $role) {
            $result[] = $role->name;
        }
        return $result;
    }



    public function getReadinessLevels()
    {
        $result = [];
        $readinessLevels = $this->tagsWithType('readiness_level')->all();
        foreach ($readinessLevels as $level) {
            $result[] = $level->name;
        }
        return $result;
    }

    public function getOperatingLanguages()
    {
        $result = [];
        $operatingLanguages = $this->tagsWithType('operating_language')->all();
        foreach ($operatingLanguages as $language) {
            $result[] = $language->name;
        }
        return $result;
    }

    public function getPeriodUnit()
    {
        $periodUnit = $this->tagsWithType('period_unit')->all();
        $result = [];
        foreach ($periodUnit as $unit) {
            $result[] = $unit->name;
        }
        return $result;
    }

    public function getTechniques()
    {
        $result = [];
        $techniques = $this->tagsWithType('technique')->all();
        foreach ($techniques as $technique) {
            $result[] = $technique->name;
        }
        return $result;
    }

    public function getPlatform()
    {
        $name = '';
        $platform = $this->tagsWithType('e-rihs_platform')->first();
        if ($platform) {
            $name = $platform->name;
        }
        return $name;
    }

    public function getPlatforms()
    {
        $servicePlatforms = new Collection();
        $platforms = $this->tagsWithType('e-rihs_platform')->all();
        if ($platforms) {
            foreach ($platforms as $platform)
                $servicePlatforms->add($platform->name);
        }
        return $servicePlatforms;
    }

    public function platformTags()
{
    return $this->morphToMany(Tag::class, 'taggable')
        ->where('type', 'e-rihs_platform');
}


    public function getResearchDisciplines()
    {
        $result = [];
        $researchDisciplines = $this->tagsWithType('research_disciplines')->all();
        foreach ($researchDisciplines as $discipline) {
            $result[] = $discipline->name;
        }
        return $result;
    }

    public function toCordraJson()
    {
        return ServiceService::createJsonToSend($this);
    }

    public function isFeasible($proposal_id)
    {
        return ProposalService::query()
            ->where('proposal_id', '=', $proposal_id)
            ->where('service_id', '=', $this->id)
            ->where('feasible', '=', 'feasible')->get()->count() > 0 ? 'checked' : '';
    }

    public function isScheduled($proposal_id)
    {
        return ProposalService::query()
            ->where('proposal_id', '=', $proposal_id)
            ->where('service_id', '=', $this->id)
            ->whereIn('access', [ProposalService::ACCESS_SCHEDULED])
            ->get()->count() > 0 ? true : false;
    }

    public function isCarriedOut($proposal_id)
    {
        return ProposalService::query()
            ->where('proposal_id', '=', $proposal_id)
            ->where('service_id', '=', $this->id)
            ->whereIn('access',  [ProposalService::ACCESS_CARRIED_OUT])
            ->get()->count() > 0 ? true : false;
    }
    public function isScheduledOrCarriedOut($proposal_id)
    {

        return ProposalService::query()
            ->where('proposal_id', '=', $proposal_id)
            ->where('service_id', '=', $this->id)
            ->whereIn('access', [ProposalService::ACCESS_SCHEDULED, ProposalService::ACCESS_CARRIED_OUT])
            ->get()->count() > 0 ? true : false;
    }

    public function isActive(): bool {
        return $this->service_active;
    }
}
