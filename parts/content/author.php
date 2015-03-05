<?php

	extract( shortcode_atts( array(
		'linked' => 'on',
		'before' => false,
		'display_as' => 'inline',
	), $atts ) );

	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	global $authordata; ?>

	<span class="vb-part author-part<?php echo $display_as ?> meta-part">
	
		<?php if($linked == 'off') : ?>
			
			<?php echo $before ?><?php echo $authordata->display_name; ?>

		<?php elseif ( $linked == 'on' ) : ?>

			<?php echo $before ?><a class="author-link fn nickname url" href="<?php echo get_author_posts_url($authordata->ID) ?>" title="View all posts by <?php echo $authordata->display_name ?>"><?php echo $authordata->display_name ?></a>

		<?php endif; ?>

	</span>

	<?php $content =  ob_get_clean(); ?>

