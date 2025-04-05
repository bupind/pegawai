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
        'gender',
        'status',
    ];

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Aktif'),
            self::STATUS_INACTIVE => __('Tidak Aktif'),
        ];
    }

    public static function genders(): array
    {
        return [
            self::GENDER_MALE   => __('Laki Laki'),
            self::GENDER_FEMALE => __('Perempuan'),
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
        return $this->belongsTo(User::class);
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