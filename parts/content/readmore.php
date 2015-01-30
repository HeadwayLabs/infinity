<?php 

	extract( shortcode_atts( array(
			'excerpt_limit' 	=> '140',
			'show_always'		=> false,
			'more_text'		=> 'Read more',
			'display_as' => 'block'
		), $atts ) );

	global $post;
	$id = $post->ID;

	ob_start();

	$excerpt_length = strlen( get_the_excerpt() );
	$excerpt_limit++;

	$display_as = $display_as != null ? ' display-' . $display_as : null;

	$more_link = '<a href="'. get_permalink($post->ID) . '" class="vb-part readmore-part' . $display_as . '">' . $more_text . '</a>';
	
	if ( $excerpt_length > $excerpt_limit || $show_always == true ) {
		
		echo $more_link;

	}

	$content = ob_get_contents();
	
 	ob_end_clean();

?>