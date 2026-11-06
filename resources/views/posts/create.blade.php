@extends('posts.layout')

@section('title', 'Create Post')

@section('content')
    <h1>Create New Post</h1>

    <form method="POST" action="{{ route('posts.store') }}">
        @csrf

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}" required>

        <label for="body">Body</label>
        <textarea id="body" name="body" rows="8" required>{{ old('body') }}</textarea>

        <p>
            <button type="submit">Save Post</button>
            <a href="{{ route('posts.index') }}">Cancel</a>
        </p>
    </form>
@endsection
