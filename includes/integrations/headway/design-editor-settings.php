<?php
/* Headway 3.2.5 has better support for the 'headway_element_data_defaults' filter */
if ( version_compare(HEADWAY_VERSION, '3.2.5', '>=') ) {

	add_filter('headway_element_data_defaults', 'infinity_builder_block_add_default_design_settings');
	function infinity_builder_block_add_default_design_settings($existing_defaults) {

		return array_merge($existing_defaults, infinity_builder_block_default_design_settings());

	}


} else {

	add_action('init', 'infinity_builder_pre325_default_design_settings');

	function infinity_builder_pre325_default_design_settings() {

		global $headway_default_element_data;

		$headway_default_element_data = array_merge($headway_default_element_data, infinity_builder_block_default_design_settings());

	}

}


function infinity_builder_block_default_design_settings() {

	return array(

		'block-infinity-block-parts' => array(
	      'properties' => array(
	         'margin-top' => '1em',
	         'margin-bottom' => '1em',
	         'margin-left' => '0',
	         'margin-right' => '0'
	      )
		),

		'block-infinity-block-content-part' => array(
			'properties' => array(
				'line-height' => '1.5em'
			)
		),

		'block-infinity-block-woocommerce-part-added-to-cart' => array(
			'properties' => array(
				'font-size' => '.8em',
				'text-align' => 'center',
				'margin-top' => '1em',
				'text-decoration' => 'none',
			)
		),

		'block-infinity-block-woocommerce-part-add-to-cart' => array(
			'properties' => array(
				'background-color' => '#EEEEEE',
				'padding-top' => '1em',
				'padding-right' => '2em',
				'padding-bottom' => '1em',
				'padding-left' => '2em',
				'text-decoration' => 'none',
			)
		),

		'block-infinity-block-woocommerce-part-sale-flash-text' => array(
			'properties' => array(
				'background-color' => '#9C5D90',
				'color' => '#FFFFFF',
				'font-size' => '14px',
				'padding-top' => '8px',
				'padding-right' => '8px',
				'padding-bottom' => '8px',
				'padding-left' => '8px',
				'text-align' => 'center',
				'border-top-left-radius' => '2px',
				'border-top-right-radius' => '2px',
				'border-bottom-left-radius' => '2px',
				'border-bottom-right-radius' => '2px'
			)
		),

		'block-infinity-block-meta-parts' => array(
			'properties' => array(
				'font-size' => '12px',
				'font-family' => 'georgia',
				'color' => '#999999',
				'font-styling' => 'italic',
				'margin-top' => '.8em',
				'margin-right' => '0px',
				'margin-bottom' => '.8em',
				'margin-left' => '0px'
		   )
		),

		'block-infinity-block-meta-parts-link' => array(
			'properties' => array(
				'color' => '#888888',
				'font-size' => '12px'
			)
		),

		'block-infinity-block-filter-nav-controls' => array(
			'properties' => array(
				'margin-top' => '2em',
				'margin-bottom' => '1em'
			)
		),

		'block-infinity-block-filter-nav-link' => array(
			'special-element-state' => array(
				'active' => array(
					'background-color' => '#EEEEEE',
					'border-color' => '#BBBBBB',
					'color' => '#333333',
				)
			),
			'properties' => array(
				'font-size' => '.9em',
				'text-decoration' => 'none',
				'padding-top' => '.6em',
				'padding-right' => '1em',
				'padding-bottom' => '.5em',
				'padding-left' => '1em',
				'margin-top' => '0px',
				'margin-right' => '1em',
				'margin-bottom' => '1em',
				'margin-left' => '0px',
				'background-color' => '#FFFFFF',
				'border-color' => '#CCCCCC',
				'border-style' => 'solid',
				'border-top-width' => '1px',
				'border-right-width' => '1px',
				'border-bottom-width' => '1px',
				'border-left-width' => '1px',
				'border-top-left-radius' => '2px',
				'border-top-right-radius' => '2px',
				'border-bottom-left-radius' => '2px',
				'border-bottom-right-radius' => '2px',
				'line-height' => '180%'
			)

		),

		'block-infinity-block-filter-nav-before-filter' => array(
			'properties' => array(
				'line-height' => '270%',
				'font-size' => '14px',
				'margin-right' => '18px'
			)
		),

		'block-infinity-block-readmore-part' => array(
			'properties' => array(
				'background-color' => '#EEEEEE',
				'padding-top' => '1em',
				'padding-right' => '2em',
				'padding-bottom' => '1em',
				'padding-left' => '2em',
				'text-decoration' => 'none',
			)
		),

		'block-infinity-block-thumbnail-cover' => array(
			'properties' => array(
				'background-color' => 'rgba(0,0,0,0.5)'
			)
		),

		'block-infinity-block-thumbnail-cover-content' => array(
			'properties' => array(
				'background-color' => 'rgba(0,0,0,0.5)'
			)
		),

		'block-infinity-block-cover-button' => array(
			'properties' => array(
				'background-color' => '#FFFFFF',
				'font-size' => '12px',
				'line-height' => '40px',
				'margin-right' => '2px',
				'margin-left' => '0px',
				'text-align' => 'center',
				'border-top-left-radius' => '50%',
				'border-top-right-radius' => '50%',
				'border-bottom-left-radius' => '50%',
				'border-bottom-right-radius' => '50%'
			)
		),

		'block-infinity-block-cover-link' => array(
			'properties' => array(
				'color' => '#222222',
				'font-styling' => 'normal',
			)
		),

		'block-infinity-block-cover-link-icon' => array(
			'properties' => array(
				'font-size' => '14px'
			)
		),

		'block-infinity-block-pagination-controls' => array(
			'properties' => array(
				'padding-top' => '.5em',
				'padding-right' => '.5em',
				'padding-bottom' => '.5em',
				'padding-left' => '.5em',
				'margin-top' => '.5em',
				'margin-right' => '.5em',
				'margin-bottom' => '.5em',
				'margin-left' => '.5em'
			)
		),

		'block-infinity-block-pagination-item' => array(
			'properties' => array(
				'background-color' => '#FFFFFF',
				'border-color' => '#CCCCCC',
				'border-style' => 'solid',
				'border-top-width' => '1px',
				'border-right-width' => '1px',
				'border-bottom-width' => '1px',
				'border-left-width' => '1px',
				'border-top-left-radius' => '3px',
				'border-top-right-radius' => '3px',
				'border-bottom-left-radius' => '3px',
				'border-bottom-right-radius' => '3px',
				'padding-top' => '.5em',
				'padding-right' => '1em',
				'padding-bottom' => '.5em',
				'padding-left' => '1em',
				'margin-top' => '1px',
				'margin-right' => '2px',
				'margin-bottom' => '1px',
				'margin-left' => '2px',
				'color' => '#999999',
				'font-size' => '12px',
			),

			'special-element-state' => array(
				'hover' => array(
					'background-color' => '#666666',
					'color' => '#FFFFFF',
					'border-color' => '#555555'
				),

				'focus' => array(
					'background-color' => '#666666',
					'color' => '#FFFFFF',
					'border-color' => '#555555'
				),

				'current' => array(
					'background-color' => '#666666',
					'color' => '#FFFFFF',
					'border-color' => '#555555'
				)

			)

		),

		'block-infinity-block-title' => array(
			'properties' => array(
				'font-size' => '20px',
				'text-decoration' => 'none',
				'line-height' => '120%'
			)
		),

		'block-infinity-block-carousel-direction-nav-button' => array(
			'properties' => array(
			'color' => '#FFFFFF',
			'font-size' => '19px',
			'margin-top' => '5px',
			'margin-right' => '5px',
			'margin-bottom' => '5px',
			'margin-left' => '5px',
			'padding-top' => '6px',
			'padding-right' => '6px',
			'padding-bottom' => '6px',
			'padding-left' => '6px',
			'background-color' => '#BBBBBB',
			'border-top-left-radius' => '2px',
			'border-top-right-radius' => '2px',
			'border-bottom-left-radius' => '2px',
			'border-bottom-right-radius' => '2px'
			),
			'special-element-state' => array(
				'hover' => array(
					'background-color' => '#785C7E',
					'color' => '#FFFFFF',
					'text-decoration' => 'none'
				)
			)

		),

		'block-infinity-block-carousel-dots-nav-dot' => array(
			'properties' => array(
				'margin-top' => '7px',
				'margin-right' => '5px',
				'margin-bottom' => '7px',
				'margin-left' => '5px',
				'background-color' => '#BBBBBB',
				'border-top-left-radius' => '50%',
				'border-top-right-radius' => '50%',
				'border-bottom-left-radius' => '50%',
				'border-bottom-right-radius' => '50%'
			),

			'special-element-state' => array(
				'hover' => array(
					'background-color' => '#3885C6'
				),

				'active' => array(
					'background-color' => '#785C7E'
				),
			)
		),

		'block-infinity-block-slider-direction-nav' => array(
			'properties' => array(
				'margin-top' => '10px',
				'text-align' => 'center'
			)
		),

		'block-infinity-block-slider-direction-nav-button' => array(
			'properties' => array(
				'background-color' => '#BBBBBB',
				'padding-top' => '10px',
				'padding-right' => '10px',
				'padding-bottom' => '10px',
				'padding-left' => '10px',
				'margin-top' => '0px',
				'margin-right' => '0px',
				'margin-bottom' => '0px',
				'margin-left' => '0px',
				'font-size' => '14px',
				'color' => '#FFFFFF',
				'border-top-left-radius' => '2px',
				'border-top-right-radius' => '2px',
				'border-bottom-left-radius' => '2px',
				'border-bottom-right-radius' => '2px'
			),

			'special-element-state' => array(
				'hover' => array(
					'background-color' => '#785C7E',
					'color' => '#FFFFFF',
					'text-decoration' => 'none'
				)
			)
		),

		'block-infinity-block-slider-dots-nav' => array(
			'properties' => array(
				'margin-top' => '10px',
				'text-align' => 'center',
				'position' => 'absolute'
			)
		),

		'block-infinity-block-slider-dots-nav-dots' => array(
			'properties' => array(
				'margin-top' => '25px'
			)
		),

		'block-infinity-block-slider-dots-nav-numbers' => array(
			'properties' => array(
				'margin-top' => '12px'
			)
		),

		'block-infinity-block-slider-dots-nav-thumbs' => array(
			'properties' => array(
				'margin-top' => '10px'
			)
		),

		'block-infinity-block-slider-dots-nav-dot' => array(
			'properties' => array(
				'background-color' => '#BBBBBB',
				'border-top-left-radius' => '50%',
				'border-top-right-radius' => '50%',
				'border-bottom-left-radius' => '50%',
				'border-bottom-right-radius' => '50%',
				'margin-top' => '0px',
				'margin-right' => '3px',
				'margin-bottom' => '0px',
				'margin-left' => '3px'
			),

			'special-element-state' => array(
				'hover' => array(
					'background-color' => '#3885C6'
				),
				'active' => array(
					'background-color' => '#785C7E',
					'color' => '#FFFFFF'
				)
			)
		),

		'block-infinity-block-slider-dots-nav-number' => array(
			'properties' => array(
				'margin-top' => '0px',
				'margin-right' => '3px',
				'margin-bottom' => '0px',
				'margin-left' => '3px',
				'background-color' => '#FFFFFF',
				'padding-top' => '13px',
				'padding-right' => '13px',
				'padding-bottom' => '13px',
				'padding-left' => '13px',
				'text-align' => 'center',
				'line-height' => '100%',
				'border-top-left-radius' => '2px',
				'border-top-right-radius' => '2px',
				'border-bottom-left-radius' => '2px',
				'border-bottom-right-radius' => '2px'
			)
		),

		'block-infinity-block-slider-dots-nav-image' => array(
			'properties' => array(
				'margin-top' => '0px',
				'margin-right' => '3px',
				'margin-bottom' => '0px',
				'margin-left' => '3px',
				'background-color' => '#FFFFFF',
				'padding-top' => '2px',
				'padding-right' => '2px',
				'padding-bottom' => '2px',
				'padding-left' => '2px',
				'text-align' => 'center',
				'line-height' => '100%',
				'border-top-left-radius' => '2px',
				'border-top-right-radius' => '2px',
				'border-bottom-left-radius' => '2px',
				'border-bottom-right-radius' => '2px'
			)
		),

		'block-infinity-block-share-part-item' => array(
			'properties' => array()
		),

		'block-infinity-block-share-part-item-link' => array(
			'properties' => array(
				'text-decoration' => 'none',
			)
		),

		'block-infinity-block-share-part-item-link-icon' => array(
			'properties' => array(
				'font-size' => '16px',
			)
		)

	);

}