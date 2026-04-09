<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@speaksmarter.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('admin1234'),
        ]);
        $admin->syncRoles(['admin']);

        $editor = User::updateOrCreate([
            'email' => 'editor@speaksmarter.com',
        ], [
            'name' => 'Editor',
            'password' => Hash::make('editor1234'),
        ]);
        $editor->syncRoles(['editor']);

        $client = User::updateOrCreate([
            'email' => 'client@speaksmarter.com',
        ], [
            'name' => 'Client',
            'password' => Hash::make('client1234'),
        ]);
        $client->syncRoles(['client']);
    }
}
