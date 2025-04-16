<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasRoles;

    const ROLE_SUPERUSER = 'superuser';
    const ROLE_PEGAWAI   = 'pegawai';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['profile_photo_url', 'full_photo'];

    public function getFullPhotoAttribute()
    {
        return $this->profile_photo_path === null ? asset('logo.svg') : asset('storage/' . $this->profile_photo_path);
    }

    /**
     * Format created_at attribute
     */
    public function getCreatedAtAttribute()
    {
        return isset($this->attributes['created_at']) && $this->attributes['created_at'] !== null ? Carbon::parse($this->attributes['created_at'])
            ->isoFormat('D MMMM Y HH:mm') : null;
    }

    /**
     * Format updated_at attribute
     */
    public function getUpdatedAtAttribute()
    {
        return isset($this->attributes['updated_at']) && $this->attributes['updated_at'] !== null ? Carbon::parse($this->attributes['updated_at'])
            ->isoFormat('D MMMM Y HH:mm') : null;
    }

    /**
     * Format email_verified_at attribute
     */
    public function getEmailVerifiedAtAttribute()
    {
        return isset($this->attributes['email_verified_at']) && $this->attributes['email_verified_at'] !== null ? Carbon::parse($this->attributes['email_verified_at'])
            ->isoFormat('D MMMM Y HH:mm') : null;
    }

    /**
     * Get all permissions as an array
     */
    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(fn($pr) => [$pr['name'] => true]);
    }
}
