@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-8 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Manage Tags</h2>

    <div class="mb-4 flex justify-between items-center">
        <form action="{{ route('admin.tags.index') }}" method="GET">
            <input type="text" name="search" placeholder="Search tags..." value="{{ request('search') }}" class="border rounded px-3 py-1">
            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Search</button>
        </form>
        <a href="{{ route('admin.tags.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Tag</a>
    </div>

    <table class="min-w-full border border-gray-300 rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Tag Name</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $tag)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $tag->name }}</td>
                    <td class="px-4 py-2 border flex gap-2">
                        <a href="{{ route('admin.tags.edit', $tag->id) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-confirm text-red-600 hover:underline" data-message="Are you sure you want to delete tag: {{ $tag->name }}?">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-2 text-center text-gray-500">No tags found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $tags->links() }}
    </div>
</div>
@endsection
