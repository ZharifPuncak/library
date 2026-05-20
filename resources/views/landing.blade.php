@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<section class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm p-6 md:p-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lib-light text-[11px] font-bold uppercase text-lib-sky mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
            Puncak Niaga Digital Library
        </div>
        <h1 class="text-2xl font-bold text-lib-navy leading-tight">Discover. Read. Explore.</h1>
        <p class="text-sm text-slate-500 mt-1 max-w-2xl">
            A curated archive of photos, videos, books and a 360&deg; virtual library tour in one place.
        </p>
        <div class="flex flex-wrap gap-3 mt-6">
            <a href="{{ route('media.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold shadow-md transition-colors">
                Browse Library
            </a>
            @guest
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-700 hover:border-lib-sky hover:text-lib-navy text-sm font-bold transition-colors">
                    Staff Login
                </a>
            @endguest
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-lib-navy">What's inside</h2>
        <p class="text-sm text-slate-500 mt-1">Four ways to explore the collection.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @php
            $features = [
                ['title' => 'Photography', 'desc' => 'High-resolution images from events, locations, and archives.', 'route' => 'media.index', 'params' => ['type' => 'photo'], 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['title' => 'Video Gallery', 'desc' => 'Documentaries, interviews, and archival footage.', 'route' => 'media.index', 'params' => ['type' => 'video'], 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                ['title' => 'Digital Books', 'desc' => 'Reports, journals, and publications you can read online.', 'route' => 'media.index', 'params' => ['type' => 'ebook'], 'icon' => 'M12 6.253v13M12 6.253C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253M12 6.253C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253'],
                ['title' => 'VR Tour', 'desc' => 'Walk the library in 360&deg; without leaving your desk.', 'route' => 'vr', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
            ];
        @endphp

        @foreach($features as $feature)
            <a href="{{ route($feature['route'], $feature['params'] ?? []) }}"
               class="group bg-white rounded-lg p-6 shadow-sm hover:shadow-lg border border-slate-100 hover:border-lib-sky/40 transition-all hover:-translate-y-0.5">
                <div class="w-11 h-11 rounded-full bg-lib-light text-lib-sky flex items-center justify-center mb-4 group-hover:bg-lib-sky group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-base text-lib-navy mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{!! $feature['desc'] !!}</p>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-white border-t border-slate-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
        <h2 class="text-xl font-bold text-lib-navy mb-2">Ready to dive in?</h2>
        <p class="text-sm text-slate-600 mb-6">Open access to the full catalog. No account needed for browsing.</p>
        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center bg-lib-navy text-white px-4 py-2 rounded-full text-xs font-bold shadow-md hover:bg-lib-blue transition-colors">
            Enter the Library
        </a>
    </div>
</section>
@endsection
