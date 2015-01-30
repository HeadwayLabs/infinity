<?php

/**
 * Admin class
 *
 * @package View_Builder
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'View_Builder_Admin' ) ) :
class View_Builder_Admin {

	/**
	 * Admin Setup
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function init() {

		add_action( 'admin_menu', array( __CLASS__, 'remove_publish_meta_box' ) );
		add_action( 'dbx_post_sidebar', array( __CLASS__, 'view_save_button' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'post_updated_messages', array( __CLASS__, 'view_updated_messages' ) );
		add_filter( 'screen_layout_columns', array( __CLASS__, 'view_screen_layout_columns' ), 10, 2 );
		add_filter( 'script_loader_src', array( __CLASS__, 'disable_autosave' ), 10, 2 );
		add_action( 'edit_form_top', array( __CLASS__, 'link_to_customizer' ) );


		require_once( views()->plugin_dir .'includes/view-meta.php' );

	}

	/**
	 * Disable autosave
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function disable_autosave( $src, $handle ) {
		if( 'autosave' == $handle && 'view' == get_current_screen()->id )
			return '';

		return $src;
	}

	public static function enqueue() {
		global $pagenow, $typenow;
		if (empty($typenow) && !empty($_GET['post'])) {
		  $post = get_post($_GET['post']);
		  $typenow = $post->post_type;
		}
		if ( $pagenow == 'post.php' && $typenow=='view') {

			wp_enqueue_style('view-admin-css', views()->plugin_url . 'includes/admin/assets/css/views.min.css');
			wp_enqueue_script('vb_like_post', views()->plugin_url . 'includes/admin/assets/js/views.js', array('jquery'), '1.0', 1 );

		}
	}

	/**
	 * Links to customizer
	 * @package View_Builder
	 * @since 0.4
	 */
	public static function link_to_customizer() {

		if ( 'view' != get_current_screen()->id )
			return;

		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			echo '<a id="open-customizer" href="' . wp_customize_url( get_template() ) . '" target="_blank" class="load-customize hide-if-no-customize button button-primary">'
		. __( 'Open Customizer' ) . '</a>';
		}

	}

	/**
	 * Add save button to the view screen
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_save_button() {
		if ( 'view' != get_current_screen()->id )
			return;

		$attr = array(
			'tabindex' => '5',
			'accesskey' => 'p'
		);
		submit_button( __( 'Save View' ), 'primary', 'publish', false, $attr );

	}

	/**
	 * Hide the screen options from the loop edit screen
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_screen_layout_columns( $columns, $screen_id ) {
		if ( 'view' == $screen_id )
			add_screen_option( 'layout_columns', array( 'max' => 1, 'default' => 1 ) );

		return $columns;
	}

	/**
	 * Remove publish metabox from the loop edit screen
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function remove_publish_meta_box() {
		remove_meta_box( 'submitdiv', 'view', 'side' );
	}

	/**
	 * Messages displayed when a loop is updated.
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_updated_messages( $messages ) {

		$messages['view'] = array(
			1 => sprintf( __( 'View updated.' ), esc_url( get_permalink() ) ),
			4 => __( 'View updated.'),
			6 => sprintf( __( 'View saved.' ), esc_url( get_permalink() ) ),
			7 => __( 'View saved.' )
		);

		return $messages;
	}

}
endif;

/**
 * Initiate admin
 *
 * @package View_Builder
 * @since 1.0.0
 */
function view_builder_admin() {
	View_Builder_Admin::init();
}
add_action ( 'wp_loaded', 'view_builder_admin' );

