<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionMulticheckInfinity extends TitanFrameworkOption {

	public $defaultSecondarySettings = array(
		'options' => array(),
	);

	/*
	 * Display for options and meta
	 */
	public function display() {
		$this->echoOptionHeader( true );

		echo "<fieldset>";

		$savedValue = $this->cleanValueForGetting( $this->getValue() );
		if ( empty( $savedValue ) ) {
			$savedValue = array();
		}

		foreach ( $this->settings['options'] as $value => $label ) {
			printf('<p><label for="%s"><input id="%s" type="checkbox" name="%s[]" value="%s" %s/> %s</label></p>',
				$this->getID() . $value,
				$this->getID() . $value,
				$this->getID(),
				esc_attr( $value ),
				checked( in_array( $value, $savedValue ), true, false ),
				$label
			);
		}

		echo "</fieldset>";

		$this->echoOptionFooter( false );
	}

	public function cleanValueForSaving( $value ) {
		if ( is_serialized( $value ) ) {
			return $value;
		}
		// CSV
		if ( is_string( $value ) ) {
			$value = explode( ',', $value );
		}
		return serialize( $value );
	}

	public function cleanValueForGetting( $value ) {
		if ( empty( $value ) ) {
			return array();
		}
		if ( is_array( $value ) ) {
			return $value;
		}
		if ( is_serialized( $value ) ) {
			return unserialize( $value );
		}
		if ( is_string( $value ) ) {
			return explode( ',', $value );
		}
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new TitanFrameworkOptionMulticheckControlInfinity( $wp_customize, $this->getID(), array(
			'label' => $this->settings['name'],
			'section' => $section->settings['id'],
			'settings' => $this->getID(),
			'type' => 'multicheck-ib',
			'description' => $this->settings['desc'],
			'options' => $this->settings['options'],
			'priority' => $priority,
		) ) );
	}
}


/*
 * WP_Customize_Control with description
 */
add_action( 'customize_register', 'registerTitanFrameworkOptionMulticheckControlInfinity', 1 );
function registerTitanFrameworkOptionMulticheckControlInfinity() {
	class TitanFrameworkOptionMulticheckControlInfinity extends WP_Customize_Control {
		public $description;
		public $options;

		private static $firstLoad = true;

		// Since theme_mod cannot handle multichecks, we will do it with some JS
		public function render_content() {
			// the saved value is an array. convert it to csv
			if ( is_array( $this->value() ) ) {
				$savedValueCSV = implode( ',', $this->value() );
				$values = $this->value();
			} else {
				$savedValueCSV = $this->value();
				$values = explode( ',', $this->value() );
			}

			if ( self::$firstLoad ) {
				self::$firstLoad = false;

				?>
				<script>
				jQuery(document).ready(function($) {
					"use strict";

					$('input.tf-multicheck').change(function(event) {
						event.preventDefault();
						var csv = '';

						$(this).parents('li:eq(0)').find('input[type=checkbox]').each(function() {
							if ($(this).is(':checked')) {
								csv += $(this).attr('value') + ',';
							}
						});

						csv = csv.replace(/,+$/, "");

						$(this).parents('li:eq(0)').find('input[type=hidden]').val(csv)
						// we need to trigger the field afterwards to enable the save button
						.trigger('change');
						return true;
					});
				});
				</script>
				<?php
			}

			$description = '';
			if ( ! empty( $this->description ) ) {
				$description = "<p class='description'>" . $this->description . "</p>";
			}
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?><?php //echo $description; ?></span>
			<div class='tf-multicheck-container hidden'>
				<?php
				
				foreach ( $this->options as $value => $label ) {
					printf('<p class="multicheck-row"><label for="%s"><input class="tf-multicheck" id="%s" type="checkbox" value="%s" %s/> %s</label></p>',
						$this->id . $value,
						$this->id . $value,
						esc_attr( $value ),
						checked( in_array( $value, $values ), true, false ),
						$label
					);
				}
				?>
				<input type="hidden" value="<?php echo esc_attr( $savedValueCSV ); ?>" <?php $this->link(); ?> />
			</div>
			<?php
		}
	}
}