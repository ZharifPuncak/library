@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back to users
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h1 class="text-xl font-bold text-lib-navy mb-1">Edit User</h1>
        <p class="text-sm text-slate-500 mb-6">Update <span class="font-semibold text-slate-700">{{ $user->name }}</span>'s account details.</p>

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('user._form', ['user' => $user])

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('users.index') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
