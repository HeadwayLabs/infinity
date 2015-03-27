<?php
/**
 * Fluent register post types
 *
 * @package Fluent
 * @since 1.0.1
 * @version 1.0.2
 */

$name = 'View';//name will be converted to lowercase with underscores for spaces and dashes
$args = array(
    //normal register_post_type() args, defaults used unless otherwise stated
    'labels'               => array(
        'name'               => sprintf(__('%ss', 'infinity'), 'Infinite'),
        'singular_name'      => $name,
        'menu_name'          => sprintf(__( '%ss', 'infinity' ), 'Infinity View'),
        'name_admin_bar'     => sprintf(__( '%s', 'infinity' ), $name),
        'add_new'            => __( 'Add New', 'infinity' ),
        'add_new_item'       => sprintf(__( 'Add New %s', 'infinity' ), $name),
        'new_item'           => sprintf(__( 'New %s', 'infinity' ), $name),
        'edit_item'          => sprintf(__( 'Editing %s', 'infinity' ), $name),
        'view_item'          => sprintf(__( 'View %s', 'infinity' ), $name),
        'all_items'          => sprintf(__( 'All %ss', 'infinity' ), $name),
        'search_items'       => sprintf(__( 'Search %ss', 'infinity' ), $name),
        'parent_item_colon'  => sprintf(__( 'Parent %ss:', 'infinity' ), $name),
        'not_found'          => sprintf(__( 'No %ss found.', 'infinity' ), strtolower($name)),
        'not_found_in_trash' => sprintf(__( 'No %ss found in Trash.', 'infinity' ), strtolower($name))
    ),
    'description'          => '',
    'public'               => true,
    'hierarchical'         => false,
    'exclude_from_search'  => null,
    'publicly_queryable'   => null,
    'show_ui'              => null,
    'show_in_menu'         => null,
    'show_in_nav_menus'    => null,
    'show_in_admin_bar'    => null,
    'menu_position'        => null,
    'menu_icon'            => null,
    'capability_type'      => 'post',
    'map_meta_cap'         => null,
    'supports'             => array( 'title' ),
    'register_meta_box_cb' => null,
    'taxonomies'           => array(),//default is empty array, we have assigned the tag taxonomy for demostration purposes. Any taxonomies added here must be builtin or registered before the init priority 10. If you want to assign Fluent_Taxonomies you need to declare this post type when registering the taxonomy.
    'has_archive'          => true,//default = false but we have set to true so you can see the archive template overwrite
    'rewrite'              => true,
    'query_var'            => true,
    'can_export'           => true,
    'delete_with_user'     => null,
    '_edit_link'           => 'post.php?post=%d',
    //our custom messages array, defaults detailed below. this allows you to change the notices when posts are changed in some way without adding yet more filters
    'messages' => array(
        0  => '', // Unused. Messages start at index 1.
        1  => __( '%s updated.', 'domain' ),
        2  => __( 'Custom field updated.', 'domain' ),
        3  => __( 'Custom field deleted.', 'domain' ),
        4  => __( '%s updated.', 'domain' ),
        5  => __( '%s restored to revision from %s', 'domain' ),
        6  => __( '%s published.', 'domain' ),
        7  => __( '%s saved.', 'domain' ),
        8  => __( '%s submitted.', 'domain' ),
        9  => __( '%s scheduled for: <strong>%s</strong>.', 'domain' ),
        10 => __( '%s draft updated.', 'domain' )
    ),
    //want to disable people adding new posts?
    'disable_add_new' => false,
    //here you can set the template paths if creating the post type in a plugin, you can also set it to override the theme version with override = true. default behavior is to provide a fallback not to override.
    'templates' => array(
        'single' => array(//the single post page
            'override' => true,//default = false. set to true to force this to be used before the theme version
            'path' => dirname(FLUENT_FILE) . '/example-singular-template.php'//full path to file, default = false
        ),
        'archive' => array(//the archive/list page (if post type supports archives)
            'override' => true,//default = false. set to true to force this to be used before the theme version
            'path' => dirname(FLUENT_FILE) . '/example-archive-template.php'//full path to file, default = false
        )
    ),
);
new Fluent_Post_Type($name, $args);


//load builder page
$args = array(
    'dev_mode' => false,
    'option_name' => 'ib_general_settings',
    'page_args' => array(
        'slug' => 'general-settings',//the unique slug of the page
        'menu_title' => __( 'General Settings', 'domain' ),//the title in the sidebar menu of the page
        'page_title' => __( 'General Settings', 'domain' ),//the page title when rendered
        'parent' => 'edit.php?post_type=view',//a string referencing the parent menu item if any, e.g: 'admin.php?page=somepage'
        'cap' => 'manage_options',//the capability of users who can access this page
        'priority' => null,//the menu item priority
        'menu_icon' => '',//the dash icon or url to image icon for use in the menu (only for top level pages)
        'page_icon' => 'icon-themes',//the dash icon if any to use on the page when rendered
        'callback' => false//the page render callback
    ),
    'restore' => true,//false to disable the restore option
    'show_updated' => true,//false to disable the last updated time
    // 'messages' => array(//here you can provide custom messages which will overwrite the default ones used on different actions
    //     'save_button' => __('Custom Save', 'domain'),//displays in the settings save box
    //     'saved' => __('Custom Saved Message', 'domain'),//displays in the settings saved notice
    //     'restore' => __('Custom Restore Message', 'domain'),//displays in the settings saved notice
    //     'save_box' => __('custom message for the save box', 'domain'),//displays a message in the save box
    // )
);

$sections['performace'] = array(
    'dash_icon' => '',
    'title' =>  __('Performance', 'fluent'),
    'priority' => 'low',
    'fields' => array(
        'enable-enqueue-performanceq' => array(
            'type' => 'switch',
            'title' => 'Enable performance',
            'options' => array(
                'off' => __('Off'),
                'on' => __('On')
            ),
            'default' => 1,
            'description' => 'Enables CSS & JS file minification and concatenation.'
        )
    ),
);

$sections['post-formats'] = array(
    'dash_icon' => '',
    'title' =>  __('Post Formats', 'fluent'),
    'priority' => 'low',
    'fields' => array(
        'enable-post-formats' => array(
            'type' => 'switch',
            'title' => 'Enable post formats?',
            'labels' => array(
                'off' => __('Off'),
                'on' => __('On')
            ),
            'default' => 0,
            'description' => 'Check this option to enable post formats if your theme does not. This will make it so you can add post format icons in the builder.'
        ),
        'select-post-formats' => array(
            'type' => 'select',
            'title' => 'Select formats?',
            'multiple' => true,
            'options' => array( 
                'gallery' => 'Gallery', 
                'quote' => 'Quote', 
                'video' => 'Video', 
                'aside' => 'Aside', 
                'image' => 'Image', 
                'link' => 'Link', 
                'audio' => 'Audio', 
                'status' => 'Status', 
                'gallery' => 'Gallery', 
                'chat' => 'Chat' 
            ),
            'default' => array('quote', 'aside', 'link', 'image'),
            'conditions' => array(
                array(
                    array(
                        'id' => 'enable-post-formats',
                        'value' => '1',
                        )
                    )
                ),
            'description' => 'Check this option to enable post formats if your theme does not. This will make it so you can add post format icons in the builder.'
        ),
    ),
);


$panel = new Fluent_Options_Page( $args, $sections );

 