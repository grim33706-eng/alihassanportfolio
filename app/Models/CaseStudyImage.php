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
        return media_url($this->path);
    }

    protected static function booted(): void
    {
        // Remove the file when its record goes (including cascades run through Eloquent).
        static::deleted(function (self $image) {
            // Screenshots shipped with the site (assets/) are not uploads.
            if (! str_starts_with($image->path, 'assets/')) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
