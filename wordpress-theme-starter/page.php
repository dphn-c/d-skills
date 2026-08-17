<?php
get_header();
while (have_posts()) : the_post();
  if (file_exists(dirname(__FILE__) . '/pages/page-' . esc_attr($post->post_name) . '.php')) {
    get_template_part('pages/page', esc_attr($post->post_name));
  } else {
    get_template_part('template_parts/breadcrumb');
?>
    <section class="p-common-page">
      <div class="p-common-page__container c-container">
        <h1 class="p-common-page__title"><?= wp_kses_post(get_the_title()); ?></h1>
        <div class="p-common-page__content">
          <?php the_content(); ?>
        </div>
      </div>
    </section>
<?php
  }
endwhile;
get_footer();
