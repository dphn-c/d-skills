# 構成についての議論メモ

チーム共通スタートアップにする前提で、現状のフォルダ設計で気になる点をまとめたもの。  
**このテンプレのコードは現行どおり**（フラットな `template_parts/` / `pages/` / `singles/` / `archives/`、CSS/JS は各1本）。規約やルーティングの変更は、ここで方針が決まってから入れる。

---

## 1. `template_parts` がコンポーネント増加でごちゃつく

### 現状

`template_parts/` 直下に PHP パーシャルを置く想定。サンプルは `breadcrumb.php` のみ。呼び出しは `get_template_part('template_parts/breadcrumb')`。

### 懸念

コンポーネントが増えると直下がファイルだらけになる。フォルダ分けは有効だが、階層を深くしすぎると「どこにあるか分からない」「パスが人によってバラバラ」になる。

### 議論したいこと

- **フォルダ分けを公式に許可するか。** 例: `template_parts/nav/breadcrumb.php`、`template_parts/cards/news.php`
- **階層の上限。** 提案: **最大2階層**（`template_parts/{group}/{name}.php`）。3階層目（`template_parts/a/b/c.php`）は禁止
- **group 名のルール。** 機能（`nav`, `cards`, `forms`）か、画面（`header`, `archive`）か。混ぜると探しにくい
- **命名。** ファイル名にプレフィックスを付けるか（`c-card.php` と SCSS の `c-` を揃えるか）、PHP は役割名だけにするか

WordPress の `get_template_part('template_parts/nav/breadcrumb')` はネストパスをそのまま扱えるので、**ルータ改造なしでフォルダ分けは可能**。決めるべきは「やってよいか」と「深さの上限」だけ。

---

## 2. `pages` / `singles` / `archives` も大規模でごちゃつく

### 現状

種類ごとに1ディレクトリ、ファイルはフラット。

| ルート | 探すファイル | キー |
|--------|----------------|------|
| `page.php` | `pages/page-{post_name}.php` | ページスラッグ |
| `single.php` | `singles/single-{post_type}.php` | 投稿タイプ |
| `archive.php` | `archives/archive-{name}.php` | 投稿タイプ / タクソノミー名 |

`file_exists(dirname(__FILE__) . '/pages/page-' . $slug . '.php')` のように **直下のファイルだけ** を見る。サブフォルダに置いても現状は読まれない。

### 懸念

固定ページが数十〜百を超えると `pages/` 直下が埋まって、関連ページ（会社情報配下、会員配下など）をまとめられない。singles / archives は投稿タイプ数に比例するので、中規模まではフラットで足りることが多い。

### 議論したいこと

- **中でのフォルダ分けを許可するか。** 例: `pages/company/page-about.php`
- **許可する場合、`page.php` 等の改造が必要。** 候補:
  1. 直下を先に見て、なければ `pages/* /page-{slug}.php` を1階層だけ探す
  2. スラッグとフォルダ名を一致させる（`pages/{slug}/page-{slug}.php`）
  3. 明示マップ（配列や設定ファイルでパスを指定）— 規模が大きいときだけ
- **階層の上限。** 1 と同じく **最大2階層** に揃えると運用が楽
- **WordPress 標準の `page-{slug}.php` をテーマルートに置く慣習と、この独自ルーティングのどちらを正とするか**（新規メンバー向けの説明コスト）

中規模までは現状維持でよい、という前提を残しつつ、「大規模になったらルータを拡張する」タイミングの目安（例: 同一ディレクトリのファイル数が N を超えたら）も決めたい。

---

## 3. 大規模サイトで CSS / JS 分割にこの構造が耐えるか

### 現状

- エントリは `assets/dev/main.js` のみ。SCSS はそこから `style.scss` を import
- 出力は **`assets/css/style.css` 1本 + `assets/js/bundle.js` 1本（IIFE）**
- `Assets.php` は全ページでこの2つを enqueue
- SCSS は FLOCSS でファイル分割済みだが、**ビルド結果は1 CSS**。JS も機能モジュールは `modules/` に分けられるが、**配信は1バンドル**

中規模まではこのままが扱いやすい（依存が単純、キャッシュも1枚、enqueue も単純）。

### 懸念

ページ固有の重い UI（チャート、スライダー、会員向けフォームなど）が増えると、全ページが同じ CSS/JS を落とす。FLOCSS の `project/` が増えても、出力が1本なら転送量は減らない。

### 議論したいこと

この構造自体は「分割の置き場」はある。足りないのは **複数エントリと条件付き enqueue の約束**。

| 層 | 今できること | 大規模で足すこと |
|----|----------------|------------------|
| SCSS | `project/_news.scss` などファイル分割 | Vite の複数 input でページ用 CSS を別出力する |
| JS | `assets/dev/modules/<feature>/` | `news.js` など第2エントリを IIFE で出し、該当テンプレだけ enqueue |
| PHP | `Assets.php` が全ページ共通 | `is_page()` / `is_singular()` で追加ハンドルを enqueue |

方針の候補:

- **A. デフォルトは1バンドルのまま。** 分割は「このページの JS が X kb を超えたら」など閾値付きの例外
- **B. 最初から `main` + `pages/*` の複数エントリをテンプレに含める。** 初期コストは上がる
- **C. CSS は1本のまま、JS だけ遅延・条件付きにする。** 見た目の FOUC を避けつつ JS 重量だけ抑える

IIFE は「1ファイルを classic script で読む」前提と相性が良い。複数エントリにする場合も、**各ファイルを IIFE のまま**にして `type="module"` にしない、は維持した方が GTM 等との衝突を避けられる。

---

## 進め方の提案（未決定）

1. **スタートアップは現状のフラット構成 + 1バンドルのまま使う**（このフォルダ）
2. `template_parts` のフォルダ分けと **階層上限2** は、合意が取れ次第 README / SKILL に規約として追記する（コード変更はほぼ不要）
3. `pages/` 等のネストは、必要になった案件でルータ拡張を先に実験し、うまくいったら starter に戻す
4. CSS/JS 分割は「全案件のデフォルト」にはせず、大規模案件の例外手順として別セクションに書く

決定したら、このファイルの結論を SKILL / README に移して、このメモは捨てるか履歴に残す。
