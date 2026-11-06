<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PostReportController extends Controller
{
    // Name printed on the PDF ("Prepared by"). Add your first name here if you want the full name.
    private const PREPARED_BY = 'Leech';

    // Display the report page.
    public function index(Request $request)
    {
        $data = $this->getReportData($request);

        return view('posts.reports.index', $data);
    }

    // Download the report as PDF.
    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView('posts.reports.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('leech_blog_report.pdf');
    }

    // Download the report as CSV.
    public function exportCsv(Request $request)
    {
        $data = $this->getReportData($request);
        $posts = $data['posts'];

        return response()->streamDownload(function () use ($posts) {
            $file = fopen('php://output', 'w');

            // Helps spreadsheet applications recognize UTF-8 text.
            fwrite($file, "\xEF\xBB\xBF");

            // CSV column headings.
            fputcsv($file, [
                'ID',
                'Title',
                'Body',
                'Created At',
            ], ',', '"', '');

            foreach ($posts as $post) {
                fputcsv($file, [
                    $post->id,
                    $this->csvText((string) $post->title),
                    $this->csvText((string) $post->body),
                    $post->created_at->format('Y-m-d H:i:s'),
                ], ',', '"', '');
            }

            fclose($file);
        }, 'leech_blog_report.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // Reuse the same validation and filters for all report formats.
    private function getReportData(Request $request): array
    {
        $endDateRules = ['nullable', 'date_format:Y-m-d'];

        if ($request->filled('start_date')) {
            $endDateRules[] = 'after_or_equal:start_date';
        }

        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => $endDateRules,
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $query = Post::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $posts = $query->latest()->orderByDesc('id')->get();

        return [
            'posts' => $posts,
            'totalPosts' => $posts->count(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'preparedBy' => self::PREPARED_BY,
        ];
    }

    // Prevent spreadsheet formulas from running in user-entered text.
    private function csvText(string $value): string
    {
        if (preg_match('/^[\s\x{FEFF}]*[=+\-@]/u', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
