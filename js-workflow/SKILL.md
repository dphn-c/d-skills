---
name: js-workflow
description: Guides JavaScript module architecture for Vite-based HTML/SCSS/JS projects — feature-based folders, MVC-style separation (View, Model/Business, Control), class-based implementation, and strict DRY. Use when creating, editing, or reviewing JS modules under assets/dev/, adding interactive features, or refactoring frontend logic.
---

# JS Workflow

## New Project Scaffold

新規プロジェクトの JS / ビルド初期構成は **`frontend-dev-package`** SKILL からコピーする。

```bash
rsync -a --exclude='SKILL.md' --exclude='README.md' \
  ~/skills/frontend-dev-package/ /path/to/new-project/
```

Template path: `~/skills/frontend-dev-package/`

## Build Pipeline

### Project types

| Type | Deploy output | Notes |
|------|---------------|-------|
| **HTML / CSS / JS only** (static) | `built/` | Upload `built/` contents to the server. See below. |
| **WordPress theme** | `assets/css/`, `assets/js/` | PHP templates load built assets; use `vite build --watch` during theme dev. |

### Static HTML projects (`built/` deploy)

Entry point: `assets/dev/main.js` → Vite bundles to `built/js/bundle.js` and `built/css/style.css`.

`npm run build` aggregates everything needed for production into **`built/`** — upload that folder (or its contents) to deploy. No manual edits to `index.html` are required; Vite rewrites script and stylesheet paths on build.

```
built/                    # ← deploy this folder
├── index.html            # script/link tags rewritten automatically
├── css/style.css
└── js/bundle.js
```

**`vite.config.js` essentials** for static projects:

```javascript
export default defineConfig({
  base: './',              // relative paths — works in subdirectories
  build: {
    outDir: 'built',
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(__dirname, 'index.html'),
      output: {
        entryFileNames: 'js/bundle.js',
        assetFileNames: (info) =>
          info.name?.endsWith('.css') ? 'css/style.css' : 'assets/[name]-[hash][extname]',
      },
    },
  },
});
```

**`.gitignore`** — exclude generated output:

```
built/
```

Dev vs production HTML script tags: see [scss-workflow](../scss-workflow/SKILL.md).

`main.js` responsibilities:

- Import SCSS
- Import feature modules and instantiate their entry classes

```javascript
import '../scss/style.scss';
import PriceChart from './modules/price-chart/index.js';

new PriceChart('#price-chart-canvas');
```

## Architecture: View / Model / Control

Organize each **feature** (e.g. `price-chart`, `accordion`, `modal`) as a self-contained module under `assets/dev/modules/<feature-name>/`.

Within a feature, separate concerns by role — **prefer class-based implementation** for each role:

| Role | Responsibility | Examples |
|------|----------------|----------|
| **Control** | Orchestration: init, event wiring, lifecycle, coordinating Model and View | `PriceChart` (`index.js`) |
| **Model / Business** | Data fetch, transform, filter, domain rules — no DOM | `PriceChartDataService` (`data-service.js`) |
| **View** | DOM rendering, UI state, presentation-only logic | `PriceChartLegend` (`legend.js`), `YAxisPriceLabelsPlugin` (`y-axis-labels.js`) |

Additional supporting files (not role-specific):

| File | Purpose |
|------|---------|
| `constants.js` | Feature constants (presets, colors, thresholds) |
| `format.js` | Pure utility functions shared within the feature |
| `*-config.js` | Third-party library configuration (e.g. Chart.js options) |

### Rules

1. **Feature-first** — one directory per user-facing feature, not one file per technical layer globally.
2. **Classes by default** — export a `class` as the public API for Control, Model, and View units. Use plain functions only for stateless utilities (`format.js`, helpers).
3. **Control owns wiring** — the Control class constructs Model and View instances, binds events, and calls `render()` / `update()` on Views.
4. **Model stays DOM-free** — no `document.querySelector`, no `innerHTML` in Model/Business classes.
5. **View stays logic-light** — Views render and reflect state; business decisions belong in Model or Control.
6. **Single default export** — each class file exports one class: `export default class Foo`.

### DRY — Do Not Repeat Yourself

Follow DRY strictly across JS modules. Before adding code, check whether the same logic, constant, or DOM query already exists in the feature or project.

**Within a feature module:**

| Concern | DRY approach |
|---------|--------------|
| Magic numbers / labels / colors | `constants.js` — single source of truth |
| Formatting / parsing | `format.js` or static methods on Model — one implementation |
| Data transforms | Model class methods — Views and Control call them, never duplicate filter/map logic |
| Third-party config | `*-config.js` factory — build options once, reuse across init and updates |
| DOM references | Query once in Control/View constructor, store on `this` — do not re-query the same selector |

**Across features:**

- If two modules need the same pure utility, extract to `assets/dev/utils/<name>.js` instead of copying.
- If two features share domain logic, prefer a shared Model or utility module over inline duplication in Control classes.

```javascript
// ❌ Duplicated date formatting in Control and Model
formatInputDate(date) { /* ... */ }  // in index.js
formatInputDate(date) { /* ... */ }  // in data-service.js

// ✅ Single implementation — static method on Model, used everywhere
PriceChartDataService.formatInputDate(date);
```

```javascript
// ❌ Re-querying the same element on every event
handleClick() {
  this.section.querySelector('.p-price-chart__legend').classList.toggle('is-open');
}

// ✅ Cached in constructor
this.legendEl = this.section?.querySelector('.p-price-chart__legend');
```

**When editing:** if you touch duplicated logic, consolidate it in the same change — do not leave two copies that drift apart.

### Example layout (`price-chart`)

```
assets/dev/modules/price-chart/
├── index.js              # Control: PriceChart
├── data-service.js       # Model: PriceChartDataService
├── legend.js             # View: PriceChartLegend
├── y-axis-labels.js      # View: YAxisPriceLabelsPlugin
├── chart-config.js       # Config factory
├── constants.js
└── format.js
```

## Adding a New Feature Module

1. Create `assets/dev/modules/<feature-name>/` with at least a Control class (`index.js`).
2. Split into Model / View classes when the feature grows beyond trivial DOM + data.
3. Import and instantiate in `assets/dev/main.js`:

```javascript
import FeatureName from './modules/feature-name/index.js';

new FeatureName('.js-feature-root');
```

4. Match existing naming: `PascalCase` class names, `kebab-case` directory names.

## Validation

After editing JS:

1. Run `npm run build` (or keep `npm run dev` running) and confirm no bundle errors.
2. Check `built/js/bundle.js` is updated (static HTML projects).
3. Smoke-test with `npm run preview` or upload `built/` to a staging server.

## Related Skills

- [frontend-dev-package](../frontend-dev-package/SKILL.md) — Copy scaffold to new projects
- [scss-workflow](../scss-workflow/SKILL.md) — SCSS architecture and build pipeline
