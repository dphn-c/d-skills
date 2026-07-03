<?php
get_header();
?>
<article class="p-common-page">
  <div class="p-common-page__container c-container">
    <h1 class="p-common-page__title">404 Error</h1>
    <div class="p-common-page__content">
      <p>お探しのページが見つかりませんでした。</p>
      <p><a href="<?= esc_url(home_url('/')); ?>">トップページへ戻る</a></p>
    </div>
  </div>
</article>
<?php
get_footer();
