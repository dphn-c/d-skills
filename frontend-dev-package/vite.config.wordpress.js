import { defineConfig } from 'vite';
import { resolve } from 'path';

const DEV_SOURCE_ROOT = resolve(__dirname, 'assets/dev');

function suppressDevFallbackSourcemapPlugin() {
  return {
    name: 'suppress-dev-fallback-sourcemap',
    enforce: 'post',
    apply: 'serve',
    transform(code, id) {
      if (!id.startsWith(DEV_SOURCE_ROOT) || id.includes('node_modules')) {
        return;
      }

      return {
        code,
        map: {
          version: 3,
          mappings: '',
          sources: [],
        },
      };
    },
  };
}

function cssUrlRebasePlugin() {
  return {
    name: 'css-url-rebase',
    enforce: 'post',
    generateBundle(_, bundle) {
      for (const [fileName, chunk] of Object.entries(bundle)) {
        if (fileName.endsWith('.css') && chunk.type === 'asset') {
          let css = typeof chunk.source === 'string' ? chunk.source : '';
          css = css.replace(
            /url\((?:["']?)((?:images|fonts)\/[^"')]+)(?:["']?)\)/g,
            'url(../$1)',
          );
          chunk.source = css;
        }
      }
    },
  };
}

/**
 * WordPress theme — build output: assets/css/style.css + assets/js/bundle.js
 *
 * Usage:
 *   cp vite.config.wordpress.js vite.config.js
 *   Set package.json "dev" script to: "vite build --watch"
 */
export default defineConfig({
  root: '.',
  server: {
    open: true,
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/dev/main.js'),
      },
      output: {
        entryFileNames: 'js/bundle.js',
        assetFileNames: (assetInfo) => {
          const fileName = assetInfo.names?.[0] || assetInfo.name || '';
          if (fileName.endsWith('.css')) {
            return 'css/style.css';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
    manifest: false,
    sourcemap: true,
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: '',
        charset: false,
      },
    },
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'assets'),
    },
  },
  plugins: [suppressDevFallbackSourcemapPlugin(), cssUrlRebasePlugin()],
});
