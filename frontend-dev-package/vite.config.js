import { defineConfig } from 'vite';
import { resolve } from 'path';

const DEV_SOURCE_ROOT = resolve(__dirname, 'assets/dev');

/**
 * Vite dev サーバーが JS に付与するフォールバック source map は
 * sources: ["module.js"] のようにファイル名のみになり、
 * GitLens が URI 解決に失敗して通知を出し続ける。
 * mappings を空にしてフォールバック注入を抑止する。
 */
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

/**
 * CSS url() パスを出力先に合わせて補正するプラグイン
 *
 * SCSSソースでは url('../images/...') / url('../fonts/...') と記述しているが、
 * Vite のビルド後 CSS (built/css/style.css) では ../  が除去されてしまう。
 * 出力 CSS から assets/images/, assets/fonts/ へ到達するには ../images/, ../fonts/
 * が必要なため、最終出力で url(images/...) → url(../images/...) に書き換える。
 */
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

/** Static HTML project — deploy `built/` to the server */
export default defineConfig({
  root: '.',
  base: './',
  server: {
    open: true,
  },
  build: {
    outDir: 'built',
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(__dirname, 'index.html'),
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
