<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NegativeWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $words = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'deficit', 'decrease', 'recession'];
    foreach ($words as $word) {
        \App\Models\NegativeWord::create(['word' => $word]);
    }
}
}
