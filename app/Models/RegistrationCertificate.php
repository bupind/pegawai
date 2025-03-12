<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationCertificate extends Model
{
    use HasFactory;

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const TYPE_NURSE  = 'nurse';
    const TYPE_DOCTOR = 'doctor';
    protected $table    = 'registration_certificate';
    protected $fillable = [
        'employeeId',
        'type',
        'registrationNumber',
        'competence',
        'certificateOfCompetenceNumber',
        'validFrom',
        'validUntil',
        'registered_by',
        'status',
    ];

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_NURSE  => __('Nurse'),
            self::TYPE_DOCTOR => __('Doctor'),
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
        return $this->belongsTo(User::class, 'registered_by');
    }
}