<?php

/**
 * CPT sample アーカイブ用テンプレート
 * archive.php から archives/archive-sample.php として自動読み込み
 */

use ThemeName\Utils\Pagination;

get_template_part('template_parts/breadcrumb');
?>
<section class="p-sample-archive">
  <div class="p-sample-archive__container c-container">
    <h1 class="p-sample-archive__title">サンプル投稿一覧</h1>

    <?php if (have_posts()) : ?>
      <ul class="p-sample-archive__list">
        <?php while (have_posts()) : the_post(); ?>
          <li class="p-sample-archive__item">
            <a href="<?= esc_url(get_permalink()); ?>"><?= esc_html(get_the_title()); ?></a>
          </li>
        <?php endwhile; ?>
      </ul>
      <?php Pagination::customPagination(); ?>
    <?php else : ?>
      <p>該当する記事がありません。</p>
    <?php endif; ?>
  </div>
</section>