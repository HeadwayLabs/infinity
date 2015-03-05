<?php

	extract( shortcode_atts( array(
		'linked' => 'on',
		'avatar_size' => 32,
		'before' => false,
		'display_as' => 'inline',
	), $atts ) );

	global $authordata;
	$avatar_img = get_avatar( get_the_author_meta('email'), $avatar_size );
	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;
	?>

	<figure class="vb-part avatar-part<?php echo $display_as ?> meta-part">
	
		<?php if($linked == 'off') : ?>
			
			<?php echo $before ?><?php echo $avatar_img; ?>

		<?php elseif ( $linked == 'on' ) : ?>

			<?php echo $before ?><a class="author-avatar fn nickname url" href="<?php echo get_author_posts_url($authordata->ID) ?>" title="View all posts by <?php echo $authordata->display_name ?>"><?php echo $avatar_img ?></a>

		<?php endif; ?>

	</figure>

	<?php $content =  ob_get_clean(); ?>

