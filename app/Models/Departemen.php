<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Departemen extends Model
{
    use HasFactory;

    protected $table      = 'departemen';
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    protected $fillable = [
        'name',
        'status',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
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

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}