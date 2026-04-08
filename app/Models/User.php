<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'cover_photo',
        'birthdate',
        'institution',
        'gender',
        'password',
        'otp',
        'reset_password_token',
        'reset_password_token_expire_at',
        'otp_expires_at',
        'remember_token',
        'email_verified_at',
        'last_seen',
        'role',
        'status',
        'is_agreed_terms',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'reset_password_token',
        'reset_password_token_expire_at',
        'is_otp_verified',
        'created_at',
        'updated_at',
        // 'role',
        'status',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'is_otp_verified' => 'boolean',
            'reset_password_token_expires_at' => 'datetime',
            'last_seen' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarAttribute($value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // For API responses return an absolute public URL (so mobile can load it).
        if (request()->is('api/*')) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            // Legacy uploads are stored in `public/uploads/...`
            if (str_starts_with($value, 'uploads/')) {
                return asset($value);
            }

            // New uploads are stored in `storage/app/public/...` (value is disk path)
            if (str_starts_with($value, 'storage/')) {
                return asset($value);
            }

            return asset('storage/'.$value);
        }

        // For web: keep DB value as-is (path).
        return $value;
    }

    public function getCoverPhotoAttribute($value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (request()->is('api/*')) {
            if (str_starts_with($value, 'uploads/')) {
                return asset($value);
            }

            if (str_starts_with($value, 'storage/')) {
                return asset($value);
            }

            return asset('storage/'.$value);
        }

        return $value;
    }

    public function mentor(): HasOne
    {
        return $this->hasOne(Mentor::class);
    }

    public function researchIdeas(): HasMany
    {
        return $this->hasMany(ResearchIdea::class, 'user_id');
    }

    public function opportunityApplications(): HasMany
    {
        return $this->hasMany(OpportunityApplication::class);
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    public function chatUsers(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function courseAccesses(): HasMany
    {
        return $this->hasMany(UserCourseAccess::class);
    }

    public function moduleAccesses(): HasMany
    {
        return $this->hasMany(UserModuleAccess::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function watchLogs(): HasMany
    {
        return $this->hasMany(UserWatchLog::class);
    }
}
