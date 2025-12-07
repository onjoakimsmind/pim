<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = \App\Models\Site::all();
        $user = \App\Models\User::factory()->create();

        foreach ($sites as $site) {
            $home = \App\Models\Document::factory()->create([
                'site_id' => $site->id,
                'author_id' => $user->id,
                'parent_id' => null,
                'type' => 'page',
                'title' => 'Home',
                'slug' => 'home',
                'published' => true,
                'published_at' => now(),
            ]);

            $about = \App\Models\Document::factory()->create([
                'site_id' => $site->id,
                'author_id' => $user->id,
                'parent_id' => null,
                'type' => 'page',
                'title' => 'About Us',
                'slug' => 'about',
                'published' => true,
                'published_at' => now(),
            ]);

            \App\Models\Document::factory()->count(3)->create([
                'site_id' => $site->id,
                'author_id' => $user->id,
                'parent_id' => $about->id,
            ]);

            \App\Models\Document::factory()->count(5)->create([
                'site_id' => $site->id,
                'author_id' => $user->id,
                'parent_id' => null,
            ]);
        }
    }
}
