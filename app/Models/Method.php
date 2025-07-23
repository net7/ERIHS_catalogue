<?php

namespace App\Models;

use App\Services\MethodService;
use App\Traits\CordraInterface;
use App\Traits\HasCordra;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class Method extends Model implements CordraInterface
{
    use HasFactory;
    use Searchable;
    use HasTags;
    use HasCordra;

    protected $guarded = ['id'];

    protected $casts = [
        'alternative_labels' => 'array',
        'method_parameter' => 'array'
    ];


    public function tools(){
        return $this->belongsToMany(Tool::class, 'method_service_tool');
    }


    public function services(){
        return $this->belongsToMany(Service::class, 'method_service_tool');
    }

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        unset($array['method_parameter']);
        $array ['alternative_labels'] = $this->getOriginal('alternative_labels');

        return $array;
    }

    public function toCordraJson()
    {
        return MethodService::createJsonToSend($this);
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

    public function parameters(): array
    {
        $result = [];
        if (!$this->method_parameter){
            return $result;
        }
        foreach ($this->method_parameter as $parameter) {
            $result []= [
                'type' => Tag::find($parameter['parameter_type_tag_field'])->name,
                'unit' => Tag::find($parameter['parameter_unit_tag_field'])->name,
                'value' => $parameter['parameter_value'],
                'tool' => $parameter['parameter_related_tool'],
            ];
        }
        return $result;
    }
}
