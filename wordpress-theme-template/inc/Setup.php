<?php

namespace ThemeName;

class Setup
{
    /**
     * 自動初期化から除外するディレクトリ
     */
    private array $excluded_dirs = ['Contracts', 'Traits', 'Interfaces', 'Exceptions'];

    /**
     * 自動初期化から除外するファイル
     */
    private array $excluded_files = ['Setup.php', 'config.php', 'autoload.php'];

    public function __invoke(): void
    {
        add_action('after_setup_theme', [$this, 'setupTheme']);

        $inc_dir = get_stylesheet_directory() . '/inc';
        $classes = $this->discoverClasses($inc_dir, 'ThemeName');

        foreach ($classes as $class_name) {
            $this->initializeClass($class_name);
        }
    }

    public function setupTheme(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

        register_nav_menus([
            'primary' => 'Primary Menu',
        ]);
    }

    /**
     * ディレクトリを再帰的にスキャンしてクラスを検出
     */
    private function discoverClasses(string $dir, string $namespace): array
    {
        $classes = [];

        if (!is_dir($dir)) {
            return $classes;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                if (in_array($item, $this->excluded_dirs, true)) {
                    continue;
                }

                $sub_namespace = $namespace . '\\' . $item;
                $classes = array_merge($classes, $this->discoverClasses($path, $sub_namespace));
            } elseif (is_file($path) && pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                if (in_array($item, $this->excluded_files, true)) {
                    continue;
                }

                $class_name = $namespace . '\\' . pathinfo($item, PATHINFO_FILENAME);
                $classes[] = $class_name;
            }
        }

        return $classes;
    }

    /**
     * クラスを初期化
     */
    private function initializeClass(string $class_name): void
    {
        if (!class_exists($class_name)) {
            return;
        }

        try {
            $instance = new $class_name();

            if (method_exists($instance, '__invoke')) {
                $instance();
            }
        } catch (\Exception $e) {
            error_log(
                sprintf(
                    'Failed to initialize class %s: %s',
                    $class_name,
                    $e->getMessage()
                )
            );
        }
    }
}