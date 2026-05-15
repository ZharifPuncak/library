<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Media;
use App\Models\MediaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class UserCollectionController extends Controller
{
    /**
     * Show the create-collection form (admin only).
     */
    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $media = Media::orderBy('title')->get(['id', 'uuid', 'title', 'type', 'thumbnail_path', 'file_path']);
        return view('users.collections.create', compact('media'));
    }

    /**
     * Persist a new collection: create the Collection row, then assign N media items.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:collections,name'],
            'media'   => ['required', 'array', 'min:1'],
            'media.*' => ['integer', 'exists:media,id'],
        ]);

        $collection = Collection::create(['name' => $data['name']]);

        // For each picked media row, upsert a media_details row tagging it with this collection name.
        foreach ($data['media'] as $mediaId) {
            $media = Media::find($mediaId);
            if (!$media) continue;

            $detail = $media->details()->where('key', 'collection')->first();
            if ($detail) {
                $detail->update(['value' => $collection->name]);
            } else {
                $media->details()->create([
                    'key'   => 'collection',
                    'value' => $collection->name,
                ]);
            }
        }

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'Collection created.');
    }

    /**
     * Display a listing of unique collections (Albums).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $collections = Collection::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get()
            ->map(function ($collection) {
            $representative = Media::whereHas('details', function ($q) use ($collection) {
                $q->where('key', 'collection')->where('value', $collection->name);
            })->first();

            $count = Media::whereHas('details', function ($q) use ($collection) {
                $q->where('key', 'collection')->where('value', $collection->name);
            })->count();

            return (object) [
                'model'          => $collection,
                'uuid'           => $collection->uuid,
                'name'           => $collection->name,
                'thumbnail_path' => $representative?->thumbnail_path ?? $representative?->file_path,
                'type'           => $representative?->type,
                'count'          => $count,
                'date'           => $representative?->created_at ?? $collection->created_at,
            ];
        });

        return view('users.collections.index', compact('collections', 'search'));
    }

    /**
     * Show the edit-collection form (admin only).
     */
    public function edit(Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $media = Media::orderBy('title')->get(['id', 'uuid', 'title', 'type', 'thumbnail_path', 'file_path']);
        // Pre-select media currently tagged with this collection.
        $assigned = MediaDetail::where('key', 'collection')
            ->where('value', $collection->name)
            ->pluck('media_id')
            ->all();

        return view('users.collections.edit', compact('collection', 'media', 'assigned'));
    }

    /**
     * Update collection name + reassign media set.
     */
    public function update(Request $request, Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:collections,name,' . $collection->id],
            'media'   => ['nullable', 'array'],
            'media.*' => ['integer', 'exists:media,id'],
        ]);

        $oldName = $collection->name;
        $newName = $data['name'];

        // If the name changed, propagate to existing media_details rows.
        if ($oldName !== $newName) {
            MediaDetail::where('key', 'collection')
                ->where('value', $oldName)
                ->update(['value' => $newName]);
        }

        $collection->update(['name' => $newName]);

        // Reassign media set: detach removed items, attach new ones.
        $currentMediaIds = MediaDetail::where('key', 'collection')
            ->where('value', $newName)
            ->pluck('media_id')
            ->all();
        $picked = $data['media'] ?? [];

        // Detach
        $toRemove = array_diff($currentMediaIds, $picked);
        if (!empty($toRemove)) {
            MediaDetail::whereIn('media_id', $toRemove)
                ->where('key', 'collection')
                ->where('value', $newName)
                ->delete();
        }

        // Attach
        $toAdd = array_diff($picked, $currentMediaIds);
        foreach ($toAdd as $mediaId) {
            $media = Media::find($mediaId);
            if (!$media) continue;

            $detail = $media->details()->where('key', 'collection')->first();
            if ($detail) {
                $detail->update(['value' => $newName]);
            } else {
                $media->details()->create(['key' => 'collection', 'value' => $newName]);
            }
        }

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'Collection updated.');
    }

    /**
     * Build and stream a ZIP archive of every uploaded file in this collection.
     */
    public function download(Collection $collection)
    {
        $name  = $collection->name;
        $items = Media::whereHas('details', fn($q) =>
                $q->where('key', 'collection')->where('value', $name))
            ->whereNotNull('file_path')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No downloadable files in this collection.');
        }

        $zipName = Str::slug($name) . '-' . now()->format('Ymd-His') . '.zip';
        $zipPath = storage_path('app/' . Str::uuid() . '.zip');

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create archive.');
        }

        $seen = [];
        foreach ($items as $media) {
            $diskPath = Storage::disk('public')->path($media->file_path);
            if (!is_file($diskPath)) continue;

            // Use a friendly file name based on title + original extension; de-dupe collisions.
            $ext   = pathinfo($media->file_path, PATHINFO_EXTENSION);
            $base  = Str::slug($media->title) ?: 'item-' . $media->id;
            $entry = $base . ($ext ? '.' . $ext : '');
            $i     = 1;
            while (isset($seen[$entry])) {
                $entry = $base . '-' . (++$i) . ($ext ? '.' . $ext : '');
            }
            $seen[$entry] = true;

            $zip->addFile($diskPath, $entry);
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Delete a collection: removes the Collection row + clears the collection tag from all media.
     */
    public function destroy(Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        MediaDetail::where('key', 'collection')
            ->where('value', $collection->name)
            ->delete();

        $collection->delete();

        return redirect()
            ->route('collections.index')
            ->with('status', 'Collection deleted.');
    }

    /**
     * Display all media within a specific collection.
     */
    public function show(Collection $collection)
    {
        $name   = $collection->name;
        $assets = Media::whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })
            ->with(['categories', 'tags'])
            ->latest()
            ->paginate(12);

        $counts = [
            'total' => Media::whereHas('details', fn($q) => $q->where('key', 'collection')->where('value', $name))->count(),
            'photo' => Media::whereHas('details', fn($q) => $q->where('key', 'collection')->where('value', $name))->where('type', 'photo')->count(),
            'video' => Media::whereHas('details', fn($q) => $q->where('key', 'collection')->where('value', $name))->where('type', 'video')->count(),
            'ebook' => Media::whereHas('details', fn($q) => $q->where('key', 'collection')->where('value', $name))->where('type', 'ebook')->count(),
        ];

        $collectionName = $name;

        return view('users.collections.show', compact('assets', 'collection', 'collectionName', 'counts'));
    }

    /**
     * Display a specific media within a collection context.
     */
    public function showAsset(Collection $collection, Media $media)
    {
        $asset = $media;
        $asset->load(['details', 'categories', 'tags']);
        $asset->incrementViews();

        $name = $collection->name;

        $relatedAssets = Media::whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            })
            ->where('id', '!=', $asset->id)
            ->latest()
            ->get();

        $collectionName = $name;
        $isCollectionContext = true;

        $recent = session()->get('recently_viewed', []);
        if (($key = array_search($asset->id, $recent)) !== false) {
            unset($recent[$key]);
        }
        array_unshift($recent, $asset->id);
        session()->put('recently_viewed', array_slice($recent, 0, 5));

        return view('users.show', compact('asset', 'relatedAssets', 'collection', 'collectionName', 'isCollectionContext'));
    }
}
