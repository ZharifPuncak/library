<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_id',
        'key',
        'value',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
