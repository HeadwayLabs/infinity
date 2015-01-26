<?php

	extract( shortcode_atts( array(
		'add_cart_text' => 'Add to cart',
		'display_as' => 'inline-block',
	), $atts ) );

	global $product, $post;

	$post = get_post( $post->ID );
  	$post_type = $post->post_type;
  	$display_as = $display_as != null ? ' display-' . $display_as : null;

	if ( $post_type !== 'product' )
		return;

	ob_start();

	echo sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="wc-part add-to-cart-part button %s %s product_type_%s">%s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( $product->id ),
		esc_attr( $product->get_sku() ),
		esc_attr( isset( $quantity ) ? $quantity : 1 ),
		$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
		esc_attr( $display_as ),
		esc_attr( $product->product_type ),
		esc_html( $add_cart_text )
	);

	?>

	<?php $content =  ob_get_clean(); ?>

