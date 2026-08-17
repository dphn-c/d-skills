<?php

namespace ThemeName\PostTypes;

class SamplePostType
{
    public function __invoke(): void
    {
        add_filter('register_post_type_args', [$this, 'customizeDefaultPost'], 10, 2);
        add_filter('post_link', [$this, 'customPostPermalink'], 10, 2);
        add_action('init', [$this, 'registerPostTypesAndTaxonomies']);
    }

    /**
     * デフォルト投稿（post）のスラッグ・ラベル変更例
     */
    public function customizeDefaultPost($args, $post_type)
    {
        if ($post_type === 'post') {
            $args['rewrite'] = [
                'slug' => 'news',
                'with_front' => false,
            ];
            $args['label'] = 'お知らせ';
        }
        return $args;
    }

    /**
     * デフォルト投稿のパーマリンク変更例
     */
    public function customPostPermalink($permalink, $post)
    {
        if ($post->post_type === 'post') {
            $permalink = home_url('/news/' . $post->post_name . '/');
        }
        return $permalink;
    }

    /**
     * カスタム投稿タイプ・タクソノミー登録
     */
    public function registerPostTypesAndTaxonomies(): void
    {
        register_post_type('sample', [
            'label' => 'サンプル投稿',
            'labels' => [
                'name' => 'サンプル投稿',
                'singular_name' => 'サンプル',
                'add_new' => '新規追加',
                'add_new_item' => '新しいサンプルを追加',
                'edit_item' => 'サンプルを編集',
                'view_item' => 'サンプルを表示',
                'search_items' => 'サンプルを検索',
                'not_found' => 'サンプルが見つかりません',
            ],
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'menu_position' => 8,
            'menu_icon' => 'dashicons-admin-post',
            'rewrite' => [
                'slug' => 'sample',
                'with_front' => false,
            ],
            'supports' => ['title', 'editor', 'thumbnail'],
        ]);

        register_taxonomy('sample_category', 'sample', [
            'label' => 'サンプルカテゴリー',
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'rewrite' => [
                'slug' => 'sample-category',
                'with_front' => false,
            ],
        ]);

        // デフォルト投稿の /news/{slug}/ リライトルール
        add_rewrite_rule(
            'news/([^/]+)/?$',
            'index.php?name=$matches[1]',
            'top'
        );
    }
}
