<?php

/**
 * Helper functions
 * 
 * @package View_Builder
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

//general settings
$general_options = get_option('ib_general_settings');
 
if ($general_options['enable-post-formats'] == true ) {
 
	function add_post_formats() {
		$general_options = get_option('ib_general_settings');
		add_theme_support( 'post-formats', $general_options['select-post-formats'] );
	}
	 
	add_action( 'after_setup_theme', 'add_post_formats', 20 );
 
}

/**
 * Returns a WP_Query
 *
 * @package View_Builder
 * @since 1.0.0
 *
 * @param int $id View ID.
 * @param string|array $query URL query string or array.
 * @return WP_Query
 */
	
function vb_loop_query( $id, $query = '' ) {

	//$content = tl_get_loop_parameters( $loop_id );

	$args = array();

	$options = vb_options( $id );

	$builder_options = TitanFramework::getInstance( 'builder-options' );
	$view_name = strtolower(views()->view_name);

	// post/content type
	$args['post_type'] = isset($options['post-type']) ? $options['post-type'] : 'post';

	// pagination
	$per_page = $builder_options->getOption( 'postopts-post-count-' . $id . '' );
	$args['posts_per_page'] = $per_page;//$options['posts-per-page'];

	//offset
	$offset = $builder_options->getOption( 'postopts-post-offset-' . $view_name . '' );
	$args['offset'] = $offset;

	$args['paged'] = max( 1, get_query_var( 'paged' ) );

	// status
	if ( ! empty( $options['post-status'] ) )
		$args['post_status'] = $options['post-status'];

	// author
	if ( ! empty( $options['post-authors'] ) ) {

		$authors_ids = $options['post-authors'];

		if ( $authors_ids ) {
			$authors_ids = implode( ',', $authors_ids );
			$args['author'] = $authors_ids;
		}

	}

	//category for simple query
	if ( empty( $options['taxonomy-group'] ) || $options['post-taxonomies-relation'] == 'none' ) {

		$categories = $builder_options->getOption( 'postopts-post-categories-' . $id . '' );
		if ( $categories && !in_array( 'all', $categories ) ) {
			$categories = implode( ',', $categories );
			$args['cat'] = $categories;
		}

	}
	

	// taxonomy
	if ( ! empty( $options['taxonomy-group'] ) ) {
		
		$tax_query = array();

		foreach ( $options['taxonomy-group'] as $taxonomy ) {

			if ( empty( $taxonomy['post-taxonomies-taxonomy'] ) || empty( $taxonomy['post-taxonomies-terms'] ) )
				continue;

			$terms = explode( ',', $taxonomy['post-taxonomies-terms'] );

			if ( is_numeric($terms[0]) ) {
				$field_type = 'id';
			} else {
				$field_type = 'slug';
			}

			$tax_query[] = array(
				'taxonomy'         => $taxonomy['post-taxonomies-taxonomy'],
				'field'            => $field_type,
				'terms'            => array_map( 'sanitize_title', $terms ),
				'include_children' => empty( $taxonomy['post-taxonomies-include-children'] ) ? false : true,
				'operator'         => empty( $taxonomy['post-taxonomies-exclude'] ) ? 'IN' : 'NOT IN'
			);
		}

		if ( $tax_query ) {
			$tax_query['relation'] = $options['post-taxonomies-relation'];
			$args['tax_query'] = $tax_query;
		}
	}

	// custom fields
	if ( ! empty( $options['custom-fields-group'] ) ) {

		$meta_query = array();

		foreach ( $options['custom-fields-group'] as $custom_field ) {

			if ( empty( $custom_field['post-custom-fields-values'] ) || empty( $custom_field['post-custom-fields-key'] ) )
				continue;

			$values = explode( ',', $custom_field['post-custom-fields-values'] );

			if ( in_array( $custom_field['post-custom-fields-compare'], array( 'LIKE', 'NOT LIKE' ) ) )
				$values = $values[0];

			if ( in_array( $custom_field['post-custom-fields-compare'], array( '>=', '<=', '<', '>' ) ) )
				$values = $custom_field['post-custom-fields-values'];

			$meta_query[] = array(
				'key'     => trim( $custom_field['post-custom-fields-key'] ),
				'value'   => $values,
				'compare' => $custom_field['post-custom-fields-compare'],
				'type'    => $custom_field['post-custom-fields-type']
			);

		}

		if ( $meta_query ) {
			$meta_query['relation'] = $options['post-custom-fields-relation'];
			$args['meta_query'] = $meta_query;
		}

	}

	// specific posts & pages
	$args['post_parent'] = $options['post-parent'];

	$args['post__in'] = $args['post__not_in'] = array();

	if ( ! empty( $options['post-individual-posts'] ) ) {

		$posts = explode( ',', $options['post-individual-posts'] );

		if ( empty( $options['post-exclude-individual-posts'] ) )
			$args['post__in'] = $posts;
		else
			$args['post__not_in'] = $posts;
	}

	// ordering
	$args['order'] = $builder_options->getOption( 'postopts-order-' . $view_name . '' );
	$order_by = $builder_options->getOption( 'postopts-order-by-' . $view_name . '' );

	if ( $order_by == 'meta_value_num' || $order_by == 'meta_value' ) {

		$options['meta-key'] = trim ( $builder_options->getOption( 'postopts-order-meta-key-' . $view_name . '' ) );

		if ( ! empty( $options['meta-key'] ) ) {
			$args['meta_key'] = $options['meta-key'];
			$args['orderby']  = $order_by;
		}

	} else if ( $order_by == 'likes' ) {

		$args['meta_key'] = '_post_like_count';
		$args['orderby'] = 'meta_value_num';

	} else {

		$args['orderby'] = $order_by;

	}

	// search terms
	if ( ! empty( $options['post-search-terms'] ) ) {

		$args['s']    = $options['post-search-terms'];

		if ( ! empty( $options['post-search-exact'] ) )
			$args['exact']    = $options['post-search-exact'];

		if ( ! empty( $options['post-search-sentence'] ) )
			$args['sentence'] = $options['post-search-sentence'];
		
	}

	// permission
	if ( ! empty( $options['post-permission'] ) )
		$args['perm'] = $options['post-permission'];

	// sticky post
	$sticky = get_option( 'sticky_posts' );
	if ( ! empty( $options['post-sticky-posts'] ) ) {

		switch( $options['post-sticky-posts'] ) {
			case 'ignore' :
				$args['ignore_sticky_posts'] = true;
				break;

			case 'only' :
				$args['ignore_sticky_posts'] = true;
				$args['post__in'] = array_merge( $args['post__in'], $sticky );
				break;

			case 'hide' :
				$args['ignore_sticky_posts'] = true;
				$args['post__not_in'] = array_merge( $args['post__not_in'], $sticky );
				break;

			case 'first' :
				$args['p'] = $sticky[0];
				break;

			default:
				break;
		}

	}


	add_filter( 'posts_where', 'vb_filter_where' );
	$query = new WP_Query( $args );
	add_filter( 'posts_where', 'vb_filter_where' );

	return $query;
}

/**
 * Render the view
 *
 * @package View_Builder
 * @since 1.0.0
 *
 * @param int $id View ID.
 * @param string $layout_name Name of the layout to use
 * @param array|string Custom query args
 * @param string Context in which the view is displayed
 */
function vb_render_view( $id, $layout_name, $args = null, $context = '' ) {

	$view_layouts = vb_get_view_layouts();
	$view_name = strtolower(views()->view_name);

	$options = vb_options( $id );

	if ( empty( $view_layouts ) ) return false;

	if (isset( $view_layouts[$layout_name] )) {
	   $single_view_layout = $view_layouts[$layout_name];
	} else { //fallback ! TO FIX best way to select it.
	   $single_view_layout = end($view_layouts);
	}

	vb_setup_query( $id, $args );

	//first we add a notice if the view is not yet customized
	$builder_options = TitanFramework::getInstance( 'builder-options' );

	$parts = $builder_options->getOption( 'builder_parts' . $view_name . '' );

	if ( empty($parts) ) {
		$parts = array('title', 'image', 'excerpt', 'date', 'readmore');
	}
	
	$customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'wp-admin/customize.php' );

	ob_start();

	load_template( $single_view_layout, false );

	$content = ob_get_clean();

	vb_reset_query();

	return $content;

}

/**
 * Start query
 *
 * @package View_Builder
 * @since 1.0.0
 */
function vb_setup_query( $id, $args ) {

	global $wp_query;

	views()->id = $id;
        
	$options = vb_options( $id );

	$query_mode = $options['query-mode'];

	//0 = custom query
	if ( $query_mode == 1 ) {

		views()->view_temp_query = clone $wp_query;
		$wp_query = vb_loop_query( $id, $args );
	
	} else {

		$wp_query = $wp_query;

	}


}

/**
 * Reset Query
 *
 * @package View_Builder
 * @since 1.0.0
 */
function vb_reset_query() {

	global $wp_query;

	wp_reset_query();
        
	//views()->id = null;
	views()->view_temp_query = null;

}

/**
 * Check if a layout file is a view layout
 *
 * @package View_Builder
 * @since 0.1
 *
 * @param string $file Template file name
 * @return string Template name
 */
function vb_is_view_layout( $file ) {
	$data = get_file_data( $file, array(
		'name'    => 'View Template'
	) );

	$layout_name    = trim( $data['name'] );

	if ( empty( $layout_name ) )
		return;

	return $layout_name;

}

/**
 * Check if a style file is a style
 *
 * @package View_Builder
 * @since 0.1
 *
 * @param string $file Template file name
 * @return string Template name
 */
function vb_is_view_style( $file ) {
	$data = get_file_data( $file, array(
		'name'    => 'View Style'
	) );

	$style_name    = trim( $data['name'] );

	if ( empty( $style_name ) )
		return;

	return $style_name;

}

/**
 * Views pagination
 *
 * @package View_Builder
 * @since 0.1
 *
 * Inspired by Simple Pagination GPL Plugin by GeekPress.
 */

function vb_pagination( $builder_options=null ) {

		global $wp_query, $wp_rewrite;

		$view_name = strtolower(views()->view_name);

		$base 						= null;
		$align 						= $builder_options->getOption( 'pagination-alignment-' . $view_name . '' );
		$text_pages 				= $builder_options->getOption( 'pagination-pages-text-' . $view_name . '' );
		$text_first_page 			= $builder_options->getOption( 'pagination-first-page-text-' . $view_name . '' );
 		$text_last_page 			= $builder_options->getOption( 'pagination-last-page-text-' . $view_name . '' );
 		$text_previous_page 		= $builder_options->getOption( 'pagination-prev-page-text-' . $view_name . '' );
 		$text_next_page 			= $builder_options->getOption( 'pagination-next-page-text-' . $view_name . '' );
		$before_pagination 		= '<div id="pagenav" class="vb-pagination '. $align .'">';
		$after_pagination 		= '</div>';
		$always_show			 	= false;
		$show_all					= true;
		//Range: the number of page links to show before and after the current page.
		$range						= $builder_options->getOption( 'pagination-range-' . $view_name . '' );
		$anchor						= 0;
		$larger_page_to_show 	= 0;
		$larger_page_multiple	= 0;

		$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1; // Current page
		$total               = $wp_query->max_num_pages; // Total posts
		$always_show         = isset( $always_show ) ? $always_show : false; // Always show
		$show_all            = isset( $show_all ) ? $show_all : false; // Show all

		$start_page = $current - floor( $range/2 );

		if ( $start_page <= 0 )
			$start_page = 1;

		$end_page = $current + ceil( ($range+1)/2 );

		if ( ( $end_page - $start_page ) != $range )
			$end_page = $start_page + $range;

		if ( $end_page > $total ) {
			$start_page = $total - $range;
			$end_page = $total;
		}

		// Rewrite link
		if( $wp_rewrite->using_permalinks() ) {
		
			$base = user_trailingslashit( trailingslashit( remove_query_arg( 's', strtok(get_pagenum_link( 1 ), '?') ) ) . $wp_rewrite->pagination_base . '/%#%/', 'paged' );
			if ( get_pagenum_link( 1 ) != strtok(get_pagenum_link( 1 ), '?') ) {
				
				$base = user_trailingslashit( trailingslashit( remove_query_arg( 's', strtok(get_pagenum_link( 1 ), '?') ) ) . $wp_rewrite->pagination_base . '/%#%/'.rtrim(str_replace('%2F','',substr(get_pagenum_link( 1 ),strlen(strtok(get_pagenum_link( 1 ), '?')))),'/'), 'paged' );
				
			}
				
		}
		else {

			$base = @add_query_arg('paged','%#%');

		}
		
		// Search value
		if( !empty($wp_query->query_vars['s']) )
			$this->add_args = array( 's' => get_query_var( 's' ) );


		// Check of pagination is always show and check if the total of pages is egal 1
		if( !$always_show && $total == 1 )
			return false;


		// Text of the beginning
		$text_pages = str_replace(  array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
			array( number_format_i18n( $current ), number_format_i18n( $total ) ),
			$text_pages
		);

         // Before link Markup
         $before_link = isset( $before_link ) ? html_entity_decode($before_link) : '';

         // After link Markup
         $after_link = isset( $after_link ) ? html_entity_decode($after_link) : '';


         // Beginning of the HTML markup of pagination
         $output = null;
         $output .= isset( $before_pagination ) ? html_entity_decode($before_pagination) : '';
         if( $text_pages )
         	$output.= '<span class="pages">' . $text_pages . '</span>';

		 // First Page
		 if( $current-1 > floor($range+1/2) ) :

	    	$link = format_link(1, $base);

	    	if( !empty( $text_first_page ) )
                	$output .= $before_link . '<a class="first" href="' . esc_url($link) . '">' . $text_first_page . '</a>' . $after_link;

	    endif;

		$larger_pages_array = array();
		if ( $larger_page_multiple )
			for ( $i = $larger_page_multiple; $i <= $total; $i+= $larger_page_multiple )
				$larger_pages_array[] = $i;

		$larger_page_start = 0;
		foreach ( $larger_pages_array as $larger_page ) {
			if ( $larger_page+floor(($range+1)/2) < $start_page && $larger_page_start < $larger_page_to_show ) {

				$link = format_link($larger_page, $base);

				$output .= $before_link . '<a class="larger-pages" href="' . esc_url($link) . '">' . $larger_page . '</a>' . $after_link;
				$larger_page_start++;
			}
		}

		//Previous Page
		if ( $current && 1 < $current ) :

                $link = format_link($current - 1, $base);

                if( !empty( $text_previous_page ) )
                		$output .= $before_link . '<a class="previous" href="' . esc_url($link) . '">' . $text_previous_page . '</a>' . $after_link;
        endif;

		$dots = false;
        for ( $n = 1; $n <= $total; $n++ ) :
                $n_display = number_format_i18n($n);
                if ( $n == $current ) :
                        $output .= $before_link . '<span class="current">' .$n_display . '</span>' . $after_link;
                        $dots = true;
                else :

                        if ( $show_all || ( $n <= $anchor || ( $current && $n >= $current - $range && $n <= $current + $range ) || $n > $total - $anchor ) ) :

                                $link = format_link($n, $base);

                                $output .= $before_link . '<a href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">'. $n_display . '</a>' . $after_link;
                                $dots = true;

                        elseif ( $dots && !$show_all && $anchor >=1 ) :
                                $output .= $before_link . '<span class="dots">...</span>' . $after_link;
                                $dots = false;
                        endif;
                endif;
        endfor;

        // Next Page
        if ( $current && ( $current < $total || -1 == $total ) ) :

                $link = format_link(number_format_i18n($current + 1), $base);

                if( !empty($text_next_page) )
                	$output .= $before_link . '<a class="next" href="' . esc_url($link) . '">' . $text_next_page . '</a>' . $after_link;
        endif;

	    $larger_page_end = 0;
		foreach ( $larger_pages_array as $larger_page ) {
			if ( $larger_page-(floor($range/2)) > $end_page && $larger_page_end < $larger_page_to_show ) {

				$link = format_link($larger_page, $base);

				$output .= $before_link . '<a class="larger-pages" href="' . esc_url($link) . '">' . $larger_page . '</a>' . $after_link;

				$larger_page_end++;
			}
		}


		// Last Page
	    if( $current < $total-(floor($range+1/2)) ) :

	    	$link = format_link($total, $base);

	    	if( !empty( $text_last_page ) )
                	$output .= $before_link . '<a class="last" href="' . esc_url($link) . '">' . $text_last_page . '</a>' . $after_link;

	    endif;

	    $output.= isset( $after_pagination ) ? html_entity_decode($after_pagination) : '';

	    echo $output;
	}


/**
 * Views pagination format link
 *
 * @package View_Builder
 * @since 0.1
 */
function format_link( $page, $base ) {
	
	global $wp_rewrite;
	
	$link = str_replace('%_%', "?page=%#%", $base);
	$link = str_replace('%#%', $page, $link);
	$link = str_replace( $wp_rewrite->pagination_base . '/1/','', $link );
	$link = str_replace('?paged=1','', $link);

   return str_replace(' ','+', $link);

}

/**
 * Get the default View Templates
 *
 * @package View_Builder
 * @since 0.2
 *
 * @param string $objects Loop objects type
 * @return array Default layouts
 */
function vb_get_default_view_layouts( $objects = 'posts' ) {

	$layouts_files = scandir( views()->layouts_dir );

	$view_layouts = array();
	foreach ( $layouts_files as $layout ) {
		if ( ! is_file( views()->layouts_dir . $layout ) )
			continue;

		$is_layout = vb_is_view_layout( views()->layouts_dir . $layout );

		if ( ! $is_layout ) continue;
		
		$view_layouts[$layout] = views()->layouts_dir . $layout;
	}

	return $view_layouts;
}

/**
 * Get view layouts
 *
 * @package View_Builder
 * @since 0.2
 *
 * @param string $objects view objects
 * @return array View layouts
 */
function vb_get_view_layouts() {

	$view_layouts = $vb_layouts_directories = $potential_layouts = array();

	//layouts priority : the last directory from the array have the highest priority.
	//this means that child layouts will override parent layouts which will override default layouts.

	$vb_layouts_directories[] = views()->layouts_dir; //the views layouts path
	$vb_layouts_directories = apply_filters( 'vb_layouts_directories' , $vb_layouts_directories ); //allow plugins to add layout paths
	$vb_layouts_directories[] = get_template_directory(); //parent theme path
	$vb_layouts_directories[] = get_stylesheet_directory(); //child theme path

	$vb_layouts_directories = array_unique( $vb_layouts_directories );
	$vb_layouts_directories = array_reverse( $vb_layouts_directories ); //reverse to have highest priority first
	foreach( (array) $vb_layouts_directories as $vb_layouts_dir ){

	   $files = (array) glob( trailingslashit( $vb_layouts_dir ) . "*.php" );

	   foreach ( $files as $layout ) {
	       
	       $filename = basename( $layout );

	       if( in_array( $layout , $view_layouts ) ) continue; //for priority
	       
	       $layout_name = strtolower(str_replace(' ', '-', vb_is_view_layout( $layout )));
	       
	       if ( $layout_name )
	           $view_layouts[$layout_name] = $layout;
	           
	   }
	   
	}

	return $view_layouts;
}

/**
 * Get view styles
 *
 * @package View_Builder
 * @since 0.2
 *
 * @param string $objects view objects
 * @return array View styles
 */
function vb_get_view_styles() {

	$view_styles = $vb_styles_directories = $potential_styles = array();

	//styles priority : the last directory from the array have the highest priority.
	//this means that child styles will override parent styles which will override default styles.

	$vb_styles_directories[] = views()->styles_dir; //the views styles path
	$vb_styles_directories = apply_filters( 'vb_styles_directories' , $vb_styles_directories ); //allow plugins to add layout paths
	$vb_styles_directories[] = get_template_directory(); //parent theme path
	$vb_styles_directories[] = get_stylesheet_directory(); //child theme path

	$vb_styles_directories = array_unique( $vb_styles_directories );
	$vb_styles_directories = array_reverse( $vb_styles_directories ); //reverse to have highest priority first
	
	foreach( (array) $vb_styles_directories as $vb_styles_dir ){

	   $files = (array) glob( trailingslashit( $vb_styles_dir ) . "*.css" );

	   foreach ( $files as $style ) {
	       
	       $filename = basename( $style );

	       if( in_array( $style , $view_styles ) ) continue; //for priority
	       
	       $style_name = vb_is_view_style( $style );
	       
	       if ( $style_name )
	           $view_styles[$style_name] = $style;
	           
	   }
	   
	}

	return $view_styles;

}


/**
 * Add the views shortcode which will render a view from an id provided as attribute
 *
 * @package View_Builder
 * @since 1.0.0
 */
function vb_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'id' => 0,
	), $atts ) );

	$options = vb_options( $id );

	$builder_options = TitanFramework::getInstance( 'builder-options' );

	$view_name = strtolower(views()->view_name);

	$layout = (!empty($builder_options->getOption( 'view-layout-' . $id . '' ))) ? $builder_options->getOption( 'view-layout-' . $id . '' ) : 'blog';

	return vb_render_view( $id, $layout, null, 'shortcode' );

}
add_shortcode( 'infinity', 'vb_shortcode' );

/**
 * Get view parameters
 *
 * @package View_Builder
 * @since 1.0.0
 *
 * @param int  $id Loop ID
 * @return array Loop parameters
 */
function vb_options( $id = false ) {
    if ( !$id ) $id = get_the_ID();

    $options = get_post_meta( $id, 'view_options', true );

    views()->options = $options;

    views()->id = $id;

    views()->view_name = str_replace(' ', '-', get_the_title($id));

    return $options;
}

/**
 * Filter WP_Query where clause
 *
 * @package View_Builder
 * @since 1.0.0
 */
function vb_filter_where( $where ) {

	$options = vb_options( views()->id );

	if ( empty( $options['date-filter'] ) )
		return $where;

	$min_date = null;
	$max_date = null;

	if ( 'days-range' == $options['date-filter'] ) {
		$min_date = ! empty( $options['min-days'] ) ? strtotime( "-{$options['min-days']} days" ) : null;
		$max_date = ! empty( $options['max-days'] ) ? strtotime( "-{$options['max-days']} days" ) : null;
	} else if( 'date-range' == $options['date-filter'] ) {
		$min_date = ! empty( $options['date-from'] ) ? strtotime( $options['date-from'] ) : null;
		$max_date = ! empty( $options['date-to'] ) ? strtotime( $options['date-to'] ) : null;
	}

	if ( $max_date )
		$max_date = $max_date + 60 * 60 * 24;

	$min_date = $min_date ? date( 'Y-m-d', $min_date ) : null;
	$max_date = $max_date ? date( 'Y-m-d', $max_date ) : null;

	if ( $min_date )
		$where .= " AND post_date >= '$min_date'";

	if ( $max_date )
		$where .= " AND post_date < '$max_date'";

	return $where;
}

/**
 * Build a meta query array based on form data
 *
 * @package The_Loops
 * @since 0.1
 * @param array $custom_fields Form data
 * @return array Meta query
 */
function vb_build_meta_query( $custom_fields ) {
	if ( empty( $custom_fields ) )
		return;

	$meta_query = array();

	foreach ( $custom_fields as $custom_field ) {
		if ( empty( $custom_field['key'] ) )
			continue;

		$values = _tl_csv_to_array( $custom_field['values'], "\t" );

		if ( in_array( $custom_field['compare'], array( 'LIKE', 'NOT LIKE' ) ) )
			$values = $values[0];

		$meta_query[] = array(
			'key'     => trim( $custom_field['key'] ),
			'value'   => $values,
			'compare' => $custom_field['compare'],
			'type'    => $custom_field['type']
		);
	}

	return $meta_query;
}

function vb_resize_image($url, $width = null, $height = null, $crop = true, $single = true, $upscale = true ) {

	if ( !$url )
		return null;

	include_once(views()->plugin_dir . '/includes/image-resizer.php');

	$HeadwayImageResize = VBImageResize::getInstance();
	$resized_image = $HeadwayImageResize->process($url, $width, $height, $crop, false, $upscale);

	if ( is_wp_error($resized_image) )
		return $url . '#' . $resized_image->get_error_code();

	return $resized_image['url'];
		
}


/* Meta Helper Functions */

/**
	* Get list of post types
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_post_types_list() {

  $post_type_options = array();

  $args = array(
	   'public'   => true,
	   '_builtin' => false
	);

  $post_types = get_post_types(false, 'objects'); 
      
  foreach($post_types as $post_type_id => $post_type){
      
      //Make sure the post type is not an excluded post type.
      if(in_array($post_type_id, array('revision', 'nav_menu_item'))) 
          continue;
      
      $post_type_options[$post_type_id] = $post_type->labels->name;
  
  }
  
  return $post_type_options;
  
}

/**
	* Get layouts list
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_layouts_list() {
        
	$layouts = array();

	$view_layouts = vb_get_view_layouts();
	foreach ( $view_layouts as $name => $file ) {
		$filename = strtolower(str_replace(' ', '-', $name));
		$layouts[$filename] = views()->plugin_url. 'includes/admin/images/'. $filename . '.png';
	}

	return $layouts;
  
}

/**
	* Get styles list
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_styles_list() {
        
	$styles = array('' => 'No Style');

	$view_styles = vb_get_view_styles();
	foreach ( $view_styles as $name => $file ) {
		$filename = strtolower(str_replace(' ', '-', $name));
		$styles[$filename] = $name;
	}

	return $styles;
  
}


/**
	* Get authors list
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_authors() {
		
	$author_options = array();
	
	$authors = get_users(array(
		'orderby' => 'post_count',
		'order' => 'desc',
		'who' => 'authors'
	));
	
	foreach ( $authors as $author )
		$author_options[$author->ID] = $author->display_name;
		
	return $author_options;
	
}

/**
	* Get taxonomy list by post type
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_filter_taxonomy_list() {
        
	$taxonomies = array();

	$options = vb_options( views()->id );

	$post_types = isset($options['post-type']) ? $options['post-type'] : 'post';

	$taxonomy_names = get_object_taxonomies( $post_types );
	foreach ( $taxonomy_names as $taxonomy ) {
		$taxonomies[$taxonomy] = ucwords(str_replace('_', ' ', $taxonomy ));
	}

	return $taxonomies;
  
}

/**
	* Get views
	*
	* @package View_Builder
	* @since 1.0.0
*/
function vb_get_views( $args = array() ) {
	$defaults = array(
		'post_type' => 'view',
		'nopaging'  => true
	);

	$args = wp_parse_args( $args, $defaults );

	return get_posts( $args );
}

/**
	* Get statuses list
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_statuses() {

	$status_options = array();

	$post_statuses = get_post_stati( array( 'show_in_admin_all_list' => true ), 'objects' );

	foreach($post_statuses as $name => $object){

      $status_options[ esc_attr( $name ) ] = $object->label;
  
	}
		
	return $status_options;
	
}

/**
	* Generates list of builder elements
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_builder_elements() {

	$elements = array(
	  'title' => 'Title',
	  'image' => 'Image Thumbnail',
	  'excerpt' => 'Content',
	  'date' => 'Date',
	  'time' => 'Time',
	  'categories' => 'Categories',
	  'tags' => 'Tags',
	  'author' => 'Author',
	  'avatar' => 'Avatar',
	  'comments' => 'Comments',
	  'share' => 'Social Share',
	  'likes' => 'Likes',
	  'readmore' => 'Read More'
	);

	if ( current_theme_supports('post-formats' )) {
		$elements['post-format'] = 'Post Format';
	}

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				
		$elements['wc-price'] = 'Woo Price';
		$elements['wc-rating'] = 'Woo Rating';
		$elements['wc-sale-flash'] = 'Woo Sale Badge';
		$elements['wc-add-to-cart'] = 'Woo Add to Cart';

	}

	return $elements;

}

/**
	* Generates list of builder elements for cover
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_cover_builder_elements() {

	$elements = array(
	  'title' => 'Title',
	  'excerpt' => 'Content',
	  'date' => 'Date',
	  'time' => 'Time',
	  'categories' => 'Categories',
	  'tags' => 'Tags',
	  'author' => 'Author',
	  'avatar' => 'Avatar',
	  'comments' => 'Comments',
	  'share' => 'Social Share',
	  'likes' => 'Likes',
	  'readmore' => 'Read More'
	);

	if ( current_theme_supports('post-formats' )) {
		$elements['post-format'] = 'Post Format';
	}

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				
		$elements['wc-price'] = 'Woo Price';
		$elements['wc-rating'] = 'Woo Rating';
		$elements['wc-sale-flash'] = 'Woo Sale Badge';
		$elements['wc-add-to-cart'] = 'Woo Add to Cart';

	}

	return $elements;

}

/**
	* Generates list of font awesome icons
	*
	* @package View_Builder
	* @since 1.0.0
*/
function font_awesome_icons() {
	return array(
      'adjust' => 'adjust',
      'asterisk' => 'asterisk',
      'ban-circle' => 'ban-circle',
      'bar-chart' => 'bar-chart',
      'barcode' => 'barcode',
      'beaker' => 'beaker',
      'beer' => 'beer',
      'bell' => 'bell',
      'bell-alt' => 'bell-alt',
      'bolt' => 'bolt',
      'book' => 'book',
      'bookmark' => 'bookmark',
      'bookmark-empty' => 'bookmark-empty',
      'briefcase' => 'briefcase',
      'bullhorn' => 'bullhorn',
      'calendar' => 'calendar',
      'camera' => 'camera',
      'camera-retro' => 'camera-retro',
      'certificate' => 'certificate',
      'check' => 'check',
      'check-empty' => 'check-empty',
      'circle' => 'circle',
      'circle-blank' => 'circle-blank',
      'cloud' => 'cloud',
      'cloud-download' => 'cloud-download',
      'cloud-upload' => 'cloud-upload',
      'coffee' => 'coffee',
      'cog' => 'cog',
      'cogs' => 'cogs',
      'comment' => 'comment',
      'comment-alt' => 'comment-alt',
      'comments' => 'comments',
      'comments-alt' => 'comments-alt',
      'credit-card' => 'credit-card',
      'dashboard' => 'dashboard',
      'desktop' => 'desktop',
      'download' => 'download',
      'download-alt' => 'download-alt',
      'edit' => 'edit',
      'envelope' => 'envelope',
      'envelope-alt' => 'envelope-alt',
      'exchange' => 'exchange',
      'exclamation-sign' => 'exclamation-sign',
      'external-link' => 'external-link',
      'eye-close' => 'eye-close',
      'eye-open' => 'eye-open',
      'facetime-video' => 'facetime-video',
      'fighter-jet' => 'fighter-jet',
      'film' => 'film',
      'filter' => 'filter',
      'fire' => 'fire',
      'flag' => 'flag',
      'folder-close' => 'folder-close',
      'folder-open' => 'folder-open',
      'folder-close-alt' => 'folder-close-alt',
      'folder-open-alt' => 'folder-open-alt',
      'food' => 'food',
      'gift' => 'gift',
      'glass' => 'glass',
      'globe' => 'globe',
      'group' => 'group',
      'hdd' => 'hdd',
      'headphones' => 'headphones',
      'heart' => 'heart',
      'heart-empty' => 'heart-empty',
      'home' => 'home',
      'inbox' => 'inbox',
      'info-sign' => 'info-sign',
      'key' => 'key',
      'leaf' => 'leaf',
      'laptop' => 'laptop',
      'legal' => 'legal',
      'lemon' => 'lemon',
      'lightbulb' => 'lightbulb',
      'lock' => 'lock',
      'unlock' => 'unlock',
      'magic' => 'magic',
      'magnet' => 'magnet',
      'map-marker' => 'map-marker',
      'minus' => 'minus',
      'minus-sign' => 'minus-sign',
      'mobile-phone' => 'mobile-phone',
      'money' => 'money',
      'move' => 'move',
      'music' => 'music',
      'off' => 'off',
      'ok' => 'ok',
      'ok-circle' => 'ok-circle',
      'ok-sign' => 'ok-sign',
      'pencil' => 'pencil',
      'picture' => 'picture',
      'plane' => 'plane',
      'plus' => 'plus',
      'plus-sign' => 'plus-sign',
      'print' => 'print',
      'pushpin' => 'pushpin',
      'qrcode' => 'qrcode',
      'question-sign' => 'question-sign',
      'quote-left' => 'quote-left',
      'quote-right' => 'quote-right',
      'random' => 'random',
      'refresh' => 'refresh',
      'remove' => 'remove',
      'remove-circle' => 'remove-circle',
      'remove-sign' => 'remove-sign',
      'reorder' => 'reorder',
      'reply' => 'reply',
      'resize-horizontal' => 'resize-horizontal',
      'resize-vertical' => 'resize-vertical',
      'retweet' => 'retweet',
      'road' => 'road',
      'rss' => 'rss',
      'screenshot' => 'screenshot',
      'search' => 'search',
      'share' => 'share',
      'share-alt' => 'share-alt',
      'shopping-cart' => 'shopping-cart',
      'signal' => 'signal',
      'signin' => 'signin',
      'signout' => 'signout',
      'sitemap' => 'sitemap',
      'sort' => 'sort',
      'sort-down' => 'sort-down',
      'sort-up' => 'sort-up',
      'spinner' => 'spinner',
      'star' => 'star',
      'star-empty' => 'star-empty',
      'star-half' => 'star-half',
      'tablet' => 'tablet',
      'tag' => 'tag',
      'tags' => 'tags',
      'tasks' => 'tasks',
      'thumbs-down' => 'thumbs-down',
      'thumbs-up' => 'thumbs-up',
      'time' => 'time',
      'tint' => 'tint',
      'trash' => 'trash',
      'trophy' => 'trophy',
      'truck' => 'truck',
      'umbrella' => 'umbrella',
      'upload' => 'upload',
      'upload-alt' => 'upload-alt',
      'user' => 'user',
      'user-md' => 'user-md',
      'volume-off' => 'volume-off',
      'volume-down' => 'volume-down',
      'volume-up' => 'volume-up',
      'warning-sign' => 'warning-sign',
      'wrench' => 'wrench',
      'zoom-in' => 'zoom-in',
      'zoom-out' => 'zoom-out',
      'file' => 'file',
      'file-alt' => 'file-alt',
      'cut' => 'cut',
      'copy' => 'copy',
      'paste' => 'paste',
      'save' => 'save',
      'undo' => 'undo',
      'repeat' => 'repeat',
      'text-height' => 'text-height',
      'text-width' => 'text-width',
      'align-left' => 'align-left',
      'align-center' => 'align-center',
      'align-right' => 'align-right',
      'align-justify' => 'align-justify',
      'indent-left' => 'indent-left',
      'indent-right' => 'indent-right',
      'font' => 'font',
      'bold' => 'bold',
      'italic' => 'italic',
      'strikethrough' => 'strikethrough',
      'underline' => 'underline',
      'link' => 'link',
      'paper-clip' => 'paper-clip',
      'columns' => 'columns',
      'table' => 'table',
      'th-large' => 'th-large',
      'th' => 'th',
      'th-list' => 'th-list',
      'list' => 'list',
      'list-ol' => 'list-ol',
      'list-ul' => 'list-ul',
      'list-alt' => 'list-alt',
      'angle-left' => 'angle-left',
      'angle-right' => 'angle-right',
      'angle-up' => 'angle-up',
      'angle-down' => 'angle-down',
      'arrow-down' => 'arrow-down',
      'arrow-left' => 'arrow-left',
      'arrow-right' => 'arrow-right',
      'arrow-up' => 'arrow-up',
      'caret-down' => 'caret-down',
      'caret-left' => 'caret-left',
      'caret-right' => 'caret-right',
      'caret-up' => 'caret-up',
      'chevron-down' => 'chevron-down',
      'chevron-left' => 'chevron-left',
      'chevron-right' => 'chevron-right',
      'chevron-up' => 'chevron-up',
      'circle-arrow-down' => 'circle-arrow-down',
      'circle-arrow-left' => 'circle-arrow-left',
      'circle-arrow-right' => 'circle-arrow-right',
      'circle-arrow-up' => 'circle-arrow-up',
      'double-angle-left' => 'double-angle-left',
      'double-angle-right' => 'double-angle-right',
      'double-angle-up' => 'double-angle-up',
      'double-angle-down' => 'double-angle-down',
      'hand-down' => 'hand-down',
      'hand-left' => 'hand-left',
      'hand-right' => 'hand-right',
      'hand-up' => 'hand-up',
      'circle' => 'circle',
      'circle-blank' => 'circle-blank',
      'play-circle' => 'play-circle',
      'play' => 'play',
      'pause' => 'pause',
      'stop' => 'stop',
      'step-backward' => 'step-backward',
      'fast-backward' => 'fast-backward',
      'backward' => 'backward',
      'forward' => 'forward',
      'fast-forward' => 'fast-forward',
      'step-forward' => 'step-forward',
      'eject' => 'eject',
      'fullscreen' => 'fullscreen',
      'resize-full' => 'resize-full',
      'resize-small' => 'resize-small',
      'phone' => 'phone',
      'phone-sign' => 'phone-sign',
      'facebook' => 'facebook',
      'facebook-sign' => 'facebook-sign',
      'twitter' => 'twitter',
      'twitter-sign' => 'twitter-sign',
      'github' => 'github',
      'github-alt' => 'github-alt',
      'github-sign' => 'github-sign',
      'linkedin' => 'linkedin',
      'linkedin-sign' => 'linkedin-sign',
      'pinterest' => 'pinterest',
      'pinterest-sign' => 'pinterest-sign',
      'google-plus' => 'google-plus',
      'google-plus-sign' => 'google-plus-sign',
      'sign-blank' => 'sign-blank',
      'ambulance' => 'ambulance',
      'beaker' => 'beaker',
      'h-sign' => 'h-sign',
      'hospital' => 'hospital',
      'medkit' => 'medkit',
      'plus-sign-alt' => 'plus-sign-alt',
      'stethoscope' => 'stethoscope',
      'user-md' => 'user-md',
  );
}

/**
	* Get taxonomies list
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_taxonomies_list() {

	$taxonomy_options = array();

	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

	foreach ( $taxonomies as $taxonomy ) {
		
		$taxonomy_options[ esc_attr( $taxonomy->name ) ] = $taxonomy->label;

	}

	return $taxonomy_options;
	
}

/**
	* Get taxonomy by taxonomy
	*
	* @package View_Builder
	* @since 1.0.0
*/
function get_taxonomies_by_taxonomy_list() {
	
	$term_options = array('None');

	$options = vb_options( views()->id );

	$taxonomy = isset($options['masonry-taxonomy']) ? $options['masonry-taxonomy'] : 'category';
	
	$taxonomies = array( $taxonomy );

	$args = array ('taxonomy' => $taxonomy);

	$terms = get_terms( $taxonomies, $args );
	
	foreach ($terms as $term)
		$term_options[$term->term_id] = $term->name;

	return $term_options;
	
}

function get_formatted_content () {
	$content = get_the_content();
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]>;', $content);
	return $content;
}

function get_trimmed_excerpt($charlength, $more) {
	$excerpt = get_the_excerpt();
	$charlength++;
	
	if (extension_loaded('mbstring')) {
		if ( mb_strlen( $excerpt ) > $charlength ) {
			/* If string needs to be trimmed */
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				$excerpt = mb_substr( $subex, 0, $excut );
			} else {
				$excerpt = $subex;
			}
			$excerpt = $excerpt.$more;
		} else {
			/* Nothing to trim */
			$excerpt = $excerpt;
		}
	} else {
		if ( strlen( $excerpt ) > $charlength ) {
			/* If string needs to be trimmed */
			$subex = substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				$excerpt = substr( $subex, 0, $excut );
			} else {
				$excerpt = $subex;
			}
			$excerpt = $excerpt.$more;
		} else {
			/* Nothing to trim */
			$excerpt = $excerpt;
		}
	}
	
	return $excerpt;
}

/* time passed */
function time_passed ($t1, $t2) {
	if($t1 > $t2) :
	  $time1 = $t2;
	  $time2 = $t1;
	else :
	  $time1 = $t1;
	  $time2 = $t2;
	endif;
	$diff = array(
	  'years' => 0,
	  'months' => 0,
	  'weeks' => 0,
	  'days' => 0,
	  'hours' => 0,
	  'minutes' => 0,
	  'seconds' =>0
	);
	$units = array('years','months','weeks','days','hours','minutes','seconds');
	foreach($units as $unit) :
	  while(true) :
	     $next = strtotime("+1 $unit", $time1);
	     if($next < $time2) :
	        $time1 = $next;
	        $diff[$unit]++;
	     else :
	        break;
	     endif;
	  endwhile;
	endforeach;
	return($diff);
}

function time_since($thetime) {
	$diff = time_passed($thetime, strtotime('now'));
	$units = 0;
	$time_since = array();
	foreach($diff as $unit => $value) :
	   if($value != 0 && $units < 2) :
			if($value === 1) :
				$unit = substr($unit, 0, -1);
			endif;
		   $time_since[]= $value . ' ' .$unit;
		   ++$units;		
	    endif;
	endforeach;
	$time_since = implode(', ',$time_since);
	$time_since .= ' ago';
	$date = $time_since;
	return $date;
}

/* outputs a string of terms from a taxonomy
** used to add a class to articles in filterable
*******************************************************/
function tax_term_classes($taxonomy, $before='', $after=' ') {
	$filter_class = '';
	if ( $taxonomy == 'post_format' ) {

		$terms = get_post_format( get_the_ID() );

		$filter_class = $terms;

	} else {

		$terms = get_the_terms( get_the_ID(), $taxonomy );
		if ( $terms ) {
			foreach ($terms as $term) { 
				$filter_class .= $before.strtolower(preg_replace('/\s+/', '-', $term->name)).$after; 
			}
		}

	}
	
	return $filter_class;
}

function display_slider_navigation($navigation_type, $tw, $th, $count) {

	$navigation = null;

	if ( $navigation_type == 'numbers' ) {

		$navigation = ' data-dot="<div class=\'dot-count\'>' . $count . '</div>"';

	} elseif ( $navigation_type == 'thumbs' ) {

		$thumbnail_id = get_post_thumbnail_id();  

		$thumbnail_object = wp_get_attachment_image_src($thumbnail_id, 'full'); 
		$thumbnail_url    = vb_resize_image($thumbnail_object[0], $tw, $th);

		if ( !$thumbnail_url ) {

			$navigation = ' data-dot="<img src=\'http://placehold.it/'. $tw .'x' . $th . '/eee/666/&text=image\'>"';

		} else {

			$navigation = ' data-dot="<img src=\''. $thumbnail_url .'\'>"';

		}
		
	} elseif ( $navigation_type == 'dots' ) {

		$navigation = null;

	}

	return $navigation;
}
