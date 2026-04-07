<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';
    protected $fillable = ['question', 'answer', 'status', 'category_id'];

    protected $casts = [
        'question' => 'string',
        'answer' => 'string',
        'status' => 'string',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
