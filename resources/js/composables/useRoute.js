import { Ziggy } from '../ziggy';

// Route function implementation based on Ziggy
function route(name, params = {}, absolute = false) {
    if (!name || !Ziggy.routes || !Ziggy.routes[name]) {
        // Log warning in development mode
        if (import.meta.env.DEV) {
            console.warn(`Route "${name}" not found in Ziggy routes`);
        }
        // Return '#' for undefined or missing routes to avoid navigation issues
        return '#';
    }

    const routeDef = Ziggy.routes[name];
    let url = routeDef.uri;

    // Handle simple value (string/number) - treat as first parameter
    // Check if params is a primitive value (not object, not array, not null)
    if ((typeof params !== 'object' || params === null) && !Array.isArray(params)) {
        const paramKeys = routeDef.parameters || [];
        if (paramKeys.length > 0) {
            params = { [paramKeys[0]]: params };
        } else {
            // Fallback for routes without defined parameters
            params = {};
        }
    }
    
    // Handle array params (for nested routes like [course.id, lesson.id])
    if (Array.isArray(params)) {
        const paramKeys = routeDef.parameters || [];
        params = params.reduce((acc, val, idx) => {
            if (paramKeys[idx]) {
                acc[paramKeys[idx]] = val;
            } else if (idx === 0 && paramKeys.length > 0) {
                acc[paramKeys[0]] = val;
            } else {
                acc[`param${idx}`] = val;
            }
            return acc;
        }, {});
    }

    // Replace route parameters
    Object.keys(params).forEach(key => {
        // Handle both {key} and {key?} patterns
        const paramPattern = new RegExp(`\\{${key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\??\\}`, 'g');
        const value = params[key];
        if (value !== null && value !== undefined && value !== '') {
            url = url.replace(paramPattern, encodeURIComponent(String(value)));
        }
    });
    
    // Remove any remaining optional parameters that weren't provided
    url = url.replace(/{[^}]*\?}/g, '');

    // Build absolute URL if needed
    if (absolute) {
        url = `${Ziggy.url}${url}`;
    } else if (!url.startsWith('/')) {
        url = `/${url}`;
    }

    return url;
}

export function useRoute() {
    return { route };
}

