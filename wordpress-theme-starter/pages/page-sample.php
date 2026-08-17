<?php

/**
 * 固定ページ slug: sample 用テンプレート
 * REST API + JS ページネーション連動のサンプル
 *
 * page.php から pages/page-sample.php として自動読み込み
 */

use ThemeName\API\SamplePostApi;
use ThemeName\Utils\Pagination;

get_template_part('template_parts/breadcrumb');

$news_data = SamplePostApi::getPosts([
  'cat' => 'all',
  'pagenum' => 1,
  'posts_per_page' => 10,
]);
?>
<section id="sample-list" class="p-sample-archive">
  <div class="p-sample-archive__container c-container">
    <h1 class="p-sample-archive__title">サンプル一覧（API + ページネーション）</h1>

    <ul class="p-sample-archive__list">
      <?php if (!empty($news_data['posts'])) : ?>
        <?php foreach ($news_data['posts'] as $post_item) : ?>
          <li class="p-sample-archive__item">
            <time datetime="<?= esc_attr($post_item['post_date']); ?>"><?= esc_html($post_item['post_date']); ?></time>
            <a href="<?= esc_url($post_item['post_link']); ?>"><?= esc_html($post_item['post_title']); ?></a>
          </li>
        <?php endforeach; ?>
      <?php else : ?>
        <li>該当する記事がありません。</li>
      <?php endif; ?>
    </ul>

    <div class="p-sample-archive__pagination">
      <?php Pagination::customPaginationJs($news_data['pagination'], '#sample-list'); ?>
    </div>
  </div>
</section>