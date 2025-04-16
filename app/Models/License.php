<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    const STATUS_VALID    = 'valid';
    const STATUS_INACTIVE = 'nearExpiration';
    const STATUS_EXPIRED  = 'expired';

    const TYPE_NURSE   = 'nurse';
    const TYPE_DOCTOR  = 'doctor';
    const TYPE_MEDICAL = 'medical';

    protected $table    = 'license';
    protected $fillable = [
        'employeeId',
        'type',
        'registrationNumber',
        'validFrom',
        'validUntil',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }
}