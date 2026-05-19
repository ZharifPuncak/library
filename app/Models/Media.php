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
        'status',
        'title',
        'file_path',
        'file_url',
        'date',
        'thumbnail_path',
    ];

    public const STATUSES = ['draft', 'published', 'archived'];

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

    /**
     * Resolved URL to the underlying file — either the external link
     * (when stored via "Use link") or the storage-relative upload.
     */
    public function getResourceUrlAttribute(): ?string
    {
        if (!empty($this->file_url)) {
            return $this->file_url;
        }
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Best-effort thumbnail URL, falling back to the default project logo.
     * Priority: explicit thumbnail_path → photo's own source → default logo.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $default = asset('images/logo.png');

        if (!empty($this->thumbnail_path)) {
            return asset('storage/' . $this->thumbnail_path);
        }
        if (strtolower((string) $this->type) === 'photo') {
            if (!empty($this->file_url))  return $this->file_url;
            if (!empty($this->file_path)) return asset('storage/' . $this->file_path);
        }
        return $default;
    }

    /**
     * True when the row has a real cover image (not the logo fallback).
     */
    public function getHasRealThumbnailAttribute(): bool
    {
        return $this->thumbnail_path
            || (strtolower((string) $this->type) === 'photo' && ($this->file_url || $this->file_path));
    }

    /** Scope: only published rows (for non-admin listings). */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Scope: rows that belong to a given collection name. */
    public function scopeInCollection($query, string $name)
    {
        return $query->whereHas('details', fn($q) =>
            $q->where('key', 'collection')->where('value', $name)
        );
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
