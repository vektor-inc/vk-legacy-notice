<?php
/**
 * VK Legacy Notice Setting
 *
 * @package VK Legacy Notice
 */

/**
 * VK Legacy Notice Setting
 */
class VK_Legacy_Notice_Setting {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ), 10, 2 );
	}

	/**
	 * Admin Menu
	 */
	public static function add_admin_menu() {
		$page_title = 'VK Legacy Notice設定';
		$menu_title = 'VK Legacy Notice設定';
		$capability = 'administrator';
		$menu_slug  = 'vk-legacy-notice-setting';
		$function   = array( __CLASS__, 'setting_page' );
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
	}

	/**
	 * Setting Page
	 */
	public static function setting_page() {
		if ( ! isset( $_GET['check'] ) ) {
			$explain_text  = '<p class="vk-legacy-notice-paragraph">' . __( 'This plugin can chack old Setting after this sentense', 'vk-legacy-notice' ) . '</p>';
			$explain_text .= '<ul class="vk-legacy-notice-list">';
			$explain_text .= '<li>' . __( 'You use legacy functions, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy files, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy options, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '</ul>';
			$explain_text .= '<a href="'.admin_url().'?page=vk-legacy-notice-setting&check=result" class="button button-primary">' . __( 'Check legacies now!', 'vk-legacy-notice' ) . '</a>';

			echo wp_kses_post( $explain_text );
		} else if (  isset( $_GET['check'] ) && $_GET['check'] === "result" ) {
			$args = array(
				'post_type'  => 'any',
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'page-onecolumn.php',
			);

			$legacy_posts = get_posts( $args );

			if ( ! empty( $legacy_posts ) ) {

				echo '<p class="legacy-notice">' . esc_html__( 'Legacy template \'page-onecolumn.php\' is using', 'vk-legacy-notice' );
				echo '<ul>';
				foreach ( $legacy_posts as $legacy_post ) {
					$legacy_post_list  = '<li>';
					$legacy_post_list .= get_post_type_object( $legacy_post->post_type )->labels->singular_name;
					$legacy_post_list .= ' <a href="' . esc_url( get_edit_post_link( $legacy_post->ID ) ) . '">';
					$legacy_post_list .= esc_html( $legacy_post->post_title );
					$legacy_post_list .= '</a> ';
					$legacy_post_list .= esc_html__( 'is used legacy template ( page-onecolumn.php ).', 'vk-legacy-notice' );
					$legacy_post_list .= '</li>';
					echo wp_kses_post( $legacy_post_list );
				}
				echo '</ul>';
			}
		}

	}

}
new VK_Legacy_Notice_Setting();
