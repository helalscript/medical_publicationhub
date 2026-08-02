<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'task_assignment_id',
        'file_path',
        'original_filename',
        'content_text',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function taskAssignment()
    {
        return $this->belongsTo(TaskAssignment::class);
    }

    public function aiFeedback()
    {
        return $this->hasOne(AiFeedback::class, 'submission_id');
    }
}
