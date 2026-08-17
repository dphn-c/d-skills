# WordPress Theme Scaffold - File Templates Reference

Replace `$THEME_SLUG`, `$NAMESPACE`, `$THEME_NAME`, `$AUTHOR`, `$AUTHOR_URI` with actual values.

---

## Config Files

### style.css (WordPress theme metadata)

```css
/*
Theme Name: $THEME_NAME
Text Domain: $THEME_SLUG
Version: 1.0
Requires at least: 6.1
Requires PHP: 7
Author: $AUTHOR
Author URI: $AUTHOR_URI
*/
```

### composer.json

```json
{
  "name": "$THEME_SLUG/theme",
  "description": "$THEME_NAME WordPress theme",
  "type": "wordpress-theme",
  "autoload": {
    "psr-4": {
      "$NAMESPACE\\": "inc/"
    }
  },
  "require": {},
  "require-dev": {
    "squizlabs/php_codesniffer": "^3.7"
  },
  "scripts": {
    "lint": "phpcs --standard=PSR12",
    "format": "phpcbf --standard=PSR12"
  }
}
```

### package.json

```json
{
  "name": "$THEME_SLUG",
  "version": "1.0.0",
  "type": "module",
  "private": true,
  "packageManager": "pnpm@10.34.5",
  "scripts": {
    "build": "vite build",
    "dev": "vite build --watch",
    "lint:scss": "stylelint \"assets/scss/**/*.scss\"",
    "lint:scss:fix": "stylelint \"assets/scss/**/*.scss\" --fix"
  },
  "devDependencies": {
    "@awmottaz/prettier-plugin-void-html": "^1.9.0",
    "sass": "^1.96.0",
    "stylelint": "^17.2.0",
    "stylelint-config-recess-order": "^7.6.1",
    "stylelint-config-standard-scss": "^17.0.0",
    "vite": "^8.0.16"
  }
}
```

### vite.config.js

```javascript
import { defineConfig } from 'vite';
import { resolve } from 'path';

/**
 * CSS url() パスを出力先に合わせて補正するプラグイン
 *
 * SCSSソースでは url('../images/...') / url('../fonts/...') と記述しているが、
 * Vite のビルド後 CSS (assets/css/style.css) では ../  が除去されてしまう。
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

export default defineConfig({
  root: '.',
  server: {
    open: true,
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    cssCodeSplit: false,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'dev/main.js'),
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
      '@': resolve(__dirname, 'assets'),
    },
  },
  plugins: [cssUrlRebasePlugin()],
});
```

### .prettierrc

```json
{
  "printWidth": 100,
  "tabWidth": 2,
  "singleQuote": true,
  "useTabs": false,
  "semi": true,
  "jsxSingleQuote": true,
  "quoteProps": "as-needed",
  "trailingComma": "none",
  "bracketSpacing": true,
  "requirePragma": false,
  "insertPragma": false,
  "htmlWhitespaceSensitivity": "css",
  "vueIndentScriptAndStyle": true,
  "endOfLine": "auto",
  "arrowParens": "avoid",
  "plugins": ["@awmottaz/prettier-plugin-void-html"]
}
```

### .stylelintrc.js

```javascript
export default {
  extends: ['stylelint-config-standard-scss', 'stylelint-config-recess-order'],
  ignoreFiles: ['**/node_modules/**'],
  rules: {
    'property-no-vendor-prefix': null,
    'comment-empty-line-before': null,
    'media-feature-range-notation': 'context',
    'at-rule-empty-line-before': [
      'always',
      {
        except: ['blockless-after-same-name-blockless', 'first-nested'],
        ignore: ['inside-block', 'after-comment']
      }
    ],
    'selector-not-notation': null
  }
};
```

### .gitignore

```
*.ai
*.psd
*.css.map
*.DS_Store
node_modules/
.vscode/
package-lock.json
vendor/
composer.lock
```

Note: `.vscode/` is gitignored in WordPress theme scaffolds. For HTML/SCSS projects, commit `.vscode/settings.json` (Stylelint on save) and omit `.vscode/` from `.gitignore`.

### .vscode/settings.json

```json
{
  "css.validate": false,
  "scss.validate": false,
  "stylelint.validate": ["css", "scss"],
  "editor.codeActionsOnSave": {
    "source.fixAll.stylelint": "explicit"
  }
}
```

### index.html (HTML projects only)

Development — Vite entry for HMR:

```html
<script type="module" src="/assets/dev/main.js"></script>
```

Production static deploy — after `npm run build`:

```html
<link rel="stylesheet" href="/assets/css/style.css">
<script type="module" src="/assets/js/bundle.js"></script>
```

Do not link `/assets/css/style.css` during `npm run dev`; SCSS must enter Vite through `main.js`.

---

## PHP Template Files

### index.php

```php
<?php
get_header();
while (have_posts()) : the_post();
  the_content();
endwhile;
get_footer();
```

### page.php

```php
<?php
get_header();
while (have_posts()) : the_post();
  if (file_exists(dirname(__FILE__) . "/pages/page-" . esc_attr($post->post_name) . ".php")) {
    get_template_part('pages/page', esc_attr($post->post_name));
  } else {
    get_template_part('/template_parts/breadcrumb');
?>
    <section class="p-common-page">
      <div class="p-common-page__container c-container c-container--large">
        <div class="p-common-page__inner c-page-container c-container c-container--fix-medium">
          <h1 class="p-common-page__title c-ttl c-ttl--linear"><?= wp_kses_post(get_the_title()); ?></h1>
          <div class="p-common-page__content-container c-common-content">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </section>
<?php
  }
endwhile;
get_footer();
?>
```

### single.php

```php
<?php
get_header();
if (file_exists(dirname(__FILE__) . "/singles/single-" . esc_attr($post->post_type) . ".php")) {
  get_template_part('singles/single', esc_attr($post->post_type));
} else {
  while (have_posts()) : the_post();
    get_template_part('/template_parts/breadcrumb');
?>
    <article class="p-single">
      <div class="p-single__container c-container c-container--large">
        <div class="p-single__inner c-page-container c-container c-container--fix-medium">
          <hgroup class="p-single__header">
            <h1 class="p-single__title c-ttl c-ttl--linear"><?= wp_kses_post(get_the_title()); ?></h1>
            <p class="p-single__date"><?= esc_html(get_the_date('Y.m.d')); ?></p>
          </hgroup>
          <div class="p-single__content-container c-common-content">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </article>
<?php
  endwhile;
}
get_footer();
?>
```

### archive.php

```php
<?php
get_header();
$archive_query = get_queried_object();
if (file_exists(dirname(__FILE__) . "/archives/archive-" . esc_attr($archive_query->name) . ".php")) {
  get_template_part('archives/archive', esc_attr($archive_query->name));
} else {
  get_template_part('/template_parts/breadcrumb');
}
get_footer();
```

### header.php

```php
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script type="module"
    src="<?= esc_url(get_stylesheet_directory_uri()); ?>/assets/js/bundle.js?<?= filemtime(get_theme_file_path('/assets/js/bundle.js')); ?>">
  </script>
  <?php wp_head();
  $logo_tag = is_home() || is_front_page() ? 'h1' : 'p';
  ?>
</head>

<body <?php body_class(); ?>>
  <header class="l-header">
    <div class="l-header__container">
      <<?= $logo_tag ?> class="l-header__logo">
        <a href="<?= esc_url(home_url()); ?>">
          <?= esc_html(get_bloginfo('name')); ?>
        </a>
      </<?= $logo_tag ?>>
      <nav class="l-header-nav" aria-label="Main navigation">
        <!-- Navigation here -->
      </nav>
    </div>
  </header>
  <main id="main" class="l-main">
```

### footer.php

```php
  </main>
  <footer class="l-footer">
    <div class="l-footer__inner">
      <div class="l-footer__container">
        <!-- Footer content here -->
      </div>
    </div>
  </footer>
  <?php wp_footer(); ?>
</body>

</html>
```

### 404.php

```php
<?php
get_header();
?>
<article class="p-common-page">
  <div class="p-common-page__container c-container c-container--large">
    <div class="p-common-page__inner c-page-container c-container c-container--fix-medium">
      <h1 class="p-common-page__title c-ttl c-ttl--linear">404 Error</h1>
      <div class="p-common-page__content-container c-common-content">
        <p>The page you are looking for could not be found.</p>
      </div>
    </div>
  </div>
</article>
<?php
get_footer();
?>
```

### functions.php

```php
<?php

use $NAMESPACE\Setup;

$autoload_path = get_stylesheet_directory() . '/vendor/autoload.php';

if (file_exists($autoload_path)) {
  require_once $autoload_path;

  try {
    if (class_exists('$NAMESPACE\Setup')) {
      (new Setup())();
    } else {
      error_log('$NAMESPACE: Setup class not found');
    }
  } catch (\Throwable $e) {
    error_log('$NAMESPACE Setup Error: ' . $e->getMessage());
    error_log('$NAMESPACE Setup Error Trace: ' . $e->getTraceAsString());
    if (defined('WP_DEBUG') && WP_DEBUG) {
      wp_die('$NAMESPACE Setup Error: ' . $e->getMessage());
    }
  }
} else {
  error_log('$NAMESPACE: vendor/autoload.php not found. Please run "composer install"');
}
```

---

## PHP Core Classes

### inc/Setup.php

```php
<?php

namespace $NAMESPACE;

class Setup
{
    private array $excluded_dirs = ['Contracts', 'Traits', 'Interfaces', 'Exceptions'];
    private array $excluded_files = ['Setup.php', 'config.php', 'autoload.php'];

    public function __invoke(): void
    {
        $inc_dir = get_stylesheet_directory() . '/inc';
        $classes = $this->discoverClasses($inc_dir, '$NAMESPACE');

        foreach ($classes as $class_name) {
            $this->initializeClass($class_name);
        }
    }

    private function discoverClasses(string $dir, string $namespace): array
    {
        $classes = [];

        if (!is_dir($dir)) {
            return $classes;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                if (in_array($item, $this->excluded_dirs, true)) {
                    continue;
                }
                $sub_namespace = $namespace . '\\' . $item;
                $classes = array_merge($classes, $this->discoverClasses($path, $sub_namespace));
            } elseif (is_file($path) && pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                if (in_array($item, $this->excluded_files, true)) {
                    continue;
                }
                $class_name = $namespace . '\\' . pathinfo($item, PATHINFO_FILENAME);
                $classes[] = $class_name;
            }
        }

        return $classes;
    }

    private function initializeClass(string $class_name): void
    {
        if (!class_exists($class_name)) {
            return;
        }

        try {
            $instance = new $class_name();

            if (method_exists($instance, '__invoke')) {
                $instance();
            }
        } catch (\Exception $e) {
            error_log(
                sprintf(
                    'Failed to initialize class %s: %s',
                    $class_name,
                    $e->getMessage()
                )
            );
        }
    }
}
```

### inc/Assets/Assets.php

```php
<?php

namespace $NAMESPACE\Assets;

class Assets
{
    public function __invoke(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_style(
            '$THEME_SLUG-style',
            get_theme_file_uri('/assets/css/style.css'),
            [],
            filemtime(get_theme_file_path('/assets/css/style.css'))
        );
    }
}
```

---

## SCSS Files

### assets/scss/style.scss

```scss
@charset "UTF-8";
@use 'foundation';
@use 'component';
@use 'layout';
@use 'project';
@use 'utility';
```

### assets/scss/foundation/_index.scss

```scss
@forward 'reset';
@forward 'vars';
@forward 'colors';
@forward 'fonts';
@forward 'functions';
@forward 'mixin';
@forward 'base';
```

### assets/scss/foundation/_vars.scss

```scss
// Breakpoints
$bp-sp: 767px;
$bp-tab: 1024px;
$bp-pc: 1025px;

// Container widths
$container-large: 1200px;
$container-medium: 960px;
$container-small: 800px;
```

### assets/scss/foundation/_colors.scss

```scss
// Brand colors
$color-primary: #333;
$color-secondary: #666;
$color-accent: #0066cc;

// Base colors
$color-bg: #fff;
$color-text: #333;
$color-border: #ddd;
```

### assets/scss/foundation/_fonts.scss

```scss
// Font families
$font-ja: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;
$font-en: 'Helvetica Neue', Arial, sans-serif;

// Font weights
$fw-regular: 400;
$fw-medium: 500;
$fw-bold: 700;
```

### assets/scss/foundation/_functions.scss

```scss
// px to rem
@function rem($px) {
  @return calc($px / 16) * 1rem;
}

// px to vw (based on 375px SP design)
@function vw($px, $base: 375) {
  @return calc($px / $base) * 100vw;
}
```

### assets/scss/foundation/_mixin.scss

```scss
@use 'vars' as *;

// Media queries
@mixin sp {
  @media (max-width: $bp-sp) {
    @content;
  }
}

@mixin tab {
  @media (min-width: ($bp-sp + 1)) and (max-width: $bp-tab) {
    @content;
  }
}

@mixin pc {
  @media (min-width: $bp-pc) {
    @content;
  }
}

// Hover (pointer devices only)
@mixin hover {
  @media (hover: hover) {
    &:hover {
      @content;
    }
  }
}
```

### assets/scss/foundation/_reset.scss

```scss
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 100%;
  -webkit-text-size-adjust: 100%;
}

body {
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
}

img,
picture,
video,
canvas,
svg {
  display: block;
  max-width: 100%;
}

input,
button,
textarea,
select {
  font: inherit;
}

a {
  color: inherit;
  text-decoration: none;
}

ul,
ol {
  list-style: none;
}
```

### assets/scss/foundation/_base.scss

```scss
@use 'vars' as *;
@use 'colors' as *;
@use 'fonts' as *;

body {
  font-family: $font-ja;
  font-weight: $fw-regular;
  color: $color-text;
  background-color: $color-bg;
}
```

### assets/scss/component/_index.scss

```scss
// Add component imports here
// @forward 'breadcrumb';
// @forward 'container';
```

### assets/scss/layout/_index.scss

```scss
@forward 'header';
@forward 'footer';
@forward 'main';
```

### assets/scss/layout/_header.scss

```scss
.l-header {
  position: relative;
  width: 100%;
}
```

### assets/scss/layout/_footer.scss

```scss
.l-footer {
  width: 100%;
}
```

### assets/scss/layout/_main.scss

```scss
.l-main {
  min-height: 50vh;
}
```

### assets/scss/project/_index.scss

```scss
// Add project-specific imports here
// @forward 'top';
// @forward 'common-page';
```

### assets/scss/utility/_index.scss

```scss
// Add utility imports here
// @forward 'common';
```

---

## JS Entry Point

### dev/main.js

```javascript
// SCSS import
import '../assets/scss/style.scss';

// Module imports (add as needed)
// import ModuleName from './modules/module-name.js';

// Initialization (add as needed)
// new ModuleName();
```

---

## Class Pattern Reference

Every class in `inc/` follows this pattern for auto-initialization:

```php
<?php

namespace $NAMESPACE\SubDirectory;

class ClassName
{
    public function __invoke(): void
    {
        // Register hooks, filters, actions here
        add_action('hook_name', [$this, 'methodName']);
    }

    public function methodName(): void
    {
        // Implementation
    }
}
```

The `__invoke()` method is called automatically by `Setup.php` when the class is discovered. Use it to register WordPress hooks only. Actual logic goes in separate methods.
