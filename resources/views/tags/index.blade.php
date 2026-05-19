@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('media.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to media
    </a>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-lib-navy">Tags</h1>
                <p class="text-sm text-slate-500">Lightweight labels used to mark media items.</p>
            </div>

            <form method="GET" action="{{ route('tags.index') }}" class="w-full sm:w-auto">
                <div class="relative sm:w-72">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="search" name="search" value="{{ $search ?? '' }}"
                           placeholder="Search tags…"
                           autocomplete="off"
                           class="w-full pl-10 pr-3 py-2.5 rounded-full bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30 focus:bg-white transition-colors">
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('tags.store') }}" class="p-6 border-b border-slate-100">
            @csrf
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3">
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       placeholder="New tag name"
                       autocomplete="off"
                       class="flex-grow px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30 transition-colors">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Tag
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            @if($tags->isEmpty())
                <div class="py-12 text-center text-slate-400 text-sm">No tags found.</div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <tr>
                            <th class="text-left px-6 py-3">Name</th>
                            <th class="text-left px-6 py-3">Media count</th>
                            <th class="text-right px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tags as $tag)
                            <tr class="hover:bg-slate-50 transition-colors" x-data="{ editing: false }">
                                <td class="px-6 py-3">
                                    <div x-show="!editing">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200">#{{ $tag->name }}</span>
                                    </div>
                                    <form x-show="editing" x-cloak method="POST" action="{{ route('tags.update', $tag) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $tag->name }}" required maxlength="100"
                                               class="flex-grow px-3 py-1.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-lib-navy text-white text-xs font-bold hover:bg-lib-sky transition-colors">Save</button>
                                        <button type="button" @click="editing = false" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors">Cancel</button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-slate-500">{{ $tag->media_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="inline-flex items-center gap-1" x-show="!editing">
                                        <button type="button" @click="editing = true"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-lib-navy transition-colors">Edit</button>
                                        @if($tag->media_count > 0)
                                            <button type="button" disabled
                                                    title="This tag is still assigned to {{ $tag->media_count }} media {{ Str::plural('item', $tag->media_count) }}."
                                                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-300 cursor-not-allowed">Delete</button>
                                        @else
                                            <form action="{{ route('tags.destroy', $tag) }}" method="POST"
                                                  onsubmit="return confirm('Delete this tag?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 transition-colors">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($tags->hasPages())
            <div class="border-t border-slate-100 p-4">
                {{ $tags->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
