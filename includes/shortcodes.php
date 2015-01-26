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

			if (current_theme_supports('post-formats')) {
  echo 'has pf';
} else {
	echo 'no pf';
}

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

		public static function get_thumb_contents_parts( $builder_options ) {

			$view_name = views()->view_name;

			$parts = $builder_options->getOption( 'image-parts-content-type-' . $view_name . '' );
			$title_tag = $builder_options->getOption( 'title-option-html-tag-' . $view_name . '' );

			if ( $parts ) {

				foreach ($parts as $position => $part) {
	
					if ( $part == 'title' ) {
						echo do_shortcode(stripslashes('[vb_title 
							linked="0" 
							html_tag="' . $title_tag . '"]'));
					}

					if ( $part == 'excerpt' ) {
						$content_to_show = $builder_options->getOption( 'excerpt-option-content-to-show-' . $view_name . '' );
						$excerpt_length = $builder_options->getOption( 'excerpt-option-length-' . $view_name . '' );
						$excerpt_more = $builder_options->getOption( 'excerpt-option-more-' . $view_name . '' );
						echo do_shortcode(stripslashes('[vb_excerpt
							content_to_show="'. $content_to_show .'" 
							excerpt_more="'. $excerpt_more .'" 
							excerpt_length="'. $excerpt_length .'"]'));
					}

					if ( $part == 'date' ) {
						echo do_shortcode(stripslashes('[vb_date]'));
					}

				}

			}

		}

		public static function get_post_parts( $builder_options ) {

			$view_name = strtolower(views()->view_name);

			$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

			//title settings
			$title_tag 					= $builder_options->getOption( 'title-option-html-tag-' . $view_name . '' );
			$title_linked 				= $builder_options->getOption( 'title-option-link-' . $view_name . '' );
			$title_shorten 			= $builder_options->getOption( 'title-option-shorten-title-' . $view_name . '' );
			$title_limit 				= $builder_options->getOption( 'title-option-shorten-limit-' . $view_name . '' );
			$title_before 				= $builder_options->getOption( 'title-option-before-text-' . $view_name . '' );
			$title_display_as 		= $builder_options->getOption( 'title-styles-display-as-' . $view_name . '' );
			
			//image settings

			$layout = $builder_options->getOption( 'view-layout-' . $view_name . '' );
			if ( $layout == 'slider' ||  $layout == 'traditional-blog') {
				$columns			 		= '1';
			} else {
				$columns = $builder_options->getOption( 'postopts-columns-' . $view_name . '' );
			}
			$thumb_align			 			= $builder_options->getOption( 'image-option-thumb-align-' . $view_name . '' );
			$auto_size			 				= $builder_options->getOption( 'image-option-auto-size-' . $view_name . '' );
			$autosize_container_width		= $builder_options->getOption( 'image-option-autosize-container-width-' . $view_name . '' );
			$crop_vertically					= $builder_options->getOption( 'image-option-crop-vertically-' . $view_name . '' );
			$crop_vertically_height_ratio	= $builder_options->getOption( 'image-option-crop-vertically-height-ratio-' . $view_name . '' );
			$thumbnail_height 				= $builder_options->getOption( 'image-option-thumbnail-height-' . $view_name . '' );
			$thumbnail_width 					= $builder_options->getOption( 'image-option-thumbnail-width-' . $view_name . '' );
			$show_spotlight  					= $builder_options->getOption( 'image-show-spotlight-' . $view_name . '' );
			$thumb_spotlight_type			= $builder_options->getOption( 'thumb-spotlight-type-' . $view_name . '' );
			$thumb_icon_effect 				= $builder_options->getOption( 'image-icon-type-effect-' . $view_name . '' );
			$thumb_icon_style					= $builder_options->getOption( 'image-icon-type-style-' . $view_name . '' );
			$thumb_spotlight_effect 		= $builder_options->getOption( 'image-icon-type-spotlight-effect-' . $view_name . '' );
			$lightbox_height					= $builder_options->getOption( 'image-icon-type-lightbox-height-' . $view_name . '' );
			$lightbox_width					= $builder_options->getOption( 'image-icon-type-lightbox-width-' . $view_name . '' );
			$thumb_content_hover_effect	= $builder_options->getOption( 'image-content-type-hover-effect-' . $view_name . '' );
			$spotlight_button1  				= $builder_options->getOption( 'btn1-option-icon-' . $view_name . '' );
			$spotlight_button_link1			= $builder_options->getOption( 'btn1-option-link-' . $view_name . '' );
			$spotlight_button2 				= $builder_options->getOption( 'btn2-option-icon-' . $view_name . '' );
			$spotlight_button_link2 		= $builder_options->getOption( 'btn2-option-link-' . $view_name . '' );
			$spotlight_button3  				= $builder_options->getOption( 'btn3-option-icon-' . $view_name . '' );
			$spotlight_button_link3  		= $builder_options->getOption( 'btn3-option-link-' . $view_name . '' );
			$spotlight_button4  				= $builder_options->getOption( 'btn4-option-icon-' . $view_name . '' );
			$spotlight_button_link4 		= $builder_options->getOption( 'btn4-option-link-' . $view_name . '' );
			$thumb_display_as 				= $builder_options->getOption( 'image-styles-display-as-' . $view_name . '' );


			//content settings
			$content_to_show 			= $builder_options->getOption( 'excerpt-option-content-to-show-' . $view_name . '' );
			$excerpt_length 			= $builder_options->getOption( 'excerpt-option-length-' . $view_name . '' );
			$excerpt_more 				= $builder_options->getOption( 'excerpt-option-more-' . $view_name . '' );
			$excerpt_display_as 		= $builder_options->getOption( 'excerpt-styles-display-as-' . $view_name . '' );

			//date settings
			$date_before 				= $builder_options->getOption( 'date-option-before-text-' . $view_name . '' );
			$date_format 				= $builder_options->getOption( 'date-option-meta-date-format-' . $view_name . '' );
			$date_display_as 			= $builder_options->getOption( 'date-styles-display-as-' . $view_name . '' );

			//time settings
			$time_before 				= $builder_options->getOption( 'time-option-time-before-' . $view_name . '' );
			$time_format 				= $builder_options->getOption( 'time-option-time-format-' . $view_name . '' );
			$time_since 				= $builder_options->getOption( 'time-option-time-since-' . $view_name . '' );
			$time_display_as 			= $builder_options->getOption( 'time-styles-display-as-' . $view_name . '' );

			//categories settings
			$categories_before 		= $builder_options->getOption( 'categories-option-before-' . $view_name . '' );
			$categories_display_as 	= $builder_options->getOption( 'categories-styles-display-as-' . $view_name . '' );

			//tags settings
			$tags_before 				= $builder_options->getOption( 'tags-option-before-' . $view_name . '' );
			$tags_display_as 			= $builder_options->getOption( 'tags-styles-display-as-' . $view_name . '' );

			//post format settings
			$post_format_before 		= $builder_options->getOption( 'post-format-option-before-' . $view_name . '' );
			$post_format_type 		= $builder_options->getOption( 'post-format-option-type-' . $view_name . '' );
			$post_format_icon_size  = $builder_options->getOption( 'post-format-option-icon-size-' . $view_name . '' );
			$post_format_display_as = $builder_options->getOption( 'post-format-styles-display-as-' . $view_name . '' );

			//author settings
			$author_linked 			= $builder_options->getOption( 'author-option-linked-' . $view_name . '' );
			$author_before 			= $builder_options->getOption( 'author-option-before-' . $view_name . '' );
			$author_display_as 		= $builder_options->getOption( 'author-styles-display-as-' . $view_name . '' );

			//avatar settings
			$avatar_size 				= $builder_options->getOption( 'avatar-option-size-' . $view_name . '' );
			$avatar_linked 			= $builder_options->getOption( 'avatar-option-linked-' . $view_name . '' );
			$avatar_before 			= $builder_options->getOption( 'avatar-option-before-' . $view_name . '' );
			$avatar_display_as 		= $builder_options->getOption( 'avatar-styles-display-as-' . $view_name . '' );

			//comments settings
			$comments_format 			= $builder_options->getOption( 'comments-option-comments-format-' . $view_name . '' );
			$comments_format_1 		= $builder_options->getOption( 'comments-option-comments-format-1-' . $view_name . '' );
			$comments_format_0 		= $builder_options->getOption( 'comments-option-comments-format-0-' . $view_name . '' );
			$comments_before 			= $builder_options->getOption( 'comments-option-before-' . $view_name . '' );
			$comments_display_as 	= $builder_options->getOption( 'comments-styles-display-as-' . $view_name . '' );

			//read more settings
			$more_text 					= $builder_options->getOption( 'readmore-option-more-text-' . $view_name . '' );
			$more_show_always			= $builder_options->getOption( 'readmore-option-show-always-' . $view_name . '' );
			$more_display_as 			= $builder_options->getOption( 'readmore-styles-display-as-' . $view_name . '' );

			//social options
			$share_icon_image_w		= $builder_options->getOption( 'share-option-icon-w-' . $view_name . '' );
			$share_icon_image_h		= $builder_options->getOption( 'share-option-icon-h-' . $view_name . '' );
			$facebook_image 			= $builder_options->getOption( 'share-option-facebook-share-icon-' . $view_name . '' );
		   $twitter_image 			= $builder_options->getOption( 'share-option-twitter-share-icon-' . $view_name . '' );
		   $googleplus_image 		= $builder_options->getOption( 'share-option-googleplus-share-icon-' . $view_name . '' );
		   $linkedin_image 			= $builder_options->getOption( 'share-option-linkedin-share-icon-' . $view_name . '' );
		   $facebook_target 			= $builder_options->getOption( 'share-option-facebook-target-' . $view_name . '' );
		   $twitter_target 			= $builder_options->getOption( 'share-option-twitter-target-' . $view_name . '' );
		   $googleplus_target 		= $builder_options->getOption( 'share-option-googleplus-target-' . $view_name . '' );
		   $linkedin_target 			= $builder_options->getOption( 'share-option-linkedin-target-' . $view_name . '' );
		   $share_before 				= $builder_options->getOption( 'share-option-before-' . $view_name . '' );
			$share_display_as 		= $builder_options->getOption( 'share-styles-display-as-' . $view_name . '' );

			//likes options
			$likes_before 				= $builder_options->getOption( 'likes-option-before-' . $view_name . '' );
			$likes_display_as 		= $builder_options->getOption( 'likes-styles-display-as-' . $view_name . '' );

			if ( self::$is_woo_active ) {
				
				$wc_price_before 			= $builder_options->getOption( 'wc-price-option-before-' . $view_name . '' );
				$wc_price_display_as 	= $builder_options->getOption( 'wc-price-styles-display-as-' . $view_name . '' );

				$wc_add_cart_add_text 			= $builder_options->getOption( 'wc-add-to-cart-option-add-text-' . $view_name . '' );
				$wc_add_cart_display_as 		= $builder_options->getOption( 'wc-add-to-cart-styles-display-as-' . $view_name . '' );

				$wc_rating_before 				= $builder_options->getOption( 'wc-rating-option-before-' . $view_name . '' );
				$wc_rating_display_as 			= $builder_options->getOption( 'wc-rating-styles-display-as-' . $view_name . '' );
				$wc_rating_show_review_count 	= $builder_options->getOption( 'wc-rating-option-show-review-count-' . $view_name . '' );
				$wc_rating_show_as_stars	 	= $builder_options->getOption( 'wc-rating-option-show-as-stars-' . $view_name . '' );

				$wc_sale_flash_before 				= $builder_options->getOption( 'wc-sale-flash-option-before-' . $view_name . '' );
				$wc_sale_flash_after 				= $builder_options->getOption( 'wc-sale-flash-option-after-' . $view_name . '' );
				$wc_sale_flash_text 					= $builder_options->getOption( 'wc-sale-flash-option-text-' . $view_name . '' );
				$wc_sale_flash_as_percent_off 	= $builder_options->getOption( 'wc-sale-flash-option-as-percent-off-' . $view_name . '' );
				$wc_sale_flash_display_as 			= $builder_options->getOption( 'wc-sale-flash-styles-display-as-' . $view_name . '' );

			}

			if ( $parts ) {

				foreach ($parts as $position => $part) {

					switch ($part) {
			  			case 'title':
			  				echo do_shortcode(stripslashes('[vb_title 
			  				html_tag="' . $title_tag . '" 
							linked="'. $title_linked .'" 
							shorten="'. $title_shorten .'" 
							limit="'. $title_limit .'" 
							before="'. $title_before .'" 
							display_as="'. $title_display_as .'"]'));
			  				break;

			  			case 'image':
			  				echo do_shortcode(stripslashes('[vb_image 
							thumb_align="'. $thumb_align .'" 
							auto_size="'. $auto_size .'" 
							autosize_container_width="'. $autosize_container_width .'" 
							crop_vertically="'. $crop_vertically .'" 
							columns="' . $columns . '" 
							crop_vertically_height_ratio="'. $crop_vertically_height_ratio .'" 
							thumb_spotlight_type="'. $thumb_spotlight_type .'" 
							thumb_content_hover_effect="'. $thumb_content_hover_effect .'" 
							thumbnail_height="' . $thumbnail_height . '" 
							thumbnail_width="' . $thumbnail_width . '" 
							show_spotlight="' . $show_spotlight . '" 
							thumb_spotlight_effect="' . $thumb_spotlight_effect . '" 
							thumb_icon_style="' . $thumb_icon_style . '" 
							thumb_icon_effect="' . $thumb_icon_effect . '" 
							spotlight_button1="' . $spotlight_button1 . '" 
							spotlight_button_link1="' . $spotlight_button_link1 . '" 
							spotlight_button2="' . $spotlight_button2 . '" 
							spotlight_button_link2="' . $spotlight_button_link2 . '" 
							spotlight_button3="' . $spotlight_button3 . '" 
							spotlight_button_link3="' . $spotlight_button_link3 . '" 
							spotlight_button4="' . $spotlight_button4 . '" 
							spotlight_button_link4="' . $spotlight_button_link4 . '" 
							lightbox_height="'. $lightbox_height .'" 
							lightbox_width="'. $lightbox_width .'" 
							display_as="'. $thumb_display_as .'"
							]'));
			  				break;

			  			case 'excerpt':
							echo do_shortcode(stripslashes('[vb_excerpt
							content_to_show="'. $content_to_show .'" 
							excerpt_more="'. $excerpt_more .'" 
							display_as="'. $excerpt_display_as .'" 
							excerpt_length="'. $excerpt_length .'"]'));
			  				break;

			  			case 'date':
			  				echo do_shortcode(stripslashes('[vb_date 
							date_format="'. $date_format .'" 
							display_as="'. $date_display_as .'" 
							before="'. $date_before .'"]'));
			  				break;

			  			case 'time':
			  				echo do_shortcode(stripslashes('[vb_time 
							show_as_since="'. $time_since .'" 
							format="'. $time_format .'" 
							before="'. $time_before .'" 
							display_as="'. $time_display_as .'"
			  				 ]'));
			  				break;

			  			case 'categories':
			  				echo do_shortcode(stripslashes('[vb_categories 
							before="'. $categories_before .'" 
							display_as="'. $categories_display_as .'"
			  				]'));
			  				break;

			  			case 'tags':
			  				echo do_shortcode(stripslashes('[vb_tags 
							before="'. $tags_before .'" 
							display_as="'. $tags_display_as .'"
			  				]'));
			  				break;

			  			case 'post-format':
			  				echo do_shortcode(stripslashes('[vb_post_format 
							before="'. $post_format_before .'" 
							format_type="' . $post_format_type . '" 
							icon_size="'. $post_format_icon_size .'" 
							display_as="'. $post_format_display_as .'"
			  				]'));
			  				break;

			  			case 'author':
			  				echo do_shortcode(stripslashes('[vb_author 
			  				linked="'. $author_linked .'"  
							before="'. $author_before .'" 
							display_as="'. $author_display_as .'"
			  				]'));
			  				break;

			  			case 'avatar':
			  				echo do_shortcode(stripslashes('[vb_avatar 
			  				avatar_size="'. $avatar_size .'" 
			  				linked="'. $avatar_linked .'" 
							before="'. $avatar_before .'" 
							display_as="'. $avatar_display_as .'"
			  				]'));
			  				break;

			  			case 'comments':
			  				echo do_shortcode(stripslashes('[vb_comments 
			  				comment_format="'. $comments_format .'" 
			  				comment_format_1="'. $comments_format_1 .'" 
			  				comment_format_0="'. $comments_format_0 .'" 
							before="'. $comments_before .'" 
							display_as="'. $comments_display_as .'"
			  				]'));
			  				break;

			  			case 'share':
			  				echo do_shortcode(stripslashes('[vb_share 
			  				share_icon_image_w="'. $share_icon_image_w .'" 
			  				share_icon_image_h="'. $share_icon_image_h .'" 
							facebook_image="'.  $facebook_image .'" 
							twitter_image="'.  $twitter_image .'" 
							googleplus_image="'.  $googleplus_image .'" 
							linkedin_image="'.  $linkedin_image .'" 
							facebook_target="'.  $facebook_target .'" 
							twitter_target="'.  $twitter_target .'" 
							googleplus_target="'.  $googleplus_target .'" 
							linkedin_target="'.  $linkedin_target .'" 
							before="'. $share_before .'" 
							display_as="'. $share_display_as .'"
			  				]'));
			  				break;

			  			case 'likes':
			  				echo do_shortcode(stripslashes('[vb_likes 
							display_as="'. $likes_display_as .'" 
							before="'. $likes_before .'" 
							]'));
			  				break;

			  			case 'readmore':
			  				echo do_shortcode(stripslashes('[vb_readmore 
							excerpt_limit="'. $excerpt_length .'" 
							show_always="'. $more_show_always .'" 
							display_as="'. $more_display_as .'" 
							more_text="'. $more_text .'"]'));
			  				break;

			  			case 'wc-price':
			  				echo do_shortcode(stripslashes('[vb_wc_price 
							before="'. $wc_price_before .'" 
							display_as="'. $wc_price_display_as .'"
			  				]'));
			  				break;

			  			case 'wc-rating':
			  				echo do_shortcode(stripslashes('[vb_wc_rating 
			  				show_as_stars="'. $wc_rating_show_as_stars .'" 
			  				show_review_count="'. $wc_rating_show_review_count .'" 
							before="'. $wc_rating_before .'" 
							display_as="'. $wc_rating_display_as .'"
			  				]'));
			  				break;

			  			case 'wc-sale-flash':
			  				echo do_shortcode(stripslashes('[vb_wc_sale_flash 
							before="'. $wc_sale_flash_before .'" 
							after="'. $wc_sale_flash_after .'"
							sale_text="'. $wc_sale_flash_text .'" 
							as_percentage_off="'. $wc_sale_flash_as_percent_off .'"
							display_as="'. $wc_sale_flash_display_as .'"
			  				]'));
			  				break;

			  			case 'wc-add-to-cart':
			  				echo do_shortcode(stripslashes('[vb_wc_add_to_cart 
							add_cart_text="'. $wc_add_cart_add_text .'"
							display_as="'. $wc_add_cart_display_as .'"
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

