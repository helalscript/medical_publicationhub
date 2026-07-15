<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_global',
        'mentor_id',
        'criteria',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'criteria' => 'array',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
