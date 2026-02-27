<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
        ]);

        try {
            Tag::create([
                'name' => $request->name,
            ]);

            return redirect()->route('admin.assets.index')
                ->with('success', 'Tag created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create tag: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name,' . $id,
        ]);

        try {
            $tag->update([
                'name' => $request->name,
            ]);

            return redirect()->route('admin.assets.index')
                ->with('success', 'Tag updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update tag: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tag
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();

            return redirect()->route('admin.assets.index')
                ->with('success', 'Tag deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tag: ' . $e->getMessage());
        }
    }
}
