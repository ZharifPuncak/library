<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
    ];

    /**
     * Route binding uses the UUID, so URLs read /collections/{uuid}.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $collection) {
            if (empty($collection->uuid)) {
                $collection->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Media currently tagged with this collection's name in `media_details`.
     */
    public function media()
    {
        return Media::whereHas('details', function ($q) {
            $q->where('key', 'collection')->where('value', $this->name);
        });
    }
}
