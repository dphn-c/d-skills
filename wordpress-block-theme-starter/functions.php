<?php

use ThemeName\Setup;

$autoload_path = get_stylesheet_directory() . '/vendor/autoload.php';

if (file_exists($autoload_path)) {
	require_once $autoload_path;

	try {
		if (class_exists(Setup::class)) {
			(new Setup())();
		}
	} catch (\Throwable $e) {
		error_log('ThemeName Setup Error: ' . $e->getMessage());
		if (defined('WP_DEBUG') && WP_DEBUG) {
			wp_die('ThemeName Setup Error: ' . $e->getMessage());
		}
	}
} else {
	error_log('ThemeName: vendor/autoload.php not found. Run composer install.');
}
