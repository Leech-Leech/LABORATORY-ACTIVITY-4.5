<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Blog Post Reports</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            color: #222;
        }

        .filter-box {
            background: #f2f5fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .field {
            display: inline-block;
            margin: 0 15px 15px 0;
        }

        label {
            display: block;
            margin-bottom: 6px;
        }

        input,
        button,
        .button {
            padding: 10px;
        }

        button,
        .button {
            display: inline-block;
            background: #2457c5;
            color: white;
            border: 0;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .csv {
            background: #21834b;
        }

        .errors {
            color: #a40000;
            background: #fff0f0;
            padding: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
            overflow-wrap: anywhere;
        }

        th {
            background: #eef2f7;
        }

        .actions {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Blog Post Reports</h1>

    <p>Filter your blog posts and download a PDF or CSV report.</p>

    @if ($errors->any())
        <div class="errors">
            <strong>Please correct the following:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('posts.reports.index') }}"
        method="GET"
        class="filter-box"
    >
        <div class="field">
            <label for="start_date">Start Date</label>

            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ old('start_date', $startDate) }}"
            >
        </div>

        <div class="field">
            <label for="end_date">End Date</label>

            <input
                type="date"
                id="end_date"
                name="end_date"
                value="{{ old('end_date', $endDate) }}"
            >
        </div>

        <div>
            <button type="submit">Generate Report</button>

            <a href="{{ route('posts.reports.index') }}">
                Reset Filters
            </a>
        </div>
    </form>

    <p>
        <strong>Period:</strong>
        {{ $startDate ?: 'Beginning' }}
        to
        {{ $endDate ?: 'Present' }}
    </p>

    <h2>Total Posts: {{ $totalPosts }}</h2>

    <div class="actions">
        <a
            class="button"
            href="{{ route('posts.reports.pdf', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]) }}"
        >
            Export PDF
        </a>

        <a
            class="button csv"
            href="{{ route('posts.reports.csv', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]) }}"
        >
            Export CSV
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Body Preview</th>
                <th>Created Date</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>

                    <td>{{ $post->title }}</td>

                    <td>
                        {{ \Illuminate\Support\Str::limit($post->body, 100) }}
                    </td>

                    <td>{{ $post->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        No posts found for the selected dates.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('posts.index') }}">Back to Blog Posts</a>

</body>
</html>
