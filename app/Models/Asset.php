<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type',         // Photo, Video, Ebook, Slideshow, Document, etc
        'title',
        'file_path',
        'file_url',
        'date',
        'thumbnail_path',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    // ================================
    // DETAILS (EBOOK / VIDEO / ETC)
    // ================================
    public function details()
    {
        return $this->hasMany(AssetDetail::class, 'asset_id');
    }

    // ================================
    // CATEGORIES
    // ================================
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'asset_category', 'asset_id', 'category_id');
    }

    // ================================
    // TAGS
    // ================================
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'asset_tag', 'asset_id', 'tag_id');
    }

    // ================================
    // Uploader (Admin)
    // ================================
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get a specific detail value by key.
     */
    public function getDetail($key, $default = null)
    {
        $detail = $this->details->where('key', $key)->first();
        return $detail ? $detail->value : $default;
    }

    /**
     * Get the collection name if it exists.
     */
    public function getCollectionAttribute()
    {
        return $this->getDetail('collection');
    }

    /**
     * Increment the view count for this asset using asset_details.
     */
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
