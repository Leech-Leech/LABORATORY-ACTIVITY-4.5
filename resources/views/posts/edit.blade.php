@extends('posts.layout')

@section('title', 'Edit Post')

@section('content')
    <h1>Edit Post</h1>

    <form method="POST" action="{{ route('posts.update', $post) }}">
        @csrf
        @method('PUT')

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>

        <label for="body">Body</label>
        <textarea id="body" name="body" rows="8" required>{{ old('body', $post->body) }}</textarea>

        <p>
            <button type="submit">Update Post</button>
            <a href="{{ route('posts.index') }}">Cancel</a>
        </p>
    </form>
@endsection
