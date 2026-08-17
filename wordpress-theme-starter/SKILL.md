---
name: wordpress-theme-starter
description: Copy a complete WordPress theme starter (Composer PHP scaffold plus Vite SCSS/JS, pnpm, IIFE bundle). Use when starting a new WordPress theme, scaffolding a team WP theme, or when PHP templates and frontend build tooling should live in one folder.
---

# WordPress Theme Starter

PHP（Composer PSR-4）とフロント（Vite + SCSS + JS）をまとめた、チーム共通の WordPress テーマ立ち上げ用テンプレート。

**Template path:** `~/skills/wordpress-theme-starter/`

PHP だけの雛形は `wordpress-theme-template`、静的 HTML 向けフロントは `frontend-dev-package`。

## When to Use

- 新規 WordPress テーマを **PHP + フロント込み** で立ち上げる
- チーム共通のスタートアップテンプレとしてコピーする
- Composer オートロード + `pages/` / `singles/` / `archives/` ルーティング + Vite ビルドが必要なとき

## Copy to New Project

`SKILL.md`、`README.md`、`DISCUSSION.md` はコピー先に含めない。

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  --exclude='DISCUSSION.md' \
  --exclude='node_modules' \
  ~/skills/wordpress-theme-starter/ \
  /path/to/wp-content/themes/my-theme-2025/
```

### 1. プレースホルダー置換

| Placeholder | Example | Files |
|-------------|---------|-------|
| `ThemeName` | `MyClient` | `inc/**/*.php`, `functions.php`, `header.php`, templates |
| `my-theme` | `my-client-2025` | `composer.json`, `style.css`, `package.json`, `Assets.php`, API namespace |
| `My Theme 2025` | display name | `style.css` |

### 2. 依存関係

パッケージマネージャは **pnpm**（`package.json` の `packageManager` を参照）。

```bash
cd /path/to/wp-content/themes/my-theme-2025
composer install
composer dump-autoload
pnpm install
pnpm run build
```

開発時は `pnpm run dev`（`vite build --watch`）。出力は `assets/css/style.css` と `assets/js/bundle.js`（IIFE）。

## What's Included

| Category | Contents |
|----------|----------|
| Core | `functions.php`, `composer.json`, `style.css`, `index.php` |
| Templates | `header.php`, `footer.php`, `page.php`, `single.php`, `archive.php`, `404.php` |
| Routing | `pages/page-{slug}.php`, `singles/single-{post_type}.php`, `archives/archive-{name}.php` |
| PHP classes | `Setup.php`, `Assets.php`, `SamplePostType.php`, `SamplePostApi.php`, `PreGetPosts.php`, `Pagination.php`, `Viewport.php` |
| Parts | `template_parts/breadcrumb.php` |
| Build | Vite (production JS: **IIFE**), Sass, Stylelint, Prettier, pnpm |
| JS | `assets/dev/main.js`, `modules/`, `utils/` |
| SCSS | FLOCSS 5 layers + `foundation/` + `utility/_common.scss` |

## Template Routing

| Root template | Loads when exists |
|---------------|-------------------|
| `page.php` | `pages/page-{post_name}.php` |
| `single.php` | `singles/single-{post_type}.php` |
| `archive.php` | `archives/archive-{queried_object->name}.php` |

## Production JS (IIFE)

`vite.config.js` の `rollupOptions.output.format` は `'iife'`。`wp_enqueue_script` は classic script のまま（`type="module"` にしない）。

## Adding New Functionality

### New PHP class

1. Create `inc/PostTypes/MyCpt.php` with `namespace ThemeName\PostTypes;`
2. Implement `__invoke()` — auto-initialized by `Setup.php`
3. Run `composer dump-autoload`

### New page / single / archive template

- `pages/page-contact.php` — loaded by `page.php` when slug is `contact`
- `singles/single-news.php` — loaded by `single.php` for post type `news`
- `archives/archive-news.php` — loaded by `archive.php` for archive `news`

### New SCSS / JS module

- SCSS: レイヤーに `_name.scss` を追加し、その `_index.scss` に `@use` を追記（`scss-workflow`）
- JS: `assets/dev/modules/<feature-name>/` を作り `main.js` から import（`js-workflow`）

## Related Skills

- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — 新規テーマ立ち上げワークフロー（このフォルダをコピー）
- [wordpress-theme-template](../wordpress-theme-template/SKILL.md) — PHP のみの雛形
- [frontend-dev-package](../frontend-dev-package/SKILL.md) — 静的 HTML 向けフロント雛形
- [js-workflow](../js-workflow/SKILL.md) — JS module architecture
- [scss-workflow](../scss-workflow/SKILL.md) — SCSS / FLOCSS workflow

構成の論点（`template_parts` の階層、大規模時の CSS/JS 分割など）は [DISCUSSION.md](./DISCUSSION.md)。

## Validation

After copy, placeholder replacement, `composer install`, and `pnpm install`:

1. `composer install` — no errors
2. `pnpm run build` — CSS/JS output to `assets/css/style.css`, `assets/js/bundle.js` (IIFE)
3. `pnpm run lint:scss` — passes
4. Activate theme in WordPress — no PHP fatal errors
5. Visit `/sample/` archive — sample CPT archive renders
6. REST: `GET /wp-json/my-theme/v1/sample-posts` — returns JSON
