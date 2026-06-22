import { Config as ZiggyConfig, route as ziggyRoute } from 'ziggy-js';

// Allow importing .vue single-file components from TypeScript.
declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent<Record<string, unknown>, Record<string, unknown>, unknown>;
    export default component;
}

// Ziggy's route() and laravel-permission-to-vuejs's can()/is() are registered
// globally through plugins in app.js; expose them to template type-checking in
// <script setup lang="ts"> components.
declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
        can: (permission: string) => boolean;
        is: (role: string) => boolean;
    }
}

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: {
                id: number;
                name: string;
                email: string;
                [key: string]: unknown;
            } | null;
        };
        ziggy: ZiggyConfig & { location: string };
        flash: {
            success?: string | null;
            error?: string | null;
        };
        js_permissions: unknown;
        [key: string]: unknown;
    }
}

declare global {
    const Ziggy: ZiggyConfig;
    const route: typeof ziggyRoute;
    interface Window {
        Ziggy: ZiggyConfig;
        Laravel: {
            csrfToken: string;
            jsPermissions?: unknown;
        };
    }
}

export {};
