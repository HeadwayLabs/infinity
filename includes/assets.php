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

if ( ! class_exists( 'View_Builder_Assets' ) ) :
class View_Builder_Assets {

	/**
	 * @var View_Builder_Assets Stores the instance of this class.
	 */
	private static $instance;

	private static $general_css;

	private static $images_css;

	/**
	 * View_Builder_Assets Instance
	 *
	 * Makes sure that there is only ever one instance of the HW Builder
	 *
	 * @since 1.0.0
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new View_Builder_Assets;
			self::$instance->init();

		}

		return self::$instance;

   }
    
	/**
	* A dummy constructor to prevent loading more than once.
	* @since 	1.0.0
 	* @see 	View_Builder_Assets::instance()
	*/
	private function __construct() { 
		// Do nothing here
	}

	/**
	 * A dummy magic method to prevent View_Builder_Assets from being cloned
	 */
	public function __clone() { wp_die( __( 'Cheatinâ€™ uh?' ) ); }

	/**
	 * A dummy magic method to prevent View_Builder_Assets from being unserialized
	 */
	public function __wakeup() { wp_die( __( 'Cheatinâ€™ uh?' ) ); }

	/**
	 * Admin Setup
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	public static function init() {
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );

		add_action( 'customize_controls_enqueue_scripts', array(__CLASS__, 'enqueueCustomizer' ) );

		add_action( 'customize_preview_init', array(__CLASS__, 'enqueueCustomizerPreview' ) );

		add_action('wp_ajax_dynamic_css', array( __CLASS__, 'dynamic_css') );
		add_action('wp_ajax_nopriv_dynamic_css', array( __CLASS__, 'dynamic_css') );

		add_action('wp_ajax_dynamic_js', array( __CLASS__, 'dynamic_js') );
		add_action('wp_ajax_nopriv_dynamic_js', array( __CLASS__, 'dynamic_js') );

		//minqueue
		define('MINQUEUE_OPTIONS', false);

		$options = get_option('ib_general_settings');

		$enable_performance = ( !isset( $options['enable-enqueue-performancez'] ) )  ? 'on' : $options['enable-enqueue-performancez'];

		if ( $enable_performance == 'on' )
			add_filter('minqueue_options', array( __CLASS__, 'min_queue_options') );

	}

	public static function min_queue_options($options) {

		$options = array(
			'cache_dir' => 'minqueue_cache',
			'styles_method' => 'manual',
			'styles_manual' => array(
				'general' => self::$general_css,
				'images' => self::$images_css,
			)
		);

		return $options;
	}


	public static function dynamic_js() {
		require( dirname(__FILE__) .'/assets/dynamic-js.php' );
		exit;
	}

	public static function dynamic_css() {
		require( dirname(__FILE__) .'/assets/dynamic-css.php' );
		exit;
	}

	public static function enqueueCustomizerPreview() {
		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'preview_scripts' ) );
	}

	public static function preview_scripts() {

		// wp_enqueue_script( 'jquery-ui-core' );
		// wp_enqueue_script( 'jquery-ui-resizable' );
		// global $wp_scripts;
		// $queryui = $wp_scripts->query('jquery-ui-core');
		// $url = "http://ajax.googleapis.com/ajax/libs/jqueryui/". $queryui->ver."/themes/smoothness/jquery-ui.css";
		// wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
		wp_enqueue_script('vb-preview', views()->plugin_url . 'includes/assets/js/preview.js', array(), views()->version, 1);
		wp_enqueue_style('vb-preview', views()->plugin_url . 'includes/assets/css/preview.css');

	}

	public static function enqueueCustomizer() {
		wp_enqueue_script('vb-customizer-js', views()->plugin_url . 'includes/admin/assets/js/min/customizer-min.js');
		wp_enqueue_style( 'vb-customizer-css', views()->plugin_url . 'includes/admin/assets/css/customizer.min.css');
	}

	public static function enqueue() {

		$enqueue_masonry_simple 	= null;
		$enqueue_carousel 			= null;
		$enqueue_blog 					= null;
		$enqueue_slider 				= null;
		$enqueue_image 				= null;
		$enqueue_likes 				= null;
		$enqueue_grid					= null;
		$enqueue_dashicons			= null;
		$is_infinite_scroll			= null;

		self::$general_css = array();
		self::$images_css = array();
		self::$general_css = array();

		$args = array(
			'post_type' => 'view',
			'posts_per_page'        => '-1',
		);

		$the_query = new WP_Query( $args );

		$meta_options = array();

		if ( $the_query->have_posts() ) :

			while($the_query->have_posts()) : $the_query->the_post();

				$id = $the_query->post->ID;

				$options = get_post_meta( $id, 'view_options', true );

				$meta_options[strtolower(str_replace(' ', '-', get_the_title()))] = $options;

				$view_name = strtolower(str_replace(' ', '-', get_the_title()));

				$builder_options = TitanFramework::getInstance( 'builder-options' );
				$layout = $builder_options->getOption( 'view-layout-' . $id . '' );
	
				$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );
				$spotlight = $builder_options->getOption( 'image-show-spotlight-hide-' . $view_name . '' );

				if (!empty($parts)) {

					if (in_array('image', $parts))
						$enqueue_image = true;

					if (in_array('likes', $parts))
						$enqueue_likes = true;

					if (in_array('post-format', $parts))
						$enqueue_dashicons = true;

				}

				if ($layout == 'simple-masonry')
					$enqueue_masonry_simple = true;

				if ($layout == 'carousel')
					$enqueue_carousel = true;

				if ($layout == 'blog')
					$enqueue_blog = true;

				if ($layout == 'slider') {
					$enqueue_slider = true;
					$enqueue_dashicons = true;
				}

				if ($layout == 'grid')
					$enqueue_grid = true;

				$is_infinite_scroll = $builder_options->getOption( 'pagination-infinite-' . $view_name . '' );

				//lets enqueue a css file for each view if it exists in layout
				$style_name = $builder_options->getOption( 'style-name-' . $view_name . '' );

				if($style_name)
					array_push(self::$general_css, 'vb-'.$style_name);

				if(!empty($style_name)) {
					
					//default styles
					$css_styles = array();
					if( file_exists( views()->plugin_dir . 'styles/'. $style_name .'.css' ) ) {
						wp_enqueue_style( 'vb-'. $style_name, views()->plugin_url . 'styles/'. $style_name .'.css');
					}

					//template styles
					if( file_exists( get_template_directory() . '/' . $style_name .'.css' ) ) {
						wp_enqueue_style( 'vb-'. $style_name, get_template_directory_uri() . '/' . $style_name .'.css');
					}

					//child theme styles
					if( file_exists( get_stylesheet_directory() . '/' . $style_name .'.css' ) ) {
						wp_enqueue_style( 'vb-'. $style_name, get_stylesheet_directory_uri() . '/' . $style_name .'.css');
					}

				}

				

			endwhile;

		endif;

		wp_reset_postdata();

		/* Enqueue dynamic styles and scripts for front end */

		wp_enqueue_style('vb-view-builder', views()->plugin_url . 'includes/assets/css/view-builder.min.css');
		array_push(self::$general_css, 'vb-view-builder');
		// wp_enqueue_script( 'vb-view-builder', views()->plugin_url . 'includes/assets/js/min/view-builder-min.js', array('jquery'), views()->version, 1 );

		if ( !is_admin() ) {

			wp_enqueue_style( 'vb-dynamic', admin_url('admin-ajax.php').'?action=dynamic_css');

			wp_enqueue_script( 'dynamic-js', admin_url('admin-ajax.php').'?action=dynamic_js', array('jquery'), views()->version, 1 );

			wp_localize_script( 'dynamic-js', 'builder_meta', $meta_options );

			// wp_enqueue_script( 'jquery-ui-core' );
			// wp_enqueue_script( 'jquery-ui-resizable' );
			// wp_enqueue_script('vb-resizable', views()->plugin_url . 'includes/assets/js/draggables.js', array(), views()->version, 1);
			// global $wp_scripts;
			// $queryui = $wp_scripts->query('jquery-ui-core');
			// $url = "http://ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";
			//	wp_enqueue_style('jquery-ui-smoothness', $url, false, null);

		}

		if ( $enqueue_likes ) {
			
			wp_enqueue_script( 'vb_like_post', views()->plugin_url .'includes/assets/js/min/post-like-min.js', array('jquery'), views()->version, 1 );
			wp_localize_script( 'vb_like_post', 'ajax_var', array(
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' )
				)
			);

		}

		if ( $enqueue_masonry_simple )
			wp_enqueue_script('vb-masonry', views()->plugin_url . 'layouts/assets/js/min/masonry-min.js', array('jquery'), views()->version, 1);

		if ( $enqueue_carousel || $enqueue_slider ) {

			wp_enqueue_script('vb-carousel', views()->plugin_url . 'layouts/assets/js/min/owl.carousel-min.js', array('jquery'), views()->version, 1);

			wp_enqueue_style('vb-owl-structure', views()->plugin_url . 'layouts/assets/css/owl.carousel.css');
			array_push(self::$general_css, 'vb-owl-structure');

		}

		if ( $enqueue_dashicons ) {
			wp_enqueue_style('dashicons');
			array_push(self::$general_css, 'dashicons');
		}

		if ( $enqueue_grid ) 
			wp_enqueue_script('vb-grid', views()->plugin_url . 'layouts/assets/js/min/grid-min.js', array('jquery'), views()->version);

		if ( $enqueue_image  ) {

			wp_enqueue_style('vb-animate', views()->plugin_url . 'includes/assets/css/animate.min.css');
			array_push(self::$images_css, 'vb-animate');
			wp_enqueue_style('vb-sinister', views()->plugin_url . 'includes/assets/css/sinister.min.css');
			array_push(self::$images_css, 'vb-sinister');
			wp_enqueue_style('vb-lightgallery', views()->plugin_url . 'includes/assets/css/lightGallery.min.css');
			array_push(self::$images_css, 'vb-lightgallery');
			
			wp_enqueue_script('vb-lightgallery', views()->plugin_url . 'includes/assets/js/min/lightGallery.min.js', array('jquery'), views()->version, 1);

		}

		if ( $is_infinite_scroll && $enqueue_grid || $enqueue_masonry_simple || $enqueue_blog)
			wp_enqueue_script('vb-infinite-scroll', views()->plugin_url . 'layouts/assets/js/min/jquery.infinitescroll.min.js', array('jquery'), views()->version);

		if ( $enqueue_likes || $enqueue_image ) {
			
			wp_enqueue_style('vb-font-awesome', views()->plugin_url . 'includes/assets/css/font-awesome.min.css');
			array_push(self::$images_css, 'vb-font-awesome');

		}

	}

	function carousel_js( $view_id ) {

	$options = views()->options;

	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$layout = $builder_options->getOption( 'view-layout-' . $view_id . '' );

	//number of items is equal to columns set
	$columns = $builder_options->getOption( 'postopts-columns-' . $view_id . '' );

	$responsive_js = null;

	/* Carousel Group - Responsive carousel options */
	if ( ! empty( $options['carousel-group'] ) ) {

		$meta_query = array();

		foreach ( $options['carousel-group'] as $option ) {

			$width = $option['carousel-width'];
			$items = ( isset( $option['carousel-items'] ) ) ? 'items:' . $option['carousel-items'] . ',': null; 
			$margin = ( isset( $option['carousel-margin'] ) ) ? 'margin:' . $option['carousel-margin'] . ',' : null;
			$loop = ( isset( $option['carousel-loop'] ) == true) ? 'loop:true,' : null;
			$center = ( isset( $option['carousel-center'] ) ) ? 'center:' . $option['carousel-center'] . ',' : 'center: false,';
			$nav = ( isset( $option['carousel-nav'] ) ) ? 'nav: true,' : 'nav: false,';
			//$slideby = ( isset( $option['carousel-slideby'] ) ) ? 'slideBy:' . $option['carousel-slideby'] . ',' : null;

			$touchdrag = ( isset( $option['carousel-touchdrag'] ) == true) ? 'touchDrag:' . $option['carousel-touchdrag'] . ',' : 'touchDrag: false,';
			$pulldrag = ( isset( $option['carousel-pulldrag'] ) == true) ? 'pullDrag:' . $option['carousel-pulldrag'] . ',' : 'pullDrag: false,';
			$auto_height = ( isset( $option['carousel-autoheight'] ) == true) ? 'autoHeight:' . $option['carousel-autoheight'] . ',' : 'autoHeight: false,';
			$dots = ( isset( $option['carousel-showdots'] ) == true) ? 'dots:' . $option['carousel-showdots'] . ',' : 'dots: false,';


			$responsive_js .= '
							' . $width . ': {
							' . $margin . '
							' . $loop . '
							' . $center . '
							' . $nav . '
							' . $items . '
							' . $touchdrag . '
							' . $pulldrag . '
							' . $auto_height . '
							' . $dots . '
							dotsEach: false,
							},
			';

		}

	} 

	//$responsive = (views()->options['carousel-responsive']) ? 'responsive:' .views()->options['carousel-responsive'] . ',' : '';

	$items = ($columns) ? 'items:' . $columns . ',' : ''; 
	$margin = $builder_options->getOption( 'postopts-post-spacing-' . $view_id . '' );
	$margin = ($margin) ? 'margin:' . $margin . ',' : '';
	$mousedrag = ( isset( $options['carousel-mousedrag'] ) == true ) ? 'mouseDrag:' . $options['carousel-mousedrag'] . ',' : 'mouseDrag: false,';
	$autoplay = ( isset( $options['carousel-autoplay'] ) == true ) ? 'autoplay:' . $options['carousel-autoplay'] . ',' : 'autoplay: false,';
	$autoplay_hover_pause = ( isset( $options['carousel-autoplay-hover-pause'] ) == true ) ? 'autoplayHoverPause:' . $options['carousel-autoplay-hover-pause'] . ',' : 'autoplayHoverPause: false,';
	$autoplay_timeout = ( isset( $options['carousel-autoplay-timeout'] ) ) ? 'autoplayTimeout:' . $options['carousel-autoplay-timeout'] . ',' : null;
	$auto_height = ( isset( $options['carousel-auto_height'] ) ) ? 'autoHeight:true,' : 'autoHeight:false,';

	$nav_text_prev = ( isset( $options['carousel-navtext-prev'] ) == true ) ? $options['carousel-navtext-prev'] : null;
	$nav_text_next = ( isset( $options['carousel-navtext-next'] ) == true ) ? $options['carousel-navtext-next'] : null;
	
	$nav_text = null;
	if( $nav_text_prev && $nav_text_next )
		$nav_text = "navText: ['". $nav_text_prev ."','". $nav_text_next ."'],";

	?>

	<script>

	(function ($) {
			$(document).ready(function() {

				$("#view-<?php echo views()->id; ?>.carousel").owlCarousel({
						<?php echo $items; ?>

						<?php echo $margin; ?>

						<?php echo $mousedrag; ?>

						<?php echo $autoplay; ?>

						<?php echo $autoplay_hover_pause; ?>

						<?php echo $autoplay_timeout; ?>

						<?php echo $nav_text; ?>

						<?php echo $auto_height; ?>

						responsive:true,
						responsive:{
							<?php echo $responsive_js; ?>
						}
					});

			});
		})(jQuery);

	</script>

	<?php
	}

	function slider_js( $view_id ) {

	$options = views()->options;

	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$layout = $builder_options->getOption( 'view-layout-' . $view_id . '' );

	$items = 'items:1,'; 
	$mousedrag = ( isset( $options['slider-mousedrag'] ) == true ) ? 'mouseDrag:' . $options['slider-mousedrag'] . ',' : 'mouseDrag: false,';
	$autoplay = ( isset( $options['slider-autoplay'] ) == true ) ? 'autoplay:' . $options['slider-autoplay'] . ',' : 'autoplay: false,';
	$autoplay_hover_pause = ( isset( $options['slider-autoplay-hover-pause'] ) == true ) ? 'autoplayHoverPause:' . $options['slider-autoplay-hover-pause'] . ',' : 'autoplayHoverPause: false,';
	$autoplay_timeout = ( isset( $options['slider-autoplay-timeout'] ) ) ? 'autoplayTimeout:' . $options['slider-autoplay-timeout'] . ',' : 'autoplayTimeout: false,';
	$nav_next_prev = ( isset( $options['slider-show-nav'] ) ) ? 'nav:' . $options['slider-show-nav'] . ',' : 'nav: false,';
	$dots_nav = ( isset( $options['slider-show-dotsnav'] ) ) ? 'dots:' . $options['slider-show-dotsnav'] . ',' : 'dots: false,';
	$animate_out = ( isset( $options['slider-animate-out'] ) ) ? 'animateOut:"' . $options['slider-animate-out'] . '",' : null;
	$animate_in = ( isset( $options['slider-animate-in'] ) ) ? 'animateIn:"' . $options['slider-animate-in'] . '",' : null;
	$slider_nav_type = ( isset( $options['slider-nav-type'] ) ) ? $options['slider-nav-type'] : 'dots';
	$slider_nav_thumb_height = ( isset( views()->options['slider-nav-thumb-height'] ) ) ? views()->options['slider-nav-thumb-height'] : '36';
	$nav_text_prev = ( isset( $options['slider-navtext-prev'] ) == true ) ? $options['slider-navtext-prev'] : null;
	$nav_text_next = ( isset( $options['slider-navtext-next'] ) == true ) ? $options['slider-navtext-next'] : null;
	
	$nav_text = null;
	if( $nav_text_prev && $nav_text_next )
		$nav_text = "navText: ['". $nav_text_prev ."','". $nav_text_next ."'],";

	$dots_data = null;
	if ( $slider_nav_type == 'numbers' || $slider_nav_type == 'thumbs' ) {
			$dots_data = 'dotData:true,';
	}

	$nav_position_class = ( isset( $options['slider-nav-position'] ) != '' ) ? $options['slider-nav-position'] : null;

	?>

	<script>

	(function ($) {
			$(document).ready(function() {

				$("#view-<?php echo views()->id; ?>.slider").owlCarousel({
						<?php echo $items; ?>

						<?php echo $mousedrag; ?>

						<?php echo $autoplay; ?>

						<?php echo $autoplay_hover_pause; ?>

						<?php echo $autoplay_timeout; ?>

						<?php echo $nav_next_prev; ?>

						<?php echo $nav_text; ?>

						<?php echo $dots_nav; ?>

						<?php echo $animate_in; ?>

						<?php echo $animate_out; ?>

						<?php echo $dots_data; ?>

						autoHeight: true,
						responsive:true,
						themeClass: 'owl-theme owl-slider-nav-type-<?php echo $slider_nav_type; ?> owl-nav-<?php echo $nav_position_class; ?>',
						controlsClass: 'owl-slider-controls',
						navContainerClass: 'owl-slider-nav position-<?php echo $nav_position_class; ?>',
						dotsClass: 'owl-dots slider <?php echo $slider_nav_type; ?>'
					});
					$("#view-<?php echo views()->id; ?>.slider.owl-slider-nav-type-thumbs").css({'margin-bottom': '<?php echo ($slider_nav_thumb_height*2)-10 ?>px'});
					
					<?php if( $slider_nav_thumb_height > 44 ) : ?>
						$("#view-<?php echo views()->id; ?>.slider.owl-slider-nav-type-thumbs").find('.owl-slider-nav.position-bottom').css({'margin-top':'<?php echo ($slider_nav_thumb_height/2)-20 ?>px'});
					<?php endif; ?>
			});
		})(jQuery);

	</script>

	<?php
	}

}
endif;

/**
 * Initiate assets
 *
 * @package View_Builder
 * @since 1.0.0
 */
function view_builder_assets() {
	return View_Builder_Assets::instance();
}
add_action ( 'plugins_loaded', 'view_builder_assets' );

