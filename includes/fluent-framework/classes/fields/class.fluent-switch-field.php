<?php
/**
 * Fluent_Switch_Field
 *
 * @package Fluent
 * @since 1.0.0
 * @version 1.0.0
 */

add_action('fluent/options/field/switch/render', array('Fluent_Switch_Field', 'render'), 1, 2);
add_action('fluent/options/field/switch/enqueue', array('Fluent_Switch_Field', 'enqueue'), 1, 2);

/**
 * Fluent_Switch_Field custom jQuery switch field made using a select element and the jQuery UI slider.
 */
class Fluent_Switch_Field extends Fluent_Field{
    
    /**
     * Returns the default field data.
     *
     * @since 1.0.0
     *
     * @return array default field data
     */
    public static function field_data(){
        $self = new self;
        return array(
            'labels' => array(
                'off' => __('Off', $self->domain), 
                'on' => __('On', $self->domain)
            )
        );
    }
    
    /**
     * Enqueue or register styles and scripts to be used when the field is rendered.
     *
     * @since 1.0.0
     *
     * @param array $data field data.
     *
     * @param array $field_data locations and other data for the field type.
     */
    public static function enqueue( $data = array(), $field_data = array() ){
        wp_enqueue_style('fluent-ui-css');
        wp_enqueue_script('jquery-ui-slider');
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
        
        $data['options'] = array(
            0 => $data['labels']['off'],
            1 => $data['labels']['on']
        );

        if($data['value'] == '' && isset($data['default']) && $data['default'] != ''){
            $data['value'] = $data['default'];
        }

        echo '<select name="'.$data['option_name'].'['.$data['name'].']" class="toggle-switch">';
            foreach($data['options'] as $key => $value){
                echo '<option value="'.$key.'"'.selected($data['value'], $key, false).'>'.$value.'</option>';
            }
        echo '</select>';
    }
    
}