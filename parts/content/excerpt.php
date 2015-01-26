<?php 

	extract( shortcode_atts( array(
			'content_to_show' 	=> 'excerpt',
			'excerpt_length'		=> '140',
			'excerpt_more'			=> '...',
			'display_as' => null
		), $atts ) );

	global $post;
	$id = $post->ID;

	$display_as = $display_as != null ? ' display-' . $display_as : null;

	ob_start();

	if ( $content_to_show == 'excerpt' ) {

		echo '<p class="vb-part excerpt-part entry-content' . $display_as . '">' . get_trimmed_excerpt($excerpt_length, $excerpt_more) . '</p>';

	} elseif ( $content_to_show == 'content' ) {

		echo '<div class="vb-part excerpt-part entry-content' . $display_as . '">' . get_formatted_content() . '</div>';

	}

	$content = ob_get_contents();
	
 	ob_end_clean();

?>