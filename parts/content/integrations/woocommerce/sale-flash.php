<?php

	extract( shortcode_atts( array(
		'as_percentage_off' => 'off',
		'sale_text' => 'On Sale!',
		'before' => false,
		'after' => false,
		'display_as' => 'inline-block',
	), $atts ) );

	global $product, $post;

  	$post = get_post( $post->ID );
  	$post_type = $post->post_type;

	$before = $before != false ? '<span class="before-part">' . $before . '</span>' : null;
	$after = $after != false ? '<span class="after-part">' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	if ( $post_type !== 'product' )
		return;

	ob_start(); ?>

	<?php if ( $product->is_on_sale() ) : ?>

		<?php 

		$sale_price = get_post_meta( $product->id, '_price', true);
		$regular_price = get_post_meta( $product->id, '_regular_price', true);

		if (empty($regular_price)){ //then this is a variable product
			$available_variations = $product->get_available_variations();
			$variation_id=$available_variations[0]['variation_id'];
			$variation= new WC_Product_Variation( $variation_id );
			$regular_price = $variation ->regular_price;
			$sale_price = $variation ->sale_price;
		}

		if ( $as_percentage_off == 'on' ) {

			$sale = '<span class="sale-flash-text">' . ceil(( ($regular_price - $sale_price) / $regular_price ) * 100) .'%</span>';

		} else {

			$sale = '<span class="sale-flash-text">' . __( $sale_text, 'woocommerce' ) . '</span>';

		}



		?>

		<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="vb-part sale-flash-part onsale'.$display_as.'"><span>' . $before . ' ' . $sale . ' ' . $after . '</span></div>', $post, $product ); ?>

	<?php endif; ?>

	<?php $content =  ob_get_clean(); ?>

