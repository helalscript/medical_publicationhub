<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'researcher_id',
        'status',
        'is_notified',
    ];

    protected $appends = ['title', 'deadline', 'description', 'video_url'];

    protected $casts = [
        'is_notified' => 'boolean',
    ];

    public function getTitleAttribute()
    {
        return $this->task?->title;
    }

    public function getDeadlineAttribute()
    {
        return $this->task?->deadline?->toIso8601String();
    }

    public function getDescriptionAttribute()
    {
        return $this->task?->description;
    }

    public function getVideoUrlAttribute()
    {
        return $this->task?->video_url;
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function researcher()
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
