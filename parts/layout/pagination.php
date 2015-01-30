<?php 

	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$view_name = strtolower(views()->view_name);
	$show_pagination = $builder_options->getOption( 'pagination-show-' . $view_name . '' );


 ?>

<?php if( $show_pagination ) : ?>

<?php vb_pagination( $builder_options ); ?>

<?php endif; ?>