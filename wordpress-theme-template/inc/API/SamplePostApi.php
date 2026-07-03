<?php

namespace ThemeName\API;

class SamplePostApi
{
    public function __invoke(): void
    {
        add_action('rest_api_init', [$this, 'registerEndpoint']);
    }

    public function registerEndpoint(): void
    {
        register_rest_route('my-theme/v1', '/sample-posts', [
            'methods' => 'GET',
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => '__return_true',
            'args' => [
                'cat' => [
                    'default' => 'all',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'pagenum' => [
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
                'posts_per_page' => [
                    'default' => 5,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);
    }

    public function handleRequest(\WP_REST_Request $request): \WP_REST_Response
    {
        $result = self::getPosts([
            'cat' => $request->get_param('cat'),
            'pagenum' => $request->get_param('pagenum'),
            'posts_per_page' => $request->get_param('posts_per_page'),
        ]);

        return new \WP_REST_Response($result, 200);
    }

    public static function getCategoryNames(): array
    {
        return [
            'all' => 'すべて',
            'notice' => 'お知らせ',
            'sample' => 'サンプル',
        ];
    }

    private static function buildQueryArgs(array $args): array
    {
        $query_args = [
            'post_status' => 'publish',
            'posts_per_page' => $args['posts_per_page'],
            'paged' => $args['pagenum'],
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        switch ($args['cat']) {
            case 'notice':
                $query_args['post_type'] = 'post';
                break;
            case 'sample':
                $query_args['post_type'] = 'sample';
                break;
            case 'all':
            default:
                $query_args['post_type'] = ['post', 'sample'];
                break;
        }

        return $query_args;
    }

    private static function buildPostData(\WP_Post $post): array
    {
        return [
            'post_title' => esc_html(get_the_title($post->ID)),
            'post_link' => esc_url(get_permalink($post->ID)),
            'post_date' => esc_html(get_the_date('Y/m/d', $post->ID)),
            'post_type' => esc_attr(get_post_type($post->ID)),
        ];
    }

    public static function getPosts(array $args = []): array
    {
        $args = wp_parse_args($args, [
            'cat' => 'all',
            'pagenum' => 1,
            'posts_per_page' => 5,
        ]);

        $query = new \WP_Query(self::buildQueryArgs($args));

        $result = [
            'posts' => [],
            'pagination' => [
                'paged' => (int) $args['pagenum'],
                'pages' => (int) $query->max_num_pages,
                'total_posts' => (int) $query->found_posts,
                'posts_per_page' => (int) $args['posts_per_page'],
            ],
        ];

        foreach ($query->posts as $post) {
            $result['posts'][] = self::buildPostData($post);
        }

        return $result;
    }
}
