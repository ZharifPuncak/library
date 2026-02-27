<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDetail;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class UserCollectionController extends Controller
{
    /**
     * Display a listing of unique collections (Albums).
     */
    public function index()
    {
        // Get unique collection names from asset_details
        $collectionNames = AssetDetail::where('key', 'collection')
            ->distinct()
            ->pluck('value');

        // Fetch one representative asset for each collection for the thumbnail
        $collections = $collectionNames->map(function ($name) {
            $representative = Asset::whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->first();

            $count = Asset::whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->count();

            return (object) [
                'name' => $name,
                'thumbnail_path' => $representative?->thumbnail_path ?? $representative?->file_path,
                'type' => $representative?->type,
                'count' => $count,
                'date' => $representative?->created_at,
            ];
        });

        return view('users.collections.index', compact('collections'));
    }

    /**
     * Display all assets within a specific collection.
     */
    public function show($name)
    {
        $assets = Asset::whereHas('details', function ($q) use ($name) {
            $q->where('key', 'collection')->where('value', $name);
        })
        ->with(['categories', 'tags'])
        ->latest()
        ->paginate(12);

        $counts = [
            'total' => Asset::whereHas('details', function($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->count(),
            'photo' => Asset::whereHas('details', function($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->where('type', 'photo')->count(),
            'video' => Asset::whereHas('details', function($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->where('type', 'video')->count(),
            'ebook' => Asset::whereHas('details', function($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->where('type', 'ebook')->count(),
        ];

        $collectionName = $name;

        return view('users.collections.show', compact('assets', 'collectionName', 'counts'));
    }

    /**
     * Display a specific asset within a collection context.
     */
    public function showAsset($name, Asset $asset)
    {
        $asset->load(['details', 'categories', 'tags']);
        $asset->incrementViews();

        // Fetch all assets in this collection for the sidebar
        $relatedAssets = Asset::whereHas('details', function ($q) use ($name) {
            $q->where('key', 'collection')->where('value', $name);
        })
        ->where('id', '!=', $asset->id)
        ->latest()
        ->get();

        $collectionName = $name;
        $isCollectionContext = true;

        // Recently viewed logic (optional but good for consistency)
        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('users.show', compact('asset', 'relatedAssets', 'collectionName', 'isCollectionContext'));
    }
}
