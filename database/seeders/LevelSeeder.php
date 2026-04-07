<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use app\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::create([
            'name' => 'A1',
            'description' => 'Suitable for those who are new to the language and want to learn basic communication skills.',
        ]);
        Level::create([
            'name' => 'A2',
            'description' => 'Suitable for those who have a basic understanding of the language and want to improve their communication skills.',
        ]);
        Level::create([
            'name' => 'B1',
            'description' => 'Suitable for those who have a good understanding of the language and want to improve their fluency and accuracy.',
        ]);
        Level::create([
            'name' => 'B2',
            'description' => 'Suitable for those who have a very good understanding of the language and want to improve their proficiency and confidence.',
        ]);
        Level::create([
            'name' => 'C1',
            'description' => 'Suitable for those who have an excellent understanding of the language and want to improve their mastery and sophistication.',
        ]);
        Level::create([
            'name' => 'C2',
            'description' => 'Suitable for those who have a near-native understanding of the language and want to improve their expertise and creativity.',
        ]);
    }
}
