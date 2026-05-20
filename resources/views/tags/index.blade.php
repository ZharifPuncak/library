@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('media.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to media
    </a>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold uppercase text-lib-sky mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
                Media labels
            </div>
            <h1 class="text-2xl font-bold text-lib-navy leading-tight">Tags</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">Manage lightweight labels used to mark media items.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-400">Total tags</p>
                <p class="text-2xl font-bold text-lib-navy leading-none mt-1">{{ $tags->total() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[380px_minmax(0,1fr)] gap-6 items-start">
        <section class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-base font-bold text-lib-navy">Add tag</h2>
                <p class="text-sm text-slate-500 mt-1">Create a short label for media discovery.</p>
            </div>

            <form method="POST" action="{{ route('tags.store') }}" class="p-6 space-y-5">
                @csrf

                @php
                    $floatInput = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
                    $floatLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
                @endphp

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <div class="relative">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder=" "
                           autocomplete="off"
                           class="{{ $floatInput }}">
                    <label for="name" class="{{ $floatLabel }}">Tag name</label>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md shadow-lib-navy/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Tag
                </button>
            </form>
        </section>

        <section class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-lib-navy">Current tags</h2>
                        <p class="text-sm text-slate-500">Review, search, and update tag names.</p>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full bg-slate-50 px-3 py-1 text-[10px] font-bold uppercase text-slate-500">
                        {{ $tags->total() }} {{ Str::plural('tag', $tags->total()) }}
                    </span>
                </div>

                @php
                    $clearUrl = route('tags.index');
                @endphp
                <form method="GET" action="{{ route('tags.index') }}" class="mt-5"
                      x-data="{ query: @js((string) ($search ?? '')), hadSearch: @js(!empty($search)) }"
                      x-init="$watch('query', value => {
                          if (hadSearch && value.trim() === '') {
                              window.location.href = @js($clearUrl);
                          }
                      })">
                    <div class="relative flex items-center gap-2">
                        <div class="relative flex-grow">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="search" name="search" x-model="query" value="{{ $search ?? '' }}"
                                   placeholder="Search tags..."
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-transparent rounded-lg text-sm font-medium text-lib-navy placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:bg-white transition-all">
                        </div>
                        @if(!empty($search))
                            <a href="{{ $clearUrl }}"
                               class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-red-500 hover:bg-red-50 transition-colors">Clear</a>
                        @endif
                        <button type="submit"
                                :disabled="!query.trim()"
                                :class="query.trim()
                                    ? 'bg-lib-sky hover:bg-lib-navy text-white shadow-md shadow-lib-sky/30 cursor-pointer'
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-colors whitespace-nowrap">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-6">
                @if($tags->isEmpty())
                    <div class="flex min-h-72 flex-col items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-white text-lib-sky shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M3 11.5V5a2 2 0 012-2h6.5a2 2 0 011.414.586l7.5 7.5a2 2 0 010 2.828l-6.5 6.5a2 2 0 01-2.828 0l-7.5-7.5A2 2 0 013 11.5z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-lib-navy">No tags found</h3>
                        <p class="text-sm text-slate-500 mt-1">Add a tag or adjust the search term.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-slate-100">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="text-left px-4 py-3">Name</th>
                                    <th class="text-left px-4 py-3">Media count</th>
                                    <th class="text-right px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($tags as $tag)
                                    <tr class="hover:bg-slate-50 transition-colors" x-data="{ editing: false }">
                                        <td class="px-4 py-3">
                                            <div x-show="!editing">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200">#{{ $tag->name }}</span>
                                            </div>
                                            <form x-show="editing" x-cloak method="POST" action="{{ route('tags.update', $tag) }}" class="flex flex-col sm:flex-row sm:items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $tag->name }}" required maxlength="100"
                                                       class="min-w-56 flex-grow px-4 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30">
                                                <button type="submit" class="px-4 py-2 rounded-lg bg-lib-navy text-white text-xs font-bold hover:bg-lib-sky transition-colors">Save</button>
                                                <button type="button" @click="editing = false" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors">Cancel</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 text-slate-500">{{ $tag->media_count }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="inline-flex items-center gap-1" x-show="!editing">
                                                <button type="button" @click="editing = true"
                                                        class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-lib-navy transition-colors">Edit</button>
                                                @if($tag->media_count > 0)
                                                    <button type="button" disabled
                                                            title="This tag is still assigned to {{ $tag->media_count }} media {{ Str::plural('item', $tag->media_count) }}."
                                                            class="px-4 py-2 rounded-lg text-xs font-bold text-slate-300 cursor-not-allowed">Delete</button>
                                                @else
                                                    <form action="{{ route('tags.destroy', $tag) }}" method="POST"
                                                          onsubmit="return confirm('Delete this tag?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-4 py-2 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 transition-colors">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if($tags->hasPages())
                <div class="border-t border-slate-100 p-4">
                    {{ $tags->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
