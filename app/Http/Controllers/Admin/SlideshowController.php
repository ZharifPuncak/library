<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideshowController extends Controller
{
    /**
     * Display slideshow management page
     */
    public function manage()
    {
        $slideshows = Slideshow::latest()->get();
        return view('admin.slideshow.manage', compact('slideshows'));
    }

    /**
     * Store a new slideshow
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slideshow_pic' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
        ], [
            'slideshow_pic.max' => 'The image is too large. Please upload an image smaller than 2MB.',
            'slideshow_pic.uploaded' => 'The image failed to upload. It might be larger than the server limit.',
            'slideshow_pic.mimes' => 'Only JPEG, PNG, and GIF images are allowed.',
        ]);

        // Store the image
        $path = $request->file('slideshow_pic')->store('slideshows', 'public');

        Slideshow::create([
            'title' => $validated['title'],
            'slideshow_pic' => $path,
        ]);

        return redirect()->route('admin.slideshow.manage')->with('success', 'Slide added successfully!');
    }

    /**
     * Update a slideshow
     */
    public function update(Request $request, $id)
    {
        $slideshow = Slideshow::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slideshow_pic' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ], [
            'slideshow_pic.max' => 'The image is too large. Please upload an image smaller than 2MB.',
            'slideshow_pic.uploaded' => 'The image failed to upload. It might be larger than the server limit.',
            'slideshow_pic.mimes' => 'Only JPEG, PNG, and GIF images are allowed.',
        ]);

        // Update title
        $slideshow->title = $validated['title'];

        // Update image if provided
        if ($request->hasFile('slideshow_pic')) {
            // Delete old image
            if ($slideshow->slideshow_pic && Storage::disk('public')->exists($slideshow->slideshow_pic)) {
                Storage::disk('public')->delete($slideshow->slideshow_pic);
            }

            // Store new image
            $path = $request->file('slideshow_pic')->store('slideshows', 'public');
            $slideshow->slideshow_pic = $path;
        }

        $slideshow->save();

        return redirect()->route('admin.slideshow.manage')->with('success', 'Slide updated successfully!');
    }

    /**
     * Delete a slideshow
     */
    public function destroy($id)
    {
        $slideshow = Slideshow::findOrFail($id);

        // Delete image file
        if ($slideshow->slideshow_pic && Storage::disk('public')->exists($slideshow->slideshow_pic)) {
            Storage::disk('public')->delete($slideshow->slideshow_pic);
        }

        $slideshow->delete();

        return redirect()->route('admin.slideshow.manage')->with('success', 'Slide deleted successfully!');
    }
}
