<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

use App\Models\Slideshow;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil hero photo terbaru (atau tandakan khusus jika ada is_hero)
       $hero = Asset::where('type', 'Photo')
                 ->latest('created_at') // Betulkan typo 'create_at' jika ada
                 ->first(); // ambil satu je sebagai hero

        $slideshows = Slideshow::latest()->get();

        // Most Viewed
        $mostViewed = Asset::join('asset_details', 'assets.id', '=', 'asset_details.asset_id')
            ->where('asset_details.key', 'views')
            ->where('asset_details.value', '>', 5) // Minimum 5 views to be "Most Viewed"
            ->select('assets.*', 'asset_details.value as views_count')
            ->orderByRaw('CAST(asset_details.value AS UNSIGNED) DESC')
            ->take(5)
            ->with(['categories', 'tags'])
            ->get();

        // Recently Viewed
        $recentlyViewedIds = session()->get('recently_viewed', []);
        $recentlyViewed = Asset::whereIn('id', $recentlyViewedIds)
            ->with(['categories', 'tags'])
            ->get()
            ->sortBy(function($asset) use ($recentlyViewedIds) {
                return array_search($asset->id, $recentlyViewedIds);
            });

        // Check if logged-in user is admin
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;

        $categories = \App\Models\Category::all();

        return view('home', compact('hero', 'isAdmin', 'slideshows', 'mostViewed', 'categories', 'recentlyViewed'));
    }
}
