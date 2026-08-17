---
name: wordpress-theme-template
description: Copy WordPress theme PHP scaffold (Composer PSR-4, template routing, CPT, REST API, pagination). Use when scaffolding PHP-only theme files, or when the frontend already exists. For a complete PHP + Vite theme, use wordpress-theme-starter.
---

# WordPress Theme Template

Composer PSR-4 + テンプレートルーティング + サンプル実装の WordPress テーマ PHP テンプレート。

**Template path:** `~/skills/wordpress-theme-template/`

PHP のみ。WordPress テーマを PHP + フロント込みで立ち上げる場合は **`wordpress-theme-starter`** を使う。

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

### 2. Frontend assets

新規 WP テーマは **`wordpress-theme-starter`**（PHP + Vite、pnpm）を優先する。PHP 既存テーマへフロントだけ足す場合は `frontend-dev-package` をマージする。

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

## NOT Included

CSS / JS ビルドは含まない。完全版は **`wordpress-theme-starter`**。

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

- [wordpress-theme-starter](../wordpress-theme-starter/SKILL.md) — PHP + Vite/SCSS/JS 完全版（pnpm、IIFE）
- [frontend-dev-package](../frontend-dev-package/SKILL.md) — 静的 HTML 向け Vite + SCSS + JS
- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — Full WP theme scaffold workflow
- [js-workflow](../js-workflow/SKILL.md) — JS module architecture
- [scss-workflow](../scss-workflow/SKILL.md) — SCSS / FLOCSS workflow

## Validation

After copy, placeholder replacement, and `composer install`:

1. `composer install` — no errors
2. Full theme (starter): `pnpm run build` — CSS/JS output to `assets/css/style.css`, `assets/js/bundle.js`
3. Activate theme in WordPress — no PHP fatal errors
4. Visit `/sample/` archive — sample CPT archive renders
5. REST: `GET /wp-json/my-theme/v1/sample-posts` — returns JSON
