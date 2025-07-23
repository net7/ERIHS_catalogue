<?php

namespace App\Models;

use App\Services\ToolService;
use App\Traits\CordraInterface;
use App\Traits\HasCordra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Tool extends Model implements CordraInterface
{
    use HasFactory;
    use HasTags;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasCordra;


    protected $guarded = ['id'];

    protected $casts = ['url' => 'array'];

    public function toCordraJson()
    {
        return ToolService::createJsonToSend($this);
    }

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    public function proposals()
    {
        return $this->belongsToMany(Proposal::class, 'proposal_tool', 'tool_id', 'proposal_id');
    }

    public function methods()
    {
        return $this->belongsToMany(Method::class, 'method_service_tool');
    }

    public function methodServiceTools()
    {
        return $this->hasMany(MethodServiceTool::class, 'tool_id');
    }

    public function shouldBeSearchable()
    {
        return count($this->toSearchableArray()) > 0;
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'method_service_tool');
    }
}
