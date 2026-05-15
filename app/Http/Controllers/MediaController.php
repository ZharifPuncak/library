<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Admin-only routes (create/store) — block non-admins here.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->isAdmin(), 403);
            return $next($request);
        })->only(['create', 'store']);
    }

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

        // Tags: support both ?tag=N (legacy single) and ?tags[]=N&tags[]=M (multi).
        $tagFilter = array_filter((array) $request->input('tags', $request->input('tag') ? [$request->input('tag')] : []));
        if (!empty($tagFilter)) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagFilter));
        }

        if ($year = $request->year) {
            $query->whereYear('created_at', $year);
        }

        $sortDir = strtolower((string) $request->input('sort')) === 'oldest' ? 'asc' : 'desc';
        $assets  = $query->orderBy('created_at', $sortDir)->paginate(12)->withQueryString();
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
            : (in_array($cookieView, ['grid', 'list'], true) ? $cookieView : 'list');

        $response = response()->view('users.all',
            compact('assets', 'categories', 'tags', 'featured', 'typeCounts', 'view')
        );

        // Persist the choice for 30 days whenever it changes or hasn't been set.
        if ($view !== $cookieView) {
            $response->cookie('media_view', $view, 60 * 24 * 30);
        }

        return $response;
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('users.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        // Accept either an uploaded file OR an external URL — at least one is required.
        $source = $request->input('source') === 'link' ? 'link' : 'upload';

        $rules = [
            'title'        => ['required', 'string', 'max:255'],
            'type'         => ['required', 'in:photo,video,ebook'],
            'date'         => ['nullable', 'date'],
            'location'     => ['nullable', 'string', 'max:255'],
            'thumbnail'    => ['nullable', 'image', 'max:1024'], // 1 MB
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['integer', 'exists:tags,id'],
        ];

        if ($source === 'link') {
            $rules['file_url'] = ['required', 'url', 'max:2048'];
        } else {
            $rules['file'] = ['required', 'file', 'max:51200']; // 50 MB
        }

        $data = $request->validate($rules);

        $filePath = $source === 'upload'
            ? $request->file('file')->store('media', 'public')
            : null;
        $fileUrl  = $source === 'link' ? $data['file_url'] : null;

        $thumbPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('media/thumbnails', 'public')
            : null;

        $media = Media::create([
            'title'          => $data['title'],
            'type'           => $data['type'],
            'file_path'      => $filePath,
            'file_url'       => $fileUrl,
            'thumbnail_path' => $thumbPath,
            'date'           => $data['date'] ?? now(),
        ]);

        if (!empty($data['categories'])) {
            $media->categories()->sync($data['categories']);
        }
        if (!empty($data['tags'])) {
            $media->tags()->sync($data['tags']);
        }

        // Location is a book-only detail; store as media_detail key/value.
        if ($data['type'] === 'ebook' && !empty($data['location'] ?? null)) {
            $media->details()->create([
                'key'   => 'location',
                'value' => $data['location'],
            ]);
        }

        return redirect()
            ->route('media.show', $media)
            ->with('status', 'Media saved.');
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
