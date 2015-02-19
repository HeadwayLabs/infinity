<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionHeadingInfinity extends TitanFrameworkOption {

	/*
	 * Display for options and meta
	 */
	public function display() {
		?>
		<tr valign="top" class="even first tf-heading">
		<th scope="row" class="first last" colspan="2">
		<h3><?php echo $this->settings['name'] ?></h3>
		</th>
		</tr>
		<?php
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new TitanFrameworkHeadingControlInfinity( $wp_customize, $this->getID(), array(
			'label' => $this->settings['name'],
			'section' => $section->settings['id'],
			'settings' => $this->getID(),
			'description' => $this->settings['desc'],
			'type' => 'heading',
			'priority' => $priority,
		) ) );
	}


}

/*
 * We create a new control for the theme customizer
 */
add_action( 'customize_register', 'registerTitanFrameworkOptionHeadingControlInfinity', 1 );
function registerTitanFrameworkOptionHeadingControlInfinity() {
	class TitanFrameworkHeadingControlInfinity extends WP_Customize_Control {
		public $description;

		public function render_content() {
			if ($this->label) {
				echo '<label><h3>'. $this->label . '</h3></label>';
			}
			if ($this->description) {
				echo '<span class="description">'. $this->description . '</span>';
			}
		}
	}
}