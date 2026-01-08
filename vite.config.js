import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/custom/hero.css',
                'resources/css/custom/home.css',
                'resources/css/custom/post.css',
                'resources/css/custom/member.css',
                'resources/css/custom/product.css',
                'resources/css/custom/auth.css',
                'resources/css/custom/counter.css',
                'resources/css/custom/search.css',
                'resources/css/custom/premium_bg.css'
            ],
            refresh: true,
        }),
    ],
});
