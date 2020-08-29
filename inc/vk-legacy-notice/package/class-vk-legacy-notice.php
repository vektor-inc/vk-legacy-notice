<?php
/**
 * VK Legacy Notice Setting
 *
 * @package VK Legacy Notice
 */

/**
 * VK Legacy Notice Setting
 */
class VK_Legacy_Notice {

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
		$custom_page = add_options_page(
			__( 'VK Legacy Notice Setting', 'vk-legacy-notice' ),
			_x( 'VK Legacy Notice', 'label in admin menu', 'vk-legacy-notice' ),
			'edit_theme_options',
			'vk-legacy-notice',
			'vk_legacy_notice_add_custom_setting_page'
		);
		if ( ! $custom_page ) {
			return;
		}
	}

	/**
	 * Setting Page
	 */
	public static function setting_page() {

		echo '<h3 id="check-legacy-setting">' . esc_html__( 'Check Legacy Setting', 'vk-legacy-notice' ) . '</h3>';

		if ( isset( $_GET['check'] ) && 'result' === $_GET['check'] ) {
			// echo '<table class="form-table">';

			$perfect = true;

			$description_change_template = __('<ol><li>記事の編集画面に移動</li><li>ページ属性 > テンプレート を「デフォルトテンプレート」に変更</li><li>Lightning デザイン設定 > レイアウト設定 を「1カラム（サブセクション無し）」に変更</li>', 'vk-legacy-notice');
			$description_change_to_gutenberg = __('<li>ウィジェットやビルダーブラグインではなくブロックエディタでページを構成してください。</li>', 'vk-legacy-notice');
			
			$post_templates = array(
				array(
					'template'    => 'page-onecolumn.php',
					'alternative' => $description_change_template.'</ol>',
				),
				array(
					'template'    => 'page-lp-builder.php',
					'alternative' => $description_change_template . __( '<li>Lightning デザイン設定 > ページヘッダーとパンくずリスト を「表示しない」にチェック</li>', 'vk-legacy-notice' ).$description_change_to_gutenberg.'</ol>',
				),
				array(
					'template'    => 'page-lp.php',
					'alternative' => $description_change_template . __( '<li>Lightning デザイン設定 > ページヘッダーとパンくずリスト を「表示しない」にチェック</li>', 'vk-legacy-notice' ).$description_change_to_gutenberg.'</ol>',
				),
				array(
					'template'    => 'single.php',
					'alternative' => __( 'Delete single.php or Rename single.php as single-{ post type }.php.', 'vk-legacy-notice' ),
				),
				array(
					'template'    => 'page.php',
					'alternative' => __( 'Delete page.php.' ),
				),
			);

			foreach ( $post_templates as $post_template ) {

				$args = array(
					'post_type'  => 'any',
					'posts_per_page' => -1,
					'meta_key'   => '_wp_page_template',
					'meta_value' => $post_template['template'],
				);

				$legacy_posts = get_posts( $args );
				if ( ! empty( $legacy_posts ) ) {
					echo '<div class="adminMain_main_content">';
					echo '<h4 class="alert alert-danger">' . esc_html__( 'Legacy template', 'vk-legacy-notice' ) . ' ( ' . esc_html( $post_template['template'] ) . ' ) ' . esc_html__( 'is used', 'vk-legacy-notice' ) . '</h4>';
					echo '<h5>' . __('対応方法', 'vk-legacy-notice' ) . '</h5>';
					
					echo wp_kses_post( $post_template['alternative'] );
					echo '<h5>' . __('対象箇所', 'vk-legacy-notice' ) . '</h5>';
					echo '<ul>';
					foreach ( $legacy_posts as $legacy_post ) {
						$legacy_post_list  = '<li>';
						$legacy_post_list .= get_post_type_object( $legacy_post->post_type )->labels->singular_name;
						$legacy_post_list .= ' <a href="' . esc_url( get_edit_post_link( $legacy_post->ID ) ) . '" target="_blank">';
						$legacy_post_list .= esc_html( $legacy_post->post_title );
						$legacy_post_list .= '</a> ';
						$legacy_post_list .= '</li>';
						echo wp_kses_post( $legacy_post_list );
					}
					echo '</ul>';
					echo '</div>';
					$perfect = false;
				}
			}
			if ( true === $perfect ) {
				echo '<p>' . esc_html__( 'Congratulations! There is no legacy setting.', 'vk-legacy-notice' ) . '</p>';
			}
			// echo '</table>';
			echo '<a href="' . admin_url() . 'options-general.php?page=vk-legacy-notice" class="button button-primary">' . __( 'Back to check page', 'vk-legacy-notice' ) . '</a>';
		} else {
			$explain_text  = '<table class="form-table">';
			$explain_text .= '<tr><th>' . __( 'This plugin can chack old Setting after this sentense', 'vk-legacy-notice' ) . '</th></tr>';
			$explain_text .= '<tr><td><ul class="vk-legacy-notice-list">';
			$explain_text .= '<li>' . __( 'You use legacy functions, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy files, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy options, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '</ul></td></tr></table>';
			$explain_text .= '<a href="' . admin_url() . 'options-general.php?page=vk-legacy-notice&check=result" class="button button-primary">' . __( 'Check legacies now!', 'vk-legacy-notice' ) . '</a>';

			echo wp_kses_post( $explain_text );

		}
	}

}
new VK_Legacy_Notice();
