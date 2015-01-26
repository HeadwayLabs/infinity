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
$grid_spacing = $builder_options->getOption( 'postopts-post-spacing-' . $view_name . '' )/2;
$style_name = $builder_options->getOption( 'style-name-' . $view_name . '' );?>

<style>

#view-<?php echo views()->id; ?> article { margin: <?php echo $grid_spacing; ?>px}

</style>

<div id="wrapper-<?php echo views()->id; ?>">

	<div id="view-<?php echo views()->id; ?>" class="filter-content view-wrapper grid <?php echo $view_name; ?>-view <?php echo $style_name; ?>" data-view="<?php echo $view_name; ?>">

		<?php if ( have_posts() ) : ?>

			<?php while( have_posts() ) : the_post(); ?>

				<?php views()->count++; ?>

				<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry">
		
					<?php View_Builder_Shortcodes::get_post_parts( $builder_options ); ?>

				</article>

			<?php endwhile; ?>

		<?php else : ?>

			<?php load_template( views()->plugin_dir . 'parts/layout/not-found.php', false ); ?>

		<?php endif; ?>

		<?php load_template( views()->plugin_dir . 'parts/layout/pagination.php', false ); ?>
		<?php //echo content builder before zone ?>

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

			$width = $option['masonry-width'];
			$columns = $option['masonry-columns']; 


			if(++$i === $numItems) {
				$responsive_js .= '['. $width .', '. $columns .']';
			} else {
				$responsive_js .= '['. $width .', '. $columns .'],';
			}

		}

	} 

	//get customizer columns instead
	$columns = $builder_options->getOption( 'postopts-columns-' . $view_name . '' );
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
			'<?php echo views()->plugin_url ?>images/loading.gif',
			'<?php echo $infinite_scroll_effect; ?>'
		);

		<?php endif; ?>

	})(jQuery);

</script>

