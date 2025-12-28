<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasEffectiveRole
{
    /**
     * Get the effective role for the current authenticated user
     * - If user has one role: returns user->role
     * - If user has multiple roles: returns user->selected_role
     * 
     * @return string|null
     */
    protected function getEffectiveRole(): ?string
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }
        
        $user->refresh(); // Get latest selected_role from database
        return $user->getEffectiveRole();
    }
    
    /**
     * Check if user's effective role matches the given role
     * 
     * @param string $role
     * @return bool
     */
    protected function hasEffectiveRole(string $role): bool
    {
        return $this->getEffectiveRole() === $role;
    }
    
    /**
     * Ensure user's effective role matches the required role
     * 
     * @param string $requiredRole
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function ensureEffectiveRole(string $requiredRole): void
    {
        $effectiveRole = $this->getEffectiveRole();
        
        if ($effectiveRole !== $requiredRole) {
            abort(403, __('You must switch to :role role to access this page.', ['role' => $requiredRole]));
        }
    }
}

