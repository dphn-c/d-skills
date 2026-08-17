# WordPress Theme Starter

PHP（Composer PSR-4）とフロント（Vite + SCSS + JS）をまとめた WordPress テーマのスタートアップテンプレート。

**Location:** `~/skills/wordpress-theme-starter/`  
エージェント向け手順は [SKILL.md](./SKILL.md)。構成の論点は [DISCUSSION.md](./DISCUSSION.md)。

## 含まれるもの

- **Composer PSR-4** オートロード（`inc/` 名前空間）
- **`Setup.php`** — `inc/` 配下クラスの自動検出・`__invoke()` 初期化
- **テンプレートルーティング** — `page.php` / `single.php` / `archive.php` からサブディレクトリへ委譲
- **Vite + pnpm** — `assets/css/style.css` と `assets/js/bundle.js`（IIFE）へビルド
- **FLOCSS SCSS** — `foundation` / `component` / `layout` / `project` / `utility`
- **サンプル実装**
  - `inc/Assets/Assets.php` — CSS / JS 読み込み
  - `inc/PostTypes/SamplePostType.php` — CPT + タクソノミー登録
  - `inc/API/SamplePostApi.php` — REST API エンドポイント
  - `inc/Utils/Pagination.php` — PHP / JS 両対応ページネーション
  - `inc/Queries/PreGetPosts.php` — メインクエリ改変サンプル
  - `inc/Utils/Viewport.php` — レスポンシブ viewport 初期値
  - `template_parts/breadcrumb.php` — 汎用パンくず

## 使い方

1. このフォルダーを新テーマディレクトリへコピー（`SKILL.md` / `README.md` / `DISCUSSION.md` は除く）
2. プレースホルダー `ThemeName` / `my-theme` / `My Theme 2025` を置換
3. `composer install` && `pnpm install` && `pnpm run build`
4. WordPress でテーマを有効化

開発時:

```bash
pnpm run dev          # vite build --watch
pnpm run build        # 本番ビルド
pnpm run lint:scss:fix
```

パッケージマネージャは **pnpm**。`npm install` は使わない。

## ディレクトリ構成

```
my-theme-2025/
├── inc/
│   ├── Setup.php
│   ├── Assets/Assets.php
│   ├── API/SamplePostApi.php
│   ├── PostTypes/SamplePostType.php
│   ├── Queries/PreGetPosts.php
│   ├── Utils/Pagination.php, Viewport.php
│   ├── Fields/          (空 — ACF 等)
│   └── Forms/           (空 — CF7 等)
├── template_parts/
├── pages/               page-{slug}.php
├── singles/             single-{post_type}.php
├── archives/            archive-{name}.php
├── assets/
│   ├── dev/             JS ソース（main.js, modules/, utils/）
│   ├── scss/            FLOCSS
│   ├── css/             ← Vite 出力
│   ├── js/              ← Vite 出力（bundle.js = IIFE）
│   ├── images/
│   └── fonts/
├── functions.php
├── style.css
├── composer.json
├── package.json
├── vite.config.js
└── index.php, page.php, single.php, archive.php, ...
```

## 関連

- PHP のみ: [wordpress-theme-template](../wordpress-theme-template/)
- 静的 HTML フロント: [frontend-dev-package](../frontend-dev-package/)
- 立ち上げ手順: [wordpress-theme-scaffold](../wordpress-theme-scaffold/)
