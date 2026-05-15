<?php

namespace App\Http\Controllers;

use App\Models\Collection;
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
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Unified listing. Filters via query string:
     *   ?type=Photo|Video|Ebook
     *   ?search=..., ?categories[]=..., ?tag=..., ?year=YYYY
     */
    public function index(Request $request)
    {
        $query = Media::with(['details', 'categories', 'tags']);

        // Status filter — non-admins are pinned to published; admins can switch via ?status.
        $isAdmin       = (bool) (auth()->user()?->isAdmin());
        $activeStatus  = $isAdmin
            ? strtolower((string) $request->input('status', 'all'))
            : 'published';
        if (!in_array($activeStatus, ['all', 'draft', 'published', 'archived'], true)) {
            $activeStatus = 'all';
        }
        if ($activeStatus !== 'all') {
            $query->where('status', $activeStatus);
        }

        // Per-status counts for the admin tab strip (respects other filters EXCEPT status).
        $statusCounts = null;
        if ($isAdmin) {
            $countBase = (clone $query)->getQuery(); // base before paginate
            // Rebuild without status constraint via a fresh query that mirrors other filters.
            $statusCounts = [
                'all'       => 0, 'draft' => 0, 'published' => 0, 'archived' => 0,
            ];
            $countsQuery = Media::query();
            // Apply same non-status filters so counts reflect the current view.
            if ($search = $request->input('search')) {
                $countsQuery->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('file_path', 'like', "%$search%")
                      ->orWhereHas('details', fn($m) => $m->where('value', 'like', "%$search%"))
                      ->orWhereHas('tags',    fn($t) => $t->where('name',  'like', "%$search%"));
                });
            }
            if ($type = $request->input('type')) {
                $countsQuery->where('type', $type);
            }
            if ($request->categories) {
                $countsQuery->whereHas('categories', fn($q) =>
                    $q->whereIn('categories.id', $request->categories)
                );
            }
            $tagFilterForCount = array_filter((array) $request->input('tags', $request->input('tag') ? [$request->input('tag')] : []));
            if (!empty($tagFilterForCount)) {
                $countsQuery->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagFilterForCount));
            }
            if ($year = $request->year) {
                $countsQuery->whereYear('created_at', $year);
            }
            $statusCounts['all'] = (clone $countsQuery)->count();
            foreach (['draft', 'published', 'archived'] as $s) {
                $statusCounts[$s] = (clone $countsQuery)->where('status', $s)->count();
            }
        }

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

        // Counts per type for the sidebar nav badges.
        // Non-admins only see published items in the listing, so the counts mirror that.
        $countBase = Media::query();
        if (!$isAdmin) {
            $countBase->where('status', 'published');
        }
        $typeCounts = [
            'total' => (clone $countBase)->count(),
            'photo' => (clone $countBase)->whereRaw('LOWER(type) = ?', ['photo'])->count(),
            'video' => (clone $countBase)->whereRaw('LOWER(type) = ?', ['video'])->count(),
            'ebook' => (clone $countBase)->whereRaw('LOWER(type) = ?', ['ebook'])->count(),
        ];

        // Sidebar extra counts.
        $collectionCount = Collection::count();
        $myListCount     = ($u = auth()->user())
            ? $u->myList()->count() + $u->myListCollections()->count()
            : 0;

        // View mode (grid/list) — persisted via cookie so it survives navigation.
        $requestedView = strtolower((string) $request->input('view'));
        $cookieView    = strtolower((string) $request->cookie('media_view', ''));
        $view          = in_array($requestedView, ['grid', 'list'], true)
            ? $requestedView
            : (in_array($cookieView, ['grid', 'list'], true) ? $cookieView : 'list');

        $response = response()->view('users.all',
            compact('assets', 'categories', 'tags', 'featured', 'typeCounts', 'view', 'activeStatus', 'statusCounts', 'collectionCount', 'myListCount')
        );

        // Persist the choice for 30 days whenever it changes or hasn't been set.
        if ($view !== $cookieView) {
            $response->cookie('media_view', $view, 60 * 24 * 30);
        }

        return $response;
    }

    public function create()
    {
        $categories  = Category::orderBy('name')->get();
        $tags        = Tag::orderBy('name')->get();
        $collections = Collection::orderBy('name')->get();
        return view('users.create', compact('categories', 'tags', 'collections'));
    }

    public function store(Request $request)
    {
        // Accept either an uploaded file OR an external URL — at least one is required.
        $source = $request->input('source') === 'link' ? 'link' : 'upload';
        $type   = $request->input('type');

        $rules = [
            'title'         => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:photo,video,ebook'],
            'status'        => ['nullable', 'in:draft,published,archived'],
            'date'          => ['nullable', 'date'],
            'location'      => ['nullable', 'string', 'max:255'],
            'collection_id' => ['nullable', 'integer', 'exists:collections,id'],
            'thumbnail'     => ['nullable', 'image', 'max:1024'], // 1 MB
            'categories'    => ['nullable', 'array'],
            'categories.*'  => ['integer', 'exists:categories,id'],
            'tags'          => ['nullable', 'array'],
            'tags.*'        => ['integer', 'exists:tags,id'],
        ];

        if ($source === 'link') {
            $rules['file_url'] = ['required', 'url', 'max:2048'];
        } else {
            $rules['file'] = $this->fileRulesForType($type, true);
        }

        $data = $request->validate($rules, $this->fileValidationMessages());

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
            'status'         => $data['status'] ?? 'draft',
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

        // Optional collection assignment.
        if (!empty($data['collection_id'] ?? null)) {
            $collection = Collection::find($data['collection_id']);
            if ($collection) {
                $media->details()->create([
                    'key'   => 'collection',
                    'value' => $collection->name,
                ]);
            }
        }

        return redirect()
            ->route('media.show', $media)
            ->with('status', 'Media saved.');
    }

    public function edit(Media $media)
    {
        $media->load(['categories', 'tags', 'details']);
        $categories  = Category::orderBy('name')->get();
        $tags        = Tag::orderBy('name')->get();
        $collections = Collection::orderBy('name')->get();
        return view('users.edit', compact('media', 'categories', 'tags', 'collections'));
    }

    public function update(Request $request, Media $media)
    {
        $source = $request->input('source', $media->file_url ? 'link' : 'upload');

        $type = $request->input('type', $media->type);

        $rules = [
            'title'         => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:photo,video,ebook'],
            'status'        => ['nullable', 'in:draft,published,archived'],
            'date'          => ['nullable', 'date'],
            'location'      => ['nullable', 'string', 'max:255'],
            'collection_id' => ['nullable', 'integer', 'exists:collections,id'],
            'thumbnail'     => ['nullable', 'image', 'max:1024'],
            'categories'    => ['nullable', 'array'],
            'categories.*'  => ['integer', 'exists:categories,id'],
            'tags'          => ['nullable', 'array'],
            'tags.*'        => ['integer', 'exists:tags,id'],
        ];

        if ($source === 'link') {
            $rules['file_url'] = ['required', 'url', 'max:2048'];
        } else {
            $rules['file'] = $this->fileRulesForType($type, false); // optional on edit
        }

        $data = $request->validate($rules, $this->fileValidationMessages());

        $payload = [
            'title'  => $data['title'],
            'type'   => $data['type'],
            'status' => $data['status'] ?? $media->status,
            'date'   => $data['date'] ?? $media->date,
        ];

        if ($source === 'link') {
            $payload['file_url']  = $data['file_url'];
            // Clean up any previously uploaded file we're switching away from.
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
                $payload['file_path'] = null;
            }
        } else {
            $payload['file_url'] = null;
            if ($request->hasFile('file')) {
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $payload['file_path'] = $request->file('file')->store('media', 'public');
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($media->thumbnail_path) {
                Storage::disk('public')->delete($media->thumbnail_path);
            }
            $payload['thumbnail_path'] = $request->file('thumbnail')->store('media/thumbnails', 'public');
        }

        $media->update($payload);
        $media->categories()->sync($data['categories'] ?? []);
        $media->tags()->sync($data['tags'] ?? []);

        // Location: book-only metadata
        $existingLoc = $media->details()->where('key', 'location')->first();
        if ($data['type'] === 'ebook' && !empty($data['location'])) {
            if ($existingLoc) {
                $existingLoc->update(['value' => $data['location']]);
            } else {
                $media->details()->create(['key' => 'location', 'value' => $data['location']]);
            }
        } elseif ($existingLoc) {
            $existingLoc->delete();
        }

        // Collection assignment: upsert when picked, clear when cleared.
        $existingCol = $media->details()->where('key', 'collection')->first();
        if (!empty($data['collection_id'])) {
            $collection = Collection::find($data['collection_id']);
            if ($collection) {
                if ($existingCol) {
                    $existingCol->update(['value' => $collection->name]);
                } else {
                    $media->details()->create(['key' => 'collection', 'value' => $collection->name]);
                }
            }
        } elseif ($existingCol) {
            $existingCol->delete();
        }

        return redirect()
            ->route('media.show', $media)
            ->with('status', 'Media updated.');
    }

    /**
     * Build the validation rules for an uploaded file, scoped to the chosen media type.
     */
    protected function fileRulesForType(?string $type, bool $required): array
    {
        $base = [$required ? 'required' : 'nullable', 'file', 'max:51200']; // 50 MB

        return match (strtolower((string) $type)) {
            'photo' => array_merge($base, ['mimes:jpg,jpeg,png,gif,webp,bmp,svg']),
            'video' => array_merge($base, ['mimes:mp4,webm,mov,avi,mkv,m4v']),
            'ebook' => array_merge($base, ['mimes:pdf,doc,docx,xls,xlsx']),
            default => $base,
        };
    }

    protected function fileValidationMessages(): array
    {
        return [
            'file.mimes' => 'The file type does not match the selected media type. Photos must be an image, videos must be a video file, books must be PDF/DOC/DOCX/XLS/XLSX.',
        ];
    }

    public function destroy(Media $media)
    {
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }
        if ($media->thumbnail_path) {
            Storage::disk('public')->delete($media->thumbnail_path);
        }
        $media->delete();

        return redirect()
            ->route('media.index')
            ->with('status', 'Media deleted.');
    }

    public function show(Media $media)
    {
        $asset = $media;
        $asset->load(['details', 'categories', 'tags']);
        $asset->incrementViews();

        $collectionDetail = $asset->details->where('key', 'collection')->first();
        $collectionName = $collectionDetail ? $collectionDetail->value : null;

        // Related items: must share EVERY category AND EVERY tag of the current item (perfect match).
        $relatedAssets    = collect();
        $sharedCategories = $asset->categories ?? collect();
        $sharedTags       = $asset->tags ?? collect();

        if ($sharedCategories->isNotEmpty() && $sharedTags->isNotEmpty()) {
            $relatedQuery = Media::where('media.id', '!=', $asset->id);

            // Each category must be present on the related item.
            foreach ($sharedCategories->pluck('id') as $catId) {
                $relatedQuery->whereHas('categories', fn($q) => $q->where('categories.id', $catId));
            }
            // Each tag must be present on the related item.
            foreach ($sharedTags->pluck('id') as $tagId) {
                $relatedQuery->whereHas('tags', fn($q) => $q->where('tags.id', $tagId));
            }

            // Non-admins only see published related items.
            if (!auth()->user()?->isAdmin()) {
                $relatedQuery->where('status', 'published');
            }

            $relatedAssets = $relatedQuery->limit(10)->get();
        }

        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('users.show', compact(
            'asset', 'relatedAssets', 'collectionName', 'sharedCategories', 'sharedTags'
        ));
    }
}
