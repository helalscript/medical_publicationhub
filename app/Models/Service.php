<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'overview',
        'description',
        'starting_price',
        'whats_app_num',
        'is_features',
        'created_by',
        'status',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'is_features' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
