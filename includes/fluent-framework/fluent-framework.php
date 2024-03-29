<?php
/*
Plugin Name: Fluent Framework
Plugin URI: http://no-half-pixels.github.io/Fluent-Framework/
Description: Fluent Framework is a WordPress Plugin/Framework which allows you to create and manage options pages, meta boxes, author meta, taxonomy meta and much more.
Author: No Half Pixels Ltd
Version: 1.0.7
Author URI: http://nohalfpixels.com
Text Domain: fluent
*/
/**
 * Fluent Framework fluent-framework.php (root file).
 *
 * This is the main bootstrap file for the framework.
 * Its job is to register the fields, setup the required variables and include the class files.
 *
 * @package Fluent
 * @since 1.0.0
 * @version 1.0.2
 */

//prevent loading if class already loaded!
if(defined('FLUENT_FILE')){
    return;
}

/**
 * Define fluent base path - needed for loading classes.
 */
define( 'FLUENT_FILE', __FILE__ );

//load base class
require dirname(FLUENT_FILE) . '/classes/class.fluent-base.php';
//load base class
require dirname(FLUENT_FILE) . '/classes/class.fluent-store.php';
//load support class
require dirname(FLUENT_FILE) . '/classes/class.fluent-support.php';
//load page class
require dirname(FLUENT_FILE) . '/classes/class.fluent-page.php';
//load options classes
require dirname(FLUENT_FILE) . '/classes/class.fluent-options.php';
require dirname(FLUENT_FILE) . '/classes/class.fluent-options-page.php';
require dirname(FLUENT_FILE) . '/classes/class.fluent-options-meta.php';
require dirname(FLUENT_FILE) . '/classes/class.fluent-options-user.php';
require dirname(FLUENT_FILE) . '/classes/class.fluent-options-taxonomy.php';
require dirname(FLUENT_FILE) . '/classes/fields/class.fluent-field.php';
//load post type class
require dirname(FLUENT_FILE) . '/classes/class.fluent-post-type.php';
//load taxonomy class
require dirname(FLUENT_FILE) . '/classes/class.fluent-taxonomy.php';

/**
 * Define path to fluent resources. Needed for loading assets, if already defined we are running as included file, if not were in the plugin.
 */
Fluent_Base::$url = plugins_url( '/', FLUENT_FILE );

/**
 * Define path to fluent jquery ui style. We will soon create our own, but for now the delta style is awesome!
 */
if(!defined('FLUENT_JQUERY_UI_STYLE')){
    define('FLUENT_JQUERY_UI_STYLE', Fluent_Base::$url . 'assets/vendor/delta-ui/jquery-ui.css');
}

//register base field types - these can be overloaded by re-registering them later on.

//groups
Fluent_Options::register_field_type(array(
    'type' => 'group',
    'class_name' => 'Fluent_Group_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-group-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/group/'
));

//text inputs
Fluent_Options::register_field_type(array(
    'type' => 'text',
    'class_name' => 'Fluent_Text_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-text-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/text/'
));

Fluent_Options::register_field_type(array(
    'type' => 'email',
    'class_name' => 'Fluent_Email_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-email-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/email/'
));

Fluent_Options::register_field_type(array(
    'type' => 'url',
    'class_name' => 'Fluent_Url_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-url-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/url/'
));

Fluent_Options::register_field_type(array(
    'type' => 'number',
    'class_name' => 'Fluent_Number_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-number-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/number/'
));

Fluent_Options::register_field_type(array(
    'type' => 'password',
    'class_name' => 'Fluent_Password_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-password-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/password/'
));

Fluent_Options::register_field_type(array(
    'type' => 'textarea',
    'class_name' => 'Fluent_Textarea_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-textarea-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/textarea/'
));

//choice inputs
Fluent_Options::register_field_type(array(
    'type' => 'radio',
    'class_name' => 'Fluent_Radio_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-radio-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/radio/'
));

Fluent_Options::register_field_type(array(
    'type' => 'checkbox',
    'class_name' => 'Fluent_Checkbox_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-checkbox-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/checkbox/'
));

Fluent_Options::register_field_type(array(
    'type' => 'select',
    'class_name' => 'Fluent_Select_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-select-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/select/'
));

//special inputs
Fluent_Options::register_field_type(array(
    'type' => 'editor',
    'class_name' => 'Fluent_Editor_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-editor-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/editor/'
));

Fluent_Options::register_field_type(array(
    'type' => 'color',
    'class_name' => 'Fluent_Color_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-color-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/color/'
));

Fluent_Options::register_field_type(array(
    'type' => 'date',
    'class_name' => 'Fluent_Date_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-date-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/date/'
));

Fluent_Options::register_field_type(array(
    'type' => 'media',
    'class_name' => 'Fluent_Media_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-media-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/media/'
));

Fluent_Options::register_field_type(array(
    'type' => 'gallery',
    'class_name' => 'Fluent_Gallery_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-gallery-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/gallery/'
));

Fluent_Options::register_field_type(array(
    'type' => 'switch',
    'class_name' => 'Fluent_Switch_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-switch-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/switch/'
));

Fluent_Options::register_field_type(array(
    'type' => 'custom',
    'class_name' => 'Fluent_Custom_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-custom-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/custom/'
));

Fluent_Options::register_field_type(array(
    'type' => 'info',
    'class_name' => 'Fluent_Info_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-info-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/info/'
));

Fluent_Options::register_field_type(array(
    'type' => 'import',
    'class_name' => 'Fluent_Import_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-import-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/import/'
));

Fluent_Options::register_field_type(array(
    'type' => 'export',
    'class_name' => 'Fluent_Export_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-export-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/export/'
));

Fluent_Options::register_field_type(array(
    'type' => 'font',
    'class_name' => 'Fluent_Font_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-font-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/font/'
));

Fluent_Options::register_field_type(array(
    'type' => 'radio-img',
    'class_name' => 'Fluent_Radio_Img_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-radio-img-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/radio-img/'
));

Fluent_Options::register_field_type(array(
    'type' => 'ace',
    'class_name' => 'Fluent_Ace_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-ace-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/ace/'
));

Fluent_Options::register_field_type(array(
    'type' => 'background',
    'class_name' => 'Fluent_Background_Field',
    'path' => dirname(FLUENT_FILE) . '/classes/fields/class.fluent-background-field.php',
    'assets_path' => Fluent_Base::$url . 'assets/fields/background/'
));

if(defined('FLUENT_DEMO') && FLUENT_DEMO == true){
    require dirname(FLUENT_FILE) . '/example-usage.php';
}
