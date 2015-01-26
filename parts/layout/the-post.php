<article id="post-<?php the_ID(); ?>" class="article-<?php echo views()->count; ?> article item clearfix hentry">
	
				<?php $builder_options = TitanFramework::getInstance( 'builder-options' );

View_Builder_Shortcodes::get_post_parts( $builder_options, get_the_ID() ); ?>

			</article>