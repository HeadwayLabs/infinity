<?php
/**
 * Fluent_Raw_Field
 *
 * @package Fluent
 * @since 1.0.0
 * @version 1.0.0
 */

add_action('fluent/options/field/raw/render', array('Fluent_Raw_Field', 'render'), 1, 2);

/**
 * Fluent_Raw_Field simple text field.
 */
class Fluent_Raw_Field extends Fluent_Field{
    
    /**
     * Returns the default field data.
     *
     * @since 1.0.0
     *
     * @return array default field data
     */
    public static function field_data(){
        return array(
            'icon' => '',
            'show_title' => true,
            'content' => ''
        );
    }
    
    /**
     * Render the field HTML based on the data provided.
     *
     * @since 1.0.0
     *
     * @param array $data field data.
     *
     * @param object $object Fluent_Options instance allowing you to alter anything if required.
     */
    public static function render($data = array(), $object){
        
        $data = self::data_setup($data);
        $icon = ($data['icon'] != '') ? '<div class="dashicons dashicons-'.$data['icon'].'"></div> ' : '';
        echo $data['content'];
        
    }
    
}