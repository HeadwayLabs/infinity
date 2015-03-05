<?php

	extract( shortcode_atts( array(
		'facebook_image'		=> 'fa-facebook',
		'facebook_target'		=> 'on',
		'twitter_image'		=> 'fa-twitter',
		'twitter_target'		=> 'on',
		'linkedin_image'		=> 'fa-linkedin',
		'linkedin_target'		=> 'on',
		'googleplus_image'	=> 'fa-google-plus',
		'googleplus_target'	=> 'on',
		'before' 				=> 'Share this: ',
		'display_as' 			=> 'block',
	), $atts ) );

	global $post;
	$id = $post->ID;

	$view_name = strtolower(str_replace(' ', '-', views()->view_name));
	$builder_options = TitanFramework::getInstance( 'builder-options' );
	
	$before = $before != false ? '<li class="before-share">' . $before . '</li>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	$facebook_target = $facebook_target == 'on' ? ' target="_blank"' : null;
	$twitter_target = $twitter_target == 'on' ? ' target="_blank"' : null;
	$googleplus_target = $googleplus_target == 'on' ? ' target="_blank"' : null;
	$linkedin_target = $linkedin_target == 'on' ? ' target="_blank"' : null;

	?>

	<?php if ( $facebook_image || $twitter_image || $linkedin_image || $googleplus_image ) : ?>

	<ul class="vb-part share-part<?php echo $display_as ?> clearfix">

		<?php echo $before; ?>

		<?php if( $facebook_image ) : ?>
		<li>
			<a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(the_permalink()); ?>" class="fb" <?php echo $facebook_target; ?>>
				<i class="fa <?php echo $facebook_image; ?>"></i>
			</a>
		</li>
		<?php endif; ?>

		<?php if( $twitter_image ) : ?>
		<li>
			<a href="https://twitter.com/share?url=<?php echo urlencode(the_permalink()); ?>&amp;text=<?php echo urlencode(the_title()); ?>" class="tw" <?php echo $twitter_target; ?>>
				<i class="fa <?php echo $twitter_image; ?>"></i>
			</a>
		</li>
		<?php endif; ?>

		<?php if( $googleplus_image ) : ?>
		<li>
			<a href="https://plusone.google.com/_/+1/confirm?hl=en&amp;url=<?php echo urlencode(the_permalink()); ?>" class="gp" <?php echo $googleplus_target; ?>>
				<i class="fa <?php echo $googleplus_image; ?>"></i>
			</a>
		</li>
		<?php endif; ?>

		<?php if( $linkedin_image ) : ?>
		<li>
			<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(the_permalink()); ?>&amp;title=<?php echo urlencode(the_title()); ?>" class="lin" <?php echo $linkedin_target; ?>>
				<i class="fa <?php echo $linkedin_image; ?>"></i>
			</a>
		</li>
		<?php endif; ?>

	</ul>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

