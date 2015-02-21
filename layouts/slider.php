<?php
/**
 * View Template: Slider
 *
 * Display as a slider
 *
 * @package View Builder
 * @since 0.2
 */

$builder_options = TitanFramework::getInstance( 'builder-options' );
$view_name = strtolower(str_replace(' ', '-', views()->view_name));
$style_name = $builder_options->getOption( 'style-name-' . $view_name . '' );

$slider_nav_thumb_height = ( isset( views()->options['slider-nav-thumb-height'] ) ) ? views()->options['slider-nav-thumb-height'] : '36';
$slider_nav_thumb_width = ( isset( views()->options['slider-nav-thumb-width'] ) ) ? views()->options['slider-nav-thumb-width'] : '36';
$slider_nav_type = ( isset( views()->options['slider-nav-type'] ) ) ? views()->options['slider-nav-type'] : 'dots';
$view_id = views()->id;
$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

?>

	<?php //echo content builder before zone ?>

	<?php if ( have_posts() ) : ?>

<div id="view-<?php echo views()->id; ?>" class="view-wrapper slider <?php echo $view_name; ?>-view <?php echo $style_name; ?>" data-view="<?php echo $view_name; ?>">

		<?php while( have_posts() ) : the_post(); ?>

			<?php views()->count++; ?>

			<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry" <?php echo display_slider_navigation($slider_nav_type, $slider_nav_thumb_width, $slider_nav_thumb_height, views()->count); ?>>
	
				<?php View_Builder_Shortcodes::get_post_parts( $builder_options, $parts, $view_name ); ?>

			</article>

		<?php endwhile; ?>

		</div>


	<?php else : ?>

		<?php load_template( views()->plugin_dir . 'parts/layout/not-found.php' ); ?>

	<?php endif; ?>

	<?php load_template( views()->plugin_dir . 'parts/layout/pagination.php' ); ?>
	
	<?php view_builder_assets()->slider_js( $view_id ); ?>

