import { Ziggy } from '../ziggy';

// Route function implementation based on Ziggy
function route(name, params = {}, absolute = false) {
    if (!name || !Ziggy.routes || !Ziggy.routes[name]) {
        // Silently return '#' for undefined or missing routes to avoid console spam
        return '#';
    }

    const routeDef = Ziggy.routes[name];
    let url = routeDef.uri;

    // Handle simple value (string/number) - treat as first parameter
    if (!Array.isArray(params) && typeof params !== 'object' || params === null) {
        const paramKeys = routeDef.parameters || [];
        if (paramKeys.length > 0) {
            params = { [paramKeys[0]]: params };
        } else {
            params = { course: params };
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
        if (value !== null && value !== undefined) {
            url = url.replace(paramPattern, encodeURIComponent(value));
        }
    });
    
    // Remove any remaining optional parameters that weren't provided
    url = url.replace(/{[^}]*\?}/g, '');

    // Remove optional parameters that weren't provided
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

