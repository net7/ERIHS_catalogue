<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class PostAccessReport extends Model
{
    use HasFactory;
    use HasTags;
    protected $fillable = ['proposal_id', 'user_id', 'summary', 'core_description', 'expected_publications', 'link', 'files', 'photos'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PostAccessReportPhotos::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(PostAccessReportFiles::class);
    }
}
