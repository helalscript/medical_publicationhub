<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'chat_id',
        'user_id',
        'message',
        'file',
        'seen_at',
    ];

    protected function casts(): array
    {
        return [
            'seen_at' => 'datetime',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileAttribute($value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // For API responses return an absolute public URL (so mobile can load it).
        if (request()->is('api/*')) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            // Legacy uploads are stored in `public/uploads/...`
            if (str_starts_with($value, 'uploads/')) {
                return asset($value);
            }

            // New uploads are stored in `storage/app/public/...` (value is disk path)
            if (str_starts_with($value, 'storage/')) {
                return asset($value);
            }

            return asset('storage/'.$value);
        }

        // For web: keep DB value as-is (path).
        return $value;
    }
}

