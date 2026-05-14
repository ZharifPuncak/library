<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class MediaController extends Controller
{
    /**
     * Unified listing. Filters via query string:
     *   ?type=Photo|Video|Ebook
     *   ?search=..., ?categories[]=..., ?tag=..., ?year=YYYY
     */
    public function index(Request $request)
    {
        $query = Media::with(['details', 'categories', 'tags']);

        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('file_path', 'like', "%$search%")
                  ->orWhereHas('details', fn($m) => $m->where('value', 'like', "%$search%"))
                  ->orWhereHas('tags',    fn($t) => $t->where('name',  'like', "%$search%"));
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($request->categories) {
            $query->whereHas('categories', fn($q) =>
                $q->whereIn('categories.id', $request->categories)
            );
        }

        if ($request->tag) {
            $query->whereHas('tags', fn($q) => $q->where('tags.id', $request->tag));
        }

        if ($year = $request->year) {
            $query->whereYear('created_at', $year);
        }

        $sortDir = strtolower((string) $request->input('sort')) === 'oldest' ? 'asc' : 'desc';
        $assets  = $query->orderBy('created_at', $sortDir)->paginate(16)->withQueryString();
        $categories = Category::all();
        $tags = Tag::all();
        $featured = $assets->first();

        // Counts per type for the sidebar nav badges (case-insensitive).
        $typeCounts = [
            'total' => Media::count(),
            'photo' => Media::whereRaw('LOWER(type) = ?', ['photo'])->count(),
            'video' => Media::whereRaw('LOWER(type) = ?', ['video'])->count(),
            'ebook' => Media::whereRaw('LOWER(type) = ?', ['ebook'])->count(),
        ];

        // View mode (grid/list) — persisted via cookie so it survives navigation.
        $requestedView = strtolower((string) $request->input('view'));
        $cookieView    = strtolower((string) $request->cookie('media_view', ''));
        $view          = in_array($requestedView, ['grid', 'list'], true)
            ? $requestedView
            : (in_array($cookieView, ['grid', 'list'], true) ? $cookieView : 'grid');

        $response = response()->view('users.all',
            compact('assets', 'categories', 'tags', 'featured', 'typeCounts', 'view')
        );

        // Persist the choice for 30 days whenever it changes or hasn't been set.
        if ($view !== $cookieView) {
            $response->cookie('media_view', $view, 60 * 24 * 30);
        }

        return $response;
    }

    public function show(Media $media)
    {
        $asset = $media;
        $asset->load(['details', 'categories', 'tags']);
        $asset->incrementViews();

        $collectionDetail = $asset->details->where('key', 'collection')->first();
        $collectionName = $collectionDetail ? $collectionDetail->value : null;

        $relatedAssets = collect();

        if ($asset->categories && $asset->categories->isNotEmpty()) {
            $categoryIds = $asset->categories->pluck('id');
            $relatedAssets = Media::whereHas('categories', function($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                })
                ->where('media.id', '!=', $asset->id)
                ->limit(10)
                ->get();
        }

        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('users.show', compact('asset', 'relatedAssets', 'collectionName'));
    }
}
