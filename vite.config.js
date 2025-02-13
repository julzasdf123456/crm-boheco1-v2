import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue'


export default defineConfig({
    plugins: [

        vue(),

        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name].[hash][extname]',
                chunkFileNames: 'js/[name].[hash].js',
                entryFileNames: 'js/[name].[hash].js',
            },
        },
    },
    // server: {
    //     proxy: {
    //         '/bills': {
    //             target: 'http://192.168.10.48', // Your Laravel server
    //             changeOrigin: true,
    //             secure: false,
    //         },
    //     },
    // },
});
