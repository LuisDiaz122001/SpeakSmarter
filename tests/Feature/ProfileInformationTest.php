<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->put('/user/profile-information', [
            'first_name' => 'Test',
            'last_name' => 'Name',
            'phone' => '3001234567',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('Test', $user->fresh()->first_name);
        $this->assertEquals('Name', $user->fresh()->last_name);
        $this->assertEquals('3001234567', $user->fresh()->phone);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }
}
