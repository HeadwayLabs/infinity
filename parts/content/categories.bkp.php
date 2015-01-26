<?php

	extract( shortcode_atts( array(
		'before' => false,
		'display_as' => 'inline',
	), $atts ) );

	global $post;
	$id = $post->ID;

	$cats = '';
	$i = '';
	$c = count(get_the_category($id));
	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); ?>

	<?php if ( $c != 0 ) : ?>
		
		<span class="vb-part categories-part<?php echo $display_as ?> meta-part">
		
		<?php echo $before; ?>

		<?php
		foreach((get_the_category($id)) as $category) {
			$i++;
			echo '<a href="'.get_category_link($category->term_id).'" class="part-category '. $category->slug .'">'.$category->cat_name.'</a>';
			echo ($i == $c) ? ' ' : ', ';
		}
		?>

		</span>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

