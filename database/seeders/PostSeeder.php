<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Different created_at dates so the date filters can be tested.
        $posts = [
            ['Learning Laravel',    'Today I learned how Laravel routes work.',                  5],
            ['Understanding Models', 'Models help us interact with database tables.',            4],
            ['Working with Blade',   'Blade displays information in our application.',           3],
            ['Creating Reports',     'Reports organize records into readable summaries.',        1],
            ['Exporting Data',       'Users can download reports as PDF or CSV files.',          0],
            ['Quotes Test',          'Today, I learned "Laravel Reports".',                      0],
        ];

        foreach ($posts as [$title, $body, $daysAgo]) {
            $post = Post::create(['title' => $title, 'body' => $body]);
            $post->created_at = now()->subDays($daysAgo);
            $post->updated_at = now()->subDays($daysAgo);
            $post->save();
        }
    }
}
