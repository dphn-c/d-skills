<?php

/**
 * 汎用パンくずリスト
 * 固定ページ・アーカイブ・詳細ページに対応
 */
function theme_get_breadcrumb_items(): array
{
  $items = [];
  $current_query = get_queried_object();

  $items[] = '<li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="' . esc_url(home_url('/')) . '">TOP</a></li>';

  if (is_page()) {
    global $post;
    $ancestors = array_reverse(get_post_ancestors($post->ID));

    foreach ($ancestors as $ancestor_id) {
      $ancestor = get_post($ancestor_id);
      $items[] = '<li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="' . esc_url(get_permalink($ancestor_id)) . '">' . esc_html($ancestor->post_title) . '</a></li>';
    }

    $items[] = '<li class="c-breadcrumb__item">' . esc_html($post->post_title) . '</li>';
  } elseif (is_archive()) {
    if (is_post_type_archive() && isset($current_query->label)) {
      $items[] = '<li class="c-breadcrumb__item">' . esc_html($current_query->label) . '</li>';
    } elseif (is_category() || is_tag() || is_tax()) {
      $items[] = '<li class="c-breadcrumb__item">' . esc_html(single_term_title('', false)) . '</li>';
    }
  } elseif (is_single()) {
    global $post;
    $post_type = get_post_type($post->ID);
    $post_type_obj = get_post_type_object($post_type);

    if ($post_type !== 'post' && $post_type_obj) {
      $items[] = '<li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="' . esc_url(get_post_type_archive_link($post_type)) . '">' . esc_html($post_type_obj->label) . '</a></li>';
    } elseif ($post_type === 'post') {
      $items[] = '<li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="' . esc_url(home_url('/news/')) . '">お知らせ</a></li>';
    }

    $items[] = '<li class="c-breadcrumb__item">' . esc_html(get_the_title()) . '</li>';
  } elseif (is_search()) {
    $items[] = '<li class="c-breadcrumb__item">検索結果</li>';
  } elseif (is_404()) {
    $items[] = '<li class="c-breadcrumb__item">ページが見つかりません</li>';
  }

  return $items;
}

$breadcrumb_items = theme_get_breadcrumb_items();

if (!empty($breadcrumb_items)) :
?>
  <nav class="c-breadcrumb" aria-label="パンくずリスト">
    <div class="c-breadcrumb__container c-container">
      <ul class="c-breadcrumb__list">
        <?php echo implode('', $breadcrumb_items); ?>
      </ul>
    </div>
  </nav>
<?php endif; ?>