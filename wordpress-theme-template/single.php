<?php
get_header();

global $post;

if (file_exists(dirname(__FILE__) . '/singles/single-' . esc_attr($post->post_type) . '.php')) {
  get_template_part('singles/single', esc_attr($post->post_type));
} else {
  get_template_part('template_parts/breadcrumb');
  while (have_posts()) : the_post();
?>
    <article class="p-single">
      <div class="p-single__container c-container">
        <hgroup class="p-single__header">
          <h1 class="p-single__title"><?= wp_kses_post(get_the_title()); ?></h1>
          <p class="p-single__date"><?= esc_html(get_the_date('Y.m.d')); ?></p>
        </hgroup>
        <div class="p-single__content">
          <?php the_content(); ?>
        </div>
      </div>
    </article>
<?php
  endwhile;
}

get_footer();
