<?php

/**
 * CPT sample 詳細用テンプレート
 * single.php から singles/single-sample.php として自動読み込み
 */

get_template_part('template_parts/breadcrumb');
?>
<article class="p-sample-single">
  <div class="p-sample-single__container c-container">
    <hgroup class="p-sample-single__header">
      <h1 class="p-sample-single__title"><?= wp_kses_post(get_the_title()); ?></h1>
      <p class="p-sample-single__date"><?= esc_html(get_the_date('Y.m.d')); ?></p>
    </hgroup>
    <div class="p-sample-single__content">
      <?php the_content(); ?>
    </div>
  </div>
</article>