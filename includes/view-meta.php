<?php
/**
 * Example usage of the framework classes.
 *
 * @package Fluent
 * @since 1.0.1
 * @version 1.0.2
 */

$builder_options = TitanFramework::getInstance( 'builder-options' );

//TODO: must make it return null if not on view edit screen
$postid = null;
if( is_admin() && isset( $_REQUEST['post'] ) ) {

    if ( !is_array($_REQUEST['post']) ) {
       $postid = $_REQUEST['post'];
    }

}

$args = array(
    'post_type' => 'view',
    'posts_per_page'        => '-1',
);

$the_query = new WP_Query( $args );

$meta_options = array();

$view_name = null;
$id = null;

if ( $the_query->have_posts() ) :

    while($the_query->have_posts()) : $the_query->the_post();

        $id = $the_query->post->ID;


        if ( $postid == $id ) {
            $view_name = strtolower(str_replace(' ', '-', get_the_title()));
            break;
        }


    endwhile;

endif;

wp_reset_postdata();

$layout = strtolower(str_replace(' ', '-', $builder_options->getOption( 'view-layout-' . $id . '' )));

//sections are groups of fields, displayed as metaboxes or blocks depending on the usage case
$sections = array();

$sections['to-move'] = array(
    'dash_icon' => '',
    'title' =>  __('Shortcode', 'fluent'),
    'context' => 'normal',
    'fields' => array(
        'shortcode-info' => array(
            'type' => 'raw',
            'show_title' => false,
            'content'=> '[infinity id="'.$postid.'"]',
            'description' => ''
        ),
        'query-mode' => array(
            'type' => 'switch',
            'title' => 'Custom Query:',
            'labels' => array(
                'on' => __('On&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 'fluent'),
                'off' => __('Off', 'fluent'),
            ),
            'default' => 0,
        )
    ),
);

$sections['advanced-query-params'] = array(
    'dash_icon' => '',
    'title' =>  __('Custom Query Options', 'fluent'),
    'description' => __('','fluent'),
    'context' => 'normal',
    'priority' => 'high',
    'caps' => array(),
    'tabs' => array(
        'post-type' => array(
            'title' => 'Content Type',
            'description' => '',
            'fields' => array(
                'post-type' => array(
                    'type' => 'select',
                    'title' => __('Content Types', 'fluent'),
                    'sub_title' => __('Select post types to fetch content for', 'fluent'),
                    'options' => get_post_types_list(),
                    'multiple' => true,
                    'default' => array('post')
                )
            ),
        ),
        'post-taxonomy' => array(
            'title' => __('Taxonomy', 'fluent'),
            'description' => 'Create and advanced taxonomy query to fetch <strong>any Wordpress content.</strong>',
            'fields' => array(
                'post-taxonomies-info' => array(
                    'type' => 'select',
                    'title' => __('Please Note:', 'fluent'),
                    'type' => 'info',
                    'icon' => 'info',//if set this must be a dash icon class name
                    'info_type' => 'notice',//the type of alert to display - default null
                    'show_title' => true,
                    'description' => 'Any query added below will overide the categories set in the customizer top toolbar.',
                ),
                'post-taxonomies-relation' => array(
                    'type' => 'select',
                    'title' => __('Select Relation', 'fluent'),
                    'sub_title' => __('The logical relationship between each taxonomy when more than one.', 'fluent'),
                    'options' => array(
                        'none' => 'No Taxonomy Filter (Uses category select in theme customizer toolbar)',
                        'AND' => 'Content matching ALL taxonomy queries',
                        'OR' => 'Content matching ANY taxonomy queries',
                    ),
                    'default' => 'none'
                ),
                'taxonomy-group' => array(
                    'title' => 'Taxonomy Query',
                    'sub_title' => '',
                    'description' => '',
                    'multiple' => true,
                    'type' => 'group',
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'post-taxonomies-relation',
                                'value' => 'AND',
                            )
                        ),
                        array(
                            array(
                                'id' => 'post-taxonomies-relation',
                                'value' => 'OR',
                            )
                        )
                    ),
                    'fields' => array(
                        'post-taxonomies-taxonomy' => array(
                            'title' => 'Select Taxonomy',
                            'type' => 'radio',
                            'options' => get_taxonomies_list(),
                            'inline' => false,//force block level options
                            'width' => '20'
                        ),
                        'post-taxonomies-terms' => array(
                            'type'=> 'text',
                            'title' => 'Terms',
                            'description' => 'Use only term slug or term ID\'s. Can be separated by comma eg: term1, term2 OR 1,4,9. Do not mix terms and ID\'s ',
                            'width' => '15'
                        ),
                        'post-taxonomies-exclude' => array(
                            'type'=> 'checkbox',
                            'title' => 'Exclude',
                            'width' => '15',
                            'description'=> 'Excludes terms from results',
                            'options' => array(
                                'agree' => __('Exclude terms', 'fluent')
                            ),
                        ),
                        'post-taxonomies-include-children' => array(
                            'type'=> 'checkbox',
                            'title' => 'Include',
                            'description'=> 'For hierarchical taxonomies',
                            'width' => '15',
                            'options' => array(
                                'agree' => __('Include children', 'fluent')
                            ),
                        ),
                    ),
                    'default' => array(
                        array(
                            'post-taxonomies-taxonomy' => 'tag',
                            'post-taxonomies-exclude' => true,
                            'post-taxonomies-include-children' => true,
                        ),
                    ),
                ),
            ),
        ),
        'posts' => array(
            'title' => 'Content by ID\'s',
            'description' => 'Select content that matches certain IDs',
            'fields' => array(
                'post-individual-posts' => array(
                    'type' => 'text',
                    'title' => __('Content by ID\'s', 'fluent'),
                    'sub_title' => __('Enter the ID\'s of specific posts to show', 'fluent')
                ),
                'post-exclude-individual-posts' => array(
                    'type'=> 'checkbox',
                    'title' => 'Excludes posts with above ID\'s',
                    'options' => array(
                        'yes' => __('Exclude id\'s', 'fluent')
                    ),
                ),
                'post-parent' => array(
                    'type' => 'text',
                    'title' => __('Posts of parent', 'fluent'),
                    'sub_title' => __('Enter a page/post id to return only child pages of hierarchial structures.', 'fluent')
                ),
            ),
        ),
        'post-authors' => array(
            'title' => __('Authors', 'fluent'),
            'description' => '',
            'fields' => array(
                'post-authors' => array(
                    'type' => 'select',
                    'title' => __('Authors', 'fluent'),
                    'sub_title' => __('Select authors', 'fluent'),
                    'options' => get_authors(),
                    'multiple' => true
                )
            ),
        ),
        'post-status' => array(
            'title' => __('Status', 'fluent'),
            'description' => '',
            'fields' => array(
                'post-status' => array(
                    'type' => 'select',
                    'title' => __('Status', 'fluent'),
                    'sub_title' => __('Select status', 'fluent'),
                    'options' => get_statuses(),
                    'multiple' => true,
                    'default' => array('publish')
                )
            ),
        ),
        'post-date' => array(
            'title' => __('Date', 'fluent'),
            'description' => '',
            'fields' => array(
                'date-filter' => array(
                    'type' => 'select',
                    'title' => __( 'Date Type', 'fluent' ),
                    'options' => array(
                        'no-date-filter' => __( 'No Date Filter', 'fluent' ),
                        'date-range' => __( 'Date Range', 'fluent' ),
                        'days-range' => __( 'Days Range', 'fluent' )
                    ),
                    'default' => 'no-date-filter'
                ),
                'date-from' => array(
                    'type' => 'date',
                    'title' => __( 'From', 'fluent' ),
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'date-filter',
                                'value' => 'date-range',
                            )
                        )
                    ),
                ),
                'date-to' => array(
                    'type' => 'date',
                    'title' => __( 'To', 'fluent' ),
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'date-filter',
                                'value' => 'date-range',
                            )
                        )
                    ),
                ),

                'min-days' => array(
                    'type' => 'number',
                    'title' => __( 'From Max Days', 'fluent' ),
                    'description' => 'eg: 7 days to show latest this week or 365 for a year',
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'date-filter',
                                'value' => 'days-range',
                            )
                        )
                    ),
                ),
                'max-days' => array(
                    'type' => 'number',
                    'title' => __( 'To Min Days', 'fluent' ),
                    'description' => 'eg: 0 for today or 1 for yesterday only',
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'date-filter',
                                'value' => 'days-range',
                            )
                        )
                    ),
                ),
            ),
        ),
        'search-terms' => array(
            'title' => 'Search Content',
            'description' => '',
            'fields' => array(
                'post-search-terms' => array(
                    'type' => 'text',
                    'title' => __('Search terms', 'fluent'),
                    'sub_title' => __('Display items that match these search terms', 'fluent')
                ),
                'post-search-exact' => array(
                    'title' => __('Search exact term matches', 'fluent'),
                    'type'=> 'checkbox',
                    'options' => array(
                        'yes' => __('Exact Matches', 'fluent')
                    ),
                ),
                'post-search-sentence' => array(
                    'title' => __('Search as a sentence', 'fluent'),
                    'type'=> 'checkbox',
                    'options' => array(
                        'yes' => __('Search as sentence', 'fluent')
                    ),
                ),
            ),
        ),
        'permission' => array(
            'title' => 'Permission',
            'description' => 'Display published and private posts if user has the appropriate capability',
            'fields' => array(
                'post-permission' => array(
                    'type' => 'select',
                    'title' => __('Permission', 'fluent'),
                    'options' => array(
                        'readable' => 'Posts are readable',
                        'editable' => 'Posts are editable'
                    )
                ),
            ),
        ),
        'custom-fields' => array(
            'title' => 'Custom Fields',
            'description' => '',
            'fields' => array(
                'post-custom-fields-relation' => array(
                    'type' => 'select',
                    'title' => __('Select Relation', 'fluent'),
                    'sub_title' => __('The logical relationship between each custom field when more than one.', 'fluent'),
                    'options' => array(
                        'none' => 'Do not filter by custom fields',
                        'AND' => 'Content matching ALL taxonomy queries',
                        'OR' => 'Content matching ANY taxonomy queries',
                    ),
                    'default' => 'none'
                ),
                'custom-fields-group' => array(
                    'title' => 'Custom Fields Query',
                    'sub_title' => 'Here you can build simple or complex custom field queries using all the power of the wordpress meta_query argument. Get any content using the key and value pairing.',
                    'description' => '',
                    'multiple' => true,
                    'type' => 'group',
                    'conditions' => array(
                        array(
                            array(
                                'id' => 'post-custom-fields-relation',
                                'value' => 'AND',
                            )
                        ),
                        array(
                            array(
                                'id' => 'post-custom-fields-relation',
                                'value' => 'OR',
                            )
                        )
                    ),
                    'fields' => array(
                        'post-custom-fields-compare' => array(
                            'type' => 'radio',
                            'title' => __('Compare Operator', 'fluent'),
                            'sub_title' => __('Compare key and value using', 'fluent'),
                            'options' => array(
                                'IN' => 'Equal to',
                                'NOT IN' => 'Not Equal to',
                                'LIKE' => 'Contains',
                                'NOT LIKE' => 'Does not contain',
                                'BETWEEN' => 'Between',
                                'NOT BETWEEN' => 'Not Between',
                                '<=' => 'Less than or equal to',
                                '<' => 'Less than',
                                '>=' => 'Greater than or equal to',
                                '>' => 'Greater than',

                            ),
                            'inline' => false,
                            'default' => 'NOT IN'
                        ),
                        'post-custom-fields-key' => array(
                            'type'=> 'text',
                            'title' => 'Key',
                            'description' => 'The meta key to search for eg: "_post_like_count" for posts with likes'
                        ),
                        'post-custom-fields-values' => array(
                            'type'=> 'text',
                            'title' => 'Values',
                            'description' => 'Separate values with a ","',
                        ),
                        'post-custom-fields-type' => array(
                            'type' => 'radio',
                            'title' => __('Field Type', 'fluent'),
                            'sub_title' => __('Custom field type', 'fluent'),
                            'options' => array(
                                'CHAR' => 'CHAR',
                                'NUMERIC' => 'NUMERIC',
                                'BINARY' => 'BINARY',
                                'DATE' => 'DATE',
                                'DATETIME' => 'DATETIME',
                                'DECIMAL' => 'DECIMAL',
                                'SIGNED' => 'SIGNED',
                                'TIME' => 'TIME',
                                'UNSIGNED' => 'UNSIGNED'
                            ),
                            'inline' => false,
                            'default' => 'CHAR'
                        ),
                    ),
                    'default' => array(
                        array(
                            'post-taxonomies-taxonomy' => 'tag',
                            'post-taxonomies-exclude' => true,
                            'post-taxonomies-include-children' => true,
                        ),
                    ),
                ),
            ),
        ),
        'sticky-posts' => array(
            'title' => 'Sticky Posts',
            'description' => 'Control how sticky content is added to the query',
            'fields' => array(
                'post-sticky-posts' => array(
                    'type' => 'radio',
                    'title' => __('Sticky Posts', 'fluent'),
                    'sub_title' => __('Set where to display sticky posts', 'fluent'),
                    'options' => array(
                        'hide' => __('Hide all sticky posts', 'fluent'),
                        'only' => __('Show only posts that are sticky', 'fluent'),
                        'ignore' => __('Show sticky posts in normal post order', 'fluent'),
                        'top' => __('Show sticky posts before other posts', 'fluent'),
                        'first' => __('Show only the first sticky post', 'fluent'),
                    ),
                    'default' => 'top',
                    'inline'=> false
                ),
            ),
        ),
    ),
); 


if ( $layout == 'carousel' ) {

    $sections['carousel'] = array(
        'dash_icon' => '',
        'title' =>  __('Carousel Settings', 'fluent'),
        'context' => 'normal',
        'priority' => 'low',
        'fields' => array(
            'carousel-mousedrag' => array(
                'title' => 'Mouse Drag',
                'description' => 'Enable click drag mouse to page carousel',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 1,
            ),
            'carousel-autoplay' => array(
                'title' => 'Autoplay',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 0,
            ),
            'carousel-autoplay-hover-pause' => array(
                'title' => 'Pause autoplay',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 1,
            ),
            'carousel-autoplay-timeout' => array(
                'title' => 'Autoplay Timeout',
                'type' => 'text',
                'default' => '5000'
            ),
            'carousel-auto_height' => array(
                'title' => 'Auto Height',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 1,
            ),
            'carousel-navtext-next' => array(
                'title' => 'Navigation Text (Next)',
                'type' => 'text',
                'default' => 'Next'
            ),
            'carousel-navtext-prev' => array(
                'title' => 'Navigation Text (Prev)',
                'type' => 'text',
                'default' => 'Prev'
            ),
            'carousel-group' => array(
                'title' => 'Responsive Settings',
                'sub_title' => '',
                'description' => '',
                'multiple' => true,
                'type' => 'group',
                'fields' => array(
                    'carousel-width' => array(
                        'title' => 'Breakpoint Width',
                        'type' => 'text'
                    ),
                    'carousel-items' => array(
                        'title' => 'Items',
                        'type' => 'text'
                    ),
                    // 'carousel-slideby' => array(
                    //     'title' => 'Slide By',
                    //     'type' => 'number'
                    // ),
                    'carousel-margin' => array(
                        'title' => 'Spacing',
                        'type' => 'text'
                    ),
                    'carousel-loop' => array(
                        'title' => 'Loop',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-center' => array(
                        'title' => 'Center',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-nav' => array(
                        'title' => 'Nav',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-touchdrag' => array(
                        'title' => 'Touch Drag',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-pulldrag' => array(
                        'title' => 'Pull Drag',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-autoheight' => array(
                        'title' => 'Auto Height',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),
                    'carousel-showdots' => array(
                        'title' => 'Dots Nav',
                        'type' => 'switch',
                        'labels' => array(
                            'on' => __('', 'fluent'),
                            'off' => __('', 'fluent'),
                        ),
                        'default' => 1,
                    ),

                    
                )
            ),
        ),
    );

}

if ( $layout == 'simple-masonry' ) {
    $sections['masonry'] = array(
        'dash_icon' => '',
        'title' =>  __('Masonry Settings', 'fluent'),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            'masonry-filter-header' => array(
                'type' => 'raw',
                'show_title' => false,
                'content'=> '<h3>Filter Navigation</h3>'
            ),
            'masonry-disable-filter' => array(
                'title' => 'Disable Filter?',
                'type' => 'checkbox',
                'options' => array(
                    'yes' => __('Yes', 'fluent')
                ) 
            ),
            'masonry-alltext' => array(
                'title' => 'All Button Text',
                'type' => 'text',
                'default' => 'Show All',
                'description' => 'Text to show for all items button' 
            ),
            'masonry-before-filter-text' => array(
                'title' => 'Before Filter Text',
                'type' => 'text',
                'default' => null,
                'description' => 'Text to display before the filters' 
            ),
            'masonry-filter-type' => array(
                'type' => 'select',
                'title' => __('Filter Type', 'fluent'),
                'options' => array(
                    'buttons' => 'Buttons',
                    'select'  => 'Select'
                ),
                'default' => 'buttons'
            ),
            'masonry-filter-animation' => array(
                'type' => 'select',
                'title' => __('Filter Animation', 'fluent'),
                'options' => array(
                    'scale' => 'Scale',
                    'fade'  => 'Fade',
                    'flip' => 'Flip',
                    'turn' => 'Turn',
                    'rotate' => 'Rotate',
                ),
                'default' => 'scale'
            ),
            'masonry-taxonomy' => array(
                'title' => 'Filter taxonomy',
                'type' => 'select',
                'options' => get_filter_taxonomy_list()
            ),
            'masonry-childof' => array(
                'title' => 'Child Of',
                'type' => 'select',
                'options' => get_taxonomies_by_taxonomy_list(),
                'description' => 'If you change the Filter taxonomy above please save to update this field.' 
            ),
            'masonry-exclude' => array(
                'title' => 'Exclude',
                'type' => 'select',
                'options' => get_taxonomies_by_taxonomy_list(),
                'multiple' => true,
                'description' => 'If you change the Filter taxonomy above please save to update this field.' 
            ),
            'masonry-include' => array(
                'title' => 'Include',
                'type' => 'select',
                'options' => get_taxonomies_by_taxonomy_list(),
                'multiple' => true,
                'description' => 'If you change the Filter taxonomy above please save to update this field.' 
            ),
            'masonry-group' => array(
                'title' => 'Responsive Settings',
                'sub_title' => '',
                'description' => '',
                'multiple' => true,
                'type' => 'group',
                'fields' => array(
                    'masonry-width' => array(
                        'title' => 'At Breakpoint',
                        'description' => 'When the screen is equal to or larger than',
                        'type' => 'text',
                        'width' => '10'
                    ),
                    'masonry-columns' => array(
                        'title' => 'Display how many columns?',
                        'description' => 'Modify number of columns per row to',
                        'type' => 'text',
                        'width' => '10'
                    )
                    
                )
            ),
        ),
    );
}

if ( $layout == 'grid' ) {
    $sections['grid'] = array(
        'dash_icon' => '',
        'title' =>  __('Grid Settings', 'fluent'),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            'grid-group' => array(
                'title' => 'Responsive Settings',
                'sub_title' => '',
                'description' => '',
                'multiple' => true,
                'type' => 'group',
                'fields' => array(
                    'grid-width' => array(
                        'title' => 'At Breakpoint',
                        'description' => 'When the screen is equal to or larger than',
                        'type' => 'text',
                        'width' => '10'
                    ),
                    'grid-columns' => array(
                        'title' => 'Display how many columns?',
                        'description' => 'Modify number of columns per row to',
                        'type' => 'text',
                        'width' => '10'
                    )
                )
            ),
        ),
    );

}

if ( $layout == 'slider' ) {
    $sections['slider'] = array(
        'dash_icon' => '',
        'title' =>  __('Slider Settings', 'fluent'),
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            'slider-mousedrag' => array(
                'title' => 'Mouse Drag',
                'type' => 'switch',
                'labels' => array(
                    'off' => 'Off', 
                    'on' => 'On'
                ),
                'default' => 1,
            ),
            'slider-autoplay' => array(
                'title' => 'Autoplay',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 0,
            ),
            'slider-autoplay-hover-pause' => array(
                'title' => 'Pause Autoplay on Hover',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('On', 'fluent'),
                    'off' => __('Off', 'fluent'),
                ),
                'default' => 1,
            ),
            'slider-autoplay-timeout' => array(
                'title' => 'Autoplay Timeout',
                'type' => 'text',
                'default' => '5000'
            ),
            'slider-show-nav' => array(
                'title' => 'Show Next & Prev Nav',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('', 'fluent'),
                    'off' => __('', 'fluent'),
                ),
                'default' => 1,
            ),
            'slider-show-dotsnav' => array(
                'title' => 'Show Dots Navigation',
                'type' => 'switch',
                'labels' => array(
                    'on' => __('', 'fluent'),
                    'off' => __('', 'fluent'),
                ),
                'default' => 1,
            ),
            'slider-navtext-next' => array(
                'title' => 'Navigation Text (Next)',
                'type' => 'textarea',
                'rows' => '1',
                'default' => '<span class="dashicons dashicons-arrow-right-alt2"></span>'
            ),
            'slider-navtext-prev' => array(
                'title' => 'Navigation Text (Prev)',
                'type' => 'textarea',
                'rows' => '1',
                'default' => '<span class="dashicons dashicons-arrow-left-alt2"></span>'
            ),
            'slider-nav-type' => array(
                'title' => 'Navigation Type',
                'type' => 'select',
                'options' => array(
                    'dots' => 'Dots',
                    'numbers' => 'Numbers',
                    'thumbs' => 'Thumbnails'
                ),
                'default' => 'dots',
            ),
            'slider-nav-position' => array(
                'title' => 'Navigation Y Position',
                'type' => 'select',
                'options' => array(
                    'top' => 'Top',
                    'center' => 'Center',
                    'bottom' => 'Bottom'
                ),
                'default' => 'center',
            ),
            'slider-nav-thumb-width' => array(
                'title' => 'Thumb Width',
                'type' => 'text',
                'default' => '36',
                'conditions' => array(
                    array(
                        array(
                            'id' => 'slider-nav-type',
                            'value' => 'thumbs',
                        )
                    )
                ),
            ),
            'slider-nav-thumb-height' => array(
                'title' => 'Thumb Height',
                'type' => 'text',
                'default' => '36',
                'conditions' => array(
                    array(
                        array(
                            'id' => 'slider-nav-type',
                            'value' => 'thumbs',
                        )
                    )
                ),
            ),
            'slider-animate-in' => array(
                'title' => 'Animate In',
                'type' => 'select',
                'description' => '',
                'default' => 'fadeIn',
                'options' => array(
                    'bounceIn' => 'bounceIn',
                    'bounceInDown' => 'bounceInDown',
                    'bounceInLeft' => 'bounceInLeft',
                    'bounceInRight' => 'bounceInRight',
                    'bounceInUp' => 'bounceInUp',
                    'fadeIn' => 'fadeIn',
                    'fadeInDown' => 'fadeInDown',
                    'fadeInDownBig' => 'fadeInDownBig',
                    'fadeInLeft' => 'fadeInLeft',
                    'fadeInLeftBig' => 'fadeInLeftBig',
                    'fadeInRight' => 'fadeInRight',
                    'fadeInRightBig' => 'fadeInRightBig',
                    'fadeInUp' => 'fadeInUp',
                    'fadeInUpBig' => 'fadeInUpBig',
                    'flip' => 'flip',
                    'flipInX' => 'flipInX',
                    'flipInY' => 'flipInY',
                    'lightSpeedIn' => 'lightSpeedIn',
                    'rotateIn' => 'rotateIn',
                    'rotateInDownLeft' => 'rotateInDownLeft',
                    'rotateInDownRight' => 'rotateInDownRight',
                    'rotateInUpLeft' => 'rotateInUpLeft',
                    'rotateInUpRight' => 'rotateInUpRight',
                    'hinge' => 'hinge',
                    'rollIn' => 'rollIn',
                    'zoomIn' => 'zoomIn',
                    'zoomInDown' => 'zoomInDown',
                    'zoomInLeft' => 'zoomInLeft',
                    'zoomInRight' => 'zoomInRight',
                    'zoomInUp' => 'zoomInUp',                   
                ),
            ),
            'slider-animate-out' => array(
                'title' => 'Animate Out',
                'type' => 'select',
                'description' => '',
                'default' => 'fadeOut',
                'options' => array(
                    'bounceOut' => 'bounceOut',
                    'bounceOutDown' => 'bounceOutDown',
                    'bounceOutLeft' => 'bounceOutLeft',
                    'bounceOutRight' => 'bounceOutRight',
                    'bounceOutUp' => 'bounceOutUp',
                    'fadeOut' => 'fadeOut',
                    'fadeOutDown' => 'fadeOutDown',
                    'fadeOutDownBig' => 'fadeOutDownBig',
                    'fadeOutLeft' => 'fadeOutLeft',
                    'fadeOutLeftBig' => 'fadeOutLeftBig',
                    'fadeOutRight' => 'fadeOutRight',
                    'fadeOutRightBig' => 'fadeOutRightBig',
                    'fadeOutUp' => 'fadeOutUp',
                    'fadeOutUpBig' => 'fadeOutUpBig',
                    'flip' => 'flip',
                    'flipOutX' => 'flipOutX',
                    'flipOutY' => 'flipOutY',
                    'lightSpeedOut' => 'lightSpeedOut',
                    'rotateOut' => 'rotateOut',
                    'rotateOutDownLeft' => 'rotateOutDownLeft',
                    'rotateOutDownRight' => 'rotateOutDownRight',
                    'rotateOutUpLeft' => 'rotateOutUpLeft',
                    'rotateOutUpRight' => 'rotateOutUpRight',
                    'hinge' => 'hinge',
                    'rollOut' => 'rollOut',
                    'zoomIn' => 'zoomIn',
                    'zoomInDown' => 'zoomInDown',
                    'zoomInLeft' => 'zoomInLeft',
                    'zoomInRight' => 'zoomInRight',
                    'zoomInUp' => 'zoomInUp',
                    'zoomOut' => 'zoomOut',
                    'zoomOutDown' => 'zoomOutDown',
                    'zoomOutLeft' => 'zoomOutLeft',
                    'zoomOutRight' => 'zoomOutRight',
                    'zoomOutUp' => 'zoomOutUp'
                ),
            ),
        )
    );

}

/* load meta boxes */
$args = array(
    //'dev_mode' => true,
    'option_name' => 'view_options',
    'post_types' => array(
        'view'
    )
);
//you can create and store in one line too
//Fluent_Store::set('metaboxes', new Fluent_Options_Meta( $args, $sections ));

$instance = new Fluent_Options_Meta($args, $sections);

Fluent_Store::set('view_metaboxes', $instance);

 