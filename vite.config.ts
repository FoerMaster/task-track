import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig } from 'vite';
import * as fs from 'node:fs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
      host: 'foer.devinside.ru',
        // https: {
        //     key: fs.readFileSync('./certs/devinside.ru_le1.key'),
        //     cert: fs.readFileSync('./certs/devinside.ru_le1.crt'),
        // },
        cors: {
          origin: 'http://foer.devinside.ru:8000',
            methods: 'GET,HEAD,PUT,PATCH,POST,DELETE',
            credentials: true
        }
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
});
