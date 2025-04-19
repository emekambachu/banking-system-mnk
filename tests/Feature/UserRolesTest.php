<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_endpoint_includes_roles(): void
    {
        // Disable middleware to call direct
        $this->withoutMiddleware();

        $adminRole = Role::factory()->create(['slug'=>'admin','name'=>'Administrator']);
        $user = User::factory()->create([
            'status' => 1
        ]);

        // Attach role pivot
        $user->roles()->attach($adminRole->id);
        $response = $this->getJson('/users');

        $response->assertStatus(200)
            ->assertJson(['success'=>true]);

        $data = $response->json('users.data');
        // Find our user
        $found = collect($data)->first(fn($u) => $u['id'] === $user->id);
        $this->assertNotNull($found);

        // Ensure 'roles' key exists and includes our role slug
        $this->assertArrayHasKey('roles', $found);
        $slugs = array_map(fn($r) => $r['slug'], $found['roles']);
        $this->assertContains('admin', $slugs);
    }
}
