<?php 

	extract( shortcode_atts( array(
			'html_tag' 	=> 'h1',
			'linked'		=> true,
			'shorten'	=>	false,
			'limit'		=> 50,
			'before'		=> null,
			'display_as' => null
		), $atts ) );

	global $post;
	$id = $post->ID;

	/* Shorten Title */
	$title_text = get_the_title($id);
	$title_length = mb_strlen($title_text);

	$title = substr($title_text, 0, $limit);
	if ($title_length > $limit) 
		$title .= "...";

	$display_as = $display_as != null ? ' display-' . $display_as : null;
	$before = $before != false ? '<span>' . $before . '</span>' : null;

	if (!$shorten)
		$title = get_the_title($id);

	ob_start();

	if($linked)
		echo '
		<' . $html_tag . ' class="vb-part title-part entry-title' . $display_as . '">
			'. $before . '
			<a href="'. get_permalink($id) .'" rel="bookmark" title="'. the_title_attribute (array('echo' => 0) ) .'">'. $title .'</a>
		</' . $html_tag . '>';
	if(!$linked)
		echo '
		<' . $html_tag . ' class="vb-part title-part entry-title' . $display_as . '">
			'. $before . '
			'. $title .'
		</' . $html_tag . '>';

	$content = ob_get_contents();
	
 	ob_end_clean();

?>