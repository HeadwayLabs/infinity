<?php

	extract( shortcode_atts( array(
		'before' => false,
		'display_as' => 'inline-block',
	), $atts ) );

	global $product, $post;

  	$post = get_post( $post->ID );
  	$post_type = $post->post_type;

	$before = $before != false ? '<span class="before-part">' . $before . '</span>' : null;
	$display_as = $display_as != null ? ' display-' . $display_as : null;

	if ( $post_type !== 'product' )
		return;

	ob_start(); ?>

	<div class="vb-part price-part<?php echo $display_as ?> wc-part" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		
		<p><?php echo $before; ?><span class="price-text"><?php echo $product->get_price_html(); ?></span></p>

		<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
		<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
		<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

	</div>

	<?php $content =  ob_get_clean(); ?>

