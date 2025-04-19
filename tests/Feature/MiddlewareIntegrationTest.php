<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TwoFactorAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MiddlewareIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_sanctum_blocks_unauthenticated(): void
    {
        $response = $this->getJson('/users/my-transactions');
        $response->assertStatus(401);
    }

    public function test_auth_sanctum_allows_authenticated(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret'), 'status'=>1]);
        $response = $this->actingAs($user, 'sanctum')->getJson('/users/my-transactions');
        // TwoFactorAuthMiddleware should let pass since no 2FA
        $response->assertStatus(200);
    }

    public function test_two_factor_middleware_blocks_when_expired(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret'), 'status'=>1]);
        TwoFactorAuth::create([
            'user_id'            => $user->id,
            'secret'             => 'DEF',
            'secret_verified_at' => Carbon::now()->subHour(),
            'secret_expires_at'  => Carbon::now()->subMinutes(10),
            'enabled'            => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->get('/some-protected-route');
        // Assuming /some-protected-route uses TwoFactorAuthMiddleware
        $response->assertRedirect('/login');
    }

    public function test_admin_middleware_integration(): void
    {
        $admin = User::factory()->create(['status'=>1]);
        $role  = Role::factory()->create(['slug'=>'admin']);
        $admin->roles()->attach($role->id);

        $response = $this->actingAs($admin, 'sanctum')->putJson('/users/'.$admin->id.'/update-status');
        $response->assertStatus(200)->assertJson(['success'=>true]);
    }

    public function test_admin_middleware_blocks_non_admin(): void
    {
        $user = User::factory()->create(['status'=>1]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/users/'.$user->id.'/update-status');
        $response->assertStatus(403)->assertJson(['success'=>false]);
    }
}
