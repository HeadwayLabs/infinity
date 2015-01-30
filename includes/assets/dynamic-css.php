<?php header("Content-type: text/css");  ?>

<?php 

$args = array(
	'post_type' => 'view',
	'posts_per_page'        => '-1',
);

$the_query = new WP_Query( $args );

$meta= array();

if ( $the_query->have_posts() ) :

	while($the_query->have_posts()) : $the_query->the_post();

		$id = $the_query->post->ID;

		$options = get_post_meta( $id, 'view_options', true ); 
		$instance = strtolower(str_replace(' ', '_', get_the_title()));

		$view_name = strtolower(str_replace(' ', '-', get_the_title()));

		$builder_options = TitanFramework::getInstance( 'builder-options' );
		$layout = strtolower($builder_options->getOption( 'view-layout-' . $id . '' ));

		$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' ); 
		?>

		<?php if( is_array($parts) ) : ?>

			<?php if (in_array('title', $parts)) : ?>
				 
				#view-<?php echo $id; ?> .date-part  {

					<?php if( $builder_options->getOption( 'date-styles-color-' . $view_name . '' ) ) : ?>
						color: <?php echo $builder_options->getOption( 'date-styles-color-' . $view_name . '' ) ?>;
					<?php endif; ?>

				}

			<?php endif ?>

			/* Post Format */

			<?php if (in_array('post-format', $parts)) : ?>

			<?php $icon_size = $builder_options->getOption( 'post-format-option-icon-size-' . $view_name . '' );
				$icon_size = $icon_size == null ? '32' : $icon_size;
			?>
				 
			#view-<?php echo $id; ?> .post-format-part i  {

				font-size: <?php echo $icon_size; ?>px;

			}

			<?php endif ?>

		<?php endif; ?>
		
	<?php endwhile; ?>

<?php endif;

wp_reset_postdata();

?>
