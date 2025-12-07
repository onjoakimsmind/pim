<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Site::factory()->create([
            'name' => 'Main Site',
            'domain' => 'main.example.com',
            'locale' => 'en',
            'is_active' => true,
        ]);

        \App\Models\Site::factory()->create([
            'name' => 'Spanish Site',
            'domain' => 'es.example.com',
            'locale' => 'es',
            'is_active' => true,
        ]);

        \App\Models\Site::factory()->count(3)->create();
    }
}
