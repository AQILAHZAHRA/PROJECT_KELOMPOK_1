import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/admin.css',
                'resources/js/admin.js',
                'resources/css/nasabah.css',
                'resources/js/nasabah.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        open: false,
        strictPort: true
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        sourcemap: false,
        minify: 'terser',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'axios', 'lodash'],
                    admin: ['./resources/js/admin.js'],
                    nasabah: ['./resources/js/nasabah.js']
                }
            }
        }
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            '@css': '/resources/css',
            '@img': '/resources/images'
        }
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "@/scss/variables.scss";`
            }
        }
    }
});
