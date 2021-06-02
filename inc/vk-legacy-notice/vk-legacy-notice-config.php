<?php
/**
 * VK Legacy Notice Config
 *
 * @package VK Legacy Notice
 */

if ( ! class_exists( 'VK_Legacy_Notice' ) ) {
	global $vk_legacy_ignore_link;
	$vk_legacy_ignore_link = 'legacy-check-ignore';
	require_once dirname( __FILE__ ) . '/package/class-vk-legacy-notice.php';
}
