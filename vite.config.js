import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/loader/loader1.css',

                'resources/js/app.js',
                'resources/js/global/main.js',
                'resources/js/orders/create/main.js',
                'resources/js/orders/edit/main.js',

                'resources/js/libs/filepond.js',
                //'resources/js/libs/calendar.js',
                'resources/js/libs/lightgalery.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
    ],
});
