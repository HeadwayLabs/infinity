<?php

	extract( shortcode_atts( array(
		'date_format' 	=> 'wordpress-default',
		'before' => '',
		'display_as' => 'inline-block'
	), $atts ) );

	$before = $before != false ? '<span>' . $before . '</span>' : null;

	$date = ($date_format != 'wordpress-default') ? get_the_time($date_format) : get_the_date();
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); ?>

	<span class="vb-part date-part<?php echo $display_as; ?> meta-part">
		<?php echo $before; ?>
		<time datetime="<?php get_the_time('c'); ?>">
			<?php echo $date; ?>
		</time>
	</span>

	<?php $content =  ob_get_clean(); ?>

