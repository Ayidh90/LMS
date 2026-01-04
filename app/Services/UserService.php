<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Modules\Roles\Models\Role;
use Modules\Roles\Services\RoleService;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $repository,
        private RoleService $roleService
    ) {}

    /**
     * Get paginated users with filters
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Get user by ID with relations
     */
    public function getUserById(int $id, array $relations = ['roles']): ?User
    {
        return $this->repository->findById($id, $relations);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set defaults
        $data['is_admin'] = $data['is_admin'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;

        // Create user
        $user = $this->repository->create($data);

        // Sync roles if provided
        if (!empty($data['role_ids']) || !empty($data['role_id']) || !empty($data['role'])) {
            $this->syncUserRoles($user, $data);
        }

        // Handle selected role
        $this->handleSelectedRole($user);

        return $user->fresh(['roles']);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data): User
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password from data if not provided
            unset($data['password']);
        }

        // Update user
        $this->repository->update($user, $data);

        // Sync roles if provided
        if (isset($data['role_ids']) || isset($data['role_id']) || isset($data['role'])) {
            $this->syncUserRoles($user, $data);
        }

        // Handle selected role
        $this->handleSelectedRole($user);

        return $user->fresh(['roles']);
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user): bool
    {
        return $this->repository->delete($user);
    }

    /**
     * Sync user roles to model_has_roles table
     */
    public function syncUserRoles(User $user, array $data): void
    {
        $roles = $this->getRolesFromData($data);
        
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
    }

    /**
     * Get roles from data array
     */
    private function getRolesFromData(array $data): array
    {
        // Multiple roles
        if (!empty($data['role_ids']) && is_array($data['role_ids'])) {
            return Role::whereIn('id', $data['role_ids'])->get()->all();
        }

        // Single role by ID
        if (!empty($data['role_id'])) {
            $role = $this->roleService->getRoleById($data['role_id']);
            return $role ? [$role] : [];
        }

        // Fallback: find role by name or slug
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])
                ->orWhere('slug', $data['role'])
                ->first();
            return $role ? [$role] : [];
        }

        return [];
    }

    /**
     * Handle selected_role based on user's roles
     */
    public function handleSelectedRole(User $user): void
    {
        $user->refresh();
        $userRoles = $user->roles()->get();
        $rolesCount = $user->countUserRoles($userRoles);

        if ($rolesCount === 0) {
            $user->update(['selected_role' => null]);
            return;
        }

        if ($rolesCount === 1) {
            $singleRole = $userRoles->first();
            $roleSlug = $singleRole->slug ?? $singleRole->name ?? $user->role;
            $user->update(['selected_role' => $roleSlug]);
            return;
        }

        // Multiple roles - validate selected_role
        $selectedRole = $user->selected_role;
        if (!$selectedRole) {
            return;
        }

        $roleExists = $userRoles->contains(function($role) use ($selectedRole) {
            return ($role->slug === $selectedRole || $role->name === $selectedRole);
        });

        // Also check legacy role field
        if (!$roleExists && $user->role === $selectedRole) {
            return;
        }

        // Clear invalid selected_role
        if (!$roleExists) {
            $user->update(['selected_role' => null]);
        }
    }

    /**
     * Get dashboard route for a given role
     */
    public function getDashboardRouteForRole(?string $roleSlug): string
    {
        if (!$roleSlug) {
            return 'admin.dashboard';
        }

        // Check if role is admin type
        $role = Role::where('slug', $roleSlug)
            ->orWhere('name', $roleSlug)
            ->first();

        if ($role && $role->is_admin == 1) {
            return 'admin.dashboard';
        }

        return match ($roleSlug) {
            'instructor' => 'instructor.dashboard',
            'student' => 'student.dashboard',
            default => 'admin.dashboard',
        };
    }

    /**
     * Find user by email or national ID
     */
    public function findUserByEmailOrNationalId(string $email, ?string $nationalId = null): ?User
    {
        return $this->repository->findByEmailOrNationalId($email, $nationalId);
    }

    /**
     * Create a student user with proper role assignment
     */
    public function createStudentUser(array $data, ?Role $studentRole = null): User
    {
        // Set defaults for student
        $data['role'] = $data['role'] ?? 'student';
        $data['is_admin'] = $data['is_admin'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;

        // Generate random password if not provided
        if (empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Str::random(16);
        }

        // Create user
        $user = $this->createUser($data);

        // Ensure student role is assigned
        if ($studentRole) {
            if (!$user->hasRole($studentRole)) {
                $user->assignRole($studentRole);
            }
        } elseif (method_exists($user, 'assignRole')) {
            // Fallback: try to assign by name/slug if role model not found
            if (!$user->hasRole('student')) {
                $user->assignRole('student');
            }
        }

        return $user->fresh(['roles']);
    }

    /**
     * Ensure user has student role assigned
     */
    public function ensureStudentRole(User $user, ?Role $studentRole = null): void
    {
        if ($user->isStudent()) {
            return;
        }

        // Update legacy role field
        if ($user->role !== 'student') {
            $this->repository->update($user, ['role' => 'student']);
        }

        // Assign role using Spatie Permission
        if ($studentRole) {
            if (!$user->hasRole($studentRole)) {
                $user->assignRole($studentRole);
            }
        } elseif (method_exists($user, 'assignRole')) {
            // Fallback: try to assign by name/slug
            if (!$user->hasRole('student')) {
                $user->assignRole('student');
            }
        }
    }

    /**
     * Update user basic information (name, national_id)
     */
    public function updateUserInfo(User $user, string $name, ?string $nationalId = null): bool
    {
        $data = [];
        
        if ($user->name !== $name) {
            $data['name'] = $name;
        }
        
        if ($nationalId && $user->national_id !== $nationalId) {
            $data['national_id'] = $nationalId;
        }

        if (empty($data)) {
            return false;
        }

        return $this->repository->update($user, $data);
    }
}

