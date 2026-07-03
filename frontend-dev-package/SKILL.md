---
name: frontend-dev-package
description: Copy Vite + SCSS + JavaScript dev scaffold to start a new static HTML or WordPress frontend project. Use when scaffolding a new frontend, setting up assets/dev and assets/scss, or copying build tooling (Vite, Stylelint, Prettier, FLOCSS) into a project.
---

# Frontend Dev Package

Vite + SCSS + JavaScript の開発テンプレート。新規プロジェクトへコピーして使う。

**Template path:** `~/skills/frontend-dev-package/`

## When to Use

- 新規の静的 HTML / CSS / JS プロジェクトを立ち上げる
- WordPress テーマに `assets/dev/` + `assets/scss/` + ビルド設定を追加する
- `js-workflow` / `scss-workflow` に沿った初期構成が必要なとき

## Copy to New Project

`SKILL.md` と `README.md` はコピー先に含めない。

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  ~/skills/frontend-dev-package/ \
  /path/to/new-project/
```

その後:

```bash
cd /path/to/new-project
# package.json の name をプロジェクト名に変更
npm install
npm run build
```

## Project Type Setup

### Static HTML (default)

- `vite.config.js` — そのまま使用（出力: `built/`）
- `index.html` — 含まれる
- `npm run dev` / `npm run build` / `npm run preview`

### WordPress theme

```bash
cp vite.config.wordpress.js vite.config.js
```

`package.json` の `dev` を `"vite build --watch"` に変更。`index.html` は不要（削除可）。

PHP・Composer 構成は **`wordpress-theme-template`** SKILL を併用する（全体ワークフローは `wordpress-theme-scaffold` も参照）。

## What's Included

| Category | Contents |
|----------|----------|
| Build | Vite, Sass, Stylelint (recess-order), Prettier |
| JS | `assets/dev/main.js`, `modules/`, `utils/` |
| SCSS | FLOCSS 5 layers + `foundation/` + `utility/_common.scss` |
| Config | `.stylelintrc.js`, `.prettierrc`, `.vscode/settings.json`, `.gitignore` |

## Customization Entry Points

| File | Purpose |
|------|---------|
| `assets/scss/foundation/_colors.scss` | Color palette |
| `assets/scss/foundation/_fonts.scss` | Typography |
| `assets/scss/utility/_product.scss` | Project-specific utilities |
| `assets/scss/project/_sample.scss` | Sample page styles (replace/remove) |

## Related Skills

- [js-workflow](../js-workflow/SKILL.md) — JS module architecture (View / Model / Control)
- [scss-workflow](../scss-workflow/SKILL.md) — FLOCSS, Stylelint, build pipeline
- [wordpress-theme-template](../wordpress-theme-template/SKILL.md) — WP theme PHP scaffold (Composer, templates, samples)
- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — Full WP theme scaffold workflow
- [css-coding-conventions](../css-coding-conventions/SKILL.md) — SCSS coding rules

## Validation

After copy and `npm install`:

1. `npm run build` — no errors
2. `npm run lint:scss` — passes
3. Static: `npm run preview` — page loads with styles
