<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blog')</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 40px auto; padding: 0 20px; color: #222; }
        nav a { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; overflow-wrap: anywhere; }
        th { background: #eef2f7; }
        input[type=text], textarea { width: 100%; padding: 10px; box-sizing: border-box; }
        label { display: block; margin: 12px 0 6px; }
        button { padding: 10px 14px; background: #2457c5; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        button.danger { background: #b3261e; }
        .success { background: #e8f7ee; color: #14633a; padding: 12px; margin: 15px 0; }
        .errors { background: #fff0f0; color: #a40000; padding: 12px; margin: 15px 0; }
        form.inline { display: inline; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('posts.index') }}">Blog Posts</a>
        <a href="{{ route('posts.reports.index') }}">View Reports</a>
    </nav>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</body>
</html>
