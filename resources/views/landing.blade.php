@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-lib-navy via-lib-blue to-lib-sky"></div>
    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 60%, white 1px, transparent 1px); background-size: 60px 60px;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 text-white">
        <div class="max-w-3xl">
            <span class="inline-block bg-white/10 backdrop-blur px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-6">
                Puncak Niaga Digital Library
            </span>
            <h1 class="font-fredoka text-4xl md:text-6xl font-bold leading-tight mb-6">
                Discover. Read. Explore.
            </h1>
            <p class="text-lg md:text-xl text-sky-100 mb-10 max-w-2xl">
                A curated archive of photos, videos, ebooks and a 360&deg; virtual library tour &mdash;
                everything in one place.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('media.index') }}"
                   class="bg-white text-lib-navy px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    Browse Library
                </a>
                @guest
                    <a href="{{ route('login') }}"
                       class="bg-white/10 backdrop-blur border border-white/30 text-white px-8 py-4 rounded-xl font-bold hover:bg-white/20 transition-all">
                        Staff Login
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
    <div class="text-center mb-14">
        <h2 class="font-fredoka text-3xl md:text-4xl font-bold text-lib-navy mb-3">What's inside</h2>
        <p class="text-slate-600 max-w-2xl mx-auto">Four ways to explore the collection.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $features = [
                ['icon' => 'photos',  'title' => 'Photography', 'desc' => 'High-resolution images from events, locations, and archives.', 'route' => 'media.index', 'params' => ['type' => 'photo']],
                ['icon' => 'videos',  'title' => 'Video Gallery', 'desc' => 'Documentaries, interviews, and archival footage.', 'route' => 'media.index', 'params' => ['type' => 'video']],
                ['icon' => 'ebooks',  'title' => 'Digital Books', 'desc' => 'Reports, journals, and publications you can read online.', 'route' => 'media.index', 'params' => ['type' => 'ebook']],
                ['icon' => 'vr',      'title' => 'VR Tour', 'desc' => 'Walk the library in 360&deg; without leaving your desk.', 'route' => 'vr'],
            ];
        @endphp

        @foreach($features as $feature)
            <a href="{{ route($feature['route'], $feature['params'] ?? []) }}"
               class="group bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl border border-slate-100 hover:border-lib-sky transition-all hover:-translate-y-1">
                <div class="w-12 h-12 rounded-xl bg-lib-light text-lib-sky flex items-center justify-center mb-4 group-hover:bg-lib-sky group-hover:text-white transition-colors text-xl font-bold">
                    @switch($feature['icon'])
                        @case('photos') 📸 @break
                        @case('videos') 🎬 @break
                        @case('ebooks') 📚 @break
                        @case('vr')     🥽 @break
                    @endswitch
                </div>
                <h3 class="font-bold text-lg text-lib-navy mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-slate-600 leading-relaxed">{!! $feature['desc'] !!}</p>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-white border-t border-slate-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="font-fredoka text-2xl md:text-3xl font-bold text-lib-navy mb-4">Ready to dive in?</h2>
        <p class="text-slate-600 mb-8">Open access to the full catalog &mdash; no account needed for browsing.</p>
        <a href="{{ route('home') }}"
           class="inline-block bg-lib-navy text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:bg-lib-blue transition-colors">
            Enter the Library
        </a>
    </div>
</section>
@endsection
