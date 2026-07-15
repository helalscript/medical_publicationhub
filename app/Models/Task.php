<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'mentor_id',
        'title',
        'description',
        'rubric_id',
        'deadline',
        'video_url',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }
}
