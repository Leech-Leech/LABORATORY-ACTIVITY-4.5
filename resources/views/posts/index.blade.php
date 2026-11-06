@extends('posts.layout')

@section('title', 'Blog Posts')

@section('content')
    <h1>Blog Posts</h1>

    <a href="{{ route('posts.create') }}">Create New Post</a>

    <a href="{{ route('posts.reports.index') }}">View Reports</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('posts.show', $post) }}">View</a> |
                        <a href="{{ route('posts.edit', $post) }}">Edit</a> |
                        <form class="inline" method="POST" action="{{ route('posts.destroy', $post) }}"
                              onsubmit="return confirm('Delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
