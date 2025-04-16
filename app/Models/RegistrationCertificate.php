<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationCertificate extends Model
{
    use HasFactory;

    const STATUS_VALID    = 'valid';
    const STATUS_INACTIVE = 'nearExpiration';
    const STATUS_EXPIRED  = 'expired';

    const TYPE_NURSE   = 'nurse';
    const TYPE_DOCTOR  = 'doctor';
    const TYPE_MEDICAL = 'medical';

    protected $table    = 'registration_certificate';
    protected $fillable = [
        'employeeId',
        'registrationNumber',
        'competence',
        'validFrom',
        'validUntil',
        'registered_by',
        'status',
    ];

    public static function statuses()
    {
        return [
            self::STATUS_VALID    => __('app.label.valid'),
            self::STATUS_INACTIVE => __('app.label.near_expiration'),
            self::STATUS_EXPIRED  => __('app.label.invalid'),
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
        return $query->where('status', self::STATUS_VALID);
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }
}