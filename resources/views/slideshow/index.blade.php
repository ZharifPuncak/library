@extends('layouts.app')

@section('title', 'Slideshow')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-100">
            <h1 class="text-xl font-bold text-lib-navy">Slideshow</h1>
            <p class="text-sm text-slate-500">Hero carousel images shown on the home page.</p>
        </div>

        {{-- Upload form --}}
        <form method="POST" action="{{ route('slideshow.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @php
                $floatInput = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
                $floatLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="relative md:col-span-2">
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required placeholder=" "
                           class="{{ $floatInput }}">
                    <label for="title" class="{{ $floatLabel }}">Slide title</label>
                </div>
                <div>
                    <label for="slideshow_pic" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Image
                    </label>
                    <input id="slideshow_pic" type="file" name="slideshow_pic" accept="image/*" required
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-lib-light file:text-lib-navy hover:file:bg-lib-sky hover:file:text-white transition-colors">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Slide
                </button>
            </div>
        </form>
    </div>

    {{-- Slides grid --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-lib-navy">All slides</h2>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $slides->count() }} slides</span>
        </div>

        @if($slides->isEmpty())
            <div class="py-12 text-center text-slate-400 text-sm">No slides yet. Add one above.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($slides as $slide)
                    <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-100">
                        <div class="aspect-[16/9] bg-slate-100">
                            <img src="{{ asset('storage/' . $slide->slideshow_pic) }}" alt="{{ $slide->title }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="p-4 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $slide->title }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $slide->created_at?->format('M j, Y') }}</p>
                            </div>
                            <form method="POST" action="{{ route('slideshow.destroy', $slide) }}"
                                  onsubmit="return confirm('Delete this slide?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 hover:bg-red-500 text-red-600 hover:text-white transition-colors"
                                        title="Delete slide">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
