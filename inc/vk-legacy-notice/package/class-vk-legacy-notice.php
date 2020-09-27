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
		add_action( 'admin_notices', array( __CLASS__, 'add_admin_notice' ) );
		add_action( 'admin_init', array( __CLASS__, 'add_admin_init' ) );
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
	 * Check List
	 *
	 * @param string $mode check mode.
	 */
	public static function check_list( $mode ) {
		$perfect = true;

		$legacy_description = '';

		$description_change_template  = '<li>' . __( '記事の編集画面に移動', 'vk-legacy-notice' ) . '</li>';
		$description_change_template .= '<li>' . __( 'ページ属性 > テンプレート を「デフォルトテンプレート」に変更', 'vk-legacy-notice' ) . '</li>';
		$description_change_template .= '<li>' . __( 'Lightning デザイン設定 > レイアウト設定 を「1カラム（サブセクション無し）」に変更', 'vk-legacy-notice' ) . '</li>';

		$description_for_lp = '<li>' . __( 'Lightning デザイン設定 > ページヘッダーとパンくずリスト を「表示しない」にチェック', 'vk-legacy-notice' ) . '</li>';

		$description_change_to_gutenberg = '<li>' . __( 'ウィジェットやビルダーブラグインではなく固定ページ内にブロックエディタでページを構成してください。', 'vk-legacy-notice' ) . '</li>';

		$post_templates = array(
			array(
				'template'    => 'page-onecolumn.php',
				'alternative' => '<ol>' . $description_change_template . '</ol>',
			),
			array(
				'template'    => 'page-lp-builder.php',
				'alternative' => '<ol>' . $description_change_template . $description_for_lp . $description_change_to_gutenberg . '</ol>',
			),
			array(
				'template'    => 'page-lp.php',
				'alternative' => '<ol>' . $description_change_template . $description_for_lp . $description_change_to_gutenberg . '</ol>',
			),

			/*
			Is it need under?
			array(
				'template'    => 'single.php',
				'alternative' => __( 'Delete single.php or Rename single.php as single-{ post type }.php.', 'vk-legacy-notice' ),
			),
			array(
				'template'    => 'page.php',
				'alternative' => __( 'Delete page.php.' ),
			),
			*/
		);

		foreach ( $post_templates as $post_template ) {

			$args = array(
				'post_type'      => 'any',
				'posts_per_page' => -1,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => $post_template['template'],
			);

			$legacy_posts = get_posts( $args );
			if ( ! empty( $legacy_posts ) ) {
				$legacy_description .= '<div class="adminMain_main_content">';
				// translators: Legacy template ( template for page ) is used.
				$legacy_description .= '<h4 class="alert alert-danger">' . sprintf( __( 'Legacy template ( %s ) is used.', 'vk-legacy-notice' ), $post_template['template'] ) . '</h4>';
				$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
				$legacy_description .= $post_template['alternative'];
				$legacy_description .= '<h5>' . __( '対象箇所', 'vk-legacy-notice' ) . '</h5>';
				$legacy_description .= '<ul>';
				foreach ( $legacy_posts as $legacy_post ) {
					$legacy_post_list  = '<li>';
					$legacy_post_list .= get_post_type_object( $legacy_post->post_type )->labels->singular_name;
					$legacy_post_list .= ' : <a href="' . esc_url( get_edit_post_link( $legacy_post->ID ) ) . '" target="_blank">';
					$legacy_post_list .= esc_html( $legacy_post->post_title );
					$legacy_post_list .= '</a> ';
					$legacy_post_list .= '</li>';

					$legacy_description .= $legacy_post_list;
				}
				$legacy_description .= '</ul>';
				$legacy_description .= '</div>';

				$perfect = false;
			}
		}

		// Lightning 関連.
		if ( function_exists( 'lightning_get_theme_name' ) ) {

			// Lightning Advanced Slider が有効な場合.
			if ( function_exists( 'las_plugin_active' ) ) {
				$legacy_description .= '<div class="adminMain_main_content">';
				$legacy_description .= '<h4 class="alert alert-danger">' . __( '「Lightning Advanced Slider」が有効化されています。', 'vk-legacy-notice' ) . '</h4>';
				$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
				$legacy_description .= '<ul>';
				$legacy_description .= '<li>' . __( 'Bootsrap 4 ベースのスキンをご利用の場合、標準で「フェード」が使用可能です。', 'vk-legacy-notice' ) . '</li>';
				$legacy_description .= '<li>' . __( 'VK Blocks Pro ご利用の場合、スライダーブロックを使用することで同等以上のことが可能です。', 'vk-legacy-notice' ) . '</li>';
				$legacy_description .= '</ul>';
				$legacy_description .= '</div>';

				$perfect = false;
			}

			// スキン関連.
			if ( class_exists( 'Lightning_Design_Manager' ) ) {

				// BS3 版スキンを使用している場合.
				global $bootstrap;
				if ( '3' === $bootstrap ) {
					$legacy_description .= '<div class="adminMain_main_content">';
					$legacy_description .= '<h4 class="alert alert-danger">' . __( 'Bootsrap 3 ベースのスキンをご利用中です。', 'vk-legacy-notice' ) . '</h4>';
					$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
					$legacy_description .= '<ul>';
					$legacy_description .= '<li>' . __( 'Bootstrap 4 ベースのスキンをご利用ください。', 'vk-legacy-notice' ) . '</li>';
					$legacy_description .= '</ul>';
					$legacy_description .= '</div>';

					$perfect = false;
				}

				// Fort の明フッターを使用している場合.
				$current_skin = get_option( 'lightning_design_skin' );
				if ( 'fort-bs4-footer-light' === $current_skin || 'fort2' === $current_skin ) {
					$legacy_description .= '<div class="adminMain_main_content">';
					$legacy_description .= '<h4 class="alert alert-danger">' . __( 'Fort のフッターが明るいバージョンをご利用中です。', 'vk-legacy-notice' ) . '</h4>';
					$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
					$legacy_description .= '<ul>';
					$legacy_description .= '<li>' . __( 'フッターカラー変更機能で同等以上のことができます。', 'vk-legacy-notice' ) . '</li>';
					$legacy_description .= '</ul>';
					$legacy_description .= '</div>';

					$perfect = false;
				}
			}

			// Old File Check.
			$old_file_array = array(
				'module_loop_post_meta' => array(
					'file' => 'module_loop_post_meta.php',
					'path' => 'template-parts/post/',
					'name' => 'meta.php',
				),
				'module_loop_post'      => array(
					'file' => 'module_loop_post.php',
					'path' => 'template-parts/post/',
					'name' => 'loop-post.php',
				),
				'module_pageTit'        => array(
					'file' => 'module_pageTit.php',
					'path' => 'template-parts/',
					'name' => 'page-header.php',
				),
				'module_panList'        => array(
					'file' => 'module_panList.php',
					'path' => 'template-parts/',
					'name' => 'breadcrumb.php',
				),
				'module_slide'          => array(
					'file' => 'module_slide.php',
					'path' => 'template-parts/',
					'name' => 'slide-bs3.php',
				),
			);
			foreach ( $old_file_array as $old_file ) {
				$old_file_path = get_stylesheet_directory() . '/' . $old_file['file'];
				if ( file_exists( $old_file_path ) ) {
					$legacy_description .= '<div class="adminMain_main_content">';
					// translators: ( Leyacy file ) is exists.
					$legacy_description .= '<h4 class="alert alert-danger">' . sprintf( __( '%s が存在しています。', 'vk-legacy-notice' ), $old_file['file'] ) . '</h4>';
					$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
					$legacy_description .= '<ol>';
					// translators: move the file into (path).
					$legacy_description .= '<li>' . sprintf( __( 'ファイルを %s ディレクトリ内に移動してください。', 'vk-legacy-notice' ), $old_file['path'] ) . '</li>';
					// translators: rename the file to (name).
					$legacy_description .= '<li>' . sprintf( __( 'その後、ファイル名を %s に変更してください。', 'vk-legacy-notice' ), $old_file['name'] ) . '</li>';
					$legacy_description .= '</li>';
					$legacy_description .= '</ol>';
					$legacy_description .= '</div>';

					$perfect = false;
				}
			}

			// Old option check.
			$options = get_option( 'lightning_theme_options' );

			$widget_front_pr_alternative  = '<li>';
			$widget_front_pr_alternative .= __( 'トップページに指定した固定ページ内に下記のいずれかを設置してください。', 'vk-legacy-notice' );
			$widget_front_pr_alternative .= '<ul>';
			$widget_front_pr_alternative .= '<li>' . __( 'VK Blocks Pro のアイコンカードブロック', 'vk-legacy-notice' ) . '</li>';
			$widget_front_pr_alternative .= '<li>' . __( 'VK Blocks の PR Blocks ブロック', 'vk-legacy-notice' ) . '</li>';
			$widget_front_pr_alternative .= '</ul>';
			$widget_front_pr_alternative .= '</li>';

			$widget_full_wide_title_alternative  = '<li>';
			$widget_full_wide_title_alternative .= __( '固定ページ内に下記のいずれかのブロックを全幅で配置してください。', 'vk-legacy-notice' );
			$widget_full_wide_title_alternative .= '<ul>';
			$widget_full_wide_title_alternative .= '<li>' . __( 'カバーブロック', 'vk-legacy-notice' ) . '</li>';
			$widget_full_wide_title_alternative .= '<li>' . __( 'VK Blocks Pro のアウターブロック', 'vk-legacy-notice' ) . '</li>';
			$widget_full_wide_title_alternative .= '</ul>';
			$widget_full_wide_title_alternative .= '</li>';
			$widget_full_wide_title_alternative .= '<li>';
			$widget_full_wide_title_alternative .= __( 'その中に VK Blocks の見出しブロックを配置してください。', 'vk-legacy-notice' );
			$widget_full_wide_title_alternative .= '</li>';

			$customize_panel = __( '外観 ＞ カスタマイズ > Lightning 機能設定', 'vk-legacy-notice' );

			$check_options = array(
				'widget_front_pr'        => array(
					'label'       => __( 'Front Page PR Block', 'vk-legacy-notice' ),
					'option'      => 'widget_front_pr',
					'alternative' => $widget_front_pr_alternative,
					'panel'       => $customize_panel,
				),
				'widget_full_wide_title' => array(
					'label'       => __( 'Full Wide Title Widget', 'vk-legacy-notice' ),
					'option'      => 'widget_front_pr',
					'alternative' => $widget_full_wide_title_alternative,
					'panel'       => $customize_panel,
				),
			);

			foreach ( $check_options as $check_option ) {
				if ( empty( $options['disable_functions'][ $check_option['option'] ] ) ) {
					$legacy_description .= '<div class="adminMain_main_content">';
					// translators: %s is old function.
					$legacy_description .= '<h4 class="alert alert-danger">' . sprintf( __( '%s は古い機能です。', 'vk-legacy-notice' ), $check_option['label'] ) . '</h4>';
					$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
					$legacy_description .= '<ol>';
					$legacy_description .= $check_option['alternative'];
					// translators: Check disable options.
					$legacy_description .= '<li>' . sprintf( __( '%1$sから %2$s にチェックを入れてください。', 'vk-legacy-notice' ), $check_option['panel'], $check_option['label'] ) . '</li>';
					$legacy_description .= '</ol>';
					$legacy_description .= '</div>';

					$perfect = false;
				}
			}
		} // Lightning 関連 END.

		// Font Awesome.
		if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
			$options = Vk_Font_Awesome_Versions::get_option_fa();
			$prefix  = '';
			if ( function_exists( 'lightning_get_prefix_customize_panel' ) ) {
				$prefix = lightning_get_prefix_customize_panel();
			} elseif ( function_exists( 'katawara_get_prefix_customize_panel' ) ) {
				$prefix = katawara_get_prefix_customize_panel();
			}
			if ( empty( $options ) || ! empty( $options ) && '4.7' === $options ) {
				$legacy_description .= '<div class="adminMain_main_content">';
				$legacy_description .= '<h4 class="alert alert-danger">' . __( 'Font Awesome 4.7 が使われています', 'vk-legacy-notice' ) . '</h4>';
				$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
				$legacy_description .= '<ol>';
				// translators: Theme Prefix.
				$legacy_description .= '<li>' . sprintf( __( '外観 ＞カスタマイズ > %s Font Awesome と進んでください。', 'vk-legacy-notice' ), $prefix ) . '</li>';
				$legacy_description .= '<li>' . __( 'その後、Font Awesome バージョン で Font Awesome 5.X を選択してください。', 'vk-legacy-notice' ) . '</li>';
				$legacy_description .= '</ol>';
				$legacy_description .= '</div>';

				$perfect = false;
			}
		}

		// Media Posts.
		if ( class_exists( 'Lightning_media_posts' ) ) {
			$enable_widgets = VEU_Widget_Control::enable_widget_ids();
			$prefix         = lightning_get_prefix_customize_panel();

			$legacy_description .= '<div class="adminMain_main_content">';
			$legacy_description .= '<h4 class="alert alert-danger">' . __( '旧 Media Posts が有効化されています。', 'vk-legacy-notice' ) . '</h4>';
			$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
			$legacy_description .= '<ol>';
			// translators: Theme Prefix.
			$legacy_description .= '<li>' . sprintf( __( '外観 ＞カスタマイズ > %s 機能設定 と進んでください。', 'vk-legacy-notice' ), $prefix ) . '</li>';
			$legacy_description .= '<li>' . __( 'その後「Media Posts を停止」にチェックを入れてください。', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '<li>';
			$legacy_description .= __( '下記のいずれかを使用することで同等のことが可能です。', 'vk-legacy-notice' );
			$legacy_description .= '<ul>';
			// translators: Theme Prefix.
			$legacy_description .= '<li>' . sprintf( __( '外観 ＞カスタマイズ > %s アーカイブ設定', 'vk-legacy-notice' ), $prefix ) . '</li>';
			$legacy_description .= '<li>' . __( 'Media Posts BS4ウィジェット', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '</ul>';
			$legacy_description .= '</li>';
			$legacy_description .= '</ol>';
			$legacy_description .= '<h5>' . __( '注意事項', 'vk-legacy-notice' ) . '</h5>';
			$legacy_description .= __( 'これを無効化することで下記ウィジェットエリアが使えなくなります', 'vk-legacy-notice' );
			$legacy_description .= '<ul>';
			$legacy_description .= '<li>' . __( 'トップページコンテンツエリア左', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '<li>' . __( 'トップページコンテンツエリア右', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '<li>' . __( 'トップページコンテンツエリア下部', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '</ul>';
			$legacy_description .= __( 'しかし、下記の方法で代替可能です。', 'vk-legacy-notice' );
			$legacy_description .= '<ul>';
			$legacy_description .= '<li>' . __( '固定ページにカラムブロックを配置し、カラム内に投稿リストブロックなどを配置', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '<li>' . __( 'ウィジェットエリアを自作後アクションフックを使って配置', 'vk-legacy-notice' ) . '</li>';
			$legacy_description .= '</ul>';
			$legacy_description .= '</div>';

			$perfect = false;
		}

		// ExUnit.
		if ( class_exists( 'VEU_Widget_Control' ) ) {
			$enable_widgets = VEU_Widget_Control::enable_widget_ids();
			$prefix         = '';
			if ( in_array( '3pr_area', $enable_widgets, true ) ) {
				$legacy_description .= '<div class="adminMain_main_content">';
				$legacy_description .= '<h4 class="alert alert-danger">' . __( 'VK 3PR エリア ウィジェットが使われています。', 'vk-legacy-notice' ) . '</h4>';
				$legacy_description .= '<h5>' . __( '対応方法', 'vk-legacy-notice' ) . '</h5>';
				$legacy_description .= '<ol>';
				// translators: Theme Prefix.
				$legacy_description .= '<li>' . __( '固定ページ内に VK Blocks Pro のカードブロックを使用する事で同様の表示が可能です。', 'vk-legacy-notice' ) . '</li>';
				$legacy_description .= '<li>' . __( 'その後、ExUnit > ExUnit から VK 3PR エリア ウィジェットを無効化してください。 ', 'vk-legacy-notice' ) . '</li>';
				$legacy_description .= '</ol>';
				$legacy_description .= '</div>';

				$perfect = false;
			}
		}

		if ( 'hide_check' === $mode ) {
			return $perfect;
		} elseif ( 'display_check' === $mode ) {
			if ( true === $perfect ) {
				echo '<p>' . esc_html__( 'Congratulations! There is no legacy setting.', 'vk-legacy-notice' ) . '</p>';
			} else {
				echo wp_kses_post( $legacy_description );
			}
			$back_button  = '<a href="' . esc_url( admin_url() ) . 'options-general.php?page=vk-legacy-notice" class="button button-primary">';
			$back_button .= esc_html__( 'Back to check page', 'vk-legacy-notice' );
			$back_button .= '</a>';
			echo wp_kses_post( $back_button );
		}
	}

	/**
	 * Setting Page
	 */
	public static function setting_page() {

		echo '<h3 id="check-legacy-setting">' . esc_html__( 'Check Legacy Setting', 'vk-legacy-notice' ) . '</h3>';

		if ( isset( $_GET['check'] ) && 'result' === $_GET['check'] ) {
			self::check_list( 'display_check' );
		} else {
			$explain_text  = '<table class="form-table">';
			$explain_text .= '<tr><th>' . __( 'This plugin can chack old Setting after this sentense', 'vk-legacy-notice' ) . '</th></tr>';
			$explain_text .= '<tr><td><ul class="vk-legacy-notice-list">';
			$explain_text .= '<li>' . __( 'You use legacy functions, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy files, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '<li>' . __( 'You use legacy options, isn\'t it?', 'vk-legacy-notice' ) . '</li>';
			$explain_text .= '</ul></td></tr></table>';
			$explain_text .= '<a href="' . admin_url() . 'options-general.php?page=vk-legacy-notice&check=result" class="button button-primary">';
			$explain_text .= __( 'Check legacies now!', 'vk-legacy-notice' );
			$explain_text .= '</a>';

			echo wp_kses_post( $explain_text );

		}
	}

	/**
	 * Get Option
	 */
	public static function get_options() {
		$options = get_option( 'vk_legacy_notice_options' );
		$default = array(
			'checked_version' => '0.0.0',
			'check_flag'      => true,
		);
		$options = wp_parse_args( $options, $default );
		return $options;
	}

	/**
	 * Admin Notice
	 */
	public static function add_admin_notice() {
		global $plugin_version;
		global $pagenow;
		global $vk_legacy_ignore_link;
		$options = self::get_options();

		if ( 'index.php' === $pagenow && false === self::check_list( 'hide_check' ) && true === $options['check_flag'] ) {
			$text  = '<div class="notice notice-info"><p>';
			$text .= '<strong>VK Legacy Notice : </strong> ';
			$text .= __( '古い機能を使用しているようです。一度チェックしてみることをおすすめします。', 'vk-legacy-notice' );
			$text .= '<a href="' . admin_url() . 'options-general.php?page=vk-legacy-notice&check=result" class="button button-primary">';
			$text .= __( '今すぐチェックする！', 'vk-legacy-notice' );
			$text .= '</a> ';
			$text .= '<a href="?' . $vk_legacy_ignore_link . '" class="button button-secondary">';
			$text .= __( '今回は無視する', 'vk-legacy-notice' );
			$text .= '</a>';
			$text .= '</p></div>';
			echo wp_kses_post( $text );
		}
	}

	/**
	 * Admin init
	 */
	public static function add_admin_init() {
		global $plugin_version;
		global $vk_legacy_ignore_link;
		$options = get_option( 'vk_legacy_notice_options' );
		if ( isset( $_GET[ $vk_legacy_ignore_link ] ) ) {
			$options['check_flag'] = false;
		}
		if ( isset( $options['checked_version'] ) && $options['checked_version'] !== $plugin_version ) {
			$options['check_flag'] = true;
		}
		$options['checked_version'] = $plugin_version;
		update_option( 'vk_legacy_notice_options', $options );
	}
}
new VK_Legacy_Notice();
