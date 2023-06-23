<?php
/**
 * Setting Page
 *
 * @package VK Legacy Notice
 */

use VektorInc\VK_Admin\VkAdmin;
VkAdmin::init();

$admin_pages = array( 'settings_page_vk_legacy_notive_plugin_options' );
VkAdmin::admin_scripts( $admin_pages );

/**
 * Setting Page
 */
function vk_legacy_notice_add_custom_setting_page() {
	$get_page_title = __( 'VK Legacy Notice', 'vk-legacy-notice' );
	$get_logo_html  = '';
	$get_menu_html  = '<li><a href="#check-legacy-setting">' . __( 'Check Legacy Setting', 'vk-legacy-notice' ) . '</a></li>';

	VkAdmin::admin_page_frame( $get_page_title, array( 'VK_Legacy_Notice', 'setting_page' ), $get_logo_html, $get_menu_html );
}