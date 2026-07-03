---
name: wordpress-theme-scaffold
description: Scaffold a new WordPress theme with Composer PSR-4 namespaces, Vite + SCSS build pipeline, and organized PHP class structure. Use when the user asks to create a new WordPress theme, start a WP theme project, or scaffold a WordPress theme from scratch.
---

# WordPress Theme Scaffold

Generates a production-ready WordPress theme scaffold with:
- Composer PSR-4 autoloading with namespaced PHP classes
- Vite + SCSS build pipeline
- FLOCSS-based SCSS architecture
- Auto-discovery class initialization pattern
- Organized template structure

## Workflow

```
Task Progress:
- [ ] Step 1: Gather project info (theme name, namespace, text domain)
- [ ] Step 2: Create directory structure
- [ ] Step 3: Generate config files (composer.json, package.json, vite.config.js, .vscode/settings.json, etc.)
- [ ] Step 4: Generate PHP core files (functions.php, Setup.php, Assets.php)
- [ ] Step 5: Generate template files (index, page, single, archive, header, footer, 404)
- [ ] Step 6: Generate SCSS foundation
- [ ] Step 7: Generate JS entry point
- [ ] Step 8: Run composer install && npm install
```

## Step 1: Gather Project Info

Ask the user for:
- **Theme name** (display name, e.g. "My Client Site 2025")
- **Text domain / directory name** (slug, e.g. "my-client-2025")
- **PHP namespace** (PascalCase, e.g. "MyClient")
- **Author name** (e.g. "dphn@wpmake")
- **Author URI** (e.g. "https://wpmake.jp/")

Derive from the above:
- `$THEME_SLUG` = text domain (e.g. `my-client-2025`)
- `$NAMESPACE` = PHP namespace (e.g. `MyClient`)
- `$THEME_NAME` = display name

## Step 2: Copy Template Packages

**PHP / Composer / templates:** Copy from **`wordpress-theme-template`** SKILL first.

```bash
rsync -a --exclude='SKILL.md' --exclude='README.md' \
  ~/skills/wordpress-theme-template/ $THEME_SLUG/
```

**Frontend assets (JS / SCSS / build config):** Merge from **`frontend-dev-package`** SKILL.

```bash
rsync -a --exclude='SKILL.md' --exclude='README.md' --exclude='index.html' \
  ~/skills/frontend-dev-package/ $THEME_SLUG/
cd $THEME_SLUG
cp vite.config.wordpress.js vite.config.js
# package.json "dev": "vite build --watch"
```

Replace placeholders (`ThemeName` → `$NAMESPACE`, `my-theme` → `$THEME_SLUG`, `My Theme 2025` → `$THEME_NAME`).

Steps 3–7 below describe individual files for reference when customizing. The template packages already include the base structure. Directories marked `(empty)` should be created but left empty (create a `.gitkeep` if needed). Directories marked `(create on use)` should NOT be created at scaffold time.

```
$THEME_SLUG/
├── assets/
│   ├── css/                    (empty - Vite output)
│   ├── dev/
│   │   ├── main.js
│   │   └── modules/            (empty)
│   ├── fonts/                  (empty)
│   ├── images/                 (empty)
│   ├── js/                     (empty - Vite output)
│   └── scss/
│       ├── style.scss
│       ├── foundation/
│       │   ├── _index.scss
│       │   ├── _base.scss
│       │   ├── _colors.scss
│       │   ├── _fonts.scss
│       │   ├── _functions.scss
│       │   ├── _mixin.scss
│       │   ├── _reset.scss
│       │   └── _vars.scss
│       ├── component/
│       │   └── _index.scss
│       ├── layout/
│       │   ├── _index.scss
│       │   ├── _header.scss
│       │   ├── _footer.scss
│       │   └── _main.scss
│       ├── project/
│       │   └── _index.scss
│       └── utility/
│           └── _index.scss
├── inc/
│   ├── Setup.php
│   ├── Assets/
│   │   └── Assets.php
│   ├── API/                    (empty)
│   ├── Fields/                 (empty)
│   ├── Forms/                  (empty)
│   ├── PostTypes/              (empty)
│   ├── Queries/                (empty)
│   └── Utils/                  (empty)
├── template_parts/             (empty)
├── archives/                   (create on use)
├── pages/                      (create on use)
├── singles/                    (create on use)
├── index.php
├── page.php
├── single.php
├── archive.php
├── header.php
├── footer.php
├── 404.php
├── front-page.php              (create on use)
├── functions.php
├── style.css
├── composer.json
├── package.json
├── vite.config.js
├── .prettierrc
├── .stylelintrc.js
├── .gitignore
├── .vscode/
│   └── settings.json
└── screenshot.png              (create on use)
```

## Step 3: Config Files

For detailed file contents, see [reference.md](reference.md).

Key points:
- `composer.json`: PSR-4 maps `$NAMESPACE\\` to `inc/`
- `package.json`, `vite.config.js`, `.stylelintrc.js`, `.prettierrc`, `.vscode/settings.json`: Copy from [frontend-dev-package](../frontend-dev-package/SKILL.md); use `vite.config.wordpress.js` as `vite.config.js`
- `vite.config.js`: Input from `assets/dev/main.js`, output JS to `assets/js/bundle.js`, CSS to `assets/css/style.css`

### Required devDependencies (always include)

```json
{
  "@awmottaz/prettier-plugin-void-html": "^1.9.0",
  "sass": "^1.96.0",
  "stylelint": "^17.2.0",
  "stylelint-config-recess-order": "^7.6.1",
  "stylelint-config-standard-scss": "^17.0.0",
  "vite": "^7.2.7"
}
```

## Step 4: PHP Core Files

### functions.php

Loads Composer autoloader, instantiates `$NAMESPACE\Setup`:

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
    if (defined('WP_DEBUG') && WP_DEBUG) {
      wp_die('$NAMESPACE Setup Error: ' . $e->getMessage());
    }
  }
} else {
  error_log('$NAMESPACE: vendor/autoload.php not found. Run "composer install"');
}
```

### inc/Setup.php

Auto-discovers and initializes all classes in `inc/` that have `__invoke()`:

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
        if (!is_dir($dir)) return $classes;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                if (in_array($item, $this->excluded_dirs, true)) continue;
                $classes = array_merge($classes, $this->discoverClasses($path, $namespace . '\\' . $item));
            } elseif (is_file($path) && pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                if (in_array($item, $this->excluded_files, true)) continue;
                $classes[] = $namespace . '\\' . pathinfo($item, PATHINFO_FILENAME);
            }
        }
        return $classes;
    }

    private function initializeClass(string $class_name): void
    {
        if (!class_exists($class_name)) return;
        try {
            $instance = new $class_name();
            if (method_exists($instance, '__invoke')) $instance();
        } catch (\Exception $e) {
            error_log(sprintf('Failed to initialize %s: %s', $class_name, $e->getMessage()));
        }
    }
}
```

### inc/Assets/Assets.php

Enqueues the Vite-built CSS:

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

## Step 5: Template Files

All templates use placeholder HTML. See [reference.md](reference.md) for exact contents.

Key patterns:
- `page.php`: Dynamically loads `pages/page-{slug}.php` if it exists, otherwise renders default layout
- `single.php`: Dynamically loads `singles/single-{post_type}.php` if it exists
- `archive.php`: Dynamically loads `archives/archive-{name}.php` if it exists
- `header.php`: Opens `<html>`, `<head>`, `<body>`, `<header>`, `<main>`
- `footer.php`: Closes `</main>`, renders `<footer>`, closes `</body>`, `</html>`

## Step 6: SCSS Foundation

- `style.scss`: Entry point using `@use` for each layer
- Foundation layer: `_base.scss`, `_colors.scss`, `_fonts.scss`, `_functions.scss`, `_mixin.scss`, `_reset.scss`, `_vars.scss`
- Other layers start with only `_index.scss`

## Step 7: JS Entry Point

`assets/dev/main.js`:

```javascript
// SCSS import
import '../scss/style.scss';

// Module imports (add as needed)
// import ModuleName from './modules/module-name.js';
```

## Step 8: Install Dependencies

After generating all files, run:

```bash
composer install
npm install
```

Then verify the build works:

```bash
npm run build
```

## Adding New Functionality

### New PHP class

1. Create file in appropriate `inc/` subdirectory (e.g. `inc/PostTypes/MyPostType.php`)
2. Use namespace matching directory: `namespace $NAMESPACE\PostTypes;`
3. Implement `__invoke()` method for auto-initialization
4. Run `composer dump-autoload`

### New page template

1. Create `pages/page-{slug}.php` — auto-loaded by `page.php`

### New archive template

1. Create `archives/archive-{post_type}.php` — auto-loaded by `archive.php`

### New single template

1. Create `singles/single-{post_type}.php` — auto-loaded by `single.php`

### New SCSS module

1. Create `_name.scss` in appropriate layer directory
2. Add `@use 'name'` to that layer's `_index.scss`

### New JS module

1. Create `assets/dev/modules/<feature-name>/` following View / Model / Control separation with class-based files (see [js-workflow](../../js-workflow/SKILL.md))
2. Import and initialize in `assets/dev/main.js`

## inc/ Directory Conventions

| Directory | Purpose | Example |
|-----------|---------|---------|
| `API/` | REST API endpoints | `NewsPostApi.php` |
| `Assets/` | Script/style enqueuing | `Assets.php` |
| `Fields/` | ACF fields, options pages | `ACFRelative.php` |
| `Forms/` | Form handling (CF7, etc.) | `Contact7.php` |
| `PostTypes/` | CPT/taxonomy registration | `PostTypeTax.php` |
| `Queries/` | Query modifications, redirects | `PreGetPosts.php` |
| `Utils/` | Utility/helper classes | `Pagination.php` |

## Additional Resources

- For PHP / Composer / template scaffold, see [wordpress-theme-template](../wordpress-theme-template/SKILL.md)
- For frontend scaffold (assets/dev, assets/scss, build config), see [frontend-dev-package](../frontend-dev-package/SKILL.md)
- For complete file templates (customization reference), see [reference.md](reference.md)
