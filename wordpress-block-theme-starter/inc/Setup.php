<?php

namespace ThemeName;

class Setup
{
    private array $excluded_dirs = ['Contracts', 'Traits', 'Interfaces', 'Exceptions'];

    private array $excluded_files = ['Setup.php', 'config.php', 'autoload.php'];

    public function __invoke(): void
    {
        $inc_dir = get_stylesheet_directory() . '/inc';
        $classes = $this->discoverClasses($inc_dir, 'ThemeName');

        foreach ($classes as $class_name) {
            $this->initializeClass($class_name);
        }
    }

    private function discoverClasses(string $dir, string $namespace): array
    {
        $classes = [];

        if (!is_dir($dir)) {
            return $classes;
        }

        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                if (in_array($item, $this->excluded_dirs, true)) {
                    continue;
                }
                $classes = array_merge(
                    $classes,
                    $this->discoverClasses($path, $namespace . '\\' . $item)
                );
            } elseif (is_file($path) && pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                if (in_array($item, $this->excluded_files, true)) {
                    continue;
                }
                $classes[] = $namespace . '\\' . pathinfo($item, PATHINFO_FILENAME);
            }
        }

        return $classes;
    }

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
            error_log(sprintf('Failed to initialize %s: %s', $class_name, $e->getMessage()));
        }
    }
}
