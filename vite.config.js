import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            vue: path.resolve(
                __dirname,
                "node_modules/vue/dist/vue.esm-bundler.js"
            ),
        },
    },
    server: {
        port: 3000,
        open: true,
    },
});
