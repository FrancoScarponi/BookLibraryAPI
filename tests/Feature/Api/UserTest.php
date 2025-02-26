<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_example(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name'=>'admin','guard_name'=>'web']);
        $user->assignRole('admin');
        
        $response = $this->get('/api/users');
        $response->assertStatus(200);
    }
}
