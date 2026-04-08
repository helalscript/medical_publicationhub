<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'file',
        'video',
        'attached_text',
        'attached_file',
        'document',
        'order_by',
        'durations',
        'status',
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

    public function getFileAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
