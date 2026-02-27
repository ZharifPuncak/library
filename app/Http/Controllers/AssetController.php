<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AssetController extends Controller
{
    // ============================
    // LOAD BY TYPE (Photo/Video/Ebook/etc.)
    // ============================
    public function index(Request $request)
    {
        $type = $request->input('type', 'Photo');

        return $this->loadByType($type, $request);
    }

    // ============================
    // UNIVERSAL "ALL ASSETS" PAGE
    // ============================
    public function all(Request $request)
    {
        $assets = Asset::with(['details', 'categories', 'tags']);

        // Search
        if ($search = $request->input('search')) {
            $assets->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('file_path', 'like', "%$search%")
                  ->orWhereHas('details', fn($m) =>
                      $m->where('value', 'like', "%$search%")
                  )
                  ->orWhereHas('tags', fn($t) =>
                      $t->where('name', 'like', "%$search%")
                  );
            });
        }

        // Type filter
        if ($type = $request->input('type')) {
            $assets->where('type', $type);
        }

        // Category filter
        if ($request->categories) {
            $assets->whereHas('categories', fn($q) =>
                $q->whereIn('categories.id', $request->categories)
            );
        }

        // Tag filter
        if ($request->tag) {
            $assets->whereHas('tags', fn($q) =>
                $q->where('tags.id', $request->tag)
            );
        }

        // Year filter
        if ($year = $request->year) {
            $assets->whereYear('created_at', $year);
        }

        $categories = Category::all();
        $tags = Tag::all();
        $assets = $assets->latest()->paginate(9)->withQueryString();

        return view('users.all', compact('assets', 'categories', 'tags'));
    }



    // ============================
    // SHOW SINGLE ASSET
    // ============================
    public function show(Asset $asset)
    {
        $asset->load(['details', 'categories', 'tags']);
        $asset->incrementViews();

        // Detect collection
        $collectionDetail = $asset->details->where('key', 'collection')->first();
        $collectionName = $collectionDetail ? $collectionDetail->value : null;

        $relatedAssets = collect();

        if ($asset->categories && $asset->categories->isNotEmpty()) {
            // Load categorical gallery items
            $categoryIds = $asset->categories->pluck('id');
            $relatedAssets = Asset::whereHas('categories', function($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->where('assets.id', '!=', $asset->id)
                ->limit(10)
                ->get();
        }
        
        // Track recently viewed
        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('users.show', compact('asset', 'relatedAssets', 'collectionName'));
    }



    // ============================
    // REUSABLE HELPER (Photo, Video, Ebook)
    // ============================
    private function loadByType($type, Request $request)
    {
        $assets = Asset::where('type', $type)
            ->with(['details', 'categories', 'tags']);

        // Search
        if ($search = $request->search) {
            $assets->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('details', fn($m) =>
                      $m->where('value', 'like', "%$search%")
                  )
                  ->orWhereHas('tags', fn($t) =>
                      $t->where('name', 'like', "%$search%")
                  );
            });
        }

        // Category filter
        if ($request->categories) {
            $assets->whereHas('categories', fn($q) =>
                $q->whereIn('categories.id', $request->categories)
            );
        }

        // Tag filter
        if ($request->tag) {
            $assets->whereHas('tags', fn($q) =>
                $q->where('tags.id', $request->tag)
            );
        }

        // Year filter
        if ($year = $request->year) {
            $assets->whereYear('created_at', $year);
        }

        $categories = Category::all();
        $tags = Tag::all();
        $assets = $assets->latest()->paginate(9)->withQueryString();

        $viewMap = [
            'Photo' => 'users.photo',
            'Video' => 'users.video',
            'Ebook' => 'users.ebook',
        ];

        $view = $viewMap[$type] ?? 'users.all';

        return view($view, compact('assets', 'categories', 'tags', 'type'));
    }
    
    public function photos(Request $request)
    {
        return $this->loadByType('Photo', $request);
    }

    public function videos(Request $request)
    {
        return $this->loadByType('Video', $request);
    }

    public function ebooks(Request $request)
    {
        return $this->loadByType('Ebook', $request);
    }

}
