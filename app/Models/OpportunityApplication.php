<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityApplication extends Model
{
    protected $fillable = [
        'user_id',
        'opportunity_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'highest_edu_level',
        'institution',
        'field_of_study',
        'research_experience',
        'current_position',
        'linkedin_profile',
        'personal_website',
        'documents',
        'cover_letter',
        'accept_term',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'accept_term' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

}

