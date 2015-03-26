<?php

	extract( shortcode_atts( array(
			'thumb_align'                    => 'left',
			'auto_size'                      => 'on',
			'auto_size_container_width'		=> '940',
			'crop_vertically'        			=> 'on',
			'columns'                        => 4,//make = number of columns
			'thumbnail_width'               	=> '250',
			'thumbnail_height'              	=> '200',
			'crop_vertically_height_ratio' 	=> '60',
			'show_cover'               	=> true,
			'thumb_cover_effect'        	=> 'ImageFade',
			'thumb_cover_type'          	=> 'icons',//icons or content
			'thumb_content_hover_effect'   	=>  'H',
			'thumb_icon_effect'             	=> 'StyleH',
			'thumb_icon_style'              	=> 'WhiteRounded',
			'cover_button1'             	=> 'search',
			'cover_button_link1'        	=> 'lightbox',
			'cover_button2'             	=> 'link',
			'cover_button_link2'        	=> 'content',
			'cover_button3'             	=> null,
			'cover_button_link3'        	=> null,
			'cover_button4'             	=> null,
			'cover_button_link4'        	=> null,
			'content_vertical_align' 		=> 'center',
			'lightbox_width'                 => '1024',
			'lightbox_height'               	=> '768',
			'display_as' => null

		), $atts ) );

	global $post;
	$id = $post->ID;

	$view_name = strtolower(str_replace(' ', '-', views()->view_name));
	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$btns = $builder_options->getOption( 'image-cover-icon-type-icons-' . $view_name . '' );
	if ( empty($btns) ) {
		$btns = array('btn1', 'btn2');
	}
	$display_as = $display_as != null ? ' display-' . $display_as : null;
	$layout = (!empty($builder_options->getOption( 'view-layout-' . $id . '' ))) ? $builder_options->getOption( 'view-layout-' . $id . '' ) : 'blog';

	if ( $layout == 'slider' ||  $layout == 'blog') {
		$columns	= '1';
	}

	ob_start(); 

	if ( has_post_thumbnail()) { 
	
	$approx_img_width = ($auto_size_container_width / $columns);

	$thumbnail_id = get_post_thumbnail_id();  

	$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'full');  

	if ( $auto_size == 'on' ) {

		/* all images height depends on ratios so set to '' */
		$thumbnail_height = '';
		$thumbnail_width = $approx_img_width + 10; /* Add a 10px buffer to insure that image will be large enough */

		/* if crop vertically make all images the same height */
		if ( $crop_vertically == 'on' )
			$thumbnail_height = round($approx_img_width * ($crop_vertically_height_ratio) * .01);

		$thumbnail_url = vb_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

	} else {

		/* if crop vertically make all images the same height */
		if ( $crop_vertically == 'on' )
			$thumbnail_height = round($thumbnail_height  * ($crop_vertically_height_ratio) *  .01);

		$thumbnail_url    = vb_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

	}

	$lightbox_url = 'data-src="' . esc_url(vb_resize_image( $thumbnail_object[0], $lightbox_width, $lightbox_height )) . '"';

	$figure_class = ($thumb_cover_type == 'content') ? ' ContentWrapper'. $thumb_content_hover_effect .' chrome-fix' : null;

	?>

	<figure class="vb-part align<?php echo $thumb_align; ?><?php echo $display_as; ?> image-part infinity-cover<?php echo $figure_class; ?>">

		<?php if ( $show_cover && $thumb_cover_type == 'icons' ) : ?>
			<div class="<?php echo $thumb_cover_effect; ?> thumb-cover"></div>
         <div class="<?php echo $thumb_icon_effect; ?> thumb-icons">

				<?php 

				if ( $btns ) {

					foreach ($btns as $position => $btn) {

						if ( $btn == 'btn1' ) { 
							$use_lightbox = ($cover_button_link1 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="cover-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($cover_button_link1)) : ?>
			             		<?php if ($cover_button_link1 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($cover_button_link1 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $cover_button_link1; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $cover_button1 ?>"></i>
			             	<?php if ($cover_button_link1) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn2' ) { 
							$use_lightbox = ($cover_button_link2 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="cover-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($cover_button_link2)) : ?>
			             		<?php if ($cover_button_link2 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($cover_button_link2 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $cover_button_link2; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $cover_button2 ?>"></i>
			             	<?php if ($cover_button_link2) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn3' ) { 
							$use_lightbox = ($cover_button_link3 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="cover-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($cover_button_link3)) : ?>
			             		<?php if ($cover_button_link3 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($cover_button_link3 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $cover_button_link3; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $cover_button3 ?>"></i>
			             	<?php if ($cover_button_link3) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn4' ) { 
							$use_lightbox = ($cover_button_link4 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="cover-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($cover_button_link4)) : ?>
			             		<?php if ($cover_button_link4 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($cover_button_link4 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $cover_button_link4; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $cover_button4 ?>"></i>
			             	<?php if ($cover_button_link4) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

					}

				}

				 ?>


         </div>

      <?php endif; ?>

      <?php if ( $show_cover && $thumb_cover_type == 'content') :  ?>

      	<div class="<?php echo 'Content'. $thumb_content_hover_effect . ''; ?> content-cover">
            <div class="Content content-cover-content vertical-<?php echo $content_vertical_align; ?>">
                 
					<?php 
						$cover_parts = $builder_options->getOption( 'image-parts-content-type-' . $view_name . '' );
						if ( empty($cover_parts) ) {
							$cover_parts = array('title', 'categories', );
						}
						View_Builder_Shortcodes::get_post_parts( $builder_options, $cover_parts, $view_name, false );
					?>

            </div>
         </div>

		<?php endif; ?>

			<a href="<?php echo get_permalink() ?>" class="post-thumb" title="<?php echo get_the_title(); ?>">
			<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo get_the_title(); ?>"  width="<?php echo $thumbnail_width; ?>" height="<?php echo $thumbnail_height; ?>"/>
		</a>


	</figure>

	<?php } ?>

	<?php $content =  ob_get_clean(); ?>

