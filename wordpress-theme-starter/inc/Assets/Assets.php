<?php

namespace ThemeName\Assets;

class Assets
{
    public function __invoke(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminStyles']);
    }

    public function enqueueScripts(): void
    {
        $css_path = get_theme_file_path('/assets/css/style.css');
        if (file_exists($css_path)) {
            wp_enqueue_style(
                'my-theme-style',
                get_theme_file_uri('/assets/css/style.css'),
                [],
                filemtime($css_path)
            );
        }

        $js_path = get_theme_file_path('/assets/js/bundle.js');
        if (file_exists($js_path)) {
            wp_enqueue_script(
                'my-theme-bundle',
                get_theme_file_uri('/assets/js/bundle.js'),
                [],
                filemtime($js_path),
                true
            );
        }

        // 外部ライブラリ例（必要に応じて削除・変更）
        // wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css', [], '12.0.0');
    }

    public function adminStyles(): void
    {
        $admin_css = get_theme_file_path('/assets/css/admin.css');
        if (file_exists($admin_css)) {
            wp_enqueue_style(
                'my-theme-admin',
                get_theme_file_uri('/assets/css/admin.css'),
                [],
                filemtime($admin_css)
            );
        }
    }
}
