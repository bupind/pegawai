<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'originalName',
        'alias',
        'path',
        'filename',
        'size',
        'extension',
        'mimeType',
        'additionalInformation',
    ];

    protected $appends = ['full_path'];
    protected $casts   = [
        'additionalInformation' => 'array',
    ];

    public function getFullPathAttribute()
    {
        return asset('storage/' . $this->path);
    }


}
