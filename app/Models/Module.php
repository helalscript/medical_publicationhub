<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'mentor_id',
        'type',
        'title',
        'video',
        'attached_file',
        'attached_text',
        'document',
        'order_by',
        'durations',
        'is_locked',
        'status',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

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

    public function getVideoAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function getAttachedFileAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function getDocumentAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
