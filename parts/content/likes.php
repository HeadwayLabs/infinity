<?php

	extract( shortcode_atts( array(
		'before' 				=> 'Likes: ',
		'display_as' 			=> 'block',
		'show_like_text'     =>  'show'
	), $atts ) );

	global $post;
	$id = $post->ID;

	$before = $before != false ? '<span class="before-share">' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); ?>

	<div class="vb-part likes-part<?php echo $display_as; ?>">
		<?php echo $before; ?>
		<?php echo getPostLikeLink( get_the_ID(), $show_like_text ); ?>
	</div>

	<?php $content =  ob_get_clean(); ?>
