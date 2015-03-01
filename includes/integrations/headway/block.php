<?php
/* This class must be included in another file and included later so we don't get an error about HeadwayBlockAPI class not existing. */

class HeadwayInfinityBlock extends HeadwayBlockAPI {
	
	
	public $id = 'infinity-block';
	
	public $name = 'Infinity';
	
	public $options_class = 'HeadwayInfinityBlockOptions';

	public $html_tag = 'div';

	public $description = 'The Infinity block for Headway is a companion block for the Infinity Builder. A plugin for Wordpress. Use it to add infinity\'s views to your website, then style every aspect of your new layouts.';
		 
	function enqueue_action($block_id) {
								
		$block = HeadwayBlocksData::get_block($block_id);

		return;
				
	}

	function init_action($block_id) {

		return;
		
	}
	
	function dynamic_js($block_id) {

		return;
		
	}
	

	function dynamic_css($block_id) {
		
		return;
		
	}

	function setup_elements() {

		// $this->register_block_element(array(
		// 	'id' => '___', 
		// 	'name' => '___',
		// 	'selector' => '.___',
		// 	'properties' => array('fonts', 'background', 'borders', 'padding', 'rounded-corners', 'box-shadow', 'text-shadow'), 
		// 	'states' => array( /* States are optional */
		// 		'Selected' => '.example-element-selector.selected', 
		// 		'Hover' => '.example-element-selector:hover', 
		// 		'Clicked' => '.example-element:active'
		// 	)
		// ));

		$this->register_block_element(array(
			'id' => 'view-wrapper', 
			'name' => 'Views Container',
			'selector' => '.view-wrapper'
		));

		$this->register_block_element(array(
			'id' => 'view-wrapper-article', 
			'parent' => 'view-wrapper', 
			'name' => 'View Article',
			'selector' => '.view-wrapper article'
		));

		$this->register_block_element(array(
			'id' => 'parts',
			'name' => 'Article Parts',
			'parent' => 'view-wrapper-article', 
			'selector' => '.vb-part'
		));

			$this->register_block_element(array(
				'id' => 'content-title',
				'name' => 'Title',
				'parent' => 'parts',
				'selector' => '.title-part.entry-title',
			));

				$this->register_block_element(array(
					'id' => 'title',
					'parent' => 'content-title',
					'name' => 'Title Link',
					'selector' => '.title-part.entry-title a',
				));

					$this->register_block_element(array(
						'id' => 'title-before',
						'parent' => 'content-title',
						'name' => 'Before Title',
						'selector' => '.title-part.entry-title > span',
					));

			$this->register_block_element(array(
				'id' => 'content-part',
				'name' => 'Content',
				'parent' => 'parts',
				'selector' => '.content-part',
			));

				$this->register_block_element(array(
					'id' => 'content-part-link',
					'name' => 'Content Link',
					'parent' => 'content-part',
					'selector' => '.content-part > a',
				));

			$this->register_block_element(array(
				'id' => 'thumbnail',
				'name' => 'Image Container',
				'parent' => 'parts',
				'selector' => 'figure.image-part',
			));

				$this->register_block_element(array(
					'id' => 'thumbnail-link',
					'parent' => 'thumbnail',
					'name' => 'Thumb Link',
					'selector' => 'figure.image-part > a',
				));

				$this->register_block_element(array(
					'id' => 'thumbnail-image',
					'parent' => 'thumbnail',
					'name' => 'Thumb Image &lt;img/&gt;',
					'selector' => 'figure.image-part a img',
				));

				$this->register_block_element(array(
					'id' => 'thumbnail-cover',
					'parent' => 'thumbnail',
					'name' => 'Thumb Cover',
					'selector' => 'figure.image-part .thumb-cover',
				));

				$this->register_block_element(array(
					'id' => 'cover-button',
					'parent' => 'thumbnail-cover',
					'name' => 'Cover Button',
					'selector' => '.thumb-icons .cover-button',
				));

				$this->register_block_element(array(
					'id' => 'cover-link',
					'parent' => 'thumbnail-cover',
					'name' => 'Cover Link',
					'selector' => '.thumb-icons .cover-button a',
				));

				$this->register_block_element(array(
					'id' => 'cover-link-icon',
					'parent' => 'thumbnail-cover',
					'name' => 'Cover Link Icon',
					'selector' => '.thumb-icons .cover-button a i',
				));

			$this->register_block_element(array(
				'id' => 'author-part',
				'name' => 'Author Part',
				'parent' => 'meta-parts',
				'selector' => '.author-part',
			));

				$this->register_block_element(array(
					'id' => 'author-part-before',
					'name' => 'Author Part Before',
					'parent' => 'author-part',
					'selector' => '.author-part > span',
				));

				$this->register_block_element(array(
					'id' => 'author-part-link',
					'name' => 'Author Part Link',
					'parent' => 'author-part',
					'selector' => '.author-part > a',
				));

			$this->register_block_element(array(
				'id' => 'avatar-part',
				'name' => 'Avatar Part',
				'parent' => 'meta-parts',
				'selector' => '.avatar-part',
			));

				$this->register_block_element(array(
					'id' => 'avatar-part-before',
					'name' => 'Avatar Before',
					'parent' => 'avatar-part',
					'selector' => '.avatar-part > span',
				));

				$this->register_block_element(array(
					'id' => 'avatar-part-link',
					'name' => 'Avatar Link',
					'parent' => 'avatar-part',
					'selector' => '.avatar-part > a',
				));

				$this->register_block_element(array(
					'id' => 'avatar-part-image',
					'name' => 'Avatar Image',
					'parent' => 'avatar-part',
					'selector' => '.avatar-part img',
				));

			$this->register_block_element(array(
				'id' => 'categories-part',
				'name' => 'Categories Part',
				'parent' => 'meta-parts',
				'selector' => '.categories-part',
			));

				$this->register_block_element(array(
					'id' => 'categories-part-before',
					'name' => 'Categories Before',
					'parent' => 'categories-part',
					'selector' => '.categories-part > span',
				));

				$this->register_block_element(array(
					'id' => 'categories-part-link',
					'name' => 'Categories Link',
					'parent' => 'categories-part',
					'selector' => '.categories-part > a',
				));

			$this->register_block_element(array(
				'id' => 'comments-part',
				'name' => 'Comments Part',
				'parent' => 'meta-parts',
				'selector' => '.comments-part',
			));

				$this->register_block_element(array(
					'id' => 'comments-part-before',
					'name' => 'Comments Before',
					'parent' => 'comments-part',
					'selector' => '.comments-part > span',
				));

				$this->register_block_element(array(
					'id' => 'comments-part-link',
					'name' => 'Comments Link',
					'parent' => 'comments-part',
					'selector' => '.comments-part > a',
				));

			$this->register_block_element(array(
				'id' => 'meta-parts',
				'name' => 'Meta Parts',
				'parent' => 'parts',
				'selector' => '.meta-part',
			));

				$this->register_block_element(array(
					'id' => 'meta-parts-link',
					'name' => 'Meta Parts Link',
					'parent' => 'meta-parts',
					'selector' => '.meta-part > a',
				));

			$this->register_block_element(array(
				'id' => 'date-part',
				'name' => 'Date Part',
				'parent' => 'meta-parts',
				'selector' => '.date-part',
			));

				$this->register_block_element(array(
					'id' => 'date-part-before',
					'name' => 'Date Before',
					'parent' => 'date-part',
					'selector' => '.date-part > span',
				));

				$this->register_block_element(array(
					'id' => 'date-part-link',
					'name' => 'Date Text',
					'parent' => 'date-part',
					'selector' => '.date-part time',
				));



			$this->register_block_element(array(
				'id' => 'likes-part',
				'name' => 'Like Post Part',
				'parent' => 'parts',
				'selector' => '.likes-part',
			));

				$this->register_block_element(array(
					'id' => 'likes-part-before',
					'name' => 'Like Post Before',
					'parent' => 'likes-part',
					'selector' => '.likes-part > span',
				));

				$this->register_block_element(array(
					'id' => 'likes-part-link',
					'name' => 'Like Post Link',
					'parent' => 'likes-part',
					'selector' => '.likes-part > a',
				));

					$this->register_block_element(array(
						'id' => 'likes-part-link-icon',
						'name' => 'Like Post Icon',
						'parent' => 'likes-part-link',
						'selector' => '.likes-part a i',
					));



			$this->register_block_element(array(
				'id' => 'post-format-part',
				'name' => 'Post Format Part',
				'parent' => 'parts',
				'selector' => '.post-format-part',
			));

				$this->register_block_element(array(
					'id' => 'post-format-part-before',
					'name' => 'Post Format Before',
					'parent' => 'post-format-part',
					'selector' => '.post-format-part > span',
				));

				$this->register_block_element(array(
					'id' => 'post-format-part-link',
					'name' => 'Post Format Link',
					'parent' => 'post-format-part',
					'selector' => '.post-format-part > a',
				));

				$this->register_block_element(array(
					'id' => 'post-format-part-icon',
					'name' => 'Post Format Icon',
					'parent' => 'post-format-part',
					'selector' => '.post-format-part > a i',
				));

			$this->register_block_element(array(
				'id' => 'readmore-part',
				'name' => 'Read More Part',
				'parent' => 'parts',
				'selector' => '.readmore-part',
			));


			$this->register_block_element(array(
				'id' => 'share-part',
				'name' => 'Share Part',
				'parent' => 'parts',
				'selector' => '.share-part',
			));

				$this->register_block_element(array(
					'id' => 'share-part-before',
					'name' => 'Share Before',
					'parent' => 'share-part',
					'selector' => '.share-part li.before-share',
				));

				$this->register_block_element(array(
					'id' => 'share-part-item-link',
					'name' => 'Share Item Link',
					'parent' => 'share-part',
					'selector' => '.share-part li a',
				));

				$this->register_block_element(array(
					'id' => 'share-part-item-link-image',
					'name' => 'Share Item Image',
					'parent' => 'share-part',
					'selector' => '.share-part li a img',
				));

			$this->register_block_element(array(
				'id' => 'tags-part',
				'name' => 'Tags Part',
				'parent' => 'meta-parts',
				'selector' => '.tags-part',
			));

				$this->register_block_element(array(
					'id' => 'tags-part-before',
					'name' => 'Tags Before',
					'parent' => 'tags-part',
					'selector' => '.tags-part > span',
				));

				$this->register_block_element(array(
					'id' => 'tags-part-link',
					'name' => 'Tags Link',
					'parent' => 'tags-part',
					'selector' => '.tags-part > a',
				));


			$this->register_block_element(array(
				'id' => 'time-part',
				'name' => 'Time Part',
				'parent' => 'meta-parts',
				'selector' => '.time-part',
			));

				$this->register_block_element(array(
					'id' => 'time-part-before',
					'name' => 'Time Before',
					'parent' => 'time-part',
					'selector' => '.time-part > span',
				));

				$this->register_block_element(array(
					'id' => 'time-part-link',
					'name' => 'Time Link',
					'parent' => 'time-part',
					'selector' => '.time-part > a',
				));	

		$this->register_block_element(array(
			'id' => 'carousel-controls',
			'name' => 'Carousel Controls',
			'parent' => 'view-wrapper-article', 
			'selector' => '.view-wrapper.carousel .owl-controls'
		));

			$this->register_block_element(array(
				'id' => 'carousel-direction-nav',
				'name' => 'Direction Nav',
				'parent' => 'carousel-controls', 
				'selector' => '.view-wrapper.carousel .owl-nav'
			));

				$this->register_block_element(array(
					'id' => 'carousel-direction-nav-button',
					'name' => 'Nav Button',
					'parent' => 'carousel-direction-nav', 
					'selector' => '.view-wrapper.carousel .owl-nav [class*=owl-]',
					'states' => array(
						'Hover' => '.view-wrapper.carousel .owl-nav [class*=owl-]:hover'
					)
				));

					$this->register_block_element(array(
						'id' => 'carousel-direction-nav-button-prev',
						'name' => 'Previous',
						'parent' => 'carousel-direction-nav-button', 
						'selector' => '.view-wrapper.carousel .owl-nav .owl-prev',
						'states' => array(
							'Hover' => '.view-wrapper.carousel .owl-nav .owl-prev:hover'
						)
					));

					$this->register_block_element(array(
						'id' => 'carousel-direction-nav-button-next',
						'name' => 'Next',
						'parent' => 'carousel-direction-nav-button', 
						'selector' => '.view-wrapper.carousel .owl-nav .owl-next',
						'states' => array(
							'Hover' => '.view-wrapper.carousel .owl-nav .owl-next:hover'
						)
					));

			$this->register_block_element(array(
				'id' => 'carousel-dots-nav',
				'name' => 'Dots Nav',
				'parent' => 'carousel-controls', 
				'selector' => '.view-wrapper .owl-dots'
			));

				$this->register_block_element(array(
					'id' => 'carousel-dots-nav-dot',
					'name' => 'Dot',
					'parent' => 'carousel-dots-nav', 
					'selector' => '.view-wrapper.carousel .owl-dot span',
					'states' => array(
						'Hover' => '.view-wrapper.carousel .owl-dot span:hover', 
						'Active' => '.view-wrapper.carousel .owl-dot.active span'
					)
				));


		/* Slider Controls */
		$this->register_block_element(array(
			'id' => 'slider-controls',
			'name' => 'Slider Controls',
			'parent' => 'view-wrapper-article', 
			'selector' => '.view-wrapper.slider .owl-slider-controls'
		));

			$this->register_block_element(array(
				'id' => 'slider-direction-nav',
				'name' => 'Direction Nav',
				'parent' => 'slider-controls', 
				'selector' => '.view-wrapper.slider .owl-slider-nav'
			));

				$this->register_block_element(array(
					'id' => 'slider-direction-nav-button',
					'name' => 'Nav Button',
					'parent' => 'slider-direction-nav', 
					'selector' => '.view-wrapper.slider .owl-slider-nav [class*=owl-]',
					'states' => array(
						'Hover' => '.view-wrapper.slider .owl-slider-nav [class*=owl-]:hover'
					)
				));

					
					$this->register_block_element(array(
						'id' => 'slider-direction-nav-icon',
						'name' => 'Direction Nav Icon',
						'parent' => 'slider-direction-nav-button', 
						'selector' => '.view-wrapper .owl-slider-nav [class*=owl-] span',
						'states' => array(
							'Hover' => '.view-wrapper .owl-slider-nav [class*=owl-] span:hover', 
							'Active' => '.view-wrapper .owl-slider-nav [class*=owl-] span.active'
						)
					));

					$this->register_block_element(array(
						'id' => 'slider-direction-nav-button-prev',
						'name' => 'Previous',
						'parent' => 'slider-direction-nav-button', 
						'selector' => '.view-wrapper.slider .owl-slider-nav .owl-prev',
						'states' => array(
							'Hover' => '.view-wrapper.slider .owl-slider-nav .owl-prev:hover'
						)
					));

					$this->register_block_element(array(
						'id' => 'slider-direction-nav-button-next',
						'name' => 'Next',
						'parent' => 'slider-direction-nav-button', 
						'selector' => '.view-wrapper.slider .owl-slider-nav .owl-next',
						'states' => array(
							'Hover' => '.view-wrapper.slider .owl-slider-nav .owl-next:hover'
						)
					));




			$this->register_block_element(array(
				'id' => 'slider-dots-nav',
				'name' => 'Item Nav',
				'parent' => 'slider-controls', 
				'selector' => '.view-wrapper.slider .owl-dots'
			));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-dots',
					'name' => 'Dots Nav',
					'parent' => 'slider-dots-nav', 
					'selector' => '.view-wrapper.slider .owl-dots.dots'
				));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-dot',
					'name' => 'Dot',
					'parent' => 'slider-dots-nav-dots', 
					'selector' => '.view-wrapper.slider .owl-dot span',
					'states' => array(
						'Hover' => '.view-wrapper.slider .owl-dot span:hover', 
						'Active' => '.view-wrapper.slider .owl-dot.active span'
					)
				));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-numbers',
					'name' => 'Numbers Nav',
					'parent' => 'slider-dots-nav', 
					'selector' => '.view-wrapper.slider .owl-dots.numbers'
				));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-number',
					'name' => 'Number',
					'parent' => 'slider-dots-nav-numbers', 
					'selector' => '.view-wrapper.slider .owl-dot div',
					'states' => array(
						'Hover' => '.view-wrapper.slider .owl-dot div:hover', 
						'Active' => '.view-wrapper.slider .owl-dot div.active'
					)
				));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-thumbs',
					'name' => 'Thumbs Nav',
					'parent' => 'slider-dots-nav', 
					'selector' => '.view-wrapper.slider .owl-dots.thumbs'
				));

				$this->register_block_element(array(
					'id' => 'slider-dots-nav-image',
					'name' => 'Image',
					'parent' => 'slider-dots-nav-thumbs', 
					'selector' => '.view-wrapper.slider .owl-dot img',
					'states' => array(
						'Hover' => '.view-wrapper.slider .owl-dot img:hover', 
						'Active' => '.view-wrapper.slider .owl-dot img.active'
					)
				));

		/* Filter Nav Controls */
		$this->register_block_element(array(
			'id' => 'filter-nav-controls',
			'name' => 'Filter Nav',
			'parent' => 'view-wrapper-article', 
			'selector' => '.infinity-filter'
		));

			$this->register_block_element(array(
				'id' => 'filter-nav-before-filter',
				'name' => 'Before Filter Text',
				'parent' => 'filter-nav-controls', 
				'selector' => '.before-filter'
			));

			$this->register_block_element(array(
				'id' => 'filter-nav-item',
				'name' => 'Filter Nav Item',
				'parent' => 'filter-nav-controls', 
				'selector' => '.infinity-filter li'
			));

			$this->register_block_element(array(
				'id' => 'filter-nav-link',
				'name' => 'Filter Nav Link',
				'parent' => 'filter-nav-item', 
				'selector' => '.infinity-filter li a',
				'states' => array(
					'Active' => '.infinity-filter li.active a', 
				)
			));

		/* Pagination Controls */
		$this->register_block_element(array(
			'id' => 'pagination-controls',
			'name' => 'Pagination',
			'parent' => 'view-wrapper-article', 
			'selector' => '.vb-pagination'
		));

			$this->register_block_element(array(
				'id' => 'pagination-item',
				'parent' => 'pagination-controls',
				'name' => 'Pagination Item',
				'selector' => '
					.vb-pagination a, 
					.vb-pagination span.pages, 
					.vb-pagination span.dots, 
					.vb-pagination span.current',
				'states' => array(
					'Hover' => '.vb-pagination a:hover', 
					'Focus' => '.vb-pagination a:focus', 
					'Current' => '.vb-pagination span.current'
				)
			));


		$this->register_block_element(array(
			'id' => 'woocommerce-parts',
			'name' => 'WooCommerce Parts',
			'parent' => 'view-wrapper-article', 
			'selector' => '.wc-part'
		));

			$this->register_block_element(array(
				'id' => 'woocommerce-part-add-to-cart',
				'name' => 'Add To Cart Button',
				'parent' => 'woocommerce-parts', 
				'selector' => '.add-to-cart-part'
			));

				$this->register_block_element(array(
					'name' => 'Added To Cart',
					'id' => 'woocommerce-part-added-to-cart', 
					'parent' => 'woocommerce-part-add-to-cart',
					'selector' => '.added_to_cart'
				));

			$this->register_block_element(array(
				'id' => 'woocommerce-part-price-wrap',
				'name' => 'Price Container',
				'parent' => 'woocommerce-parts', 
				'selector' => '.price-part'
			));

				$this->register_block_element(array(
					'id' => 'woocommerce-part-price-text-wrap',
					'name' => 'Price Text Container',
					'parent' => 'woocommerce-part-price-wrap', 
					'selector' => '.price-part > p'
				));

					$this->register_block_element(array(
						'id' => 'woocommerce-part-price-before',
						'name' => 'Price Before',
						'parent' => 'woocommerce-part-price-text-wrap',
						'selector' => '.price-part > p span.before-part'
					));

					$this->register_block_element(array(
						'id' => 'woocommerce-part-price-text',
						'name' => 'Price Text',
						'parent' => 'woocommerce-part-price-text-wrap',
						'selector' => '.price-part > p span.price-text'
					));

			$this->register_block_element(array(
				'id' => 'woocommerce-part-rating-wrap',
				'name' => 'Rating Wrap',
				'parent' => 'woocommerce-parts', 
				'selector' => '.rating-part'
			));
			
				$this->register_block_element(array(
					'id' => 'woocommerce-part-rating-before', 
					'parent' => 'woocommerce-part-rating-wrap',
					'name' => 'Rating Before',
					'selector' => '.rating-part span.before-part'
				));

				$this->register_block_element(array(
					'id' => 'woocommerce-part-rating-review-count', 
					'parent' => 'woocommerce-part-rating-wrap',
					'name' => 'Rating Review',
					'selector' => '.rating-part p.wc-rating-review'
				));

					$this->register_block_element(array(
						'parent' => 'woocommerce-part-rating-review-count', 
						'id' => 'woocommerce-part-rating-review-count-link',
						'name' => 'Rating Review Link',
						'selector' => '.rating-part p.wc-rating-review'
					));

				$this->register_block_element(array(
					'id' => 'woocommerce-part-rating-star-rating', 
					'parent' => 'woocommerce-part-rating-wrap',
					'name' => 'Stars Rating',
					'selector' => '.rating-part .star-rating'
				));

			$this->register_block_element(array(
				'id' => 'woocommerce-part-sale-flash-part',
				'name' => 'Sale Flash Wrap',
				'parent' => 'woocommerce-parts', 
				'selector' => '.sale-flash-part'
			));

				$this->register_block_element(array(
					'parent' => 'woocommerce-part-sale-flash-part',
					'name' => 'Sale Text',
					'id' => 'woocommerce-part-sale-flash-text', 
					'selector' => '.sale-flash-part span.sale-flash-text'
				));

				$this->register_block_element(array(
					'parent' => 'woocommerce-part-sale-flash-part',
					'name' => 'Before Sale',
					'id' => 'woocommerce-part-sale-flash-before', 
					'selector' => '.sale-flash-part span.before-part'
				));

				$this->register_block_element(array(
					'parent' => 'woocommerce-part-sale-flash-part',
					'name' => 'After Sale',
					'id' => 'woocommerce-part-sale-flash-after', 
					'selector' => '.sale-flash-part span.after-part'
				));








	}
	
	function content($block) {
		
		$view_id = parent::get_setting($block, 'view', null); 

		if( $view_id == null ) {

			echo '
				<div class="alert alert-yellow"><p>You have not selected a view. Open the block options and select a view which you can then style in Headway using design mode.</p></div>
			';

		} else {

			$options = vb_options( $view_id );

			$builder_options = TitanFramework::getInstance( 'builder-options' );

			$layout = (!empty($builder_options->getOption( 'view-layout-' . $view_id . '' ))) ? $builder_options->getOption( 'view-layout-' . $view_id . '' ) : 'blog';

			echo vb_render_view( $view_id, $layout, null, 'shortcode' );

		}
		
	}
	
	
}