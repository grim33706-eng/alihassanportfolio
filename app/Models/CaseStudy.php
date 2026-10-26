<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseStudy extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'brand',
        'industry',
        'country',
        'category',
        'platforms',
        'period',
        'strategy',
        'improvements',
        'metrics',
        'is_demo',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'platforms' => 'array',
            'metrics' => 'array',
            'is_demo' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(CaseStudyImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function beforeImages()
    {
        return $this->images->where('stage', 'before')->values();
    }

    public function afterImages()
    {
        return $this->images->where('stage', 'after')->values();
    }

    /**
     * A slider needs at least one real screenshot on each side.
     */
    public function hasComparison(): bool
    {
        return $this->beforeImages()->isNotEmpty() && $this->afterImages()->isNotEmpty();
    }

    /**
     * Metrics with the change between before and after worked out, when both are numeric.
     */
    public function metricRows(): array
    {
        return collect($this->metrics ?? [])
            ->filter(fn ($m) => filled($m['label'] ?? null) && filled($m['after'] ?? null))
            ->map(function ($m) {
                $before = is_numeric($m['before'] ?? null) ? (float) $m['before'] : null;
                $after = is_numeric($m['after']) ? (float) $m['after'] : null;
                $direction = ($m['direction'] ?? 'up') === 'down' ? 'down' : 'up';
                $change = ($before && $after !== null) ? round(($after - $before) / abs($before) * 100) : null;
                $unit = trim($m['unit'] ?? '');
                // Currencies read before the number ($7.25, PKR 1,200); everything else after (4.2x, 35%).
                $isCurrency = (bool) preg_match('/^(\$|£|€|PKR|Rs\.?|AED|SAR|USD|GBP|EUR)$/i', $unit);

                return [
                    'label' => $m['label'],
                    'before' => $m['before'] ?? null,
                    'after' => $m['after'],
                    'after_numeric' => $after,
                    'prefix' => $isCurrency ? $unit.(strlen($unit) > 1 ? ' ' : '') : '',
                    'suffix' => $isCurrency ? '' : $unit,
                    'change' => $change,
                    'improved' => $change === null ? null : ($direction === 'up' ? $change > 0 : $change < 0),
                ];
            })
            ->values()
            ->all();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('id');
    }
}
