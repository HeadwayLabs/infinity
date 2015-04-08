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

		add_action( 'admin_menu', array( __CLASS__, 'view_remove_publish_meta_box' ) );
		add_action( 'dbx_post_sidebar', array( __CLASS__, 'view_save_button' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'view_enqueue' ) );
		add_filter( 'post_updated_messages', array( __CLASS__, 'view_updated_messages' ) );
		add_filter( 'screen_layout_columns', array( __CLASS__, 'view_screen_layout_columns' ), 10, 2 );
		add_filter( 'script_loader_src', array( __CLASS__, 'view_disable_autosave' ), 10, 2 );
		add_filter( 'enter_title_here', array( __CLASS__, 'view_change_title_text' ) );
		add_action( 'edit_form_top', array( __CLASS__, 'view_add_toolbar' ) );
		add_action( 'admin_head', array( __CLASS__, 'view_hide_post_box_on_add_screen' ) );
		add_action( 'admin_notices', array( __CLASS__, 'view_add_help_to_add_screen' ) );
		
		add_action( 'admin_head', array( __CLASS__, 'view_add_tinymce' ) );
		add_action( 'before_wp_tiny_mce', array( __CLASS__, 'views_mce_head' ) );



		require_once( views()->plugin_dir .'includes/view-meta.php' );

	}

	/**
	 * Disable autosave
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_disable_autosave( $src, $handle ) {
		if( 'autosave' == $handle && 'view' == get_current_screen()->id )
			return '';

		return $src;
	}

	public static function view_enqueue() {
		global $pagenow, $typenow;
		if (empty($typenow) && !empty($_GET['post'])) {
		  $post = get_post($_GET['post']);
		  $typenow = $post->post_type;
		}

		if ($typenow=='view') {

			if ( $pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) {

				wp_enqueue_style('view-admin-css', views()->plugin_url . 'includes/admin/assets/css/views.min.css');
				wp_enqueue_script('vb_like_post', views()->plugin_url . 'includes/admin/assets/js/min/views-min.js', array('jquery'), '1.0', 1 );

			}

		}

	}

	/**
	 * Links to customizer
	 * @package View_Builder
	 * @since 0.4
	 */
	public static function view_add_toolbar() {

		$screen = get_current_screen();

		global $post;

		if ( 'view' != $screen->id || $screen->action == 'add')
			return;

		echo '<div class="start-message"><p>Welcome to your new view. The next step is to add this view to your website. The toolbar below links to the places you will add this view in Wordpress. Once you have added it, you can access the Customizer to build your layouts. You can also enable query mode below to filter specific wordpress content.<a href="#" class="close-start-message"><span class="dashicons dashicons-no-alt"></span></a></p></div>';

		echo '<div id="views-toolbar">';

			if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
				echo '<a id="open-customizer" href="' . wp_customize_url( get_stylesheet() ) . '" title="Open Wordpress Customizer to start editing this View." target="_blank" class="load-customize hide-if-no-customize open-customizer toolbar-wrap"><span class="dashicons dashicons-admin-appearance"></span></a>';
			}

			$id = $post->ID;

			$options = vb_options( $id );

			$query_mode = $options['query-mode'];

			echo '<div class="add-toolbar toolbar-wrap"">';

				if ( $query_mode == 1 && current_user_can( 'edit_theme_options' ) ) {
					echo '<a id="open-posts" href="edit.php" title="Open Posts to add this view to a post" target="_blank" class="load-customize hide-if-no-customize open-posts"><span class="dashicons dashicons-admin-post"></span></a>';
					echo '<a id="open-posts" href="edit.php?post_type=page" title="Open Posts to add this view to a post" target="_blank" class="load-customize hide-if-no-customize open-posts"><span class="dashicons dashicons-admin-page"></span></a>';
				}

				//disable for default mode
				if ( $query_mode == 0 && current_user_can( 'edit_theme_options' ) ) {
					echo '<p id="open-posts" title="Disabled: You can\'t add a view to a page when custom query is off. With the query off it will try load the same page you add it to inside itself since default mode does what wordpress does." target="_blank" class="disabled load-customize hide-if-no-customize open-page"><span class="dashicons dashicons-admin-post"></span></p>';
					echo '<p id="open-posts" title="Disabled: You can\'t add a view to a post when custom query is off. With the query off it will try load the same post you add it to inside itself since default mode does what wordpress does." target="_blank" class="disabled load-customize hide-if-no-customize open-posts"><span class="dashicons dashicons-admin-page"></span></p>';
				}
				if ( current_user_can( 'edit_theme_options' ) ) {
					echo '<a id="open-widgets" href="widgets.php" title="Open Widgets to add a widget for this view" target="_blank" class="load-customize hide-if-no-customize open-widgets"><span class="dashicons dashicons-welcome-widgets-menus"></span></a>';
				}
				if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
					echo '<a id="open-headway" href="' . home_url() . '/?visual-editor=true' . '" title="Open Headway to add a block for this view" target="_blank" class="load-customize hide-if-no-customize open-headway"><span></span></a>';
				}

			echo '</div>';

		echo '</div>';

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
	 * Hide the screen options from the view edit screen
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
	 * Remove publish metabox from the view edit screen
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_remove_publish_meta_box() {
		remove_meta_box( 'submitdiv', 'view', 'side' );
	}

	/**
	 * Messages displayed when a view is updated.
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

	/**
	 * Hides the edit metaboxes on create new view page
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_hide_post_box_on_add_screen() {
	    $screen = get_current_screen();
	    if ( $screen->action == 'add' ) {
	        echo '<script>
	        /* <![CDATA[ */
	        (function ($) {
	            $(document).ready(function() {
	                $(".postbox-container").hide();
	            });
	        })(jQuery);
				/* ]]> */
	        </script>';

	    }
	}

	public static function view_change_title_text( $title ){
     $screen = get_current_screen();
 
     if  ( 'view' == $screen->post_type ) {
          $title = 'Enter a name for your view';
     }
 
     return $title;
	}

	/**
	 * Adds help box to add new view screen 
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_add_help_to_add_screen() {
	    $screen = get_current_screen();
	    if ( 'view' == $screen->post_type && $screen->action == 'add' ) {
	        echo '
					<div class="view-create-new-notice-wrapper">
						<div class="view-create-new-notice">
							<div class="inner">
								<h1>Getting Started</h1>
								<p class="intro"><b>Are you unsure how to use Infinity?</b> Please take a moment to familiarize yourself with the process of creating, setting up and displaying your <a href="#" class="create-view">new infinite view.</a></p>	
						 		<div id="create-view-steps">
						 			<div class="step-column step-one">
						 				<h2>Name your view</h2>
						 				<p><b>Start by giving you view a name.</b></p>
						 				<img src="'. views()->plugin_url .'includes/admin/images/name-view.png" alt="Name your view">
						 				<em>eg: My Products or Latest Posts</em>
						 				<p>This view will save all your <b>Content Query</b> &amp; <b>Layout Settings</b> that generate the view.</p>
						 				<p>You will come back to this page to Query Content and Configure your Layouts such as Grids, Carousels, Sliders &amp; Masonry layouts.</p>
						 			</div>
						 			<div class="step-column step-two">
						 				<h2>Query Content</h2>
						 				<p><b>Set the query mode & content.</b></p>
						 				<img src="'. views()->plugin_url .'includes/admin/images/query.png" alt="Enable Query Mode">
						 				<em>Show specific wordpress content.</em>
						 				<p>To display specific content from your Wordpress site, you must enable "Query Mode"</p>
						 				<p>When you first create a view. Query mode is off. The <b>relevant Wordpress content</b> for the page you are on will display.</p>
						 				<em>eg: categories, blog index, single page etc.</em>
						 			</div>
						 			<div class="step-column step-three">
						 				<h2>Add &amp; Customize</h2>
						 				<p><b>Now its ready to add and build.</b></p>
						 				<img src="'. views()->plugin_url .'includes/admin/images/add-customize.png" alt="Add Toolbar quick links to places to add a view">
						 				<em>First you add a view, then Customize it.</em>
						 				<p>There are several ways to add a view to your website depending on your needs. Then use the Wordpress Customizer to build your infinite view.</p>
						 				<ul>
						 					<li>Use Headway <div><em>Headway View Block displays any view.</em></div> </li>
						 					<li>Any Wordpress Content<div><em>Add view to any Wordpress posts or pages.</em></div> </li>
						 					<li>Using a Wordpress Widget <div><em>Use the Views Widget to display any view.</em></div> </li>
						 				</ul>
						 			</div>
						 		</div>
							</div>
					 		<a href="#" class="confirm-create-view create-view">I Understand - Create my new view now</a>
						</div>
					</div>
	        ';

	    }
	}


	/**
	 * Initiates mce button 
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_add_tinymce() {
	    global $typenow;

	    // only on Post Type: post and page
	    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
	        return ;

	    add_filter( 'mce_external_plugins', array( __CLASS__, 'view_add_tinymce_plugin' ) );
	    // Add to line 1 form WP TinyMCE
	    add_filter( 'mce_buttons', array( __CLASS__, 'view_add_tinymce_button' ) );
	}

	/**
	 *  Includes JS for tinymce
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_add_tinymce_plugin( $plugin_array ) {
	    $plugin_array['views'] = views()->plugin_url . 'includes/admin/assets/js/min/add-shortcode-min.js';
	    return $plugin_array;
	}

	/**
	 * Add the button key to use via JS
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function view_add_tinymce_button( $buttons ) {

	    array_push( $buttons, 'view_button_key' );
	    return $buttons;
	}

	/**
	 * Create JSON object with list of views
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function views_mce_head() {
		$all_views = vb_get_views();
		$views = array();
		foreach ( $all_views as $view ) {
			$views[] = array(
				'value'   => $view->ID,
				'text' => $view->post_title
			);
		}
		$views = json_encode( array(
			'views' => $views
		) );
	?>
	<style>
		i.mce-i-icon.infinity-icon {
		    background: url('../wp-content/plugins/infinity/includes/admin/images/infinity-tiny_mce-button.png') no-repeat center center;
		    background-size: 18px 8px;
		    padding: 0;
		    vertical-align: top;
		    speak: none;
		    -webkit-font-smoothing: antialiased;
		    -moz-osx-font-smoothing: grayscale;
		    margin-left: -2px;
		    padding-right: 2px
		}
	</style>
	<script type='text/javascript'>
	/* <![CDATA[ */
	var infinityViews = <?php echo $views; ?>;
	/* ]]> */
	</script>
<?php
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

