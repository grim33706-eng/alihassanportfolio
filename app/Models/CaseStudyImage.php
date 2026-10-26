<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CaseStudyImage extends Model
{
    protected $fillable = [
        'stage',
        'path',
        'caption',
        'width',
        'height',
        'sort_order',
    ];

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }

    public function url(): string
    {
        // Relative to the current host, so it works wherever the site is served.
        return asset('storage/'.$this->path);
    }

    protected static function booted(): void
    {
        // Remove the file when its record goes (including cascades run through Eloquent).
        static::deleted(fn (self $image) => Storage::disk('public')->delete($image->path));
    }
}
