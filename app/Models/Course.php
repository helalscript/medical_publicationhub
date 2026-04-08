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
