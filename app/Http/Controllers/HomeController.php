<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;

use App\Models\Slideshow;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil hero photo terbaru (atau tandakan khusus jika ada is_hero)
       $hero = Media::where('type', 'Photo')
                 ->latest('created_at')
                 ->first();

        $slideshows = Slideshow::latest()->get();

        $mostViewed = Media::join('media_details', 'media.id', '=', 'media_details.media_id')
            ->where('media_details.key', 'views')
            ->where('media_details.value', '>', 5)
            ->select('media.*', 'media_details.value as views_count')
            ->orderByRaw('CAST(media_details.value AS UNSIGNED) DESC')
            ->take(5)
            ->with(['categories', 'tags'])
            ->get();

        $recentlyViewedIds = session()->get('recently_viewed', []);
        $recentlyViewed = Media::whereIn('id', $recentlyViewedIds)
            ->with(['categories', 'tags'])
            ->get()
            ->sortBy(function($media) use ($recentlyViewedIds) {
                return array_search($media->id, $recentlyViewedIds);
            });

        // Check if logged-in user is admin
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;

        $categories = \App\Models\Category::all();

        return view('home', compact('hero', 'isAdmin', 'slideshows', 'mostViewed', 'categories', 'recentlyViewed'));
    }
}
