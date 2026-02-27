@extends('layouts.app')

@section('title', 'Manage Tags')

@section('content')
<div class="main-content">
    <h1 class="page-title">Manage Tags</h1>

    <div style="max-width:700px;">
        <form action="{{ route('admin.tags.store') }}" method="POST" style="display:flex; gap:8px; margin-bottom:1rem;">
            @csrf
            <input type="text" name="tagName" placeholder="New tag name" class="year-input" required>
            <button type="submit" class="apply-btn">Add Tag</button>
        </form>

        <div>
            <h3>Existing Tags</h3>
            <ul>
                @foreach($tags as $tag)
                    <li>{{ $tag->tagName }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
