<?php

define('INFINITY_BLOCK_VERSION', views()->version);

/**
 * Everything is ran at the after_setup_theme action to insure that all of Headway's classes and functions are loaded.
 **/
add_action('after_setup_theme', 'infinity_block_register');
function infinity_block_register() {

	/* Make sure that Headway is activated, otherwise don't register the block because errors will be thrown. */
	if ( !class_exists('Headway') )
		return;
	
	require_once 'block.php';
	require_once 'block-options.php';

	require_once 'design-editor-settings.php';	

	/**
	 * @param Class name in block.php.  
	 * @param Path to the folder that contains the block icons.  In this example block it's this plugin's folder.
	 **/
	return headway_register_block('HeadwayInfinityBlock', plugins_url(false, __FILE__));

}


/**
 * If you plan on adding your block to Headway Extend, then this will be the code that will enable auto-updates for the block/plugin.
 **/
add_action('init', 'infinity_block_extend_updater');
function infinity_block_extend_updater() {

	if ( !class_exists('HeadwayUpdaterAPI') )
		return;

	$updater = new HeadwayUpdaterAPI(array(
		'slug' => 'infinity-block',
		'path' => plugin_basename(__FILE__),
		'name' => 'Infinity Block',
		'type' => 'block',
		'current_version' => INFINITY_BLOCK_VERSION
	));

}