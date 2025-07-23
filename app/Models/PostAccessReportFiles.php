<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAccessReportFiles extends Model
{
    use HasFactory;

    protected $fillable = ['post_access_report_id', 'file_path'];
    /**
     * Get the post access report that owns the file.
     */
    public function postAccessReport()
    {
        return $this->belongsTo(PostAccessReport::class);
    }
}
