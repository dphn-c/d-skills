---
name: scss-workflow
description: Guides SCSS development workflow including Vite build pipeline, FLOCSS-based folder structure, Stylelint property ordering, Prettier formatting, coding conventions, and strict DRY (avoid redundant styles already covered by reset/base/inheritance). Use when creating, editing, or reviewing SCSS/SASS files, adding new stylesheets, or setting up SCSS architecture.
---

# SCSS Workflow

## New Project Scaffold

新規プロジェクトの SCSS / ビルド初期構成は **`frontend-dev-package`** SKILL からコピーする。

```bash
rsync -a --exclude='SKILL.md' --exclude='README.md' \
  ~/skills/frontend-dev-package/ /path/to/new-project/
```

Template path: `~/skills/frontend-dev-package/` — includes full FLOCSS `assets/scss/` tree and `foundation/` + `utility/_common.scss`.

## Build Pipeline

| Tool | Role | Command |
|------|------|---------|
| **Vite** | SCSS → CSS compilation & bundling | `npm run build` / `npm run dev` (dev server + HMR) |
| **Stylelint** | Linting + property order auto-sort (recess-order) | `npm run lint:scss:fix` |
| **Prettier** | Code formatting | Runs via editor or CLI |

Entry point: `assets/dev/main.js` → imports `assets/scss/style.scss`

| Project type | Build output |
|--------------|--------------|
| **HTML / CSS / JS only** | `built/css/style.css` + `built/js/bundle.js` (+ `built/index.html`) |
| **WordPress theme** | `assets/css/style.css` + `assets/js/bundle.js` |

### Dev vs Production — static HTML projects

**Development (`npm run dev`)** — `index.html` loads the Vite entry; SCSS is imported via JS so HMR works:

```html
<!-- ✅ Dev only — do not link built CSS -->
<script type="module" src="/assets/dev/main.js"></script>
```

Do **not** link `/assets/css/style.css` or `/built/css/style.css` during dev. Vite injects CSS through the JS module graph.

**Production (`npm run build`)** — Vite reads `index.html` as the build input and writes a deploy-ready site to **`built/`**:

```
built/
├── index.html      # <script src="./js/bundle.js"> + <link href="./css/style.css"> injected
├── css/style.css
└── js/bundle.js
```

Upload **`built/`** to the server. The source `index.html` in the repo root stays on the dev entry (`/assets/dev/main.js`); no manual tag switching.

**Verify before deploy:** `npm run preview` serves `built/` locally.

### WordPress themes

WordPress projects build into `assets/css/` and `assets/js/`; PHP templates enqueue those paths. Use `vite build --watch` (often via `npm run dev`) instead of the `built/` workflow.

### VS Code (`.vscode/settings.json`)

Include on project scaffold:

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

Requires the [Stylelint VS Code extension](https://marketplace.visualstudio.com/items?itemName=stylelint.vscode-stylelint).

## Folder Structure (FLOCSS-based)

```
assets/scss/
├── style.scss              # Main entry - @use each layer in order
├── foundation/             # Base styles (shared across ALL projects)
│   ├── _index.scss         # @forward all partials
│   ├── _functions.scss     # SCSS functions (encode-color, arrow-icon-svg)
│   ├── _reset.scss         # CSS reset (elad2412/the-new-css-reset)
│   ├── _mixin.scss         # Responsive mixins (pc, sp, hover, media) + UI mixins
│   ├── _base.scss          # html/body/element base styles
│   ├── _colors.scss        # CSS custom properties + SCSS variables for colors
│   ├── _fonts.scss         # @font-face + typography CSS/SCSS variables
│   └── _vars.scss          # General CSS custom properties + SCSS variables
├── component/              # Reusable UI components (c- prefix)
│   ├── _index.scss
│   ├── _container.scss
│   ├── _links-and-btns.scss
│   └── ...
├── layout/                 # Page structure (l- prefix)
│   ├── _index.scss
│   ├── _header.scss
│   ├── _footer.scss
│   └── _main.scss
├── project/                # Page-specific styles (p- prefix)
│   ├── _index.scss
│   ├── _top.scss
│   └── ...
└── utility/                # Utility classes (u- prefix)
    ├── _index.scss
    ├── _common.scss        # Shared across ALL projects
    └── _product.scss       # Project-specific utilities
```

### Layer Load Order (style.scss)

```scss
@charset "UTF-8";
@use 'foundation';
@use 'component';
@use 'layout';
@use 'project';
@use 'utility';
```

**This order is strict.** Foundation must come first (provides variables/mixins), utility last (highest specificity override).

### Shared Across All Projects

- **`foundation/` (entire directory)** — Reset, variables, mixins, base styles
- **`utility/_common.scss`** — Common utility classes (margins, text-align, display, font-size, etc.)

These files should remain project-agnostic. Project-specific overrides go in `project/` or `utility/_product.scss`.

## Module System & Naming

### @use / @forward Pattern

Each layer directory has an `_index.scss` that `@forward`s its partials:

```scss
// foundation/_index.scss
@forward 'functions';
@forward 'reset';
@forward 'mixin';
@forward 'base';
@forward 'colors';
@forward 'fonts';
@forward 'vars';
```

Component/layout/project/utility files import foundation with namespace `b`:

```scss
@use '../foundation' as b;

.c-example {
  color: b.$primary;

  @include b.pc {
    width: 120rem;
  }
}
```

Within foundation, files reference siblings directly:

```scss
// foundation/_base.scss
@use 'mixin' as mx;
@use 'vars' as var;
@use 'colors' as c;
@use 'fonts' as f;
```

### CSS Class Naming (BEM + FLOCSS Prefixes)

| Layer | Prefix | Example |
|-------|--------|---------|
| Component | `c-` | `.c-container`, `.c-breadcrumb` |
| Layout | `l-` | `.l-header`, `.l-footer` |
| Project | `p-` | `.p-top-slider`, `.p-seminar-archive` |
| Utility | `u-` | `.u-tac`, `.u-mt-20`, `.u-pc-none` |

BEM pattern: `.block__element--modifier`
Stylelint enforces: `^[a-z0-9]+(-[a-z0-9]+)*(__[a-z0-9]+(-[a-z0-9]+)*)?(--[a-z0-9]+(-[a-z0-9]+)*)?$`

## Responsive Design

### Breakpoints

| Key | Width | Usage |
|-----|-------|-------|
| `xs` | 321px | Small mobile |
| `sm` | 576px | Mobile landscape |
| `md` | 768px | Tablet / PC-SP boundary |
| `lg` | 1024px | Desktop |
| `xl` | 1320px | Wide desktop |

### Mixin Usage

```scss
// PC (>= 768px)
@include b.pc { ... }

// SP (<= 767px)
@include b.sp { ... }

// Hover-capable devices only
@include b.hover { &:hover { ... } }

// Custom breakpoint (min-width by default)
@include b.media('lg') { ... }

// Max-width variant
@include b.media('sm', max) { ... }
```

## CSS Custom Properties Pattern

Colors, fonts, and general variables are defined as CSS custom properties in `:root`, then mirrored as SCSS variables for use in SCSS expressions:

```scss
// _colors.scss
:root {
  --primary: #009995;
}
$primary: var(--primary);

// For data URIs (CSS variables don't work), use -hex suffix:
$white-hex: #fff;
$secondary-hex: #22b49d;
```

## Adding New Files

### New Component

1. Create `assets/scss/component/_new-component.scss`
2. Start with `@use '../foundation' as b;`
3. Use `c-` prefix: `.c-new-component { ... }`
4. Add `@use 'new-component';` to `component/_index.scss`

### New Project Page

1. Create `assets/scss/project/_new-page.scss`
2. Start with `@use '../foundation' as b;`
3. Use `p-` prefix: `.p-new-page { ... }`
4. Add `@use 'new-page';` to `project/_index.scss`

### New Layout Section

1. Create `assets/scss/layout/_new-layout.scss`
2. Start with `@use '../foundation' as b;`
3. Use `l-` prefix: `.l-new-layout { ... }`
4. Add `@use 'new-layout';` to `layout/_index.scss`

## Coding Conventions

### DRY — Do Not Repeat Existing Styles

**Before adding any property to a new class, check what is already applied** by reset, base, parent, or utility layers. Only declare what actually changes the computed style. Follow DRY strictly — redundant declarations add noise and make overrides harder to reason about.

**Check in this order:**

1. **`foundation/_reset.scss`** — `all: unset` on most elements; `list-style: none` on lists; `box-sizing: border-box` globally
2. **`foundation/_base.scss`** — `body` sets font-family, font-size, line-height, color; headings/lists/paragraphs inherit via `font-size: inherit; font-weight: inherit; color: inherit`; `figure`/`picture` already have `margin: 0`
3. **Parent block** — child elements inside a styled block often inherit color, font-size, and font-weight without re-declaration
4. **`utility/_common.scss`** — prefer existing `u-*` classes (e.g. `.u-tac`, `.u-fw-b`, `.u-mt-20`) over duplicating the same rules in component/project SCSS

**Do not write when already true:**

| Skip | Why |
|------|-----|
| `margin: 0` on most elements | Reset clears margins |
| `padding: 0` | Reset clears padding |
| `list-style: none` on `ul`/`ol` | Reset already removes bullets |
| `color: inherit` on `h1`–`p` inside a colored parent | `_base.scss` already sets `color: inherit` on these elements |
| `font-size: inherit` / `font-weight: inherit` on headings | `_base.scss` already inherits from parent |
| `box-sizing: border-box` | Reset sets this globally |
| Same color as parent block | Inherits automatically — only set when overriding |

```scss
// ❌ Redundant — parent .p-price-chart already sets color; base resets margin
.p-price-chart__title {
  margin: 0;
  color: inherit;
  font-size: 2.4rem;
}

// ✅ Only what differs from inherited/reset state
.p-price-chart__title {
  font-size: 2.4rem;
}
```

For shorthand rules, logical properties, and more examples, see [css-coding-conventions](../css-coding-conventions/SKILL.md) §4.

### Units

- Use `rem` (base: `html { font-size: 62.5% }` → 1rem = 10px)
- Use CSS functions: `clamp()`, `calc()`, `min()`, `max()` for responsive sizing

### Stylelint Rules

- Property order: **Recess order** (position → display → box-model → typography → visual → misc)
- Vendor prefixes: allowed
- Selector: kebab-case or BEM
- `@media` range notation: `context` (both `>=` and traditional syntax allowed)

### Prettier Config (applied to SCSS)

- `printWidth: 100`, `tabWidth: 2`, `singleQuote: true`, `useTabs: false`
- `trailingComma: "none"`, `semi: true`

### Nesting

Use SCSS nesting for BEM elements/modifiers and responsive variants:

```scss
.c-example {
  display: flex;

  &__item {
    padding: 2rem;
  }

  &--large {
    padding: 4rem;
  }

  @include b.sp {
    flex-direction: column;
  }
}
```

## Available Foundation Mixins

| Mixin | Purpose |
|-------|---------|
| `pc` | `@media (width >= 768px)` |
| `sp` | `@media (width <= 767px)` |
| `hover` | `@media (any-hover: hover)` |
| `media($bp, $rule)` | Custom breakpoint, `$rule`: `min` (default) or `max` |
| `full-width` | Break out of container to full viewport width |
| `secondary-ttl` | Styled h2-level title with left border accent |
| `tertiary-title` | Styled h3-level title |

## Validation Workflow

After editing SCSS:

1. Save — Stylelint auto-fix runs via `.vscode/settings.json` (if extension installed)
2. Run `npm run lint:scss:fix` to auto-fix property order and lint issues
3. Verify build: `npm run build`
4. Check output in `built/css/style.css` (static HTML) or `assets/css/style.css` (WordPress)

During development, run `npm run dev` and confirm SCSS changes hot-reload in the browser (via `/assets/dev/main.js` entry).

## Additional Resources

- For new project scaffold (copy template), see [frontend-dev-package](../frontend-dev-package/SKILL.md)
- For detailed foundation variables and utility classes, see [reference.md](reference.md)
- For JavaScript module architecture (View / Model / Control, class-based features), see [js-workflow](../js-workflow/SKILL.md)
