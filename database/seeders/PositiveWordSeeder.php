<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositiveWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $words = ['growth', 'increase', 'profit', 'stable', 'improve', 'surplus', 'recovery'];
    foreach ($words as $word) {
        \App\Models\PositiveWord::create(['word' => $word]);
    }
}
}
