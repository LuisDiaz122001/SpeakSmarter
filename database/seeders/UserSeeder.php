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
            'name' => 'Admin Demo',
            'first_name' => 'Admin',
            'last_name' => 'Demo',
            'phone' => '3000001001',
            'password' => Hash::make('admin1234'),
        ]);
        $admin->syncRoles(['admin']);

        $editor = User::updateOrCreate([
            'email' => 'editor@speaksmarter.com',
        ], [
            'name' => 'Editor Demo',
            'first_name' => 'Editor',
            'last_name' => 'Demo',
            'phone' => '3000001002',
            'password' => Hash::make('editor1234'),
        ]);
        $editor->syncRoles(['editor']);

        $client = User::updateOrCreate([
            'email' => 'client@speaksmarter.com',
        ], [
            'name' => 'Cliente Demo',
            'first_name' => 'Cliente',
            'last_name' => 'Demo',
            'phone' => '3000001003',
            'password' => Hash::make('client1234'),
        ]);
        $client->syncRoles(['client']);
    }
}
