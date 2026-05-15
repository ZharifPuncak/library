<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->isAdmin(), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $categories = Category::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount(['media' => fn($q) => $q])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('categories.index', compact('categories', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        Category::create(['name' => $data['name']]);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category added.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
        ]);

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'Category deleted.');
    }
}
