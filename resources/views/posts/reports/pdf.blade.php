<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Blog Post Report</title>

    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            color: #222;
        }

        h1 {
            text-align: center;
            font-size: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #888;
            padding: 7px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #e8edf5;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <h1>Blog Post Report</h1>

    <div class="details">
        <p><strong>Prepared by:</strong> {{ $preparedBy }}</p>

        <p>
            <strong>Date Range:</strong>

            @if (!$startDate && !$endDate)
                All Dates
            @else
                {{ $startDate ?: 'Beginning' }}
                to
                {{ $endDate ?: 'Present' }}
            @endif
        </p>

        <p><strong>Generated:</strong> {{ $generatedAt }}</p>

        <p><strong>Total Posts:</strong> {{ $totalPosts }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Title</th>
                <th style="width: 45%;">Body Preview</th>
                <th style="width: 22%;">Created Date</th>
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

</body>
</html>
