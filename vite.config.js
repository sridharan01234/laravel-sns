import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Define input files dynamically
const cssFiles = [
    'resources/css/app.css',
    'resources/css/custom.css',
    'resources/css/animations.css',
    'resources/css/theme.css',
];

const jsFiles = [
    'resources/js/app.js'
];

const inputFiles = [...cssFiles, ...jsFiles];

export default defineConfig({
    plugins: [
        laravel({
            input: inputFiles,
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const extType = info[info.length - 1];

                    if (extType === 'css') {
                        const fileName = info[0];
                        if (cssFiles.some(file => file.includes(fileName))) {
                            return `css/${fileName}[extname]`;
                        }
                    }

                    return 'assets/[name]-[hash][extname]';
                },
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
            }
        }
    }
});
