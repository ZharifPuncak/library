<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function categoryManagement()
    {
        // Logika untuk mengambil data kategori (jika diperlukan)
        $categories = Category::all();

        // Mengembalikan view yang akan menampilkan antarmuka manajemen kategori
        // Ganti 'admin.categories.management' jika nama file view Anda berbeda
        return view('admin.categories.categoryManagement', compact('categories'));
    }
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('assets')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
   public function store(Request $request)
  {
    $request->validate([
        'name' => 'required|string|max:255|unique:categories,name',
    ]);

    Category::create([
        'name' => $request->name,
    ]);

    return redirect()->back()->with('success', 'Category added successfully.');
  }


    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has assets
        if ($category->assets()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with existing assets.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Show manage categories page
     */
    public function manage()
    {
        $categories = Category::withCount('assets')->orderBy('name')->get();
        return view('admin.categories.manage', compact('categories'));
    }
}