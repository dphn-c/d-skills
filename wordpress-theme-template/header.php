<?php

use ThemeName\Utils\Viewport;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="<?= esc_attr(Viewport::getInitialViewport()); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <header class="l-header">
    <div class="l-header__container">
      <?php $logo_tag = is_front_page() ? 'h1' : 'p'; ?>
      <<?= $logo_tag ?> class="l-header__logo">
        <a href="<?= esc_url(home_url('/')); ?>"><?= esc_html(get_bloginfo('name')); ?></a>
      </<?= $logo_tag ?>>
      <nav class="l-header-nav" aria-label="メインナビゲーション">
        <?php
        wp_nav_menu([
          'theme_location' => 'primary',
          'container' => false,
          'fallback_cb' => false,
        ]);
        ?>
      </nav>
    </div>
  </header>

  <main id="main" class="l-main">