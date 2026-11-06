<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

// REPORT ROUTES: must come BEFORE the resource route,
// otherwise /posts/reports is read as /posts/{post}.
Route::get('/posts/reports', [PostReportController::class, 'index'])
    ->name('posts.reports.index');

Route::get('/posts/reports/pdf', [PostReportController::class, 'exportPdf'])
    ->name('posts.reports.pdf');

Route::get('/posts/reports/csv', [PostReportController::class, 'exportCsv'])
    ->name('posts.reports.csv');

// CRUD ROUTES
Route::resource('posts', PostController::class);
