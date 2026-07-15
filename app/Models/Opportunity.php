<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'category_ids',
        'title',
        'slug',
        'overview',
        'price',
        'thumbnail',
        'desciptions',
        'dead_line',
        'durations',
        'start_date',
        'end_date',
        'speciality_ids',
        'studydesign_type_ids',
        'contribution_type_ids',
        'university_hospital_ids',
        'place_ids',
        'experience_level_ids',
        'is_future',
        'status',
        'is_verified',
        'verification_deadline',
        'coordinator_id',
    ];

    protected function casts(): array
    {
        return [
            'category_ids' => 'array',
            'desciptions' => 'array',
            'slug' => 'string',
            'overview' => 'string',
            'dead_line' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'speciality_ids' => 'array',
            'studydesign_type_ids' => 'array',
            'contribution_type_ids' => 'array',
            'university_hospital_ids' => 'array',
            'place_ids' => 'array',
            'experience_level_ids' => 'array',
            'is_future' => 'boolean',
            'price' => 'decimal:2',
            'is_verified' => 'boolean',
            'verification_deadline' => 'date',
        ];
    }

    private function toPublicUrl(?string $value): ?string
    {
        if (empty($value)) {
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

    public function getThumbnailAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(OpportunityApplication::class);
    }

    public function chat(): MorphOne
    {
        return $this->morphOne(Chat::class, 'chatable');
    }

    public function favourites(): MorphMany
    {
        return $this->morphMany(Favourite::class, 'favouriteable');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
}
