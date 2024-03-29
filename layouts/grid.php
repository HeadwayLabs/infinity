<?php
/**
 * View Template: Grid
 *
 * Display content in a grid
 *
 * The "View Template:" bit above allows this to be selectable
 * from a dropdown menu on the edit loop screen.
 *
 * @package View Builder
 * @since 0.1
 */

$builder_options = TitanFramework::getInstance( 'builder-options' );
$options = views()->options;
$view_name = strtolower(str_replace(' ', '-', views()->view_name));
$view_id = views()->id;
$grid_spacing = views()->get_infinity_option( 'postopts-post-spacing-' . $view_id . '', '20' )/2;
$style_name = views()->get_infinity_option( 'style-name-' . $view_name . '', 'headway' );
$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );
if ( empty($parts) ) {
	$parts = array('title', 'image', 'excerpt', 'date', 'readmore');
}
?>

<style>

#view-<?php echo views()->id; ?> article { margin: <?php echo $grid_spacing; ?>px}

</style>

<div id="wrapper-<?php echo views()->id; ?>">

	<div id="view-<?php echo views()->id; ?>" class="filter-content view-wrapper grid <?php echo $view_name; ?>-view <?php echo $style_name; ?>" data-view="<?php echo $view_name; ?>">

		<?php if ( have_posts() ) : ?>

			<?php while( have_posts() ) : the_post(); ?>

				<?php views()->count++; ?>

				<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry">
		
					<div class="article-inner">
						<?php View_Builder_Shortcodes::get_post_parts( $builder_options, $parts, $view_name ); ?>
					</div>

				</article>

			<?php endwhile; ?>

		<?php else : ?>

			<?php load_template( views()->plugin_dir . 'parts/layout/not-found.php', false ); ?>

		<?php endif; ?>

		<?php load_template( views()->plugin_dir . 'parts/layout/pagination.php', false ); ?>

	</div>

</div>

<?php 



	$responsive_js = null;

	/* Carousel Group  */
	if ( ! empty( $options['grid-group'] ) ) {

		$meta_query = array();

		$numItems = count($options['grid-group']);
		$i = 0;

		foreach ( $options['grid-group'] as $option ) {

			$width = $option['grid-width'];
			$columns = $option['grid-columns']; 


			if(++$i === $numItems) {
				$responsive_js .= '['. $width .', '. $columns .']';
			} else {
				$responsive_js .= '['. $width .', '. $columns .'],';
			}

		}

	} 

	//get customizer columns instead
	$columns = views()->get_infinity_option( 'postopts-columns-' . $view_id . '', '4' );
	$infinite_scroll = $builder_options->getOption( 'pagination-infinite-' . $view_name . '' );
	$infinite_scroll_effect = $builder_options->getOption( 'pagination-infinite-effect-' . $view_name . '' );

 ?>
<script>

	(function ($) {

      var view = $('#view-<?php echo views()->id; ?>');
      var wrapper = $('#wrapper-<?php echo views()->id; ?>');
		var article = $('#view-<?php echo views()->id; ?> article');

		initBoxfish(article, <?php echo $columns; ?>, <?php echo '['.$responsive_js.']'; ?>);

      <?php if( $infinite_scroll ) : ?>

		initInfiniteScroll(
			view,
			wrapper,
			<?php echo $columns; ?>,
			<?php echo '['.$responsive_js.']'; ?>,
			'<?php echo views()->plugin_url ?>images/loading.gif'
		);

		<?php endif; ?>

	})(jQuery);

</script>

