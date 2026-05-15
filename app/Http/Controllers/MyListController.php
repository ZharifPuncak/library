<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Media;
use Illuminate\Http\Request;

class MyListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $items = $user
            ->myList()
            ->with(['categories', 'tags'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(12, ['*'], 'mediaPage');

        $collections = $user
            ->myListCollections()
            ->orderByPivot('created_at', 'desc')
            ->paginate(8, ['*'], 'collectionsPage');

        return view('users.mylist.index', compact('items', 'collections'));
    }

    /**
     * Show the bulk picker — pick multiple media AND collections at once.
     */
    public function add()
    {
        $user = auth()->user();

        $media       = Media::orderBy('title')->get(['id', 'uuid', 'title', 'type', 'thumbnail_path', 'file_path']);
        $collections = Collection::orderBy('name')->get(['id', 'uuid', 'name']);

        $assignedMedia       = $user->myList()->pluck('media.id')->all();
        $assignedCollections = $user->myListCollections()->pluck('collections.id')->all();

        return view('users.mylist.add', compact('media', 'collections', 'assignedMedia', 'assignedCollections'));
    }

    /**
     * Sync the picker submission with the user's lists.
     */
    public function sync(Request $request)
    {
        $data = $request->validate([
            'media'         => ['nullable', 'array'],
            'media.*'       => ['integer', 'exists:media,id'],
            'collections'   => ['nullable', 'array'],
            'collections.*' => ['integer', 'exists:collections,id'],
        ]);

        auth()->user()->myList()->sync($data['media'] ?? []);
        auth()->user()->myListCollections()->sync($data['collections'] ?? []);

        return redirect()
            ->route('mylist.index')
            ->with('status', 'My List updated.');
    }

    /**
     * Add a single media item to the current user's list.
     */
    public function store(Media $media)
    {
        auth()->user()->myList()->syncWithoutDetaching([$media->id]);

        return back()->with('status', 'Added to My List.');
    }

    /**
     * Remove a single media item from the current user's list.
     */
    public function destroy(Media $media)
    {
        auth()->user()->myList()->detach($media->id);

        return back()->with('status', 'Removed from My List.');
    }

    /**
     * Add a single collection to the current user's list.
     */
    public function storeCollection(Collection $collection)
    {
        auth()->user()->myListCollections()->syncWithoutDetaching([$collection->id]);

        return back()->with('status', 'Collection added to My List.');
    }

    /**
     * Remove a single collection from the current user's list.
     */
    public function destroyCollection(Collection $collection)
    {
        auth()->user()->myListCollections()->detach($collection->id);

        return back()->with('status', 'Collection removed from My List.');
    }
}
