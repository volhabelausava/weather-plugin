<?php
/**
 * Plugin Name: Weather plugin
 */
defined('ABSPATH') or die('Access denied.');

if(file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once (__DIR__ . '/vendor/autoload.php');
}

define ('PLUGIN', plugin_basename(__FILE__));
define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));

use Weather\Activate;
use Weather\Deactivate;
use Weather\Init;

/**
 * Activate the plugin.
 */
function weather_activate() {
    Activate::activate();
}
register_activation_hook(__FILE__, 'weather_activate');

/**
 * Deactivate the plugin.
 */
function weather_deactivate() {
    Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'weather_deactivate');

/**
 * Run the plugin.
 */
if (class_exists(Init::class)) {
    Init::run();
}

