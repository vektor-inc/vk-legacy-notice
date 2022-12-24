<?php
/**
 * Plugin Name: VK Legacy Notice
 * Plugin URI: https://lightning.nagoya/
 * Description: This plugin notices legacy functions.
 * Version: 0.1.0
 * Author:  Vektor,Inc.
 * Author URI: https://lightning.nagoya/
 * Text Domain: vk-legacy-notice
 * Domain Path: /languages
 * License: GPL 2.0 or Later
 *
 * @package VK Legacy Notice
 */

defined( 'ABSPATH' ) || exit;

global $plugin_version;
$plugin_data    = get_file_data( __FILE__, array( 'version' => 'Version' ) );
$plugin_version = $plugin_data['version'];

load_plugin_textdomain( 'vk-legacy-notice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
require_once plugin_dir_path( __FILE__ ) . 'inc/vk-admin/vk-admin-config.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/vk-legacy-notice/vk-legacy-notice-config.php';

/**
 * Updater
 */
if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	$myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/vektor-inc/vk-legacy-notice',
		__FILE__,
		'vk-legacy-notice'
	);
}
