<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\RoleRepository;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_role_by_id_and_slug(): void
    {
        $role = Role::factory()->create([
            'name' => 'Manager',
            'slug' => 'manager'
        ]);

        $repo = new RoleRepository();

        $byId = $repo->getRoleById($role->id);
        $this->assertInstanceOf(Role::class, $byId);
        $this->assertEquals('manager', $byId->slug);

        $bySlug = $repo->getRoleBySlug('manager');
        $this->assertInstanceOf(Role::class, $bySlug);
        $this->assertEquals($role->id, $bySlug->id);
    }
}
