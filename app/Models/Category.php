<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class, 'media_category', 'category_id', 'media_id');
    }
}
