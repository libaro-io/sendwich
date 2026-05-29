import { Config as ZiggyConfig } from 'ziggy-js';

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
    interface Window {
        Ziggy: ZiggyConfig;
        Laravel: {
            csrfToken: string;
            jsPermissions?: unknown;
        };
    }
}

export {};
