<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_ids',
        'title',
        'overview',
        'description',
        'attachment',
        'status',
    ];

    protected $casts = [
        'category_ids' => 'array',
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

    public function getAttachmentAttribute($value): ?string
    {
        return $this->toPublicUrl($value);
    }
}
