<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
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
            self::STATUS_ACTIVE   => __('Berlaku'),
            self::STATUS_INACTIVE => __('Akan Kadaluarsa'),
            self::STATUS_EXPIRED  => __('Tidak Berlaku'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_NURSE   => __('Perawat'),
            self::TYPE_DOCTOR  => __('Dokter'),
            self::TYPE_MEDICAL => __('Penunjang Medis'),
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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId');
    }
}