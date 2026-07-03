<?php

namespace ThemeName\Queries;

class PreGetPosts
{
    public function __invoke(): void
    {
        add_action('pre_get_posts', [$this, 'customPreGetPosts']);
    }

    public function customPreGetPosts(\WP_Query $query): void
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // サンプル CPT アーカイブ: 1ページあたりの件数
        if ($query->is_post_type_archive('sample')) {
            $query->set('posts_per_page', 12);
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
}
