<?php
/**
 * View Template: Traditional Blog
 *
 * Display a post title and excerpt
 *
 * @package View Builder
 * @since 0.2
 */

views()->count = -1;

$builder_options = TitanFramework::getInstance( 'builder-options' );
$view_name = strtolower(str_replace(' ', '-', views()->view_name));
$style_name = $builder_options->getOption( 'style-name-' . $view_name . '' );
$grid_spacing = $builder_options->getOption( 'postopts-post-spacing-' . $view_name . '' );?>

<style>

#view-<?php echo views()->id; ?> article { margin-bottom: <?php echo $grid_spacing; ?>px}

</style>

<div id="view-<?php echo views()->id; ?>" class="view-wrapper blog <?php echo $view_name; ?>-view <?php echo $style_name; ?>" data-view="<?php echo $view_name; ?>">

	<?php //echo content builder before zone ?>

	<?php if ( have_posts() ) : ?>

		<?php while( have_posts() ) : the_post(); ?>

			<?php views()->count++; ?>

			<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry">
	
				<?php View_Builder_Shortcodes::get_post_parts( $builder_options ); ?>

			</article>

		<?php endwhile; ?>

	<?php else : ?>

		<?php load_template( views()->plugin_dir . 'parts/layout/not-found.php' ); ?>

	<?php endif; ?>

	<?php load_template( views()->plugin_dir . 'parts/layout/pagination.php' ); ?>
	<?php //echo content builder before zone ?>

</div>
