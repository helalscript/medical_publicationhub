<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchIdea extends Model
{
    protected $table = 'reasearch_ideas';

    protected $fillable = [
        'user_id',
        'mentor_id',
        'study_title',
        'study_descritions',
        'research_objectives',
        'perposed_methodology',
        'dead_line',
        'start_date',
        'end_date',
        'submission_proof',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dead_line' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'submission_proof' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
