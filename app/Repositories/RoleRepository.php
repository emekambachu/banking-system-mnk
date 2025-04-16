<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\UserRole;

class RoleRepository
{
    private Role $role;
    private UserRole $userRole;
    public function __construct()
    {
        $this->role = new Role();
        $this->userRole = new UserRole();
    }

    public function addRoleToUser($userId, $roleId): void
    {
        $this->userRole->create([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }

    public function getRoleById($id): Role
    {
        return $this->role->findOrFail($id);
    }

    public function getRoleBySlug($slug): Role
    {
        return $this->role->where('slug', $slug)->firstOrFail();
    }

}
