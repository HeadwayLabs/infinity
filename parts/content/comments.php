<?php

	extract( shortcode_atts( array(
		'comment_format' => 'Comments',
		'comment_format_1' => 'Comment',
		'comment_format_0' => 'Comments',
		'before' => false,
		'display_as' => 'inline',
	), $atts ) );

	global $post;
	$id = $post->ID;

	if ( (int)get_comments_number($id) === 0 ) 
		$comments_format = stripslashes('%num% '.$comment_format_0);
	elseif ( (int)get_comments_number($id) == 1 ) 
		$comments_format = stripslashes('%num% '.$comment_format_1);
	elseif ( (int)get_comments_number($id) > 1 ) 
		$comments_format = stripslashes('%num% '.$comment_format);
	
	$before = $before != false ? '<span>' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	$comments = str_replace('%num%', get_comments_number($id), $comments_format);

	?>

	<span class="vb-part comments-part<?php echo $display_as; ?> meta-part">
		<?php echo $before; ?>
		<a href="<?php echo get_comments_link() ?>" title="<?php echo get_the_title() ?> Comments" class="entry-comments"><?php echo $comments ?></a>
	</span>

	<?php $content =  ob_get_clean(); ?>

