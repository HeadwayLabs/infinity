<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'View_Builder_Shortcodes' ) ) :
	/**
	 * View Builder Short Codes
	 *
	 * @package View_Builder
	 * @since 1.0.0
	 */
	class View_Builder_Shortcodes {

		static public $elements = null;

		/**
		 * @var View_Builder_Shortcodes Stores the instance of this class.
		 */
		private static $instance;

	   public static $is_woo_active;

		/**
		 * View_Builder_Shortcodes Instance
		 *
		 * Makes sure that there is only ever one instance of the View Builder
		 *
		 * @since 1.0.0
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new View_Builder_Shortcodes;
				self::$instance->init();

			}

			return self::$instance;

	   }

		/**
		* A dummy constructor to prevent loading more than once.
		* @since 	1.0.0
	 	* @see 	View_Builder::instance()
		*/
		private function __construct() { 
			// Do nothing here
		}

		//dummy function to get settings while porting from Headway
		public static function get_setting( $option, $default ) {
			return $default;
		}

		public static function init() {

			// $elements = array('title', 'date');
			// self::$elements = $elements;

			// "title",
			// "image",
			// "excerpt",
			// "date",
			// "time",
			// "categories",
			// "author",
			// "avatar",
			// "comments",
			// "share",
			// "like",
			// "readmore"

			$shortcodes = array(
				'vb_title'     	=> __CLASS__ . '::vb_title',
				'vb_image'    		=> __CLASS__ . '::vb_image',
				'vb_excerpt'		=> __CLASS__ . '::vb_excerpt',
				'vb_date'      	=> __CLASS__ . '::vb_date',
				'vb_time'      	=> __CLASS__ . '::vb_time',
				'vb_categories'   => __CLASS__ . '::vb_categories',
				'vb_tags'   		=> __CLASS__ . '::vb_tags',
				'vb_post_format'  => __CLASS__ . '::vb_post_format',
				'vb_author'      	=> __CLASS__ . '::vb_author',
				'vb_avatar'      	=> __CLASS__ . '::vb_avatar',
				'vb_comments'     => __CLASS__ . '::vb_comments',
				'vb_share'      	=> __CLASS__ . '::vb_share',
				'vb_likes'      	=> __CLASS__ . '::vb_likes',
				'vb_readmore'		=> __CLASS__ . '::vb_readmore'
			);

			/**
			 * Load woocommerce specific shortcodes if its in use
			 **/
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				
				self::$is_woo_active = true;
				$woocommerce_shortocdes = array(
					'vb_wc_price'     		=> __CLASS__ . '::vb_wc_price',
					'vb_wc_rating'     		=> __CLASS__ . '::vb_wc_rating',
					'vb_wc_sale_flash'     	=> __CLASS__ . '::vb_wc_sale_flash',
					'vb_wc_add_to_cart'		=> __CLASS__ . '::vb_wc_add_to_cart'
				);

			}

			if ( !empty($woocommerce_shortocdes) ) {
				$shortcodes = array_merge($shortcodes, $woocommerce_shortocdes);
			}

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
			}

		}

		public static function vb_title($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/title.php');
			return $content;
		}

		public static function vb_image($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/image.php');
			return $content;
		}

		public static function vb_excerpt($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/excerpt.php');
			return $content;
		}

		public static function vb_date($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/date.php');
			return $content;
		}

		public static function vb_time($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/time.php');
			return $content;
		}

		public static function vb_categories($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/categories.php');
			return $content;
		}

		public static function vb_tags($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/tags.php');
			return $content;
		}

		public static function vb_post_format($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/post-format.php');
			return $content;
		}

		public static function vb_author($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/author.php');
			return $content;
		}

		public static function vb_avatar($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/avatar.php');
			return $content;
		}

		public static function vb_comments($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/comments.php');
			return $content;
		}

		public static function vb_share($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/share.php');
			return $content;
		}

		public static function vb_likes($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/likes.php');
			return $content;
		}

		public static function vb_readmore($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/readmore.php');
			return $content;
		}

		//woocommerce shortcodes
		public static function vb_wc_price($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/integrations/woocommerce/price.php');
			return $content;
		}

		public static function vb_wc_rating($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/integrations/woocommerce/rating.php');
			return $content;
		}

		public static function vb_wc_sale_flash($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/integrations/woocommerce/sale-flash.php');
			return $content;
		}

		public static function vb_wc_add_to_cart($atts, $content = null) {
			include(views()->plugin_dir . 'parts/content/integrations/woocommerce/add-to-cart.php');
			return $content;
		}

		public static function get_post_parts( $builder_options, $parts, $view_name, $include_image=true ) {
			$id= views()->id;

			if ( $parts ) {

				foreach ($parts as $position => $part) {

					switch ($part) {
			  			case 'title':
							$title_tag 					= $builder_options->getOption( 'title-option-html-tag-' . $view_name . '' );
							$title_tag 					= (!empty($title_tag)) ? 'html_tag="' . $title_tag . '"' : null;
							
							$title_linked 				= $builder_options->getOption( 'title-option-link-' . $view_name . '' );
							$title_linked 				= (!empty($title_linked)) ? 'linked="' . $title_linked . '"' : null;

							$title_shorten 			= $builder_options->getOption( 'title-option-shorten-title-' . $view_name . '' );
							$title_shorten 			= (!empty($title_shorten)) ? 'shorten="' . $title_shorten . '"' : null;

							$title_limit				= $builder_options->getOption( 'title-option-shorten-limit-' . $view_name . '' );
							$title_limit 				= (!empty($title_limit)) ? 'limit="'. $title_limit .'"' : null;

							$title_before 				= $builder_options->getOption( 'title-option-before-text-' . $view_name . '' );
							$title_before 				= (!empty($title_before)) ? 'before="'. $title_before .'"' : null;

							$title_display_as 		= $builder_options->getOption( 'title-styles-display-as-' . $view_name . '' );
							$title_display_as 		= (!empty($title_display_as)) ? 'display_as="'. $title_display_as .'"' : null;

			  				echo do_shortcode(stripslashes('[vb_title 
			  				' . $title_tag . ' 
							' . $title_linked . '
							' . $title_shorten . ' 
							' . $title_limit . '
							' . $title_before .'
							' . $title_display_as .']'));
			  				break;

			  			case 'image':

			  				if ($include_image) {
				  				$layout = ( $builder_options->getOption( 'view-layout-' . $id . '' ) == true ) ? $builder_options->getOption( 'view-layout-' . $id . '' ) : 'blog';
				
								$columns = $builder_options->getOption( 'postopts-columns-' . $id . '' );
								$columns = (!empty($columns)) ? 'columns="' . $columns . '"' : null;

								if ( $layout == 'slider' ||  $layout == 'blog') {
									$columns	= 'columns="1"';
								}

								$thumb_align			 			= $builder_options->getOption( 'image-option-thumb-align-' . $view_name . '' );
				  				$thumb_align 						= (!empty($thumb_align)) ? 'thumb_align="' . $thumb_align . '"' : null;
				  				
				  				$auto_size 							= $builder_options->getOption( 'image-option-auto-size-' . $view_name . '' );
				  				$auto_size 							= (!empty($auto_size)) ? 'auto_size="' . $auto_size . '"' : null;

								$autosize_container_width		= $builder_options->getOption( 'image-option-autosize-container-width-' . $view_name . '' );
								$autosize_container_width 		= (!empty($autosize_container_width)) ? 'autosize_container_width="' . $autosize_container_width . '"' : null;

								$crop_vertically					= $builder_options->getOption( 'image-option-crop-vertically-' . $view_name . '' );
								$crop_vertically 					= (!empty($crop_vertically)) ? 'crop_vertically="' . $crop_vertically . '"' : null;

								$crop_vertically_height_ratio	= $builder_options->getOption( 'image-option-crop-vertically-height-ratio-' . $view_name . '' );
								$crop_vertically_height_ratio = (!empty($crop_vertically_height_ratio)) ? 'crop_vertically_height_ratio="' . $crop_vertically_height_ratio . '"' : null;

								$thumbnail_height 				= $builder_options->getOption( 'image-option-thumbnail-height-' . $view_name . '' );
								$thumbnail_height 				= (!empty($thumbnail_height)) ? 'thumbnail_height="' . $thumbnail_height . '"' : null;

								$thumbnail_width 					= $builder_options->getOption( 'image-option-thumbnail-width-' . $view_name . '' );
								$thumbnail_width 					= (!empty($thumbnail_width)) ? 'thumbnail_width="' . $thumbnail_width . '"' : null;

								$show_cover  					= $builder_options->getOption( 'image-show-cover-hide-' . $view_name . '' );
				  				$show_cover 					= (!empty($show_cover)) ? 'show_cover="' . $show_cover . '"' : 'show_cover="' . $show_cover . '"';

				  				$content_vertical_align  	= $builder_options->getOption( 'image-content-type-content-vertical-align-' . $view_name . '' );
				  				$content_vertical_align 	= (!empty($content_vertical_align)) ? 'content_vertical_align="' . $content_vertical_align . '"' : null;

								$thumb_cover_type			= $builder_options->getOption( 'thumb-cover-type-hide-' . $view_name . '' );
								$thumb_cover_type 			= (!empty($thumb_cover_type)) ? 'thumb_cover_type="' . $thumb_cover_type . '"' : null;

								$thumb_icon_effect 				= $builder_options->getOption( 'image-icon-type-effect-' . $view_name . '' );
								$thumb_icon_effect 				= (!empty($thumb_icon_effect)) ? 'thumb_icon_effect="' . $thumb_icon_effect . '"' : null;

								$thumb_icon_style					= $builder_options->getOption( 'image-icon-type-style-' . $view_name . '' );
								$thumb_icon_style 				= (!empty($thumb_icon_style)) ? 'thumb_icon_style="' . $thumb_icon_style . '"' : null;

								$thumb_cover_effect 		= $builder_options->getOption( 'image-icon-type-cover-effect-' . $view_name . '' );
								$thumb_cover_effect 		= (!empty($thumb_cover_effect)) ? 'thumb_cover_effect="' . $thumb_cover_effect . '"' : null;

								$lightbox_height					= $builder_options->getOption( 'image-icon-type-lightbox-height-' . $view_name . '' );
								$lightbox_height 					= (!empty($lightbox_height)) ? 'lightbox_height="' . $lightbox_height . '"' : null;

								$lightbox_width					= $builder_options->getOption( 'image-icon-type-lightbox-width-' . $view_name . '' );
								$lightbox_width 					= (!empty($lightbox_width)) ? 'lightbox_width="' . $lightbox_width . '"' : null;

								$thumb_content_hover_effect	= $builder_options->getOption( 'image-content-type-hover-effect-' . $view_name . '' );
								$thumb_content_hover_effect 	= (!empty($thumb_content_hover_effect)) ? 'thumb_content_hover_effect="' . $thumb_content_hover_effect . '"' : null;

								$cover_button1  					= $builder_options->getOption( 'btn1-option-icon-' . $view_name . '' );
								$cover_button1 					= (!empty($cover_button1)) ? 'cover_button1="' . $cover_button1 . '"' : null;

								$cover_button_link1				= $builder_options->getOption( 'btn1-option-link-' . $view_name . '' );
								$cover_button_link1 				= (!empty($cover_button_link1)) ? 'cover_button_link1="' . $cover_button_link1 . '"' : null;

								$cover_button2 					= $builder_options->getOption( 'btn2-option-icon-' . $view_name . '' );
								$cover_button2 					= (!empty($cover_button2)) ? 'cover_button2="' . $cover_button2 . '"' : null;

								$cover_button_link2 				= $builder_options->getOption( 'btn2-option-link-' . $view_name . '' );
								$cover_button_link2 				= (!empty($cover_button_link2)) ? 'cover_button_link2="' . $cover_button_link2 . '"' : null;

								$cover_button3  					= $builder_options->getOption( 'btn3-option-icon-' . $view_name . '' );
								$cover_button3 					= (!empty($cover_button3)) ? 'cover_button3="' . $cover_button3 . '"' : null;

								$cover_button_link3  			= $builder_options->getOption( 'btn3-option-link-' . $view_name . '' );
								$cover_button_link3 				= (!empty($cover_button_link3)) ? 'cover_button_link3="' . $cover_button_link3 . '"' : null;

								$cover_button4  					= $builder_options->getOption( 'btn4-option-icon-' . $view_name . '' );
								$cover_button4 					= (!empty($cover_button4)) ? 'cover_button4="' . $cover_button4 . '"' : null;

								$cover_button_link4 				= $builder_options->getOption( 'btn4-option-link-' . $view_name . '' );
								$cover_button_link4 				= (!empty($cover_button_link4)) ? 'cover_button_link4="' . $cover_button_link4 . '"' : null;

								$thumb_display_as 				= $builder_options->getOption( 'image-styles-display-as-' . $view_name . '' );
								$thumb_display_as 				= (!empty($thumb_display_as)) ? 'display_as="' . $thumb_display_as . '"' : null;


				  				echo do_shortcode(stripslashes('[vb_image 
								' . $thumb_align . ' 
								' . $auto_size . ' 
								' . $autosize_container_width . ' 
								' . $crop_vertically . ' 
								' . $columns . ' 
								' . $crop_vertically_height_ratio . ' 
								' . $thumb_cover_type . ' 
								' . $thumb_content_hover_effect . ' 
								' . $thumbnail_height . ' 
								' . $thumbnail_width . '
								' . $content_vertical_align . ' 
								' . $show_cover . ' 
								' . $thumb_cover_effect . ' 
								' . $thumb_icon_style . ' 
								' . $thumb_icon_effect . ' 
								' . $cover_button1 . ' 
								' . $cover_button_link1 . ' 
								' . $cover_button2 . ' 
								' . $cover_button_link2 . ' 
								' . $cover_button3 . ' 
								' . $cover_button_link3 . ' 
								' . $cover_button4 . ' 
								' . $cover_button_link4 . ' 
								' . $lightbox_height . '  
								' . $lightbox_width . '  
								' . $thumb_display_as . '
								]'));

			  				}

			  				break;

			  			case 'excerpt':
							$content_to_show 			= $builder_options->getOption( 'excerpt-option-content-to-show-' . $view_name . '' );
							$content_to_show 			= (!empty($content_to_show)) ? 'content_to_show="' . $content_to_show . '"' : null;							
							
							$excerpt_length 			= $builder_options->getOption( 'excerpt-option-length-' . $view_name . '' );
							$excerpt_length 			= (!empty($excerpt_length)) ? 'excerpt_length="' . $excerpt_length . '"' : null;

							$excerpt_more 				= $builder_options->getOption( 'excerpt-option-more-' . $view_name . '' );
							$excerpt_more 				= (!empty($excerpt_more)) ? 'excerpt_more="' . $excerpt_more . '"' : null;

							$excerpt_display_as 		= $builder_options->getOption( 'excerpt-styles-display-as-' . $view_name . '' );
							$excerpt_display_as 		= (!empty($excerpt_display_as)) ? 'display_as="' . $excerpt_display_as . '"' : null;

							echo do_shortcode(stripslashes('[vb_excerpt
							'. $content_to_show .' 
							'. $excerpt_more .' 
							'. $excerpt_display_as .' 
							'. $excerpt_length .']'));
			  				break;

			  			case 'date':
							$date_format 				= $builder_options->getOption( 'date-option-meta-date-format-' . $view_name . '' );
							$date_format 				= (!empty($date_format)) ? 'date_format="' . $date_format . '"' : null;

							$date_display_as 			= $builder_options->getOption( 'date-styles-display-as-' . $view_name . '' );
							$date_display_as 			= (!empty($date_display_as)) ? 'display_as="' . $date_display_as . '"' : null;

							$date_before 				= $builder_options->getOption( 'date-option-before-text-' . $view_name . '' );
							$date_before 				= (!empty($date_before)) ? 'before="' . $date_before . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_date 
							'. $date_format .' 
							'. $date_display_as .' 
							'. $date_before .' 
							]'));
			  				break;

			  			case 'time':
							$time_before 				= $builder_options->getOption( 'time-option-time-before-' . $view_name . '' );
							$time_before 				= (!empty($time_before)) ? 'before="' . $time_before . '"' : null;

							$time_format 				= $builder_options->getOption( 'time-option-time-format-' . $view_name . '' );
							$time_format 				= (!empty($time_format)) ? 'format="' . $time_format . '"' : null;

							$time_since 				= $builder_options->getOption( 'time-option-time-since-' . $view_name . '' );
							$time_since 				= (!empty($time_since)) ? 'show_time_since="' . $time_since . '"' : null;

							$time_display_as 			= $builder_options->getOption( 'time-styles-display-as-' . $view_name . '' );
							$time_display_as 			= (!empty($time_display_as)) ? 'display_as="' . $time_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_time 
							'. $time_since .' 
							'. $time_format .' 
							'. $time_before .' 
							'. $time_display_as .'
			  				 ]'));
			  				break;

			  			case 'categories':
							$categories_before 		= $builder_options->getOption( 'categories-option-before-' . $view_name . '' );
							$categories_before 		= (!empty($categories_before)) ? 'before="' . $categories_before . '"' : null;

							$categories_display_as 	= $builder_options->getOption( 'categories-styles-display-as-' . $view_name . '' );
							$categories_display_as 	= (!empty($categories_display_as)) ? 'display_as="' . $categories_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_categories 
							'. $categories_before .' 
							'. $categories_display_as .' 
			  				]'));
			  				break;

			  			case 'tags':
							$tags_before 				= $builder_options->getOption( 'tags-option-before-' . $view_name . '' );
							$tags_before 				= (!empty($tags_before)) ? 'before="' . $tags_before . '"' : null;

							$tags_display_as 			= $builder_options->getOption( 'tags-styles-display-as-' . $view_name . '' );
							$tags_display_as 			= (!empty($tags_display_as)) ? 'display_as="' . $tags_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_tags 
							'. $tags_before .' 
							'. $tags_display_as .'
			  				]'));
			  				break;

			  			case 'post-format':
							$post_format_before 		= $builder_options->getOption( 'post-format-option-before-' . $view_name . '' );
							$post_format_before 		= (!empty($post_format_before)) ? 'before="' . $post_format_before . '"' : null;

							$post_format_type 		= $builder_options->getOption( 'post-format-option-type-' . $view_name . '' );
							$post_format_type 		= (!empty($post_format_type)) ? 'format_type="' . $post_format_type . '"' : null;

							$post_format_icon_size  = $builder_options->getOption( 'post-format-option-icon-size-' . $view_name . '' );
							$post_format_icon_size 	= (!empty($post_format_icon_size)) ? 'icon_size="' . $post_format_icon_size . '"' : null;

							$post_format_display_as = $builder_options->getOption( 'post-format-styles-display-as-' . $view_name . '' );
							$post_format_display_as = (!empty($post_format_display_as)) ? 'display_as="' . $post_format_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_post_format 
							'. $post_format_before .' 
							' . $post_format_type . ' 
							'. $post_format_icon_size .' 
							'. $post_format_display_as .'
			  				]'));
			  				break;

			  			case 'author':
							$author_linked 			= $builder_options->getOption( 'author-option-linked-' . $view_name . '' );
							$author_linked 			= (!empty($author_linked)) ? 'linked="' . $author_linked . '"' : null;

							$author_before 			= $builder_options->getOption( 'author-option-before-' . $view_name . '' );
							$author_before 			= (!empty($author_before)) ? 'before="' . $author_before . '"' : null;

							$author_display_as 		= $builder_options->getOption( 'author-styles-display-as-' . $view_name . '' );
							$author_display_as 		= (!empty($author_display_as)) ? 'display_as="' . $author_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_author 
			  				'. $author_linked .' 
							'. $author_before .' 
							'. $author_display_as .'
			  				]'));
			  				break;

			  			case 'avatar':
							$avatar_size 				= $builder_options->getOption( 'avatar-option-size-' . $view_name . '' );
							$avatar_size 				= (!empty($avatar_size)) ? 'avatar_size="' . $avatar_size . '"' : null;

							$avatar_linked 			= $builder_options->getOption( 'avatar-option-linked-' . $view_name . '' );
							$avatar_linked 			= (!empty($avatar_linked)) ? 'linked="' . $avatar_linked . '"' : null;

							$avatar_before 			= $builder_options->getOption( 'avatar-option-before-' . $view_name . '' );
							$avatar_before 			= (!empty($avatar_before)) ? 'before="' . $avatar_before . '"' : null;

							$avatar_display_as 		= $builder_options->getOption( 'avatar-styles-display-as-' . $view_name . '' );
							$avatar_display_as 		= (!empty($avatar_display_as)) ? 'display_as="' . $avatar_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_avatar 
			  				'. $avatar_size .' 
			  				'. $avatar_linked .' 
							'. $avatar_before .' 
							'. $avatar_display_as .'
			  				]'));
			  				break;

			  			case 'comments':
							$comments_format 		= $builder_options->getOption( 'comments-option-comments-format-' . $view_name . '' );
							$comments_format 		= (!empty($comments_format)) ? 'comments_format="' . $comments_format . '"' : null;

							$comments_format_1 	= $builder_options->getOption( 'comments-option-comments-format-1-' . $view_name . '' );
							$comment_format_1 	= (!empty($comment_format_1)) ? 'comment_format_1="' . $comment_format_1 . '"' : null;

							$comments_format_0 	= $builder_options->getOption( 'comments-option-comments-format-0-' . $view_name . '' );
							$comment_format_0 	= (!empty($comment_format_0)) ? 'comment_format_0="' . $comment_format_0 . '"' : null;

							$comments_before 		= $builder_options->getOption( 'comments-option-before-' . $view_name . '' );
							$comments_before 		= (!empty($comments_before)) ? 'before="' . $comments_before . '"' : null;

							$comments_display_as = $builder_options->getOption( 'comments-styles-display-as-' . $view_name . '' );
							$comments_display_as = (!empty($comments_display_as)) ? 'display_as="' . $comments_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_comments 
			  				'. $comments_format .' 
			  				'. $comments_format_1 .' 
			  				'. $comments_format_0 .' 
							'. $comments_before .' 
							'. $comments_display_as .'
			  				]'));
			  				break;

			  			case 'share':
							$facebook_image 			= $builder_options->getOption( 'share-option-icon-fa-facebook-' . $view_name . '' );
						  	$facebook_image 			= (!empty($facebook_image)) ? 'facebook_image="' . $facebook_image . '"' : null;

						   $twitter_image 			= $builder_options->getOption( 'share-option-icon-fa-twitter-' . $view_name . '' );
						   $twitter_image 			= (!empty($twitter_image)) ? 'twitter_image="' . $twitter_image . '"' : null;

						   $googleplus_image 		= $builder_options->getOption( 'share-option-icon-fa-google-plus-' . $view_name . '' );
						  	$googleplus_image 		= (!empty($googleplus_image)) ? 'googleplus_image="' . $googleplus_image . '"' : null;

						   $linkedin_image 			= $builder_options->getOption( 'share-option-icon-fa-linkedin-' . $view_name . '' );
						   $linkedin_image 			= (!empty($linkedin_image)) ? 'linkedin_image="' . $linkedin_image . '"' : null;

						   $facebook_target 			= $builder_options->getOption( 'share-option-facebook-target-' . $view_name . '' );
						   $facebook_target 			= (!empty($facebook_target)) ? 'facebook_target="' . $facebook_target . '"' : null;

						   $twitter_target 			= $builder_options->getOption( 'share-option-twitter-target-' . $view_name . '' );
						   $twitter_target 			= (!empty($twitter_target)) ? 'twitter_target="' . $twitter_target . '"' : null;

						   $googleplus_target 		= $builder_options->getOption( 'share-option-googleplus-target-' . $view_name . '' );
						  	$googleplus_target 		= (!empty($googleplus_target)) ? 'googleplus_target="' . $googleplus_target . '"' : null;

						   $linkedin_target 			= $builder_options->getOption( 'share-option-linkedin-target-' . $view_name . '' );
						   $linkedin_target 			= (!empty($linkedin_target)) ? 'linkedin_target="' . $linkedin_target . '"' : null;

						   $share_before 				= $builder_options->getOption( 'share-option-before-' . $view_name . '' );
							$share_before 				= (!empty($share_before)) ? 'before="' . $share_before . '"' : 'before=""';

							$share_display_as 		= $builder_options->getOption( 'share-styles-display-as-' . $view_name . '' );
							$share_display_as 		= (!empty($share_display_as)) ? 'display_as="' . $share_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_share 
							'. $facebook_image .' 
							'. $twitter_image .' 
							'. $googleplus_image .' 
							'. $linkedin_image .' 
							'. $facebook_target .' 
							'. $twitter_target .' 
							'. $googleplus_target .' 
							'. $linkedin_target .' 
							'. $share_before .' 
							'. $share_display_as .'"
			  				]'));
			  				break;

			  			case 'likes':
							$likes_before 				= $builder_options->getOption( 'likes-option-before-' . $view_name . '' );
							$likes_before 					= (!empty($likes_before)) ? 'before="' . $likes_before . '"' : null;

							$show_like_text 				= $builder_options->getOption( 'likes-option-show_like_text-' . $view_name . '' );
							$show_like_text 				= (!empty($show_like_text)) ? 'show_like_text="' . $show_like_text . '"' : null;

							$likes_display_as 		= $builder_options->getOption( 'likes-styles-display-as-' . $view_name . '' );
			  				$likes_display_as 					= (!empty($likes_display_as)) ? 'display_as="' . $likes_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_likes 
							'. $likes_display_as .' 
							' . $show_like_text .' 
							'. $likes_before .' 
							]'));
			  				break;

			  			case 'readmore':
							$excerpt_length 			= $builder_options->getOption( 'excerpt-option-length-' . $view_name . '' );
							$excerpt_length 			= (!empty($excerpt_length)) ? 'excerpt_limit="' . $excerpt_length . '"' : null;
							
							$more_text 					= $builder_options->getOption( 'readmore-option-more-text-' . $view_name . '' );
							$more_text 					= (!empty($more_text)) ? 'more_text="' . $more_text . '"' : null;

							$more_show_always			= $builder_options->getOption( 'readmore-option-show-always-' . $view_name . '' );
							$more_show_always 		= (!empty($more_show_always)) ? 'show_always="' . $more_show_always . '"' : null;

							$more_display_as 			= $builder_options->getOption( 'readmore-styles-display-as-' . $view_name . '' );
							$more_display_as 			= (!empty($more_display_as)) ? 'display_as="' . $more_display_as . '"' : null;


			  				echo do_shortcode(stripslashes('[vb_readmore 
							'. $excerpt_length .' 
							'. $more_show_always .' 
							'. $more_display_as .' 
							'. $more_text .'
							]'));
			  				break;

			  			case 'wc-price':
							$wc_price_before 			= $builder_options->getOption( 'wc-price-option-before-' . $view_name . '' );
							$wc_price_before 			= (!empty($wc_price_before)) ? 'before="' . $wc_price_before . '"' : null;

							$wc_price_display_as 	= $builder_options->getOption( 'wc-price-styles-display-as-' . $view_name . '' );
							$wc_price_display_as 	= (!empty($wc_price_display_as)) ? 'display_as="' . $wc_price_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_wc_price 
							'. $wc_price_before .' 
							'. $wc_price_display_as .' 
			  				]'));
			  				break;

			  			case 'wc-rating':
							$wc_rating_before 				= $builder_options->getOption( 'wc-rating-option-before-' . $view_name . '' );
							$wc_rating_before 				= (!empty($wc_rating_before)) ? 'before="' . $wc_rating_before . '"' : null;

							$wc_rating_display_as 			= $builder_options->getOption( 'wc-rating-styles-display-as-' . $view_name . '' );
							$wc_rating_display_as 			= (!empty($wc_rating_display_as)) ? 'display_as="' . $wc_rating_display_as . '"' : null;

							$wc_rating_show_review_count 	= $builder_options->getOption( 'wc-rating-option-show-review-count-' . $view_name . '' );
							$wc_rating_show_review_count 	= (!empty($wc_rating_show_review_count)) ? 'show_review_count="' . $wc_rating_show_review_count . '"' : null;

							$wc_rating_show_as_stars	 	= $builder_options->getOption( 'wc-rating-option-show-as-stars-' . $view_name . '' );
							$wc_rating_show_as_stars 		= (!empty($wc_rating_show_as_stars)) ? 'show_as_stars="' . $wc_rating_show_as_stars . '"' : null;


			  				echo do_shortcode(stripslashes('[vb_wc_rating 
			  				'. $wc_rating_show_as_stars .' 
			  				'. $wc_rating_show_review_count .' 
							'. $wc_rating_before .' 
							'. $wc_rating_display_as .'
			  				]'));
			  				break;

			  			case 'wc-sale-flash':
			  				$wc_sale_flash_before 				= $builder_options->getOption( 'wc-sale-flash-option-before-' . $view_name . '' );
							$wc_sale_flash_before 				= (!empty($wc_sale_flash_before)) ? 'before="' . $wc_sale_flash_before . '"' : null;

							$wc_sale_flash_after 				= $builder_options->getOption( 'wc-sale-flash-option-after-' . $view_name . '' );
							$wc_sale_flash_after 				= (!empty($wc_sale_flash_after)) ? 'after="' . $wc_sale_flash_after . '"' : null;

							$wc_sale_flash_text 					= $builder_options->getOption( 'wc-sale-flash-option-text-' . $view_name . '' );
							$wc_sale_flash_text 					= (!empty($wc_sale_flash_text)) ? 'sale_text="' . $wc_sale_flash_text . '"' : null;

							$wc_sale_flash_as_percent_off 	= $builder_options->getOption( 'wc-sale-flash-option-as-percent-off-' . $view_name . '' );
							$wc_sale_flash_as_percent_off 	= (!empty($wc_sale_flash_as_percent_off)) ? 'as_percentage_off="' . $wc_sale_flash_as_percent_off . '"' : null;

							$wc_sale_flash_display_as 			= $builder_options->getOption( 'wc-sale-flash-styles-display-as-' . $view_name . '' );
							$wc_sale_flash_display_as 			= (!empty($wc_sale_flash_display_as)) ? 'display_as="' . $wc_sale_flash_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_wc_sale_flash 
							'. $wc_sale_flash_before .' 
							'. $wc_sale_flash_after .'
							'. $wc_sale_flash_text .' 
							'. $wc_sale_flash_as_percent_off .'
							'. $wc_sale_flash_display_as .'
			  				]'));
			  				break;

			  			case 'wc-add-to-cart':
			  				$wc_add_cart_add_text 			= $builder_options->getOption( 'wc-add-to-cart-option-add-text-' . $view_name . '' );
							$wc_add_cart_add_text 			= (!empty($wc_add_cart_add_text)) ? 'add_cart_text="' . $wc_add_cart_add_text . '"' : null;

							$wc_add_cart_display_as 		= $builder_options->getOption( 'wc-add-to-cart-styles-display-as-' . $view_name . '' );
							$wc_add_cart_display_as 		= (!empty($wc_add_cart_display_as)) ? 'display_as="' . $wc_add_cart_display_as . '"' : null;

			  				echo do_shortcode(stripslashes('[vb_wc_add_to_cart 
							'. $wc_add_cart_add_text .' 
							'. $wc_add_cart_display_as .'
			  				]'));
			  				break;
			  			
			  			default:
			  				return;
			  				break;

			  		}	 

				}

			}

		}

	} //end class

	/**
	 * Return the class instance with a function and call it on the init add_action
	 */
	function view_builder_shortcodes() {
	   return View_Builder_Shortcodes::instance();
	}

	add_action ( 'plugins_loaded', 'view_builder_shortcodes' );

endif;

