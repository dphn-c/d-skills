# WordPress Theme Template

WordPress テーマ PHP テンプレート。

**Location:** `~/skills/wordpress-theme-template/`

## 含まれるもの

- **Composer PSR-4** オートロード（`inc/` 名前空間）
- **`Setup.php`** — `inc/` 配下クラスの自動検出・`__invoke()` 初期化
- **テンプレートルーティング** — `page.php` / `single.php` / `archive.php` からサブディレクトリへ委譲
- **サンプル実装**
  - `inc/Assets/Assets.php` — CSS / JS 読み込み
  - `inc/PostTypes/SamplePostType.php` — CPT + タクソノミー登録
  - `inc/API/SamplePostApi.php` — REST API エンドポイント
  - `inc/Utils/Pagination.php` — PHP / JS 両対応ページネーション
  - `inc/Queries/PreGetPosts.php` — メインクエリ改変サンプル
  - `inc/Utils/Viewport.php` — レスポンシブ viewport 初期値
  - `template_parts/breadcrumb.php` — 汎用パンくず

## 含まれないもの（別スキル参照）

CSS / JS コンパイラ設定は **[frontend-dev-package](../frontend-dev-package/)** からコピーしてください。

- Vite, Sass, Stylelint, Prettier
- `assets/dev/`, `assets/scss/`

## 使い方

1. このフォルダーを新テーマディレクトリへコピー
2. プレースホルダー `ThemeName` / `my-theme` / `My Theme 2025` を置換
3. `frontend-dev-package` を同じディレクトリへマージ
4. `composer install` && `npm install` && `npm run build`
5. WordPress でテーマを有効化

詳細は [SKILL.md](./SKILL.md) を参照。

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
├── assets/css/          ← Vite 出力先
├── assets/js/           ← Vite 出力先
├── functions.php
├── style.css
└── index.php, page.php, single.php, archive.php, ...
```
