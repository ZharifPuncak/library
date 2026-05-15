<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideshowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->isAdmin(), 403);
            return $next($request);
        });
    }

    public function index()
    {
        $slides = Slideshow::latest()->get();
        return view('slideshow.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'slideshow_pic' => ['required', 'image', 'max:2048'], // 2 MB
        ]);

        $path = $request->file('slideshow_pic')->store('slideshows', 'public');

        Slideshow::create([
            'title'         => $data['title'],
            'slideshow_pic' => $path,
        ]);

        return redirect()
            ->route('slideshow.index')
            ->with('status', 'Slide added.');
    }

    public function destroy(Slideshow $slideshow)
    {
        if ($slideshow->slideshow_pic) {
            Storage::disk('public')->delete($slideshow->slideshow_pic);
        }
        $slideshow->delete();

        return redirect()
            ->route('slideshow.index')
            ->with('status', 'Slide removed.');
    }
}
