<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_ids',
        'title',
        'price',
        'old_price',
        'short_description',
        'thumbnail',
        'thumbnail_description',
        'thumbnail_button_text',
        'intro_video',
        'overview',
        'descriptions',
        'mentor_ids',
        'is_future',
        'total_durations',
        'start_module',
        'status',
    ];

    protected $casts = [
        'category_ids' => 'array',
        'descriptions' => 'array',
        'mentor_ids' => 'array',
        'is_future' => 'boolean',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
    ];

    private function toPublicUrl(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Only convert paths when returning API JSON.
        if (! request()->is('api/*')) {
            return $value;
        }

        if (str_starts_with($value, 'uploads/')) {
            return asset($value);
        }

        if (str_starts_with($value, 'storage/')) {
            return asset($value);
        }

        // `Storage::disk('public')->putFileAs()` returns a disk-relative path.
        return asset('storage/'.$value);
    }

    public function getThumbnailAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function getIntroVideoAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function userAccesses(): HasMany
    {
        return $this->hasMany(UserCourseAccess::class);
    }
}
