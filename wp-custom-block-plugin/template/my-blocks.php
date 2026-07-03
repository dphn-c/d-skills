<?php

/**
 * Plugin Name:       My Blocks
 * Description:       Sample multi-block plugin scaffolded from column-blocks template.
 * Version:           0.1.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Your Name
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-blocks
 *
 * @package MyBlocks
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Register all block types from the metadata collection.
 *
 * Blocks are auto-discovered from each folder under src/my-blocks/ via --blocks-manifest.
 * No PHP changes needed when adding new blocks — just build.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 */
function create_block_my_blocks_block_init()
{
	$blocks_path   = __DIR__ . '/build/my-blocks';
	$manifest_path = __DIR__ . '/build/blocks-manifest.php';
	wp_register_block_types_from_metadata_collection($blocks_path, $manifest_path);
}
add_action('init', 'create_block_my_blocks_block_init');

/**
 * Enqueue format types script (built from src/format-types/index.js).
 */
function my_blocks_format_types_init()
{
	$script_path = __DIR__ . '/build/format-types.js';
	$asset_path  = __DIR__ . '/build/format-types.asset.php';

	if (! file_exists($script_path) || ! file_exists($asset_path)) {
		return;
	}

	$asset = include $asset_path;
	wp_enqueue_script(
		'my-blocks-format-types',
		plugins_url('build/format-types.js', __FILE__),
		$asset['dependencies'],
		$asset['version'],
		true
	);
}
add_action('enqueue_block_editor_assets', 'my_blocks_format_types_init');
