---
name: css-coding-conventions
description: CSS/SCSSファイルを記述・レビュー・リファクタリングする際のコーディング規約。新規CSS/SCSSファイルの作成、既存スタイルの編集・整理、メディアクエリの配置見直し、shorthandプロパティの整理を行う場面で使用する。Use when creating, editing, reviewing, or refactoring CSS/SCSS files.
---

# CSS コーディング規約

## 1. 単方向のみ使う場合は個別プロパティで指定する

shorthand で0や不要な値を並べない。使う方向だけを指定する。

```css
/* ❌ */
margin: 0 0 30px;
margin: 10px 0;
padding: 80px 0 140px;

/* ✅ */
margin-bottom: 30px;
margin-block: 10px;
padding-block: 80px 140px;
```

logical properties（`margin-block`, `margin-inline`, `padding-block`, `padding-inline`）を積極的に使う。

## 2. font shorthand は使わない

`font:` shorthand は使わず、各プロパティを個別に書く。

```css
/* ❌ */
font: italic bold 16px/1.5 Montserrat, sans-serif;

/* ✅ */
font-style: italic;
font-weight: bold;
font-size: 16px;
line-height: 1.5;
font-family: Montserrat, sans-serif;
```

## 3. メディアクエリは対象クラスの直後に配置する

クラス定義から離れた場所にまとめて書かない。

```css
/* ❌ 末尾にまとめて書く */
.p-hero { padding-bottom: 120px; }
.p-lead { font-size: 25px; }

@media (width < 1050px) {
  .p-hero { padding-bottom: 75px; }
  .p-lead { font-size: 20px; }
}

/* ✅ クラスの直後に書く */
.p-hero { padding-bottom: 120px; }
@media (width < 1050px) {
  .p-hero { padding-bottom: 75px; }
}

.p-lead { font-size: 25px; }
@media (width < 1050px) {
  .p-lead { font-size: 20px; }
}
```

## 4. デフォルト値・リセット済み値は書かない

CSSリセット（`all: unset` 系）や仕様上のデフォルトと同じ値を明示しない。

| 書かなくてよい例 | 理由 |
|---|---|
| `margin: 0` | リセットで0になる |
| `padding: 0` | リセットで0になる |
| `list-style: none` | ul/ol はリセット済み |
| `color: inherit`（h1–p など） | `_base.scss` で `color: inherit` 済み。親と同じ色なら指定不要 |
| `font-size: inherit` / `font-weight: inherit`（見出し等） | `_base.scss` で継承済み |
| `display: block`（position:absolute の要素） | absolute で自動的にブロック化される |
| `background-color: transparent` | initial値 |
| `box-shadow: none`（未設定のプロパティ） | 未設定なら none が既定 |
| `z-index: 0`（position なし） | position がないと z-index は無効 |

## 5. メディアクエリの書き方

ブレークポイントは Range Syntax で書く（`min-width`/`max-width` より可読性が高い）。

```css
/* ✅ */
@media (width >= 1050px) { }
@media (width < 1050px) { }
```

## 6. 0値には単位を付けない

```css
/* ❌ */
margin: 0px;

/* ✅ */
margin: 0;
```

## 適用例

```css
/* ❌ Before */
.p-section {
  padding: 90px 0 110px;
  margin: 0;
}
.p-section__title {
  margin: 0 0 8px;
  font: bold 32px/1.2 Inter, sans-serif;
}
@media (width < 1050px) {
  .p-section { padding: 70px 0 90px; }
  .p-section__title { font-size: 24px; }
}

/* ✅ After */
.p-section {
  padding-block: 90px 110px;
}
@media (width < 1050px) {
  .p-section {
    padding-block: 70px 90px;
  }
}

.p-section__title {
  margin-bottom: 8px;
  font-weight: bold;
  font-size: 32px;
  line-height: 1.2;
  font-family: Inter, sans-serif;
}
@media (width < 1050px) {
  .p-section__title {
    font-size: 24px;
  }
}
```
