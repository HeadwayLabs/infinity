<?php
/**
 * Fluent_Text_Field
 *
 * @package Fluent
 * @since 1.0.0
 * @version 1.0.0
 */

add_action('fluent/options/field/text/render', array('Fluent_Text_Field', 'render'), 1, 2);

/**
 * Fluent_Text_Field simple text field.
 */
class Fluent_Text_Field extends Fluent_Field{
    
    /**
     * Returns the default field data.
     *
     * @since 1.0.0
     *
     * @return array default field data
     */
    public static function field_data(){
        return array(
            'classes' => array(
                'large-text'
            ),
            'placeholder' => ''
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

        if ( $data['value'] == null ) {
            $value = $data['default'];
        } else {
            $value = $data['value'];
        }
        
        echo '<input type="text" name="'.$data['option_name'].'['.$data['name'].']" id="'.$data['id'].'" value="'.$value.'" class="'.implode(' ', $data['classes']).'" placeholder="'.$data['placeholder'].'" />';   
    }
    
}