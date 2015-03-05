<?php
/* This class must be included in another file and included later so we don't get an error about HeadwayBlockOptionsAPI class not existing. */

class HeadwayInfinityBlockOptions extends HeadwayBlockOptionsAPI {

	public $tab_notices = array(
		'style-view' => 'Make sure you are in design mode. Then use the Headway inspector to style elements. We suggest you set the views style to <strong>Headway</strong> option in the Customizer. See: <strong>1) Select a style</strong>',
		'add-view' => ''
	);
	
	
	public $tabs = array(
		'add-view' => 'Add View',
		'style-view' => 'Style View'
	);

	public $inputs = array(
		'add-view' => array(

			'view' => array(
				'type' => 'select',
				'name' => 'view', //This will be the setting you retrieve from the database.
				'label' => 'Select a view',
				'default' => '',
				'options' => 'get_views()',
				'tooltip' => 'Select a view to show.'
			)

		)
	);

	function get_views() {
		
		$views_list = array('' => ' -- Select a view -- ');
		
		$views = vb_get_views();
		
		foreach ( $views as $view )
			$views_list[$view->ID] = $view->post_title;
			
		return $views_list;
		
	}

	function modify_arguments($args = false) {

		$customizer_link = '<p>
									<strong>First </strong>make sure you have created a 
									<a href="wp-admin/edit.php?post_type=view" target="_blank">view</a>
									. Then select your 
									<a href="wp-admin/edit.php?post_type=view" target="_blank">view</a>
									 from the list below.</p>
									 <p> If you want to style your view, make sure you are in design mode in Headway. To 
									 <a id="open-customizer" href="' . wp_customize_url( get_stylesheet() ) . '" title="Open Wordpress Customizer to start editing this View." target="_blank">Build the layout & Configure your view</a>
									 . You use the <a id="open-customizer" href="' . wp_customize_url( get_stylesheet() ) . '" title="Open Wordpress Customizer to start editing this View." target="_blank">Wordpress Customizer.</a></p>';
		
		$this->tab_notices['add-view'] = $customizer_link;
		
	}
	
	
	
}
