<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Media;
use App\Models\MediaDetail;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class UserCollectionController extends Controller
{
    private const PHOTO_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    private const VIDEO_EXT = ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v'];
    private const EBOOK_EXT = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();
        return view('users.collections.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:collections,name'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'status'       => ['nullable', 'in:draft,published,archived'],
            'date'         => ['nullable', 'date'],
            'thumbnail'    => ['nullable', 'image', 'max:1024'],
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['integer', 'exists:tags,id'],
        ]);

        $thumb = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('media/thumbnails', 'public')
            : null;

        $collection = Collection::create([
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'status'         => $data['status'] ?? 'draft',
            'date'           => $data['date']   ?? now(),
            'thumbnail_path' => $thumb,
        ]);

        $collection->categories()->sync($data['categories'] ?? []);
        $collection->tags()->sync($data['tags'] ?? []);

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'Collection created. Add files to it below.');
    }

    /**
     * Add one or more media files into an existing collection.
     * New media inherit the collection's status/date/thumbnail/categories/tags.
     */
    public function addMedia(Request $request, Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $mimesByType = [
            'photo' => implode(',', self::PHOTO_EXT),
            'video' => implode(',', self::VIDEO_EXT),
            'ebook' => implode(',', self::EBOOK_EXT),
        ];
        $selectedType = (string) $request->input('upload_type', '');
        $selectedMimes = $mimesByType[$selectedType] ?? implode(',', array_merge(self::PHOTO_EXT, self::VIDEO_EXT, self::EBOOK_EXT));

        $data = $request->validate([
            'source_mode' => ['required', 'in:file,link'],
            'upload_type' => ['required', 'in:photo,video,ebook'],
            'files'       => ['required_if:source_mode,file', 'array', 'min:1'],
            'files.*'     => ['required_if:source_mode,file', 'file', 'max:25600', 'mimes:' . $selectedMimes],
            'links'       => ['required_if:source_mode,link', 'nullable', 'string'],
            'location'    => ['nullable', 'string', 'max:255'],
        ], [
            'source_mode.in' => 'Please choose whether to upload files or add links.',
            'upload_type.in' => 'Please choose a valid upload type.',
            'files.*.mimes'  => 'Each file must match the selected upload type.',
        ]);

        $location = $data['location'] ?? null;
        $catIds   = $collection->categories()->pluck('categories.id')->all();
        $tagIds   = $collection->tags()->pluck('tags.id')->all();

        if ($data['source_mode'] === 'file') {
            foreach ($request->file('files', []) as $file) {
                $this->createMediaFromUpload(
                    $file,
                    $collection,
                    $collection->status ?? 'draft',
                    $collection->date ?? now(),
                    $location,
                    $collection->thumbnail_path,
                    $catIds,
                    $tagIds,
                );
            }
        } else {
            $links = collect(preg_split('/\R+/', (string) ($data['links'] ?? '')))
                ->map(fn($link) => trim($link))
                ->filter()
                ->values();

            if ($links->isEmpty()) {
                throw ValidationException::withMessages([
                    'links' => 'Add at least one link.',
                ]);
            }

            foreach ($links as $index => $link) {
                $this->createMediaFromLink(
                    $link,
                    $collection,
                    $data['upload_type'],
                    $collection->status ?? 'draft',
                    $collection->date ?? now(),
                    $location,
                    $collection->thumbnail_path,
                    $catIds,
                    $tagIds,
                    $index + 1,
                );
            }
        }

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', $data['source_mode'] === 'file' ? 'Files added to collection.' : 'Links added to collection.');
    }

    public function edit(Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        $sharedStatus     = $collection->status ?? 'draft';
        $sharedDate       = $collection->date?->format('Y-m-d');
        $sharedCatIds     = $collection->categories()->pluck('categories.id')->all();
        $sharedTagIds     = $collection->tags()->pluck('tags.id')->all();
        $mediaCount       = Media::inCollection($collection->name)->count();
        $unpublishedCount = Media::inCollection($collection->name)->where('status', '!=', 'published')->count();

        return view('users.collections.edit', compact(
            'collection', 'categories', 'tags',
            'sharedStatus', 'sharedDate', 'sharedCatIds', 'sharedTagIds',
            'mediaCount', 'unpublishedCount'
        ));
    }

    public function update(Request $request, Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:collections,name,' . $collection->id],
            'description'  => ['nullable', 'string', 'max:5000'],
            'status'       => ['nullable', 'in:draft,published,archived'],
            'date'         => ['nullable', 'date'],
            'thumbnail'    => ['nullable', 'image', 'max:1024'],
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['integer', 'exists:tags,id'],
        ]);

        $oldName = $collection->name;
        $newName = $data['name'];

        if ($oldName !== $newName) {
            MediaDetail::where('key', 'collection')
                ->where('value', $oldName)
                ->update(['value' => $newName]);
        }

        $thumb = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('media/thumbnails', 'public')
            : null;
        if ($thumb && $collection->thumbnail_path && $collection->thumbnail_path !== $thumb) {
            Storage::disk('public')->delete($collection->thumbnail_path);
        }

        $status = $data['status'] ?? 'draft';
        $date   = $data['date']   ?? $collection->date ?? now();
        $catIds = $data['categories'] ?? [];
        $tagIds = $data['tags']       ?? [];

        $collection->update([
            'name'           => $newName,
            'description'    => $data['description'] ?? null,
            'status'         => $status,
            'date'           => $date,
            'thumbnail_path' => $thumb ?? $collection->thumbnail_path,
        ]);
        $collection->categories()->sync($catIds);
        $collection->tags()->sync($tagIds);

        // Re-sync metadata on every remaining media item so collection-level
        // edits propagate to existing files.
        $finalThumb = $collection->fresh()->thumbnail_path;
        $existing = Media::inCollection($newName)->get();
        foreach ($existing as $m) {
            $m->update([
                'status'         => $status,
                'date'           => $date,
                'thumbnail_path' => $finalThumb,
            ]);
            $m->categories()->sync($catIds);
            $m->tags()->sync($tagIds);
        }

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'Collection updated.');
    }

    public function destroyMedia(Collection $collection, Media $media)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->abortUnlessMediaBelongsToCollection($collection, $media);

        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }

        if ($media->thumbnail_path && $media->thumbnail_path !== $collection->thumbnail_path) {
            Storage::disk('public')->delete($media->thumbnail_path);
        }

        $media->delete();

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'File removed from collection.');
    }

    public function updateMedia(Request $request, Collection $collection, Media $media)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->abortUnlessMediaBelongsToCollection($collection, $media);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', 'in:draft,published,archived'],
            'date'        => ['nullable', 'date'],
            'location'    => ['nullable', 'string', 'max:255'],
            'file_url'    => ['nullable', 'url', 'max:2048'],
        ]);

        $fileUrl = $media->file_url
            ? (!empty($data['file_url']) ? $data['file_url'] : $media->file_url)
            : $media->file_url;

        $media->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'date'        => $data['date'] ?? null,
            'file_url'    => $fileUrl,
        ]);

        if (!empty($data['location'])) {
            $media->details()->updateOrCreate(
                ['key' => 'location'],
                ['value' => $data['location']]
            );
        } else {
            $media->details()->where('key', 'location')->delete();
        }

        return redirect()
            ->route('collections.show', $collection)
            ->with('status', 'Media item updated.');
    }

    protected function abortUnlessMediaBelongsToCollection(Collection $collection, Media $media): void
    {
        abort_unless(
            $media->details()
                ->where('key', 'collection')
                ->where('value', $collection->name)
                ->exists(),
            404
        );
    }

    /**
     * Persist a single upload as a new Media row tagged to the given collection.
     */
    protected function createMediaFromUpload(
        UploadedFile $file,
        Collection $collection,
        string $status,
        $date,
        ?string $location,
        ?string $sharedThumb,
        array $categoryIds,
        array $tagIds,
    ): void {
        $ext  = strtolower($file->getClientOriginalExtension());
        $type = $this->detectType($ext);
        if (!$type) {
            return;
        }

        $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $title = trim($title) !== '' ? $title : 'Untitled';

        $filePath = $file->store('media', 'public');

        $media = Media::create([
            'title'          => $title,
            'type'           => $type,
            'status'         => $status,
            'file_path'      => $filePath,
            'thumbnail_path' => $sharedThumb,
            'date'           => $date,
        ]);

        $media->details()->create([
            'key'   => 'collection',
            'value' => $collection->name,
        ]);

        if (!empty($location)) {
            $media->details()->create([
                'key'   => 'location',
                'value' => $location,
            ]);
        }

        if (!empty($categoryIds)) $media->categories()->sync($categoryIds);
        if (!empty($tagIds))      $media->tags()->sync($tagIds);
    }

    /**
     * Persist a direct URL as a new Media row tagged to the given collection.
     */
    protected function createMediaFromLink(
        string $url,
        Collection $collection,
        string $selectedType,
        string $status,
        $date,
        ?string $location,
        ?string $sharedThumb,
        array $categoryIds,
        array $tagIds,
        int $lineNumber,
    ): void {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages([
                'links' => "Line {$lineNumber} must be a valid URL.",
            ]);
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $type = $selectedType;

        $title = pathinfo($path, PATHINFO_FILENAME);
        $title = trim(urldecode($title ?: ''));
        if ($title === '') {
            $title = $host !== '' ? $host : 'Linked media';
        }

        $media = Media::create([
            'title'          => $title,
            'type'           => $type,
            'status'         => $status,
            'file_url'       => $url,
            'thumbnail_path' => $sharedThumb,
            'date'           => $date,
        ]);

        $media->details()->create([
            'key'   => 'collection',
            'value' => $collection->name,
        ]);

        if (!empty($location)) {
            $media->details()->create([
                'key'   => 'location',
                'value' => $location,
            ]);
        }

        if (!empty($categoryIds)) $media->categories()->sync($categoryIds);
        if (!empty($tagIds))      $media->tags()->sync($tagIds);
    }

    protected function detectType(string $ext): ?string
    {
        if (in_array($ext, self::PHOTO_EXT, true)) return 'photo';
        if (in_array($ext, self::VIDEO_EXT, true)) return 'video';
        if (in_array($ext, self::EBOOK_EXT, true)) return 'ebook';
        return null;
    }

    public function download(Collection $collection)
    {
        $name  = $collection->name;
        $items = Media::visibleTo(auth()->user())
            ->whereHas('details', fn($q) =>
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

    public function destroy(Collection $collection)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $items = Media::inCollection($collection->name)->get();
        foreach ($items as $m) {
            if ($m->file_path)      Storage::disk('public')->delete($m->file_path);
            if ($m->thumbnail_path) Storage::disk('public')->delete($m->thumbnail_path);
            $m->delete();
        }

        $collection->delete();

        return redirect()
            ->route('media.index')
            ->with('status', 'Collection deleted.');
    }

    public function show(Request $request, Collection $collection)
    {
        $name   = $collection->name;
        $user   = auth()->user();
        $search = trim((string) $request->input('q'));
        $type   = strtolower((string) $request->input('type'));
        if (!in_array($type, ['photo', 'video', 'ebook'], true)) {
            $type = '';
        }

        $collectionMedia = fn() => Media::visibleTo($user)
            ->whereHas('details', function ($q) use ($name) {
                $q->where('key', 'collection')->where('value', $name);
            });

        $listQuery = $collectionMedia()->with(['categories', 'tags', 'details']);
        if ($type !== '') {
            $listQuery->where('type', $type);
        }
        if ($search !== '') {
            $listQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $assets = $listQuery->latest()->paginate(12)->withQueryString();

        $counts = [
            'total' => $collectionMedia()->count(),
            'photo' => $collectionMedia()->where('type', 'photo')->count(),
            'video' => $collectionMedia()->where('type', 'video')->count(),
            'ebook' => $collectionMedia()->where('type', 'ebook')->count(),
        ];

        $collectionName  = $name;
        $hasDownloadable = $collectionMedia()->whereNotNull('file_path')->exists();
        $activeType      = $type;
        $activeSearch    = $search;

        return view('users.collections.show', compact(
            'assets', 'collection', 'collectionName', 'counts', 'hasDownloadable', 'activeType', 'activeSearch'
        ));
    }

}
