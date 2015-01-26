<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionMulticheckCategoriesIb extends TitanFrameworkOptionMulticheckIb {

	public $defaultSecondarySettings = array(
		'options' => array(),
		'orderby' => 'name',
		'order' => 'ASC',
		'taxonomy' => 'category',
		'hide_empty' => false,
		'show_count' => false,
	);

	/*
	 * Display for options and meta
	 */
	public function display() {
		$args = array(
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
			'taxonomy' => $this->settings['taxonomy'],
			'hide_empty' => $this->settings['hide_empty'] ? '1' : '0',
		);

		$categories = get_categories( $args );

		$this->settings['options'] = array();
		foreach ( $categories as $category ) {
			$this->settings['options'][$category->term_id] = $category->name . ( $this->settings['show_count'] ? " (" . $category->count . ")" : '' );
		}

		parent::display();
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$args = array(
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
			'taxonomy' => $this->settings['taxonomy'],
			'hide_empty' => $this->settings['hide_empty'] ? '1' : '0',
		);

		$categories = get_categories( $args );

		$this->settings['options'] = array('all' => 'All Categories');
		foreach ( $categories as $category ) {
			$this->settings['options'][$category->term_id] = $category->name . ( $this->settings['show_count'] ? " (" . $category->count . ")" : '' );
		}

		$wp_customize->add_control( new TitanFrameworkOptionMulticheckControlIb( $wp_customize, $this->getID(), array(
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