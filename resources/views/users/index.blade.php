@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold uppercase text-lib-sky mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
            Library
        </div>
        <h1 class="text-2xl font-bold text-lib-navy leading-tight">Digital Library</h1>
        <p class="text-sm text-slate-500 mt-1 max-w-2xl">Open a media type to browse the archive.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach([
            ['title' => 'Photos', 'copy' => 'Browse image collections', 'route' => route('media.photos'), 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['title' => 'Videos', 'copy' => 'Watch the video library', 'route' => route('media.videos'), 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
            ['title' => 'E-books', 'copy' => 'Read the e-book collection', 'route' => route('media.ebooks'), 'icon' => 'M12 6.253v13M12 6.253C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253M12 6.253C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253'],
        ] as $item)
            <a href="{{ $item['route'] }}"
               class="group bg-white rounded-lg border border-slate-100 p-6 shadow-sm hover:shadow-lg hover:border-lib-sky/40 transition-all">
                <span class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-full bg-lib-light text-lib-sky group-hover:bg-lib-sky group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </span>
                <h2 class="text-base font-bold text-lib-navy">{{ $item['title'] }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $item['copy'] }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
