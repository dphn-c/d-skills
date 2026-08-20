<?php

namespace ThemeName\Theme;

class ThemeSupports
{
    private const VERSION_OPTION = 'my_theme_theme_version';

    public function __invoke(): void
    {
        add_action('after_setup_theme', [$this, 'register']);
        add_action('after_setup_theme', [$this, 'maybeRefreshPatternCache'], 20);
    }

    public function maybeRefreshPatternCache(): void
    {
        $theme = wp_get_theme();
        $current = (string) $theme->get('Version');
        $stored = (string) get_option(self::VERSION_OPTION, '');

        if ($stored === $current) {
            return;
        }

        if (function_exists('wp_clean_themes_cache')) {
            wp_clean_themes_cache();
        }

        update_option(self::VERSION_OPTION, $current, false);
    }

    public function register(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('responsive-embeds');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);
    }
}
