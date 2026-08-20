<?php

namespace ThemeName\Blocks;

class Blocks
{
    private const EDITOR_SCRIPT = 'my-theme-editor';

    public function __invoke(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function register(): void
    {
        $this->registerEditorScript();

        $render_names = $this->registerBlockDir(
            get_theme_file_path('theme-blocks/render'),
            true,
            2
        );
        $this->registerBlockDir(
            get_theme_file_path('theme-blocks/bundled'),
            false,
            1
        );

        if (wp_script_is(self::EDITOR_SCRIPT, 'registered')) {
            wp_localize_script(self::EDITOR_SCRIPT, 'myThemeEditor', [
                'names' => $render_names,
            ]);
        }
    }

    private function registerEditorScript(): void
    {
        $editor_js = get_theme_file_path('theme-blocks/editor.js');

        if (!file_exists($editor_js)) {
            return;
        }

        wp_register_script(
            self::EDITOR_SCRIPT,
            get_theme_file_uri('theme-blocks/editor.js'),
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render'],
            filemtime($editor_js),
            true
        );
    }

    /**
     * @param int $max_depth 1 = flat, 2 = one group folder under render/
     * @return list<string>
     */
    private function registerBlockDir(string $base, bool $attach_editor_script, int $max_depth = 1): array
    {
        $names = [];

        if (!is_dir($base)) {
            return $names;
        }

        foreach ($this->findBlockDirs($base, $max_depth) as $dir) {
            $args = [];
            if ($attach_editor_script && wp_script_is(self::EDITOR_SCRIPT, 'registered')) {
                $args['editor_script_handles'] = [self::EDITOR_SCRIPT];
            }

            $type = register_block_type($dir, $args);
            if ($type instanceof \WP_Block_Type) {
                $names[] = $type->name;
            }
        }

        return $names;
    }

    /**
     * @return list<string>
     */
    private function findBlockDirs(string $base, int $max_depth): array
    {
        $found = [];
        $this->collectBlockDirs($base, 1, $max_depth, $found);

        return $found;
    }

    /**
     * @param list<string> $found
     */
    private function collectBlockDirs(string $dir, int $depth, int $max_depth, array &$found): void
    {
        if ($depth > $max_depth) {
            return;
        }

        foreach (glob($dir . '/*', GLOB_ONLYDIR) ?: [] as $child) {
            if (file_exists($child . '/block.json')) {
                $found[] = $child;
                continue;
            }

            $this->collectBlockDirs($child, $depth + 1, $max_depth, $found);
        }
    }
}
