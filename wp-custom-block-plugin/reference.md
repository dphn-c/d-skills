# Reference

Supplement to [SKILL.md](SKILL.md). The canonical file set lives in [template/](template/).

## Template Layout

```
template/
├── my-blocks.php              # → rename to {plugin-slug}.php
├── package.json
├── webpack.config.js
├── stylelint.config.mjs
├── .editorconfig
├── .gitignore
├── scripts/restore-unchanged-builds.sh
└── src/
    ├── my-blocks/             # → rename to {plugin-slug}/
    │   └── sample-box/        # → rename to {first-block}/
    └── format-types/
        ├── index.js
        └── sample-format.js
```

Excluded from template (generated locally): `node_modules/`, `build/`, `package-lock.json` (removed by scaffold script).

## Rename Table

Apply when scaffolding manually (scaffold script does this automatically):

| Find | Replace with | Notes |
|------|--------------|-------|
| `my-blocks.php` | `{plugin-slug}.php` | Main plugin file |
| `src/my-blocks/` | `src/{plugin-slug}/` | Block collection folder |
| `sample-box` | `{first-block-slug}` | Optional |
| `my-blocks` | `{plugin-slug}` | Paths, text-domain, block namespace |
| `my_blocks` | `{plugin_slug_underscored}` | PHP function prefixes |
| `create_block_my_blocks` | `create_block_{plugin_slug_underscored}` | Block init function |
| `My Blocks` | `{Plugin Title}` | Plugin header title |

`{plugin_slug_underscored}` = hyphens → underscores (e.g. `my-blocks` → `my_blocks`).

## Main Plugin PHP

```php
function create_block_{plugin_slug_underscored}_block_init()
{
	$blocks_path   = __DIR__ . '/build/{plugin-slug}';
	$manifest_path = __DIR__ . '/build/blocks-manifest.php';
	wp_register_block_types_from_metadata_collection($blocks_path, $manifest_path);
}
add_action('init', 'create_block_{plugin_slug_underscored}_block_init');
```

Format-types enqueue: see `template/my-blocks.php`.

## block.json Conventions

```json
{
	"apiVersion": 3,
	"name": "{plugin-slug}/{block-slug}",
	"textdomain": "{plugin-slug}",
	"editorScript": "file:./index.js",
	"editorStyle": "file:./index.css",
	"style": "file:./style-index.css"
}
```

## format-types Pattern

1. Create `src/format-types/{name}.js` with `registerFormatType('{plugin-slug}/{name}', { ... })`
2. Add `import './{name}';` to `src/format-types/index.js`
3. `npm run build` → `build/format-types.js`

See `template/src/format-types/sample-format.js`.

## package.json Scripts

```json
{
	"prebuild": "rm -rf build",
	"build": "wp-scripts build --blocks-manifest",
	"postbuild": "bash scripts/restore-unchanged-builds.sh",
	"start": "wp-scripts start --blocks-manifest",
	"lint:scss": "stylelint \"src/**/*.{css,scss}\"",
	"lint:scss:fix": "stylelint \"src/**/*.{css,scss}\" --fix"
}
```

## webpack.config.js

Extends `@wordpress/scripts` with:

1. RTL CSS plugin removed (not needed for Japanese sites)
2. `format-types` entry → `src/format-types/index.js`

See `template/webpack.config.js`.
