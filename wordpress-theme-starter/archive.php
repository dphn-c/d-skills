<?php

use ThemeName\Utils\Pagination;

get_header();
$archive_query = get_queried_object();

if (file_exists(dirname(__FILE__) . '/archives/archive-' . esc_attr($archive_query->name) . '.php')) {
  get_template_part('archives/archive', esc_attr($archive_query->name));
} else {
  get_template_part('template_parts/breadcrumb');
?>
  <section class="p-archive">
    <div class="p-archive__container c-container">
      <h1 class="p-archive__title"><?= esc_html($archive_query->label ?? 'Archive'); ?></h1>
      <?php if (have_posts()) : ?>
        <ul class="p-archive__list">
          <?php while (have_posts()) : the_post(); ?>
            <li class="p-archive__item">
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
<?php
}
get_footer();
