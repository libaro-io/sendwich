import {tailwindReferencePlugin} from '@libaro-io/libaro-utilities';
import * as path from 'node:path';

import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import {defineConfig} from "vite";

export default defineConfig({
    resolve: {
        alias: {
            "@css": path.resolve(__dirname, "resources/css"),
            "@layouts": path.resolve(__dirname, "resources/js/layouts"),
            "@components": path.resolve(__dirname, "resources/js/components"),
        },
    },
    server: {
        host: '0.0.0.0',
        hmr: { host: 'localhost'}
    },
    plugins: [
        tailwindReferencePlugin(),
        laravel({
            input: 'resources/js/app.js',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
});
