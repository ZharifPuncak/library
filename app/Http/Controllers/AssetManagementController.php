<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetDetail;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use getID3;

class AssetManagementController extends Controller
{
    public function dashboard()
    {
        $slideshows = \App\Models\Slideshow::latest()->get();
        
        // Most Viewed
        $mostViewed = Asset::join('asset_details', 'assets.id', '=', 'asset_details.asset_id')
            ->where('asset_details.key', 'views')
            ->where('asset_details.value', '>', 5) // Minimum 5 views to be "Most Viewed"
            ->select('assets.*', 'asset_details.value as views_count')
            ->orderByRaw('CAST(asset_details.value AS UNSIGNED) DESC')
            ->take(5)
            ->with(['categories', 'tags'])
            ->get();

        // Recently Viewed
        $recentlyViewedIds = session()->get('recently_viewed', []);
        $recentlyViewed = Asset::whereIn('id', $recentlyViewedIds)
            ->with(['categories', 'tags'])
            ->get()
            ->sortBy(function($asset) use ($recentlyViewedIds) {
                return array_search($asset->id, $recentlyViewedIds);
            });

        $categories = \App\Models\Category::all();

        return view('admin.dashboard', compact('slideshows', 'mostViewed', 'categories', 'recentlyViewed'));
    }

    /**
     * List all assets (Admin only) - Used for the main index page.
     */
    public function index()
    {
        $assets = Asset::with(['categories', 'tags', 'details'])
            ->latest()
            ->paginate(9);

        return view('admin.assets.index', compact('assets'));
    }

    /**
     * List all assets (explicit route, usually /admin/assets/all).
     * @return \Illuminate\View\View
     */
    public function allAssets(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $assets = Asset::with(['categories', 'tags', 'details']);

        // Search filter
        if ($search = $request->input('search')) {
            $assets->where(function ($q) use ($search) {
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

        // Category filter
        if ($request->categories) {
            $assets->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', $request->categories);
            });
        }

        // Year filter
        if ($year = $request->input('year')) {
            $assets->whereYear('created_at', $year);
        }

        $assets = $assets->latest()->paginate(9)->withQueryString();

        return view('admin.assets.all', compact('assets', 'categories', 'tags'));
    }


    /**
     * Store new asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|string|max:50',
            'file'           => 'required|file',
            'category_id'    => 'nullable|integer',
            'categories'     => 'array',
            'categories.*'   => 'integer',
            'tags'           => 'array',
            'tags.*'         => 'integer',
            'metadata'       => 'array',
            'thumbnail'      => 'nullable|image|max:2048',
            'isbn'           => 'nullable|string|max:50',
        ]);

        // Store file
        $path = $request->file('file')->store('uploads/assets', 'public');

        // Store thumbnail if present
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $asset = Asset::create([
            'title'          => $validated['title'],
            'type'           => strtolower($validated['type']),
            'file_path'      => $path,
            'thumbnail_path' => $thumbnailPath,
        ]);

        // Save ISBN as metadata if present
        if (!empty($validated['isbn'])) {
            AssetDetail::create([
                'asset_id' => $asset->id,
                'key'      => 'isbn',
                'value'    => $validated['isbn'],
            ]);
        }

        // Save categories - handle both single category_id and multiple categories
        $categoriesToSync = [];
        if ($request->filled('category_id')) {
            $categoriesToSync[] = $request->category_id;
        }
        if ($request->filled('categories')) {
            $categoriesToSync = array_merge($categoriesToSync, $validated['categories']);
        }
        if (!empty($categoriesToSync)) {
            $asset->categories()->sync($categoriesToSync);
        }

        // Save tags - directly sync tag IDs
        if ($request->filled('tags')) {
            $asset->tags()->sync($validated['tags']);
        }

        // Save metadata
        if ($request->filled('metadata')) {
            foreach ($validated['metadata'] as $key => $value) {
                AssetDetail::create([
                    'asset_id' => $asset->id,
                    'key'      => $key,
                    'value'    => $value,
                ]);
            }
        }

        // Auto-detect duration for video
        if ($asset->type === 'video') {
            try {
                $getID3 = new \getID3;
                $file = $getID3->analyze(storage_path('app/public/' . $path));
                if (isset($file['playtime_seconds'])) {
                    AssetDetail::updateOrCreate(
                        ['asset_id' => $asset->id, 'key' => 'duration'],
                        ['value' => round($file['playtime_seconds'])]
                    );
                }
            } catch (\Exception $e) {
                // Ignore duration extraction failure
            }
        }

        $typeDisplay = ucfirst($validated['type'] === 'ebook' ? 'e-book' : $validated['type']);
    return redirect()->back()->with('success', "{$typeDisplay} successfully uploaded!");
    }



    /**
     * Edit asset
     */
    public function edit($id)
    {
        // Fetch the main asset, eagerly loading its relationships
        $asset = Asset::with(['categories', 'tags', 'details'])->findOrFail($id);

        // Fetch all tags and categories, using the variable names expected by the view
        $tags = Tag::all();
        $categories = Category::all();

        // Pass the variables to the view using the expected names
        return view('admin.assets.edit', compact('asset', 'tags', 'categories'));
    }



    /**
     * Update asset
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'date'           => 'nullable|date',
            'category_id'    => 'nullable|integer',
            'categories'     => 'array',
            'categories.*'   => 'integer',
            'tags'           => 'array',
            'tags.*'         => 'integer',
            'metadata'       => 'array',
            'collection'     => 'nullable|string|max:255',
            'file'           => 'nullable|file',
            'thumbnail'      => 'nullable|image|max:2048',
        ]);

        // Update basic info
        $updateData = ['title' => $validated['title']];
        if ($request->filled('date')) {
            $updateData['date'] = $validated['date'];
        }
        $asset->update($updateData);

        // Replace file if uploaded
        if ($request->hasFile('file')) {
            if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
                Storage::disk('public')->delete($asset->file_path);
            }

            $path = $request->file('file')->store('uploads/assets', 'public');
            $asset->update(['file_path' => $path]);

            // Auto-detect duration for new video file
            if ($asset->type === 'video') {
                try {
                    $getID3 = new \getID3;
                    $file = $getID3->analyze(storage_path('app/public/' . $path));
                    if (isset($file['playtime_seconds'])) {
                        AssetDetail::updateOrCreate(
                            ['asset_id' => $asset->id, 'key' => 'duration'],
                            ['value' => round($file['playtime_seconds'])]
                        );
                    }
                } catch (\Exception $e) {
                    // Ignore duration extraction failure
                }
            }
        }

        // Replace thumbnail if uploaded
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($asset->thumbnail_path && Storage::disk('public')->exists($asset->thumbnail_path)) {
                Storage::disk('public')->delete($asset->thumbnail_path);
            }

            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $asset->update(['thumbnail_path' => $thumbnailPath]);
        }

        // Update categories - handle both single category_id and multiple categories
        $categoriesToSync = [];
        if ($request->filled('category_id')) {
            $categoriesToSync[] = $request->category_id;
        }
        if ($request->filled('categories')) {
            $categoriesToSync = array_merge($categoriesToSync, $validated['categories']);
        }
        $asset->categories()->sync($categoriesToSync);

        // Update tags - directly sync tag IDs
        $asset->tags()->sync($validated['tags'] ?? []);

        // Reset & re-save metadata
        $asset->details()->delete();
        
        // Save collection if provided
        if ($request->filled('collection')) {
            AssetDetail::create([
                'asset_id' => $asset->id,
                'key'      => 'collection',
                'value'    => $request->collection,
            ]);
        }

        if ($request->filled('metadata')) {
            foreach ($validated['metadata'] as $key => $value) {
                AssetDetail::create([
                    'asset_id' => $asset->id,
                    'key'      => $key,
                    'value'    => $value,
                ]);
            }
        }

        return redirect()->route('admin.assets.show', $asset->id)->with('success', 'Asset successfully updated!');
    }



    /**
     * Delete asset
     */
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->details()->delete();
        $asset->categories()->detach();
        $asset->tags()->detach();
        $asset->delete();

        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }





    // ==================== ADMIN LISTING METHODS ====================

    public function photos(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $assets = Asset::with(['categories', 'tags']) 
            ->where('type', 'photo')
            // Filter by categories
            ->when($request->categories, function($query) use ($request) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->whereIn('categories.id', $request->categories);
                });
            })
            // Filter by year
            ->when($request->year, function($query) use ($request) {
                $query->whereYear('date', $request->year);
            })
            // Filter by search term
            ->when($request->search, function($query) use ($request) {
                $query->where('title', 'like', "%".$request->search."%")
                      ->orWhereHas('tags', fn($t) =>
                          $t->where('name', 'like', "%".$request->search."%")
                      );
            })
            ->latest()
            ->paginate(9);

        return view('admin.assets.photo', compact('assets', 'categories', 'tags'));
    }

    public function videos(Request $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $assets = Asset::with(['categories', 'tags'])
            ->where('type', 'video')
            // Filter by categories
            ->when($request->categories, function($query) use ($request) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->whereIn('categories.id', $request->categories);
                });
            })
            // Filter by year
            ->when($request->year, function($query) use ($request) {
                $query->whereYear('date', $request->year);
            })
            // Filter by search term
            ->when($request->search, function($query) use ($request) {
                $query->where('title', 'like', "%".$request->search."%")
                      ->orWhereHas('tags', fn($t) =>
                          $t->where('name', 'like', "%".$request->search."%")
                      );
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('admin.assets.video', compact('assets', 'categories', 'tags'));
    }

    public function ebooks()
    {
        $categories = Category::all();
        $tags = Tag::all();
        $assets = Asset::with(['categories', 'tags'])->where('type', 'ebook')->latest()->paginate(9);
        return view('admin.assets.ebook', compact('assets', 'categories', 'tags'));
    }

    public function slideshows()
    {
        $assets = Asset::with(['categories', 'tags'])->where('type', 'slideshow')->latest()->paginate(9);
        return view('admin.assets.slideshow', compact('assets'));
    }


    public function show($id)
    {
        $asset = Asset::with(['categories', 'tags'])->findOrFail($id);
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

        return view('admin.assets.show', compact('asset', 'relatedAssets', 'collectionName'));
    }

    // ==================== COLLECTIONS (BULK UPLOAD) ====================

    /**
     * List all distinct collection names.
     */
    public function collections()
    {
        $collectionNames = \App\Models\AssetDetail::where('key', 'collection')
            ->distinct()
            ->pluck('value');

        $collections = $collectionNames->map(function ($name) {
            $representative = \App\Models\Asset::whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })->first();

            $count = \App\Models\Asset::whereHas('details', function ($q) use ($name) {
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

        return view('admin.collections.index', compact('collections'));
    }

    /**
     * Show the bulk upload form.
     */
    public function bulkCreate()
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('admin.assets.bulk-create', compact('categories', 'tags'));
    }

    /**
     * Store multiple assets belonging to a collection.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'collection_name' => 'required|string|max:255',
            'type'            => 'required|string|max:50',
            'files'           => 'required|array',
            'files.*'         => 'file',
            'category_id'     => 'nullable|integer',
            'tags'            => 'array',
            'tags.*'          => 'integer',
        ]);

        $collectionName = $validated['collection_name'];
        $type = strtolower($validated['type']);
        $categoriesToSync = $request->filled('category_id') ? [$request->category_id] : [];
        $tagsToSync = $validated['tags'] ?? [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads/assets', 'public');
            
            // Auto-generate title from filename
            $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $title = str_replace(['-', '_'], ' ', $title);
            $title = ucwords($title);

            // Type detection logic
            $detectedType = $type;
            if ($type === 'mixed') {
                $extension = strtolower($file->getClientOriginalExtension());
                $photoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'mkv'];
                $ebookExtensions = ['pdf', 'epub'];

                if (in_array($extension, $photoExtensions)) {
                    $detectedType = 'photo';
                } elseif (in_array($extension, $videoExtensions)) {
                    $detectedType = 'video';
                } elseif (in_array($extension, $ebookExtensions)) {
                    $detectedType = 'ebook';
                } else {
                    $detectedType = 'photo'; // Default
                }
            }

            $asset = \App\Models\Asset::create([
                'title'     => $title,
                'type'      => $detectedType,
                'file_path' => $path,
            ]);

            // Auto-detect duration for video
            if ($detectedType === 'video') {
                try {
                    $getID3 = new \getID3;
                    $fileAnalysis = $getID3->analyze(storage_path('app/public/' . $path));
                    if (isset($fileAnalysis['playtime_seconds'])) {
                        \App\Models\AssetDetail::create([
                            'asset_id' => $asset->id,
                            'key'      => 'duration',
                            'value'    => round($fileAnalysis['playtime_seconds']),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Ignore duration extraction failure
                }
            }

            // Assign collection metadata
            \App\Models\AssetDetail::create([
                'asset_id' => $asset->id,
                'key'      => 'collection',
                'value'    => $collectionName,
            ]);

            // Sync categories/tags
            if (!empty($categoriesToSync)) {
                $asset->categories()->sync($categoriesToSync);
            }
            if (!empty($tagsToSync)) {
                $asset->tags()->sync($tagsToSync);
            }
        }

        return redirect()->route('admin.collections.index')->with('success', "Collection successfully added");
    }

    /**
     * Show all assets in a collection.
     */
    public function showCollection($name)
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

        return view('admin.collections.show', compact('assets', 'collectionName', 'counts'));
    }

    /**
     * Show a specific asset within an admin collection context.
     */
    public function showCollectionAsset($name, $id)
    {
        $asset = Asset::with(['categories', 'tags', 'details'])->findOrFail($id);
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

        // Track recently viewed
        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('admin.assets.show', compact('asset', 'relatedAssets', 'collectionName', 'isCollectionContext'));
    }
}
