<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
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

        $tags = Tag::query()
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount(['media' => fn($q) => $q])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('tags.index', compact('tags', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
        ]);

        Tag::create(['name' => $data['name']]);

        return redirect()
            ->route('tags.index')
            ->with('status', 'Tag added.');
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:tags,name,' . $tag->id],
        ]);

        $tag->update($data);

        return redirect()
            ->route('tags.index')
            ->with('status', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('status', 'Tag deleted.');
    }
}
