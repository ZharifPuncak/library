<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
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
        Log::info('SLIDER INDEX — page loaded', [
            'php_upload' => ini_get('upload_max_filesize'),
            'php_post'   => ini_get('post_max_size'),
            'ini_file'   => php_ini_loaded_file(),
        ]);
        $slides = Slider::latest()->get();
        return view('slider.index', compact('slides'));
    }

    public function store(Request $request)
    {
        Log::info('SLIDER STORE — request hit', [
            'method'       => $request->method(),
            'has_file'     => $request->hasFile('slider_pic'),
            'file_valid'   => $request->hasFile('slider_pic') ? $request->file('slider_pic')->isValid() : null,
            'file_error'   => $request->hasFile('slider_pic') ? $request->file('slider_pic')->getError() : null,
            'file_size'    => $request->hasFile('slider_pic') ? $request->file('slider_pic')->getSize() : null,
            'file_mime'    => $request->hasFile('slider_pic') ? $request->file('slider_pic')->getMimeType() : null,
            'title'        => $request->input('title'),
            'inputs'       => array_keys($request->all()),
            'files'        => array_keys($request->allFiles()),
            'content_type' => $request->header('Content-Type'),
            'php_upload'   => ini_get('upload_max_filesize'),
            'php_post'     => ini_get('post_max_size'),
        ]);

        $data = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'slider_pic' => ['required', 'image', 'max:25600'],
        ]);
        Log::info('SLIDER STORE — validation passed');

        $path = $request->file('slider_pic')->store('sliders', 'public');
        Log::info('SLIDER STORE — stored on disk', ['path' => $path]);

        $slide = Slider::create([
            'title'      => $data['title'],
            'slider_pic' => $path,
        ]);
        Log::info('SLIDER STORE — DB row created', ['id' => $slide->id]);

        return redirect()
            ->route('slider.index')
            ->with('status', 'Slide added.');
    }

    public function destroy(Slider $slider)
    {
        if ($slider->slider_pic) {
            Storage::disk('public')->delete($slider->slider_pic);
        }
        $slider->delete();

        return redirect()
            ->route('slider.index')
            ->with('status', 'Slide removed.');
    }
}
