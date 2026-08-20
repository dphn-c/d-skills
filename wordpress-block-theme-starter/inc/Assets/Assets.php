<?php

namespace ThemeName\Assets;

class Assets
{
    public function __invoke(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        $theme_uri = get_theme_file_uri();
        $theme_path = get_theme_file_path();

        $css = $theme_path . '/assets/css/style.css';
        if (file_exists($css)) {
            wp_enqueue_style(
                'my-theme-style',
                $theme_uri . '/assets/css/style.css',
                [],
                (string) filemtime($css)
            );
        }

        $js = $theme_path . '/assets/js/bundle.js';
        if (file_exists($js)) {
            wp_enqueue_script(
                'my-theme-bundle',
                $theme_uri . '/assets/js/bundle.js',
                [],
                (string) filemtime($js),
                true
            );
        }
    }
}
