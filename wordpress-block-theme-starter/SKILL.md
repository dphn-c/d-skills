---
name: wordpress-block-theme-starter
description: Copy a WordPress block theme starter (templates/parts/patterns + theme-blocks/render + Vite SCSS/JS). Use when starting a new block theme, scaffolding FSE theme structure, or when the user wants plant-library style file placement (dynamic render blocks for file-owned content).
---

# WordPress Block Theme Starter

ブロックテーマ向けのチーム共通スターター。`plant-library` で固めた配置方針をそのまま雛形にした。

**Template path:** `~/.cursor/skills/wordpress-block-theme-starter/`

クラシックテーマ（`header.php` 等）は `wordpress-theme-starter`。

配置方針・parts/patterns の同期の違い・テンプレートパーツ差し替え用パターンは [README.md](./README.md)。

## When to Use

- 新規 **ブロックテーマ**（`templates/*.html` + `theme.json`）を立ち上げる
- 「後からファイルだけで変えたい表示 → `theme-blocks/render/`」の棲み分けで始めたい
- FSE の初期注入（templates / parts / patterns）と PHP ダイナミックブロックを併用する

## Copy to New Project

`SKILL.md`、`README.md` はコピー先に含めない。

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  --exclude='node_modules' \
  --exclude='vendor' \
  ~/.cursor/skills/wordpress-block-theme-starter/ \
  /path/to/wp-content/themes/my-theme-2025/
```

### 1. プレースホルダー置換

| Placeholder | Example | Files |
|-------------|---------|-------|
| `ThemeName` | `MyClient` | `inc/**/*.php`, `functions.php` |
| `my-theme` | `my-client-2025` | `composer.json`, `style.css`, `package.json`, `theme.json`, block.json, templates, patterns |
| `My Theme` | display name | `style.css` |

```bash
cd /path/to/wp-content/themes/my-theme-2025
find . -type f \( -name '*.php' -o -name '*.json' -o -name '*.css' -o -name '*.html' -o -name '*.js' -o -name '*.mjs' -o -name '*.scss' \) \
  -not -path './vendor/*' -not -path './node_modules/*' \
  -exec sed -i '' -e 's/ThemeName/MyClient/g' -e 's/my-theme/my-client-2025/g' -e 's/My Theme/My Client 2025/g' {} +
```

### 2. 依存関係

```bash
composer install && composer dump-autoload
pnpm install
pnpm run build
```

## What's Included

| Category | Contents |
|----------|----------|
| Core | `functions.php`, `composer.json`, `style.css`, `theme.json` |
| Templates | `index`, `front-page`, `page`, `single`, `archive`, `404` |
| Parts | `header.html`, `footer.html`（薄い参照） |
| Patterns | `patterns/sample/sample-hero.php` |
| Render block | `theme-blocks/render/sample-section/` + 共有 `editor.js` |
| PHP | `Setup.php`, `ThemeSupports`, `Blocks`, `Assets` |
| Frontend | Vite IIFE + 最小 FLOCSS |

## Related Skills

- [wordpress-theme-starter](../wordpress-theme-starter/SKILL.md) — クラシックテーマ
- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — コピー手順のワークフロー
- [scss-workflow](../scss-workflow/SKILL.md) / [js-workflow](../js-workflow/SKILL.md)

## Validation

1. `composer install` / `pnpm run build` が通る
2. テーマ有効化で fatal がない
3. フロントで sample-section ブロックが描画される
4. `theme-blocks/render/` にフォルダを足すと自動登録される
