<?php
/**
 * Fluent_Textarea_Field
 *
 * @package Fluent
 * @since 1.0.0
 * @version 1.0.0
 */

add_action('fluent/options/field/textarea/render', array('Fluent_Textarea_Field', 'render'), 1, 2);

/**
 * Fluent_Textarea_Field simple textarea field.
 */
class Fluent_Textarea_Field extends Fluent_Field{
    
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
            'placeholder' => '',
            'cols' => 60,
            'rows' => 6
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
            $data['value'] = $data['default'];
        } else {
            $data['value'] = $data['value'];
        }
        
        echo '<textarea name="'.$data['option_name'].'['.$data['name'].']" cols="'.$data['cols'].'" rows="'.$data['rows'].'" id="'.$data['id'].'" class="'.implode(' ', $data['classes']).'" placeholder="'.$data['placeholder'].'">'.$data['value'].'</textarea>';   
    }
    
}