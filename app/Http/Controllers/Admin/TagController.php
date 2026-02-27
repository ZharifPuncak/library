<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index()
    {
        $tags = Tag::withCount('assets')->orderBy('name')->get();
        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ]);

        Tag::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Tag added successfully.');
    }

    /**
     * Show the form for editing the specified tag
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        
        // Detach from all assets before deleting
        $tag->assets()->detach();
        $tag->delete();

        return redirect()->back()->with('success', 'Tag deleted successfully.');
    }

    /**
     * Show manage tags page
     */
    public function manage()
    {
        $tags = Tag::withCount('assets')->orderBy('name')->get();
        return view('admin.tags.tagManagement', compact('tags'));
    }
}