<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiFeedback extends Model
{
    protected $table = 'ai_feedback';

    protected $fillable = [
        'submission_id',
        'overall_score',
        'rubric_scores',
        'feedback_comments',
        'general_feedback',
        'flagged_issues',
        'edited_excerpt',
        'mentor_score_override',
        'mentor_comments_override',
        'status',
        'mentor_id',
    ];

    protected $casts = [
        'rubric_scores' => 'array',
        'feedback_comments' => 'array',
        'flagged_issues' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
