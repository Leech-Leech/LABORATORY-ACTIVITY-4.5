@extends('posts.layout')

@section('title', $post->title)

@section('content')
    <h1>{{ $post->title }}</h1>

    <p><small>Created {{ $post->created_at->format('M d, Y H:i') }}</small></p>

    <p style="white-space: pre-line;">{{ $post->body }}</p>

    <p>
        <a href="{{ route('posts.edit', $post) }}">Edit</a> |
        <a href="{{ route('posts.index') }}">Back to Blog Posts</a>
    </p>
@endsection
