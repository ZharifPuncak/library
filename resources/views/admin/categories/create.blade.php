@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="main-content">
    <h1 class="page-title">Add Category</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST" style="max-width:480px;">
        @csrf
        <div style="margin-bottom:1rem;">
            <label for="categoryName">Category Name</label>
            <input id="categoryName" name="categoryName" class="year-input" required>
        </div>
        <button type="submit" class="apply-btn">Create Category</button>
    </form>
</div>
@endsection
