<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'mentor_id',
        'opportunity_id',
        'title',
        'description',
        'rubric_id',
        'deadline',
        'video_url',
        'document_path',
        'document_name',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function getDocumentPathAttribute($value)
    {
        if (empty($value)) return $value;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        if (str_starts_with($value, 'storage/')) return asset($value);
        return asset('storage/' . $value);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
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
