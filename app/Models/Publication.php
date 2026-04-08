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
}
