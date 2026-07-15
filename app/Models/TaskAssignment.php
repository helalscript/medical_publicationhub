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

    protected $casts = [
        'is_notified' => 'boolean',
    ];

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
