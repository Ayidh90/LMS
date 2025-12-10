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
     * 
     * @param {string|string[]} permissionOrPermissions - Single permission string or array of permissions
     * @returns {boolean} True if user has at least one of the required permissions
     * 
     * @example
     * can('courses.create')
     * can(['courses.create', 'courses.edit'])
     */
    const can = (permissionOrPermissions) => {
        // Super admin always has all permissions
        if (user.value?.role === 'super_admin') {
            return true;
        }
        
        // Admin with is_admin flag also has all permissions
        if (user.value?.is_admin && (user.value?.role === 'admin' || user.value?.role === 'super_admin')) {
            return true;
        }
        
        const authCan = permissions.value;
        
        if (!authCan || typeof authCan !== 'object') {
            return false;
        }
        
        // Check if user has the wildcard permission (super admin)
        if (authCan['*'] === true) {
            return true;
        }
        
        try {
            if (permissionOrPermissions === null || permissionOrPermissions === undefined || permissionOrPermissions === '') {
                return false;
            }
            
            // Handle array of permissions or single permission
            const perms = Array.isArray(permissionOrPermissions) 
                ? permissionOrPermissions 
                : [permissionOrPermissions];
            
            // Check if user has at least one of the required permissions
            return perms.some((permission) => {
                if (!permission || typeof permission !== 'string') {
                    return false;
                }
                // Check if permission exists in auth.can and is truthy
                return authCan[permission] === true;
            });
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
    
    return {
        can,
        canAll,
        hasRole,
        isAdmin,
        isInstructor,
        isStudent,
        user,
        permissions,
    };
}

