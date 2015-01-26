<?php

	extract( shortcode_atts( array(
		'before' => false,
		'display_as' => 'inline',
	), $atts ) );

	global $post;
	$id = $post->ID;
  	$post = get_post( $post->ID );
  	$post_type = $post->post_type;
	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start(); ?>
			
	<?php 

	$taxonomies = get_object_taxonomies( $post_type, 'objects' );

	foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) {

		if (strpos($taxonomy_slug, 'cat') === FALSE)
			continue;

		$i = '';
		$c = count(get_the_terms( $id, $taxonomy_slug ));

		$terms = get_the_terms( $id, $taxonomy_slug );

		if ( !empty( $terms ) ) {

		echo '<span class="vb-part categories-part' . $display_as . ' meta-part">';

			echo $before;

			foreach ( $terms as $term ) {
				$i++;
				$end = ($i == $c) ? ' ' : ', ';
				echo '<a href="'
				.    get_term_link( $term->slug, $taxonomy_slug ) .'" class="category-item '. $term->slug .'">'
				.    $term->name
				.	  $end
				. "</a>\n";
			}

		echo '</span>';

		}

	}

 	?>

	<?php $content =  ob_get_clean(); ?>

