<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MethodServiceTool extends Pivot
{
    use HasFactory;
    protected  $table = 'method_service_tool';
    protected $guarded = ['id'];

    public function tool (){
        return $this->belongsTo(Tool::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function method(){
        return $this->belongsTo(Method::class);
    }
}
