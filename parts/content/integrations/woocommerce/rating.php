<?php

	extract( shortcode_atts( array(
		'show_as_stars' => 'on',
		'show_review_count' => 'off',
		'before' => false,
		'display_as' => 'inline-block',
	), $atts ) );

	global $product, $post;

  	$post = get_post( $post->ID );
  	$post_type = $post->post_type;

	$before = $before != false ? '<span class="before-part">' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;
	$show_as_stars = $show_as_stars == 'on' ? ' woocommerce' : '';

	if ( $post_type !== 'product' )
		return;

	$count   = $product->get_rating_count();
	$average = $product->get_average_rating();

	ob_start();

	if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
		echo 'Woocommerce rating option is disabled.';
	?>

	<?php if( $product->get_rating_html() ) : ?>
		<div class="vb-part rating-part<?php echo $display_as ?> wc-part<?php echo $show_as_stars; ?>">
			<?php echo $before; ?>
			<?php echo $product->get_rating_html(); ?>
			<?php if( $show_review_count == 'on' ) : ?>
				<p class="wc-rating-review"><a href="<?php echo the_permalink(); ?>#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $count, 'woocommerce' ), '<span itemprop="ratingCount" class="count">' . $count . '</span>' ); ?>)</a></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

