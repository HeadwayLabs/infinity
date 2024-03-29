<?php

/**
 * Sortable Option Class
 *
 * @author	Benjamin Intal
 * @package	Titan Framework Core
 * @since	1.4
 **/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Code Option Class
 *
 * @since	1.4
 **/
class TitanFrameworkOptionSortableInfinity extends TitanFrameworkOption {

	// Default settings specific to this option
	public $defaultSecondarySettings = array(
		'options' => array(),
		'visible_button' => true,
	);

	private static $firstLoad = true;


	/**
	 * Constructor
	 *
	 * @since	1.4
	 */
	function __construct( $settings, $owner ) {
		parent::__construct( $settings, $owner );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueSortable' ) );
		add_action( 'admin_head', array( __CLASS__, 'createSortableScriptInfinity' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueueSortable' ) );
	}


	/**
	 * Enqueues the jQuery UI scripts
	 *
	 * @return	void
	 * @since	1.4
	 */
	public function enqueueSortable() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}


	/**
	 * Creates the javascript needed for sortable to run
	 *
	 * @return	void
	 * @since	1.4
	 */
	public static function createSortableScriptInfinity() {
		if ( ! self::$firstLoad ) {
			return;
		}
		self::$firstLoad = false;

		?>
		<script>
		jQuery(document).ready(function($) {
			"use strict";

			// initialize
			$('.if-sortable > ul ~ input').each(function() {
				var value = $(this).val();
				try {
					value = unserialize( value );
				} catch (err) {
					return;
				}

				var ul = $(this).siblings('ul:eq(0)');
				ul.find('li.ui-sortable-handle').addClass('invisible').find('i.visibility').toggleClass('dashicons-visibility-faint');
				$.each(value, function(i, val) {
					ul.find('li.ui-sortable-handle[data-value=' + val + ']').removeClass('invisible').find('i.visibility').toggleClass('dashicons-visibility-faint');
				});
			});

			$('.if-sortable > ul').each(function() {
				$(this).sortable()
				.disableSelection()
				.on( "sortstop", function( event, ui ) {
					ifUpdateSortable(ui.item.parent());
				})
				.find('li.ui-sortable-handle').each(function() {
					$(this).find('i.visibility').click(function() {
						$(this).toggleClass('dashicons-visibility-faint').parents('li:eq(0)').toggleClass('invisible');
					});
				})
				.click(function() {
					ifUpdateSortable( $(this).parents('ul:eq(0)') );
				})
			});
		});

		function ifUpdateSortable(ul) {
			"use strict";
			var $ = jQuery;

			var values = [];

			ul.find('li.ui-sortable-handle').each(function() {
				if ( ! $(this).is('.invisible') ) {
					values.push( $(this).attr('data-value') );
				}
			});

			ul.siblings('input').eq(0).val( serialize( values ) ).trigger('change');
		}
		</script>
		<?php
	}


	/**
	 * Displays the option in admin panels and meta boxes
	 *
	 * @return	void
	 * @since	1.4
	 */
	public function display() {
		if ( ! is_array( $this->settings['options'] ) ) {
			return;
		}
		if ( ! count( $this->settings['options'] ) ) {
			return;
		}

		$this->echoOptionHeader( true );

		$values = $this->getValue();
		if ( $values == '' ) {
			$values = array_keys( $this->settings['options'] );
		}
		if ( is_serialized( $values ) ) {
			$values = unserialize( $values );
		}
		if ( count( $values ) != count( $this->settings['options'] ) ) {
			$this->settings['visible_button'] = true;
		}

		$visibleButton = '';
		if ( $this->settings['visible_button'] === true ) {
			$visibleButton = "<i class='dashicons dashicons-visibility visibility'></i>";
		}
		?>
		<ul>
			<?php
			foreach ( $values as $dummy => $value ) {
				printf( "<li data-value='%s'><i class='dashicons dashicons-menu'></i>%s%s</li>",
					esc_attr( $value ),
					$visibleButton,
					$this->settings['options'][ $value ]
				);
			}

			$invisibleKeys = array_diff( array_keys( $this->settings['options'] ), $values );
			foreach ( $invisibleKeys as $dummy => $value ) {
				printf( "<li data-value='%s'><i class='dashicons dashicons-menu'></i>%s%s</li>",
					esc_attr( $value ),
					$visibleButton,
					$this->settings['options'][ $value ]
				);
			}
			?>
		</ul>
		<div class='clear: both'></div>
		<?php

		if ( ! is_serialized( $values ) ) {
			$values = serialize( $values );
		}

		printf( "<input type='hidden' name=\"%s\" id=\"%s\" value=\"%s\" />",
			$this->getID(),
			$this->getID(),
			esc_attr( $values )
		);

		$this->echoOptionFooter( false );
	}


	/**
	 * Cleans up the serialized value before saving
	 *
	 * @param	string $value The serialized value
	 * @return	string The cleaned value
	 * @since	1.4
	 */
	public function cleanValueForSaving( $value ) {
		return stripslashes( $value );
	}


	/**
	 * Cleans the raw value for getting
	 *
	 * @param	string $value The raw value
	 * @return	string The cleaned value
	 * @since	1.4
	 */
	public function cleanValueForGetting( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}
		if ( is_serialized( stripslashes( $value ) ) ) {
			return unserialize( $value );
		}
		return $value;
	}


	/**
	 * Registers the theme customizer control, for displaying the option
	 *
	 * @param	WP_Customize $wp_enqueue_script The customize object
	 * @param	TitanFrameworkCustomizerSection $section The section where this option will be placed
	 * @param	int $priority The order of this control in the section
	 * @return	void
	 * @since	1.4
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new TitanFrameworkOptionSortableControlInfinity( $wp_customize, $this->getID(), array(
			'label' => $this->settings['name'],
			'section' => $section->settings['id'],
			'settings' => $this->getID(),
			'description' => $this->settings['desc'],
			'priority' => $priority,
			'options' => $this->settings['options'],
			'visible_button' => $this->settings['visible_button'],
		) ) );
	}
}



/*
 * We create a new control for the theme customizer
 */
add_action( 'customize_register', 'registerTitanFrameworkOptionSortableControlInfinity', 1 );


/**
 * Creates the option for the theme customizer
 *
 * @return	void
 * @since	1.4
 */
function registerTitanFrameworkOptionSortableControlInfinity() {
	class TitanFrameworkOptionSortableControlInfinity extends WP_Customize_Control {
		public $description;
		public $options;
		public $visible_button;

		public function render_content() {
			TitanFrameworkOptionSortableInfinity::createSortableScriptInfinity();

			if ( ! is_array( $this->options ) ) {
				return;
			}
			if ( ! count( $this->options ) ) {
				return;
			}

			?>
			<label class='if-sortable'>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php

			$values = $this->value();

			if ( $values == '' ) {
				$values = array_keys( $this->options );
			}
			if ( is_serialized( $values ) ) {
				$values = unserialize( $values );
			}
			if ( count( $values ) != count( $this->options ) ) {
				$this->visible_button = true;
			}

			$visibleButton = '';
			if ( $this->visible_button === true ) {
				$visibleButton = "<i class='dashicons dashicons-visibility visibility'></i>";
			}
			$admin_button = "<i class='dashicons dashicons-admin-generic'></i>";
			$appearance_button = "<i class='dashicons dashicons-admin-appearance'></i>";
			//$values = array_filter( $values );

			?>
			<ul class="infinity-sortable">
				<?php
				foreach ( $values as $dummy => $value ) {
					printf( "<li data-value='%s' class='%s'><i class='dashicons dashicons-menu'></i>%s%s%s<span class='sort-title'>%s</span></li>",
						esc_attr( $value ),
						esc_attr( $value ),
						$visibleButton,
						$appearance_button,
						$admin_button,
						$this->options[ $value ]
					);
				}

				$invisibleKeys = array_diff( array_keys( $this->options ), $values );
				foreach ( $invisibleKeys as $dummy => $value ) {
					printf( "<li data-value='%s' class='%s invisible'><i class='dashicons dashicons-menu'></i>%s%s%s<span class='sort-title'>%s</span></li>",
						esc_attr( $value ),
						esc_attr( $value ),
						$visibleButton,
						$appearance_button,
						$admin_button,
						$this->options[ $value ]
					);
				}
				?>
			</ul>
			<div class='clear: both'></div>
			<?php

			if ( ! is_serialized( $values ) ) {
				$values = serialize( $values );
			}

			?>
				<input type='hidden' <?php $this->link(); ?> value='<?php echo esc_attr( $values )  ?>'/>
			</label>
			<?php
			echo "<p class='description'>{$this->description}</p>";
		}
	}
}