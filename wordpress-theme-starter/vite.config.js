import { defineConfig } from 'vite';
import { resolve } from 'path';

const rootDir = import.meta.dirname;
const DEV_SOURCE_ROOT = resolve(rootDir, 'assets/dev');

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
 * WordPress theme — build output: assets/css/style.css + assets/js/bundle.js (IIFE)
 *
 * Production JS is IIFE so the bundle does not leak module bindings onto `window`
 * (GTM / analytics / other classic scripts). Enqueue with `wp_enqueue_script`
 * without the module strategy — do not load this file with `type="module"`.
 */
export default defineConfig({
  root: '.',
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    cssCodeSplit: false,
    rollupOptions: {
      input: {
        main: resolve(rootDir, 'assets/dev/main.js'),
      },
      output: {
        format: 'iife',
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
      '@': resolve(rootDir, 'assets'),
    },
  },
  plugins: [suppressDevFallbackSourcemapPlugin(), cssUrlRebasePlugin()],
});
