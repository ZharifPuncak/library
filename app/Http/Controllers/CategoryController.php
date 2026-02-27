<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            Category::create([
                'name' => $request->name,   // BETULKAN
                'description' => $request->description
            ]);

            return back()->with('success', 'Category created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $category->update([
                'name' => $request->name,  // BETULKAN
                'description' => $request->description
            ]);

            return back()->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return back()->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}
