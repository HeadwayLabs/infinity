<?php

	extract( shortcode_atts( array(
		'time_format' 	=> 'wordpress-default',
		'show_time_since'	=> 'on',
		'before' => '',
		'display_as' => 'inline-block',
	), $atts ) );

	global $post;
	$id = $post->ID;

	$time = ($time_format != 'wordpress-default') ? get_the_time($time_format) : get_the_time();
	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); ?>

	<?php if ( $show_time_since == 'off' ) : ?>
		
		<span class="vb-part time-part<?php echo $display_as ?> meta-part"><?php echo $before ?><?php echo $time ?></span>

	<?php elseif ( $show_time_since == 'on' ) : ?>

	<time class="vb-part time-part time-since<?php echo $display_as ?> meta-part" datetime="<?php echo get_the_time('c') ?>">
		<?php echo $before ?>
		<a href="<?php echo get_post_permalink($id) ?>" rel="bookmark" class="time" title="<?php echo the_title_attribute (array('echo' => 0) ) ?>">
			<?php echo time_since(get_the_time('U')) ?>
		</a>
	</time>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

