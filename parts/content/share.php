<?php

	extract( shortcode_atts( array(
		'share_icon_image_w'	=> 32,
		'share_icon_image_h'	=> 32,
		'facebook_image'		=> views()->plugin_url.'parts/content/images/social/share-facebook.png',
		'facebook_target'		=> false,
		'twitter_image'		=> views()->plugin_url.'parts/content/images/social/share-twitter.png',
		'twitter_target'		=> true,
		'linkedin_image'		=> views()->plugin_url.'parts/content/images/social/share-linkedin.png',
		'linkedin_target'		=> true,
		'googleplus_image'	=> views()->plugin_url.'parts/content/images/social/share-googleplus.png',
		'googleplus_target'	=> true,
		'before' 				=> 'Share this: ',
		'display_as' 			=> 'block',
	), $atts ) );

	global $post;
	$id = $post->ID;

	$view_name = strtolower(str_replace(' ', '-', views()->view_name));
	$builder_options = TitanFramework::getInstance( 'builder-options' );
	
	$before = $before != false ? '<li class="before-share">' . $before . '</li>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	$facebook_target = $facebook_target ? ' target="_blank"' : null;
	$twitter_target = $twitter_target ? ' target="_blank"' : null;
	$googleplus_target = $googleplus_target ? ' target="_blank"' : null;
	$linkedin_target = $linkedin_target ? ' target="_blank"' : null;

	if ( is_numeric( $facebook_image ) ) {
	
	   $facebook_image = vb_resize_image(wp_get_attachment_image_src( $facebook_image )[0], $share_icon_image_w, $share_icon_image_h);

	} 

	if ( is_numeric( $twitter_image ) ) {
	
	   $twitter_image = vb_resize_image(wp_get_attachment_image_src( $twitter_image )[0], $share_icon_image_w, $share_icon_image_h);

	} 

	if ( is_numeric( $linkedin_image ) ) {

	   $linkedin_image = vb_resize_image(wp_get_attachment_image_src( $linkedin_image )[0], $share_icon_image_w, $share_icon_image_h);

	} 

	if (is_numeric( $googleplus_image ) ) {
	
	   $googleplus_image = vb_resize_image(wp_get_attachment_image_src( $googleplus_image )[0], $share_icon_image_w, $share_icon_image_h);

	} 

	?>

	<?php if ( $facebook_image || $twitter_image || $linkedin_image || $googleplus_image ) : ?>

	<ul class="vb-part share-part<?php echo $display_as ?> clearfix">

		<?php echo $before; ?>

		<?php if( $facebook_image ) : ?>
		<li>
			<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(the_permalink()); ?>" class="fb" <?php echo $facebook_target; ?>>
				<img src="<?php echo $facebook_image; ?>" alt="facebook">
			</a>
		</li>
		<?php endif; ?>

		<?php if( $twitter_image ) : ?>
		<li>
			<a href="https://twitter.com/share?url=<?php echo urlencode(the_permalink()); ?>&amp;text=<?php echo urlencode(the_title()); ?>" class="tw" <?php echo $twitter_target; ?>>
				<img src="<?php echo $twitter_image; ?>" alt="twitter">
			</a>
		</li>
		<?php endif; ?>

		<?php if( $googleplus_image ) : ?>
		<li>
			<a href="https://plusone.google.com/_/+1/confirm?hl=en&amp;url=<?php echo urlencode(the_permalink()); ?>" class="gp" <?php echo $googleplus_target; ?>>
				<img src="<?php echo $googleplus_image; ?>" alt="google-plus">
			</a>
		</li>
		<?php endif; ?>

		<?php if( $linkedin_image ) : ?>
		<li>
			<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(the_permalink()); ?>&amp;title=<?php echo urlencode(the_title()); ?>" class="lin" <?php echo $linkedin_target; ?>>
				<img src="<?php echo $linkedin_image; ?>" alt="linkedin" title="linkedin">
			</a>
		</li>
		<?php endif; ?>

	</ul>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

