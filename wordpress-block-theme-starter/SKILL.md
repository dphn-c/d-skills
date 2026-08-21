---
name: wordpress-block-theme-starter
description: Use when starting a WordPress block theme, scaffolding an FSE theme structure, or copying this starter with templates/parts/patterns and PHP dynamic render blocks.
---

# WordPress Block Theme Starter

WordPress のブロックテーマ向けスターターです。FSE の標準ディレクトリと、表示をファイルで管理する PHP ダイナミックブロックの配置方針を含みます。

**テンプレートのパス:** `~/.cursor/skills/wordpress-block-theme-starter/`

クラシックテーマ（`header.php` など）を作成する場合は `wordpress-theme-starter` を使用します。

配置方針、`parts/` と `patterns/` の同期の違い、テンプレートパーツの表示名設定、テンプレートパーツ差し替え用パターンは [README.md](./README.md) を参照してください。

## 実装先を決める前の必須確認

テンプレートや表示機能の実装を始める前に、README の「配置の判断フロー」に沿って、必ずユーザーへ次の内容を確認してください。ユーザーの回答を得る前に、実装先を決めたりコードを書き始めたりしてはいけません。

1. Site Editor で編集する表示ですか？
2. Site Editor で編集する場合、HTML の骨格はコードで管理し、属性や InnerBlocks だけを編集可能にしますか？
3. 内容も Site Editor で管理する場合、全ページに同期する共通枠ですか？

回答と実装先の対応は次のとおりです。

- Site Editor で編集しない表示 → `theme-blocks/render/`
- 骨格をコードで管理し、属性だけを編集する自作ブロック → `src/blocks/` から `theme-blocks/bundled/`
- Site Editor で管理し、全ページに同期する共通枠 → `parts/`
- Site Editor で管理し、同期する必要のないひな型やデザイン候補 → `patterns/`

## 使用する場面

- 新規 **ブロックテーマ**（`templates/*.html` と `theme.json`）を立ち上げる
- Site Editor で編集しない表示を `theme-blocks/render/` で管理する
- Site Editor で編集する属性を持つ自作ブロックを `src/blocks/` から `theme-blocks/bundled/` にビルドする
- FSE の初期構成（`templates/`、`parts/`、`patterns/`）と PHP ダイナミックブロックを併用する

## 新しいプロジェクトへのコピー

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

### 1. プレースホルダーの置換

| プレースホルダー | 例 | 対象ファイル |
| ------------- | --------- | ------- |
| `ThemeName` | `MyClient` | `inc/**/*.php`、`functions.php` |
| `my-theme` | `my-client-2025` | `composer.json`、`style.css`、`package.json`、`theme.json`、`block.json`、`templates/`、`patterns/` |
| `My Theme` | 表示名 | `style.css` |

```bash
cd /path/to/wp-content/themes/my-theme-2025
find . -type f \( -name '*.php' -o -name '*.json' -o -name '*.css' -o -name '*.html' -o -name '*.js' -o -name '*.mjs' -o -name '*.scss' \) \
  -not -path './vendor/*' -not -path './node_modules/*' \
  -exec sed -i '' -e 's/ThemeName/MyClient/g' -e 's/my-theme/my-client-2025/g' -e 's/My Theme/My Client 2025/g' {} +
```

### 2. 依存関係のインストールとビルド

```bash
composer install && composer dump-autoload
pnpm install
pnpm run build
```

## 含まれるもの

| 分類 | 内容 |
| ---------- | ---------- |
| 基本 | `functions.php`、`composer.json`、`style.css`、`theme.json` |
| テンプレート | `index`、`front-page`、`page`、`single`、`archive`、`404`、`company` |
| テンプレートパーツ | `header.html`、`footer.html`、`cta.html` |
| パターン | `patterns/sample/sample-hero.php` |
| ダイナミックブロック | `theme-blocks/render/sample-section/` と共有 `editor.js` |
| PHP | `Setup.php`、`ThemeSupports`、`Blocks`、`Assets` |
| フロントエンド | Vite IIFE と最小構成の FLOCSS |

## 関連するスキル

- [wordpress-theme-starter](../wordpress-theme-starter/SKILL.md) — クラシックテーマ
- [wordpress-theme-scaffold](../wordpress-theme-scaffold/SKILL.md) — コピー手順のワークフロー
- [scss-workflow](../scss-workflow/SKILL.md) / [js-workflow](../js-workflow/SKILL.md)

## 検証項目

1. `composer install` と `pnpm run build` が成功する
2. テーマを有効化しても fatal error が発生しない
3. フロントエンドで `sample-section` ブロックが表示される
4. `theme-blocks/render/` にブロックを追加すると自動登録される
5. `theme.json` に登録した `company` テンプレートが固定ページのテンプレート候補に表示される
