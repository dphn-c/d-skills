# frontend-dev-package

Vite + SCSS + JavaScript の開発テンプレート。プロジェクトのビルド設定・FLOCSS 構成・共通ユーティリティを、そのままコピーして使える形にまとめたものです。

**保存場所:** `~/skills/frontend-dev-package/`  
エージェント向け手順は同フォルダの `SKILL.md` を参照。

---

## 含まれるもの

| カテゴリ | 内容 |
|----------|------|
| **ビルド** | Vite, Sass, Stylelint (recess-order), Prettier |
| **JS** | `assets/dev/main.js` + `modules/` / `utils/` ディレクトリ |
| **SCSS** | FLOCSS 5 層 (`foundation` / `component` / `layout` / `project` / `utility`) |
| **共通スタイル** | `foundation/` 一式、`utility/_common.scss` |
| **エディタ** | `.vscode/settings.json` (Stylelint 保存時 fix) |

---

## 使い方

### 1. 新規プロジェクトへコピー

`SKILL.md` と `README.md` はコピー先に含めない。

```bash
rsync -a \
  --exclude='SKILL.md' \
  --exclude='README.md' \
  ~/skills/frontend-dev-package/ \
  /path/to/new-project/
cd /path/to/new-project
```

`package.json` の `name` をプロジェクト名に変更してください。

### 2. 依存関係のインストール

```bash
npm install
```

### 3. 開発・ビルド

**静的 HTML プロジェクト（デフォルト）**

```bash
npm run dev      # 開発サーバー (HMR)
npm run build    # built/ に出力
npm run preview  # built/ をローカル確認
```

デプロイ時は `built/` フォルダの中身をアップロードします。

**WordPress テーマ**

```bash
cp vite.config.wordpress.js vite.config.js
```

`package.json` の `dev` スクリプトを以下に変更:

```json
"dev": "vite build --watch"
```

```bash
npm run dev    # assets/css/style.css + assets/js/bundle.js を watch ビルド
npm run build  # 本番ビルド
```

PHP 側で `assets/css/style.css` と `assets/js/bundle.js` を enqueue してください。  
WordPress テーマの PHP スキャフォールドは `wordpress-theme-scaffold` SKILL を参照。

---

## ディレクトリ構成

```
frontend-dev-package/
├── assets/
│   ├── dev/
│   │   ├── main.js           # エントリーポイント
│   │   ├── modules/          # 機能モジュール (View / Model / Control)
│   │   └── utils/            # 共通ユーティリティ
│   ├── scss/
│   │   ├── style.scss
│   │   ├── foundation/       # 全プロジェクト共通（リセット・変数・mixin）
│   │   ├── component/        # c- プレフィックス
│   │   ├── layout/           # l- プレフィックス
│   │   ├── project/          # p- プレフィックス（ページ固有）
│   │   └── utility/          # u- プレフィックス
│   ├── images/
│   └── fonts/
├── index.html                # 静的 HTML 用（WP テーマでは不要）
├── vite.config.js            # 静的 HTML 用（built/ 出力）
├── vite.config.wordpress.js  # WordPress 用（assets/css, assets/js 出力）
├── package.json
├── .stylelintrc.js
├── .prettierrc
└── .vscode/settings.json
```

---

## カスタマイズの起点

| ファイル | 用途 |
|----------|------|
| `assets/scss/foundation/_colors.scss` | カラーパレット（CSS 変数 + SCSS 変数） |
| `assets/scss/foundation/_fonts.scss` | フォント・タイポグラフィスケール |
| `assets/scss/utility/_product.scss` | プロジェクト固有ユーティリティ |
| `assets/scss/project/_sample.scss` | サンプルページスタイル（削除・差し替え可） |

---

## JS モジュールの追加

`js-workflow` SKILL に従い、機能ごとに `assets/dev/modules/<feature-name>/` を作成します。

```javascript
// assets/dev/main.js
import FeatureName from './modules/feature-name/index.js';

new FeatureName('.js-feature-root');
```

---

## SCSS の追加

`scss-workflow` SKILL に従い、各レイヤーに `_*.scss` を追加し、対応する `_index.scss` に `@use` を追記します。

```bash
npm run lint:scss:fix   # Stylelint 自動修正
```

---

## 関連 SKILL

- `js-workflow` — JS モジュール構成 (View / Model / Control)
- `scss-workflow` — FLOCSS 構成・Stylelint・ビルドパイプライン
- `wordpress-theme-scaffold` — WordPress テーマ全体のスキャフォールド
- `css-coding-conventions` — SCSS コーディング規約
