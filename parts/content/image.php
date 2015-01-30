<?php

	extract( shortcode_atts( array(
			'thumb_align'                    => 'left',
			'auto_size'                      => false,
			'auto_size_container_width'		=> '940',
			'crop_vertically'        			=> false,
			'columns'                        => 4,//make = number of columns
			'thumbnail_width'               	=> '200',
			'thumbnail_height'              	=> '120',
			'crop_vertically_height_ratio' 	=> '75',
			'show_spotlight'               	=> true,
			'thumb_spotlight_effect'        	=> 'vb-overlay',
			'thumb_spotlight_type'          	=> 'icons',//icons or content
			'thumb_content_hover_effect'   	=>  'Al',
			'thumb_icon_effect'             	=> 'CStyleC',
			'thumb_icon_style'              	=> 'WhiteSquare',
			'spotlight_button1'             	=> 'search',
			'spotlight_button_link1'        	=> false,
			'spotlight_button2'             	=> 'link',
			'spotlight_button_link2'        	=> false,
			'spotlight_button3'             	=> false,
			'spotlight_button_link3'        	=> false,
			'spotlight_button4'             	=> false,
			'spotlight_button_link4'        	=> false,
			'lightbox_width'                 => '1024',
			'lightbox_height'               	=> '768',
			'display_as' => null

		), $atts ) );

	global $post;
	$id = $post->ID;

	$view_name = strtolower(str_replace(' ', '-', views()->view_name));
	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$btns = $builder_options->getOption( 'image-spotlight-icon-type-icons-' . $view_name . '' );
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); 

	if ( has_post_thumbnail()) { 
	
	$approx_img_width = ($auto_size_container_width / $columns);

	$thumbnail_id = get_post_thumbnail_id();  

	$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'full');  

	if ( $auto_size ) {

		/* all images height depends on ratios so set to '' */
		$thumbnail_height = '';
		$thumbnail_width = $approx_img_width + 10; /* Add a 10px buffer to insure that image will be large enough */

		/* if crop vertically make all images the same height */
		if ( $crop_vertically )
			$thumbnail_height = round($approx_img_width * ($crop_vertically_height_ratio) * .01);

		$thumbnail_url = vb_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

	} else {

		/* if crop vertically make all images the same height */
		// if ( $crop_images_vertically )
		// 	$thumbnail_height = round($thumbnail_height  * ($crop_vertically_height_ratio) *  .01);

		$thumbnail_url    = vb_resize_image($thumbnail_object[0], $thumbnail_width, $thumbnail_height);

	}

	$lightbox_url = 'data-src="' . esc_url(vb_resize_image( $thumbnail_object[0], $lightbox_width, $lightbox_height )) . '"';

	$figure_class = ($thumb_spotlight_type == 'content') ? ' ContentWrapper'. $thumb_content_hover_effect .' chrome-fix' : null;


	?>

	<figure class="vb-part align<?php echo $thumb_align; ?><?php echo $display_as; ?> image-part vb-spotlight<?php echo $figure_class; ?>">

		<a href="<?php echo get_permalink() ?>" class="post-thumbnail" title="<?php echo get_the_title(); ?>">
			<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo get_the_title(); ?>"  width="<?php echo $thumbnail_width; ?>" height="<?php echo $thumbnail_height; ?>"/>
		</a>

		<?php if ( $show_spotlight && $thumb_spotlight_type == 'icons') : ?>
			<div class="<?php echo $thumb_spotlight_effect; ?>"></div>
         <div class="<?php echo $thumb_icon_effect; ?>">

				<?php 

				if ( $btns ) {

					foreach ($btns as $position => $btn) {

						if ( $btn == 'btn1' ) { 
							$use_lightbox = ($spotlight_button_link1 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="spotlight-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($spotlight_button_link1)) : ?>
			             		<?php if ($spotlight_button_link1 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($spotlight_button_link1 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $spotlight_button_link1; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $spotlight_button1 ?>"></i>
			             	<?php if ($spotlight_button_link1) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn2' ) { 
							$use_lightbox = ($spotlight_button_link2 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="spotlight-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($spotlight_button_link2)) : ?>
			             		<?php if ($spotlight_button_link2 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($spotlight_button_link2 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $spotlight_button_link2; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $spotlight_button2 ?>"></i>
			             	<?php if ($spotlight_button_link2) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn3' ) { 
							$use_lightbox = ($spotlight_button_link3 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="spotlight-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($spotlight_button_link3)) : ?>
			             		<?php if ($spotlight_button_link3 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($spotlight_button_link3 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $spotlight_button_link3; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $spotlight_button3 ?>"></i>
			             	<?php if ($spotlight_button_link3) : ?>
			             	</a>
			             	<?php endif; ?>
		             	</span>

						<?php 
						}

						if ( $btn == 'btn4' ) { 
							$use_lightbox = ($spotlight_button_link4 == 'lightbox') ? 'vb-lightbox' : null;
							?>
							
							<span class="spotlight-button <?php echo $use_lightbox.' '.$thumb_icon_style; ?>">
			             	<?php if (!empty($spotlight_button_link4)) : ?>
			             		<?php if ($spotlight_button_link4 == 'lightbox') : ?>
			             			<a href="#" <?php echo $lightbox_url; ?>>
			             		<?php elseif ($spotlight_button_link4 == 'content') : ?>
			             			<a href="<?php echo get_permalink($id); ?>">
			             		<?php else : ?> 
			             			<a href="<?php echo $spotlight_button_link4; ?>">
			             		<?php endif; ?>   
			             	<?php endif; ?>
			             		<i class="fa fa-<?php echo $spotlight_button4 ?>"></i>
			             	<?php if ($spotlight_button_link4) : ?>
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

      <?php if ( $show_spotlight && $thumb_spotlight_type == 'content') :  ?>

      	<div class="<?php echo 'Content'. $thumb_content_hover_effect . ''; ?>">
            <div class="Content">
                 
					<?php View_Builder_Shortcodes::get_thumb_contents_parts( $builder_options ); ?>

            </div>
         </div>

		<?php endif; ?>

	</figure>

	<?php } ?>

	<?php $content =  ob_get_clean(); ?>

