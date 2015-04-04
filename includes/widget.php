<?php

class Infinity_Widget extends WP_Widget  {

	/**
		* Widget Init 
		* @package Infinity_Builder
		* @since 1.0.0
	*/
	function Infinity_Widget() {

		$widget_opts = array(
			'classname' => 'infinity_widget',
			'description' => 'A widget to add an infinite view to your site.',
		);
	 
		$this->WP_Widget('infinity_widget', 'Infinity Views', $widget_opts); 

	}

	/**
		* Widget
		* Displays widget output
		* @package Infinity_Builder
		* @since 1.0.0
	*/ 
	function widget($args, $instance) {

		extract( $args );

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		if ( $instance['title'] )
			echo $before_title . $instance['title'] . $after_title;

		echo $before_widget;

		$id = $instance['view_id'];
		$options = vb_options( $id );

		$builder_options = TitanFramework::getInstance( 'builder-options' );
		$view_name = strtolower(views()->view_name);

		$layout = ( $builder_options->getOption( 'view-layout-' . $id . '' ) == true ) ? $builder_options->getOption( 'view-layout-' . $id . '' ) : 'blog';

		echo vb_render_view( $id, $layout, null, 'shortcode' );

		echo $after_widget;

	}

	/**
		* Form
		* Displays the form which shows on the Widgets management panel. 
		* @package Infinity_Builder
		* @since 1.0.0
	*/ 
	function form($instance) {

	$defaults = array(
		'view_id'        => 0,
		'title'          => ''
	);

	$instance = (array) $instance;

	$instance 	= wp_parse_args( (array) $instance, $defaults );
	$title 		= esc_attr($instance['title']);
	$view_id    = $instance['view_id'];
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('view_id'); ?>"><?php _e('View:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('view_id'); ?>" name="<?php echo $this->get_field_name('view_id'); ?>">
		<?php
		$views = vb_get_views();
		foreach ( $views as $view ) {
			$selected = selected( $view_id, $view->ID, false );
			echo "<option value='$view->ID'$selected>$view->post_title</option>";
		}
		?>
		</select>
	</p>
	<p>
	<?php 
		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			echo '<a href="' . wp_customize_url( get_stylesheet() ) . '" class="load-customize hide-if-no-customize button button-secondary">'
		. __( 'Open Customizer' ) . '</a>';
		}
	?>
	</p>

	<?php
	}

	/**
		* Update
		* Updates the widget options when saved. 
		* @package Infinity_Builder
		* @since 1.0.0
	*/
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['view_id'] = absint( $new_instance['view_id'] );
		return $instance; 
	}
}

// Load and Register the widget
add_action('widgets_init', 'infinity_plugin_load_widgets');

function infinity_plugin_load_widgets() {
  register_widget('Infinity_Widget');
}                                                                                                                                                                         