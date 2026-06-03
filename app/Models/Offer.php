<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon',
        'promo_code',
        'discount_percentage',
        'end_date',
        'background_color',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'icon' => 'string',
            'promo_code' => 'string',
            'discount_percentage' => 'integer',
            'end_date' => 'datetime',
            'background_color' => 'string',
            'sort_order' => 'integer',
            'status' => 'string',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('end_date', '>=', now());
    }

    public function isExpired(): bool
    {
        return $this->end_date instanceof Carbon
            ? $this->end_date->isPast()
            : Carbon::parse($this->end_date)->isPast();
    }

    public function isIconImage(): bool
    {
        $icon = $this->getRawOriginal('icon');

        if (empty($icon)) {
            return false;
        }

        $icon = (string) $icon;

        return str_starts_with($icon, 'uploads/')
            || str_starts_with($icon, 'storage/')
            || filter_var($icon, FILTER_VALIDATE_URL);
    }

    public function getIconAttribute($value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (! $this->isIconImage()) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (! request()->is('api/*')) {
            return $value;
        }

        if (str_starts_with($value, 'uploads/')) {
            return asset($value);
        }

        if (str_starts_with($value, 'storage/')) {
            return asset($value);
        }

        return asset('storage/'.$value);
    }

    public function countdown(): array
    {
        $end = $this->end_date instanceof Carbon
            ? $this->end_date->copy()
            : Carbon::parse($this->end_date);

        if ($end->isPast()) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'total_seconds' => 0,
                'is_expired' => true,
            ];
        }

        $diff = now()->diff($end);

        return [
            'days' => (int) $diff->days,
            'hours' => (int) $diff->h,
            'minutes' => (int) $diff->i,
            'seconds' => (int) $diff->s,
            'total_seconds' => (int) now()->diffInSeconds($end),
            'is_expired' => false,
        ];
    }
}
