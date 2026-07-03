---
name: wordpress-theme-template
description: Copy WordPress theme PHP scaffold (Composer PSR-4, template routing, CPT, REST API, pagination) to start a new WP theme. Use with frontend-dev-package for CSS/JS build tooling.
---

# WordPress Theme Template

Composer PSR-4 + テンプレートルーティング + サンプル実装の WordPress テーマ PHP テンプレート。

**Template path:** `~/skills/wordpress-theme-template/`

CSS / JS ビルド設定（Vite, SCSS, Stylelint, Prettier）は **`frontend-dev-package`** SKILL を併用する。

## When to Use

- 新規 WordPress テーマの PHP 構成を立ち上げる
- Composer オートロード + `inc/` クラス自動初期化パターンが必要なとき
- テンプレートルーティング（`pages/`, `singles/`, `archives/`）の雛形が必要なとき
- REST API / CPT / ページネーションの実装サンプルが必要なとき

## Copy to New Project

`SKILL.md` と `README.md` はコピー先に含めない。

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  ~/skills/wordpress-theme-template/ \
  /path/to/wp-content/themes/my-theme-2025/
```

### 1. プレースホルダー置換

| Placeholder | Example | Files |
|-------------|---------|-------|
| `ThemeName` | `MyClient` | `inc/**/*.php`, `functions.php`, `header.php`, templates |
| `my-theme` | `my-client-2025` | `composer.json`, `style.css`, `Assets.php`, API namespace |
| `My Theme 2025` | display name | `style.css` |

### 2. Frontend assets（別スキル）

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  --exclude='index.html' \
  ~/skills/frontend-dev-package/ \
  /path/to/wp-content/themes/my-theme-2025/

cd /path/to/wp-content/themes/my-theme-2025
cp vite.config.wordpress.js vite.config.js
# package.json "dev": "vite build --watch"
npm install
npm run build
```

### 3. Composer

```bash
composer install
composer dump-autoload
```

## What's Included

| Category | Contents |
|----------|----------|
| Core | `functions.php`, `composer.json`, `style.css`, `index.php` |
| Templates | `header.php`, `footer.php`, `page.php`, `single.php`, `archive.php`, `404.php` |
| Routing | `pages/page-{slug}.php`, `singles/single-{post_type}.php`, `archives/archive-{name}.php` |
| PHP classes | `Setup.php`, `Assets.php`, `SamplePostType.php`, `SamplePostApi.php`, `PreGetPosts.php`, `Pagination.php`, `Viewport.php` |
| Parts | `template_parts/breadcrumb.php` |

## NOT Included (use frontend-dev-package)

- `package.json`, `vite.config.js`, `.stylelintrc.js`, `.prettierrc`
- `assets/dev/`, `assets/scss/`

## Template Routing

| Root template | Loads when exists |
|---------------|-------------------|
| `page.php` | `pages/page-{post_name}.php` |
| `single.php` | `singles/single-{post_type}.php` |
| `archive.php` | `archives/archive-{queried_object->name}.php` |

## Adding New Functionality

### New PHP class

1. Create `inc/PostTypes/MyCpt.php` with `namespace ThemeName\PostTypes;`
2. Implement `__invoke()` — auto-initialized by `Setup.php`
3. Run `composer dump-autoload`

### New page / single / archive template

- `pages/page-contact.php` — loaded by `page.php` when slug is `contact`
- `singles/single-news.php` — loaded by `single.php` for post type `news`
- `archives/archive-news.php` — loaded by `archive.php` for archive `news`

## Related Skills

- [frontend-dev-package](../frontend-dev-package/SKILL.md) — Vite + SCSS + JS build scaffold
- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — Full WP theme scaffold workflow
- [js-workflow](../js-workflow/SKILL.md) — JS module architecture
- [scss-workflow](../scss-workflow/SKILL.md) — SCSS / FLOCSS workflow

## Validation

After copy, placeholder replacement, `composer install`, and frontend-dev-package merge:

1. `composer install` — no errors
2. `npm run build` — CSS/JS output to `assets/css/style.css`, `assets/js/bundle.js`
3. Activate theme in WordPress — no PHP fatal errors
4. Visit `/sample/` archive — sample CPT archive renders
5. REST: `GET /wp-json/my-theme/v1/sample-posts` — returns JSON
