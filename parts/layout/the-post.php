<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry">
	
				<?php $builder_options = TitanFramework::getInstance( 'builder-options' );
				$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

View_Builder_Shortcodes::get_post_parts( $builder_options, $parts, $view_name )?>

			</article>