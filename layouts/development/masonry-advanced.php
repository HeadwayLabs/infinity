<?php
/**
 * View Template: Simple Masonry 
 *
 * Display content in a Simple Masonry Layout
 *
 * @package View Builder
 * @since 0.2
 */

views()->count = -1;

$builder_options = TitanFramework::getInstance( 'builder-options' );
$view_name = strtolower(str_replace(' ', '-', views()->view_name));
$view_id = views()->id;
$style_name = views()->get_infinity_option( 'style-name-' . $view_name . '', 'headway' );
$grid_spacing = $builder_options->getOption( 'postopts-post-spacing-' . $view_id . '' )/2;
$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

//filter settings
$disable_filter = isset(views()->options['masonry-disable-filter']) ? views()->options['masonry-disable-filter'] : false;
$filter_type = views()->options['masonry-filter-type'];

if ( !$disable_filter ) {

	$taxonomy = isset(views()->options['masonry-taxonomy']) ? views()->options['masonry-taxonomy'] : 'post';
	$all_text = isset(views()->options['masonry-alltext']) ? views()->options['masonry-alltext'] : 'Show All';
	$child_of = isset(views()->options['masonry-childof']) ? views()->options['masonry-childof'] : false;
	$exclude  = isset(views()->options['masonry-exclude']) ? views()->options['masonry-exclude'] : false;
	$include  = isset(views()->options['masonry-include']) ? views()->options['masonry-include'] : false;

	$taxonomies = array( $taxonomy );

	/* arguments to build nav query */
	$args = array ('taxonomy' => $taxonomy, 'child_of' => $child_of, 'exclude' => $exclude, 'include' => $include);
	/* query categories to build nav */
	$terms = get_terms( $taxonomies, $args );

}
	

$filter_before_text = isset(views()->options['masonry-before-filter-text']) ? views()->options['masonry-before-filter-text'] : null;

?>
<style>
#view-<?php echo views()->id; ?> article { margin: <?php echo $grid_spacing; ?>px}
</style>


<div id="wrapper-<?php echo views()->id; ?>">

	<?php if ( !$disable_filter ) : ?>

		<?php if( $filter_type == 'buttons' ) : ?>

		<nav class="magnet-filter">

			<ul class="reset-list clearer">

				<?php if( $filter_before_text ) : ?>
					<li class="before-filter"><?php echo $filter_before_text; ?></li>
				<?php endif; ?>

				<?php if( $all_text ) : ?>		
				<li class="active"><a href="#" data-filter="*"><?php echo $all_text; ?></a></li>
				<?php endif; ?>

				<?php 
					foreach ( $terms as $term ) {
						echo '<li><a href="#filter'. $term->name .'" data-filter="'. strtolower(str_replace (" ", "-", $term->name)) .'" data-termid="'. $term->term_taxonomy_id .'">' . $term->name . '</a></li>';
					}
				?>	

			</ul>

		</nav>

		<?php endif; ?>

		<?php if( $filter_type == 'select' ) : ?>
		<?php if( $filter_before_text ) : ?>
			<div class="before-filter"><?php echo $filter_before_text; ?></div>
		<?php endif; ?>
		<select id="size" name="filter by" class="magnet-filter">
		<option value="">View by...',
		<option value="all">All',
		<?php 
			foreach($terms as $term){
				echo '<option value="' . strtolower(str_replace (" ", "-", $term->name)) . '" data-filter="' . strtolower(str_replace (" ", "-", $term->name)) . '">' . $term->name . '',';
			}
		 ?>
		</select>

		<?php endif; ?>

	<?php endif; ?>

	<?php //echo content builder before zone ?>

	<?php if ( have_posts() ) : ?>

	<div id="view-<?php echo views()->id; ?>" class="view-wrapper magnet <?php echo $view_name; ?>-view <?php echo $style_name; ?>" data-view="<?php echo $view_name; ?>">

	<?php while( have_posts() ) : the_post(); ?>

		<?php views()->count++; ?>

		<?php 

		//TODO: implement masonry advanced
		$span=null;
		if (views()->count == 1) {
			$span_class='w2';
			$span='data-span="3"';
		} else {
			$span_class = '';
		}

		 ?>

		<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article magnet-item item clearfix hentry <?php echo tax_term_classes( $taxonomy ) ?> <?php echo $span_class; ?>" <?php echo $span; ?>>

			<?php View_Builder_Shortcodes::get_post_parts( $builder_options, $parts, $view_name ); ?>

		</article>

	<?php endwhile; ?>

	</div>


	<?php else : ?>

	<?php load_template( views()->plugin_dir . 'parts/layout/not-found.php' ); ?>

	<?php endif; ?>

	<?php load_template( views()->plugin_dir . 'parts/layout/pagination.php' ); ?>
	<?php //echo content builder before zone ?>

</div>

<?php 

	$options = views()->options;


	$responsive_js = '';

	/* Masonry Group  */
	if ( ! empty( $options['masonry-group'] ) ) {

		$meta_query = array();

		$numItems = count($options['masonry-group']);
		$i = 0;

		foreach ( $options['masonry-group'] as $option ) {

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
	$columns = $builder_options->getOption( 'postopts-columns-' . $view_id . '' );
	$filter_animation = views()->options['masonry-filter-animation'];

	$infinite_scroll = $builder_options->getOption( 'pagination-infinite-' . $view_name . '' );
	$infinite_scroll_effect = $builder_options->getOption( 'pagination-infinite-effect-' . $view_name . '' );

 ?>

<script>

	(function ($) {
		$(document).ready(function() {

	      var view = $('#view-<?php echo views()->id; ?>');
	      var wrapper = $('#wrapper-<?php echo views()->id; ?>');
			var article = $('#view-<?php echo views()->id; ?> article');
	      <?php if( $filter_type == 'select' ) : ?>

	      //initiate select filter
			filterSelect(wrapper);

			<?php endif; ?>

			//Initiate boxfish for layout
			initBoxfish(article, <?php echo $columns; ?>, <?php echo '['.$responsive_js.']'; ?>);

			//Initiate magnet
			initMagnet(wrapper, 900, '<?php echo $filter_animation ?>');

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

			<?php if ( ! empty( $options['masonry-group'] ) ) : ?>

			addResizeEvent(function() {
				setTimeout(function(){
			      wrapper.magnet('refresh');
				}, 700);
			});

			<?php endif; ?>

		});
	})(jQuery);
</script>
