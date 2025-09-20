import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig } from 'vite';

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
    // server: {
    //   host: process.env.APP_URL.replace('https://','').replace('http://','').replace('/',''),
    //     // https: {
    //     //     key: fs.readFileSync('./certs/devinside.ru_le1.key'),
    //     //     cert: fs.readFileSync('./certs/devinside.ru_le1.crt'),
    //     // },
    //     cors: {
    //       origin: process.env.APP_URL,
    //         methods: 'GET,HEAD,PUT,PATCH,POST,DELETE',
    //         credentials: true
    //     }
    // },
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
