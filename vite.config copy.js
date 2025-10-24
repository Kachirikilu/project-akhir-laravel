import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// vite.config.js
// removed @vitejs/plugin-basic-ssl to force HTTP during dev


export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    // basicSsl() removed so the dev server uses plain HTTP
    ],
    
    server: {
    https: false,
        host: '0.0.0.0',
        port: 5173,
        cors: {
            origin: [
                'https://4m445w5p-8000.asse.devtunnels.ms',
                'https://fxkkrc1l-5173.asse.devtunnels.ms',
                // 'http://localhost:8000',
                // 'http://localhost:5173',
                // 'https://unpkg.com/leaflet/dist/leaflet.js',
                // 'https://unpkg.com/leaflet/dist/leaflet.css',
                // null,
            ],
            // origin: [
            //     'https://4m445w5p-8000.asse.devtunnels.ms',
            //     // 'https://8c22-101-128-109-80.ngrok-free.app',
            // ],
            methods: ['GET', 'HEAD', 'POST', 'OPTIONS'],
            credentials: true,
        }, 
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: 5173,
        },
        
    }
});
