<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])
            ->each(fn (string $level) => Level::firstOrCreate(['name' => $level]));
    }
}
