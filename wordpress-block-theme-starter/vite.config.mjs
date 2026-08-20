import { defineConfig } from 'vite';
import { resolve } from 'path';

const rootDir = import.meta.dirname;

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

export default defineConfig({
  root: '.',
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    cssCodeSplit: false,
    rollupOptions: {
      input: {
        main: resolve(rootDir, 'src/frontend/main.js'),
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
    sourcemap: true,
  },
  plugins: [cssUrlRebasePlugin()],
});
