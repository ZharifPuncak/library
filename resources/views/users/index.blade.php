@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 text-center">

    <h1 class="text-3xl font-bold mb-8">Welcome to Digital Library</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        {{-- Photos --}}
        <a href="{{ route('assets.photos') }}" 
           class="block p-6 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
            <h2 class="text-xl font-semibold mb-2">Photos</h2>
            <p>Browse all image collections</p>
        </a>

        {{-- Videos --}}
        <a href="{{ route('assets.videos') }}" 
           class="block p-6 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
            <h2 class="text-xl font-semibold mb-2">Videos</h2>
            <p>Watch the video library</p>
        </a>

        {{-- Ebooks --}}
        <a href="{{ route('assets.ebooks') }}" 
           class="block p-6 bg-purple-600 text-white rounded-lg shadow hover:bg-purple-700">
            <h2 class="text-xl font-semibold mb-2">Ebooks</h2>
            <p>Read the e-book collection</p>
        </a>

    </div>

</div>
@endsection
