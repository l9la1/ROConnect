<<<<<<< HEAD
import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
=======
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
>>>>>>> 9c8a8b2 (Initialize Laravel WebRTC Video Call project)

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
<<<<<<< HEAD
        cors: true,
=======
>>>>>>> 9c8a8b2 (Initialize Laravel WebRTC Video Call project)
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
