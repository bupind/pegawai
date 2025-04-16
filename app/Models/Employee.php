<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $table    = 'employee';
    protected $fillable = [
        'code',
        'name',
        'type',
        'gender',
        'user_id',
        'status',
    ];

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    const TYPE_NURSE   = 'nurse';
    const TYPE_DOCTOR  = 'doctor';
    const TYPE_MEDICAL = 'medical';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('app.label.active'),
            self::STATUS_INACTIVE => __('app.label.inactive'),
        ];
    }

    public static function genders(): array
    {
        return [
            self::GENDER_MALE   => __('app.label.male'),
            self::GENDER_FEMALE => __('app.label.female'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_NURSE   => __('app.label.nurse'),
            self::TYPE_DOCTOR  => __('app.label.doctor'),
            self::TYPE_MEDICAL => __('app.label.medical_support'),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->isoFormat('D MMMM Y HH:mm');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->isoFormat('D MMMM Y HH:mm');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function certificates()
    {
        return $this->hasMany(RegistrationCertificate::class, 'employeeId');
    }

    public function licenses()
    {
        return $this->hasMany(License::class, 'employeeId');
    }
}