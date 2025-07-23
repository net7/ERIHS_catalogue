<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAccessReportPhotos extends Model
{
    use HasFactory;

    protected $fillable = ['post_access_report_id', 'image_path'];

    public function postAccessReport()
    {
        return $this->belongsTo(PostAccessReport::class);
    }
}
