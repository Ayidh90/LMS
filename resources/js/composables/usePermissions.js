import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Composable for handling permissions in Vue components
 * Provides a can() function to check if user has specific permissions
 * 
 * @returns {Object} Object containing can function and other permission utilities
 */
export function usePermissions() {
    const page = usePage();
    
    /**
     * Get the current user from page props
     */
    const user = computed(() => page.props?.auth?.user);
    
    /**
     * Get permissions object from page props
     */
    const permissions = computed(() => page.props?.auth?.can || {});
    
    /**
     * Check if user has a specific permission or permissions
     * Uses page.props.auth.can from HandleInertiaRequests
     * 
     * @param {string|string[]} permissionOrPermissions - Single permission string or array of permissions
     * @returns {boolean} True if user has at least one of the required permissions
     * 
     * @example
     * can('courses.create')
     * can(['courses.create', 'courses.edit'])
     */
    const can = (permissionOrPermissions) => {
        // Safety check: ensure auth and can exist
        if (!page.props?.auth?.can) {
            return false;
        }
        try {
            // Safety check: ensure permissionOrPermissions is valid
            if (permissionOrPermissions === null || permissionOrPermissions === undefined) {
                return false;
            }
            let perms = [].concat(permissionOrPermissions);
            let allPermissions = page.props.auth.can;
            // Safety check: ensure allPermissions is an object
            if (!allPermissions || typeof allPermissions !== 'object') {
                return false;
            }
            let hasPermission = false;
            perms.forEach((item) => {
                if (item && allPermissions[item]) {
                    hasPermission = true;
                }
            });
            return hasPermission;
        } catch (error) {
            console.error('Error in can method:', error);
            return false;
        }
    };
    
    /**
     * Check if user has all of the specified permissions
     * 
     * @param {string[]} permissions - Array of permissions to check
     * @returns {boolean} True if user has all permissions
     * 
     * @example
     * canAll(['courses.create', 'courses.edit'])
     */
    const canAll = (permissionsArray) => {
        if (!Array.isArray(permissionsArray) || permissionsArray.length === 0) {
            return false;
        }
        
        return permissionsArray.every(permission => can(permission));
    };
    
    /**
     * Check if user has a specific role
     * 
     * @param {string|string[]} roleOrRoles - Single role string or array of roles
     * @returns {boolean} True if user has at least one of the required roles
     */
    const hasRole = (roleOrRoles) => {
        if (!user.value) {
            return false;
        }
        
        const roles = Array.isArray(roleOrRoles) ? roleOrRoles : [roleOrRoles];
        const userRoles = page.props?.auth?.roles || [];
        
        return roles.some(role => 
            userRoles.includes(role) || 
            user.value.role === role
        );
    };
    
    /**
     * Check if user is admin
     * @returns {boolean}
     */
    const isAdmin = computed(() => {
        return user.value?.is_admin === true || 
               user.value?.role === 'admin' || 
               user.value?.role === 'super_admin';
    });
    
    /**
     * Check if user is instructor
     * @returns {boolean}
     */
    const isInstructor = computed(() => {
        return hasRole('instructor');
    });
    
    /**
     * Check if user is student
     * @returns {boolean}
     */
    const isStudent = computed(() => {
        return hasRole('student');
    });
    
    /**
     * Get the effective role for the user
     * - If user has one role: returns user->role
     * - If user has multiple roles: returns user->selected_role
     * 
     * @returns {string|null}
     */
    const getEffectiveRole = computed(() => {
        if (!user.value) {
            return null;
        }
        
        const availableRoles = page.props?.auth?.availableRoles || [];
        
        // If user has only one role, use that role
        if (availableRoles.length === 1) {
            return user.value.role || availableRoles[0]?.slug || null;
        }
        
        // If user has multiple roles, use selected_role (check both camelCase and snake_case)
        if (availableRoles.length > 1) {
            return page.props?.auth?.selected_role || 
                   page.props?.auth?.selectedRole || 
                   user.value.selected_role ||
                   user.value.role || 
                   null;
        }
        
        // Fallback to user->role
        return user.value.role || null;
    });
    
    return {
        can,
        canAll,
        hasRole,
        isAdmin,
        isInstructor,
        isStudent,
        getEffectiveRole,
        user,
        permissions,
    };
}

