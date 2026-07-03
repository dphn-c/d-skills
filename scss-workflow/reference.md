# SCSS Reference

## Color Variables (`foundation/_colors.scss`)

### CSS Custom Properties (`:root`)

| Variable | Value | Usage |
|----------|-------|-------|
| `--black` | `#222` | Default text color |
| `--white` | `#fff` | Background, inverted text |
| `--primary` | `#009995` | Brand primary (teal) |
| `--secondary` | `#22b49d` | Brand secondary |
| `--tertiary` | `#066` | Brand tertiary (dark teal) |
| `--accent` | `#ff4d79` | Accent (pink) |
| `--attention` | `#e60000` | Error / warning (red) |
| `--gray` | `#b1b1b1` | Gray text |
| `--dark-gray` | `#b1b1b1` | Dark gray |
| `--bg-dark-gray` | `#ddd` | Background dark gray |
| `--bg-gray` | `#f7f7f7` | Background light gray |
| `--bg-green` | `#e5f5f4` | Background green |
| `--bg-yellow` | `#fffac2` | Background yellow |
| `--bg-youtube` | `#ff9f43` | YouTube badge |
| `--bg-campaign` | `#f368e0` | Campaign badge |
| `--bg-notice` | `#5f27cd` | Notice badge |
| `--bg-seminar` | `#2d85dd` | Seminar badge |
| `--link` | `#009995` | Link color |
| `--btn` | `#009995` | Button color |
| `--btn-bg` | `#fff` | Button background |

### SCSS Variables

All CSS custom properties are mirrored: `$primary: var(--primary);`

For data URIs (CSS variables don't work inside `url()`):
- `$white-hex: #fff`
- `$secondary-hex: #22b49d`

---

## Typography Variables (`foundation/_fonts.scss`)

### Font Family

`$jp-common` → `'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', '游ゴシック', meiryo, sans-serif`

Font weights loaded: 400, 500, 700 (woff format from `assets/fonts/`)

### Font Size Scale

| Variable | PC | SP (clamp) |
|----------|-----|-----|
| `$main-ttl` | 3.2rem | clamp(2.8rem, 4vw, 3.2rem) |
| `$sub-ttl` | 2.4rem | clamp(2rem, 3.5vw, 2.4rem) |
| `$xxl-tx` | 2.2rem | clamp(1.9rem, 3.2vw, 2.2rem) |
| `$xl-tx` | 2rem | clamp(1.8rem, 3vw, 2rem) |
| `$l-tx` | 1.8rem | clamp(1.7rem, 2.8vw, 1.8rem) |
| `$common-tx` | 1.6rem | clamp(1.6rem, 2.6vw, 1.7rem) |
| `$m-tx` | 1.4rem | clamp(1.4rem, 2.5vw, 1.6rem) |
| `$s-tx` | 1.2rem | clamp(1.2rem, 2.2vw, 1.4rem) |
| `$xs-tx` | 1rem | — |

---

## General Variables (`foundation/_vars.scss`)

| Variable | Value |
|----------|-------|
| `$shadow` | `0 0 0.7rem rgb(0 0 0 / 20%)` |
| `$menu-gap` | `clamp(1.2rem, 1vw, 2.5rem)` |
| `$link-hover` | `0.6` |
| `$transition` | `0.3s ease` |
| `$drop-shadow` | `0 0 0.5rem rgb(0 0 0 / 20%)` |

---

## Functions (`foundation/_functions.scss`)

| Function | Purpose | Example |
|----------|---------|---------|
| `encode-color($color)` | Encode hex color for data URI (`#` → `%23`) | `encode-color(#fff)` → `%23fff` |
| `arrow-icon-svg($color)` | Generate arrow icon data URI SVG | `background: arrow-icon-svg($secondary-hex)` |

---

## Mixins (`foundation/_mixin.scss`)

### Responsive

```scss
@include b.pc { ... }          // width >= 768px
@include b.sp { ... }          // width <= 767px
@include b.hover { ... }       // any-hover: hover

@include b.media('lg') { ... }       // min-width: 1024px
@include b.media('sm', max) { ... }  // max-width: 575px
```

Breakpoint map: `xs: 321px`, `sm: 576px`, `md: 768px`, `lg: 1024px`, `xl: 1320px`

### UI

```scss
@include b.full-width;         // Break out of container to 100vw
@include b.secondary-ttl;      // h2 style: primary color, left border accent
@include b.tertiary-title;     // h3 style: primary color, bold
```

---

## Utility Classes (`utility/_common.scss`)

### Text Alignment

| Class | Effect | Responsive |
|-------|--------|------------|
| `.u-tac` | `text-align: center` | — |
| `.u-pc-tac` | `text-align: center` | PC only |
| `.u-sp-tac` | `text-align: center` | SP only |
| `.u-tal` | `text-align: left` | — |
| `.u-tar` | `text-align: right` | — |

### Block Alignment

| Class | Effect |
|-------|--------|
| `.u-center` | `margin: 0 auto` |
| `.u-right` | `margin: 0 0 0 auto` |
| `.u-left` | `margin: 0 auto 0 0` |
| `.u-wid-fit` | `width: fit-content; margin-inline: auto` |

### Font Size

| Class | Size variable |
|-------|--------------|
| `.u-fz-s` | `$s-tx` |
| `.u-fz-common` | `$common-tx` |
| `.u-fz-m` | `$m-tx` |
| `.u-fz-l` | `$l-tx` |
| `.u-fz-xl` | `$xl-tx` |

### Font Weight

`.u-fw-b` (bold), `.u-fw300`, `.u-fw400`, `.u-fw600`, `.u-fw700`, `.u-fw900`

### Spacing (margin)

Pattern: `.u-mt-{value}` / `.u-mb-{value}` where value = 00, 10, 20, ..., 100 (in rem/10)

Example: `.u-mt-30` → `margin-top: 3rem`

### Spacing (padding)

| Pattern | Values |
|---------|--------|
| `.u-pt-{n}` | 10, 15, 20, 30, 40 |
| `.u-pb-{n}` | 0, 10, 15, 20, 30, 40 |
| `.u-pl-{n}` | 10, 15, 20 |
| `.u-pr-{n}` | 10, 15, 20 |
| `.u-p-20` | `padding: 2rem` |

### Line Height

`.u-lh12` (1.2), `.u-lh13` (1.3), `.u-lh15` (1.5), `.u-lh16` (1.6), `.u-lh18` (1.8), `.u-lh20` (2)

### Display / Visibility

| Class | Effect |
|-------|--------|
| `.u-pc-none` | `display: none` on PC |
| `.u-sp-none` | `display: none` on SP |
| `.u-none` | `display: none` |
| `.u-hidden` | `visibility: hidden` |

### Misc

| Class | Effect |
|-------|--------|
| `.u-udline` | `text-decoration: underline` |
| `.u-central` | Flex centering (both axes) |
| `.u-pointer` | `cursor: pointer` |
| `.u-overflow-hidden` | `overflow: hidden` |
| `.u-indent-1em` | Hanging indent (1em) |

---

## Stylelint Configuration

Extends: `stylelint-config-standard-scss` + `stylelint-config-recess-order`

Key rules:
- Property order: Recess order (position → display → box-model → typography → visual → misc)
- Vendor prefixes: allowed (`property-no-vendor-prefix: null`)
- Media query notation: `context` (both `>=` and traditional)
- Selector pattern: kebab-case or BEM (`block__element--modifier`)
- `selector-not-notation: null` (legacy `:not()` syntax allowed)

---

## Vite Build Details

- Entry: `assets/dev/main.js` (imports `../scss/style.scss`)
- Output CSS: `assets/css/style.css`
- Output JS: `assets/js/bundle.js`
- Sourcemaps: enabled
- CSS URL rebase plugin: rewrites `url(images/...)` → `url(../images/...)` and `url(fonts/...)` → `url(../fonts/...)` in output CSS
- Alias: `@` → `assets/`

### package.json scripts (HTML projects)

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "lint:scss": "stylelint \"assets/scss/**/*.scss\"",
    "lint:scss:fix": "stylelint \"assets/scss/**/*.scss\" --fix"
  }
}
```

WordPress themes use `"dev": "vite build --watch"` instead (no dev server; PHP loads built files).

### vite.config.js (HTML projects)

Add `root: '.'` and `server.open: true` for the dev server. Keep `build.outDir: 'assets'` and the css-url-rebase plugin.

### index.html (HTML projects)

**Development** — only the Vite entry (HMR enabled):

```html
<script type="module" src="/assets/dev/main.js"></script>
```

**Production static deploy** — after `npm run build`:

```html
<link rel="stylesheet" href="/assets/css/style.css">
<script type="module" src="/assets/js/bundle.js"></script>
```

### .vscode/settings.json

```json
{
  "css.validate": false,
  "scss.validate": false,
  "stylelint.validate": ["css", "scss"],
  "editor.codeActionsOnSave": {
    "source.fixAll.stylelint": "explicit"
  }
}
```
