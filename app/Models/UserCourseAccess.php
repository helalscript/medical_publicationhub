<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCourseAccess extends Model
{
    use HasFactory;

    protected $table = 'user_course_access';

    protected $fillable = [
        'user_id',
        'course_id',
        'access_type',
        'start_at',
        'expire_at',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
