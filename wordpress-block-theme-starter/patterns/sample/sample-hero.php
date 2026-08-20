<?php

/**
 * Title: Sample Hero
 * Slug: my-theme/sample-hero
 * Categories: featured
 * Inserter: no
 *
 * パーツ差し替え用にする例（footer）:
 * Categories: footer
 * Block Types: core/template-part/footer
 * （Inserter: no は付けない）
 */
?>
<!-- wp:group {"tagName":"section","className":"p-sample-hero","layout":{"type":"default"}} -->
<section class="wp-block-group p-sample-hero">
	<!-- wp:heading {"level":1,"className":"c-container"} -->
	<h1 class="wp-block-heading c-container">My Theme</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"c-container"} -->
	<p class="c-container">patterns/ は初期注入のひな型。ファイル変更を常に正にしたい中身は theme-blocks/render/ へ。</p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->