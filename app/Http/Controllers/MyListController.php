<?php

namespace App\Http\Controllers;

use App\Models\Collection;
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

        $collections = $user
            ->myListCollections()
            ->orderByPivot('created_at', 'desc')
            ->paginate(12, ['*'], 'collectionsPage');

        return view('users.mylist.index', compact('collections'));
    }

    public function storeCollection(Collection $collection)
    {
        auth()->user()->myListCollections()->syncWithoutDetaching([$collection->id]);

        return back()->with('status', 'Collection added to My List.');
    }

    public function destroyCollection(Collection $collection)
    {
        auth()->user()->myListCollections()->detach($collection->id);

        return back()->with('status', 'Collection removed from My List.');
    }
}
