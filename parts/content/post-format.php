<?php

	extract( shortcode_atts( array(
		'before' => '',
		'display_as' => 'inline-block',
		'format_type' => 'icon',
		'icon_size' => '32'
	), $atts ) );

	global $post;
	$id = $post->ID;

	$format = get_post_format( $id );
	$format_link = get_post_format_link($format);

	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	$display_format = $format_type == 'icon' ? '<i class="dashicons dashicons-format-'. $format .'"></i>' : $format;

	ob_start(); ?>

	<?php if ($format_link) : ?>

		<div class="vb-part post-format-part<?php echo $display_as; ?>">
			<?php echo $before; ?>
			<a href="<?php echo $format_link ?>"><?php echo $display_format; ?></a>
		</div>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

