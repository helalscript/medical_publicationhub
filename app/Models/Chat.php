<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Chat extends Model
{
    protected $fillable = [
        'chatable_id',
        'chatable_type',
        'type',
        'status',
    ];

    public function chatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function users(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}

