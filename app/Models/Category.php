<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'status',
    ];
    protected $casts = [
        'type' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'status' => 'string',
    ];
    public function faqs()
    {
        return $this->hasMany(Faq::class, 'category_id');
    }
}
