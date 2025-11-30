import { defineConfig } from 'vite';
import commonjs from "vite-plugin-commonjs";
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        commonjs(),
        laravel({
            input: [
                'resources/assets/voting.ts',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
