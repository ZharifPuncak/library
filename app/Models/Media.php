<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'type',
        'title',
        'file_path',
        'file_url',
        'date',
        'thumbnail_path',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Route model binding uses the UUID, so URLs read /media/{uuid}.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $media) {
            if (empty($media->uuid)) {
                $media->uuid = (string) Str::uuid();
            }
        });
    }

    public function details()
    {
        return $this->hasMany(MediaDetail::class, 'media_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'media_category', 'media_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'media_tag', 'media_id', 'tag_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDetail($key, $default = null)
    {
        $detail = $this->details->where('key', $key)->first();
        return $detail ? $detail->value : $default;
    }

    public function getCollectionAttribute()
    {
        return $this->getDetail('collection');
    }

    public function incrementViews()
    {
        $viewDetail = $this->details()->where('key', 'views')->first();

        if ($viewDetail) {
            $viewDetail->update([
                'value' => (int)$viewDetail->value + 1
            ]);
        } else {
            $this->details()->create([
                'key' => 'views',
                'value' => '1'
            ]);
        }
    }
}
