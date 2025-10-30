import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    /*server: {
        host: "0.0.0.0",
        port: 5173,
        cors: true, // 🔑 habilita CORS no servidor Vite
        hmr: {
            host: "192.168.4.52", // seu IP local
        },
    },*/
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
