import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/flash.min.js',
                'resources/js/flash.jquery.min.js',
                'resources/css/dashboard/main.css',
                'resources/css/flash.min.css',
                'resources/fonts/dashboard/TrebuchetMS/Trebuchet-BoldItalic.eot',
                'resources/fonts/dashboard/TrebuchetMS/Trebuchet-BoldItalic.ttf',
                'resources/fonts/dashboard/TrebuchetMS/Trebuchet-BoldItalic.woff',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Bold.eot',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Bold.ttf',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Bold.woff',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Italic.eot',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Italic.ttf',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS-Italic.woff',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS.eot',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS.ttf',
                'resources/fonts/dashboard/TrebuchetMS/TrebuchetMS.woff',
            ],
            refresh: true,
        }),
    ],
});
