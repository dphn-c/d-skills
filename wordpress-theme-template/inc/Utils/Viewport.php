<?php

namespace ThemeName\Utils;

class Viewport
{
    public static function getInitialViewport(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $is_tablet = preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua);

        if ($is_tablet) {
            return 'width=1366';
        }

        return 'width=device-width, initial-scale=1';
    }
}
