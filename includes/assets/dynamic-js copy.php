<?php
  header("Content-type: application/javascript");
?>

<?php 

		// $args = array(
		// 	'post_type' => 'view',
		// 	'posts_per_page'        => '-1',
		// );

		// $the_query = new WP_Query( $args );

		// $meta= array();

		// if ( $the_query->have_posts() ) : ?>

		// 	(function ($) {
		// 			$(document).ready(function() { 

		// 	<?php 
		// 	while($the_query->have_posts()) : $the_query->the_post();

		// 		$id = $the_query->post->ID;

		// 		$options = get_post_meta( $id, 'view_options', true ); 
		// 		$instance = strtolower(str_replace(' ', '_', get_the_title()));

		// 		$view_name = strtolower(str_replace(' ', '-', get_the_title()));

		// 		$builder_options = TitanFramework::getInstance( 'builder-options' );
		// 		$layout = strtolower($builder_options->getOption( 'view-layout-' . $id . '' ));
	
		// 		$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

		// 		if (in_array('image', $parts))
		// 			 echo '$(\'#view-' . $layout . '-' . $view_name . ' > article\').hoverdir()';

				
				
		// 	endwhile; ?>

		// 			});
		// 		})(jQuery);

		// <?php endif;

		// wp_reset_postdata();

 ?>


