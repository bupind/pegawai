<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    const LOGIN_TRUE  = 'true';
    const LOGIN_FALSE = 'false';

    protected $fillable = [
        'user_id',
        'logo',
        'favicon',
        'name',
        'employeecanlogin',
        'short_name',
        'description'
    ];

    public static function canLogin(): array
    {
        return [
            self::LOGIN_TRUE  => __('app.label.true'),
            self::LOGIN_FALSE => __('app.label.false'),
        ];
    }

    protected $appends = ['full_path_logo', 'full_path_favicon'];

    public function getFullPathLogoAttribute()
    {
        return $this->attributes['logo'] == null ? asset('logo.svg') : asset('storage/image/setting/' . $this->attributes['logo']);
    }

    public function getFullPathFaviconAttribute()
    {
        return $this->attributes['favicon'] == null ? asset('favicon.png') : asset('storage/image/setting/' . $this->attributes['favicon']);
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->isoFormat('D MMMM Y HH:mm');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->isoFormat('D MMMM Y HH:mm');
    }

    /**
     * Get the user that owns the Setting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
