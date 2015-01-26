<?php 

	$builder_options = TitanFramework::getInstance( 'builder-options' );

	$show_pagination = $builder_options->getOption( 'pagination-show-' . strtolower(views()->view_name) . '' );


 ?>

<?php if( $show_pagination ) : ?>

<?php vb_pagination( $builder_options ); ?>

<?php endif; ?>