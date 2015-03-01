<?php

function view_widget_in_use($view_id){

    $widget_settings = get_option('widget_infinity_widget');

    $view_in_use = null;

    foreach((array)$widget_settings as $instance => $options){

        $id_base = 'infinity_widget';

        $id = "{$id_base}-{$instance}";

        // is this the instance
        if(!is_active_widget(false, $id, $id_base)) continue;

        // check if the view id option matches the id we are checking for
        if($options['view_id'] == $view_id) {
           $view_in_use = true;
        } 

    }

    return $view_in_use;

}


function builder_options() {

$builder_options = TitanFramework::getInstance( 'builder-options' );


$args = array(
    'post_type' => 'view',
    'posts_per_page'        => '-1',
);

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) :

    while($the_query->have_posts()) : $the_query->the_post();

        $id = $the_query->post->ID;

        $options = vb_options( $id );

        $query_mode = $options['query-mode'];

        $title = get_the_title();
        $view_name = strtolower(str_replace(' ', '-', get_the_title()));

            /*
             * View customizer options
             */
            if (view_widget_in_use($id)) {

                $section = $builder_options->createThemeCustomizerSection( array(
                    'name' =>  get_the_title(),
                    'id'=> 'builder-' . $view_name,
                    'position' => 3,
                ) );

                $section->createOption( array(
                    'name' => get_the_title(),
                    'id' => 'id-hide-'.$id,
                    'desc' => $id,
                    'type' => 'heading-infinity',
                ) );

                if ( $query_mode == 1 ) {

                    $section->createOption( array(
                        'name' => 'Posts:',
                        'id' => 'postopts-post-count-' . $id . '',
                        'desc' => 'The total number of posts to show per view.',
                        'type' => 'text',
                        'default' => '20'
                    ) );

                }

                $section->createOption( array(
                    'name' => 'Columns:',
                    'id' => 'postopts-columns-' . $id . '',
                    'type' => 'select',
                    'desc' => 'The number of columns to display for grid, masonry and carousel modes.',
                    'options' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6',
                        '7' => '7',
                        '8' => '8',
                        '9' => '9',
                        '10' => '10',
                        '11' => '11',
                        '12' => '12',
                        '13' => '13',
                        '14' => '14',

                    ),
                    'default' => '4'
                ) );

                $section->createOption( array(
                    'name' => 'Spacing:',
                    'id' => 'postopts-post-spacing-' . $id . '',
                    'desc' => 'The amount spacing in pixels between each item.',
                    'type' => 'text',
                    'default' => '20'
                ) );

                if ( $query_mode == 1 ) {

                    $section->createOption( array(
                        'name' => 'Categories ',
                        'id' => 'postopts-post-categories-' . $id . '',
                        'type' => 'multicheck-categories-infinity',
                        'desc' => 'Select a category to display content from',
                        'default' => 'all'
                    ) );

                    $section->createOption( array(
                        'name' => 'Order:',
                        'id' => 'postopts-order-by-' . $view_name . '',
                        'desc' => 'What to order the posts by. Set to <b>Meta value</b> or Meta value (Numeric) to order by custom meta values.',
                        'type' => 'select',
                        'default' => 'date',
                        'options' => array(
                            'date' => 'Date Published',
                            'ID' => 'ID',
                            'author' => 'Author',
                            'title' => 'Title',
                            'modified' => 'Date Modified',
                            'parent' => 'Parent ID',
                            'rand' => 'Random order',
                            'likes' => 'Order by post likes',
                            'comment_count' => 'Comment Count',
                            'menu_order' => 'Page order',
                            'meta_value' => 'Meta value',
                            'meta_value_num' => 'Meta value (Numeric)'
                        )
                    ) );

                    $section->createOption( array(
                        'name' => 'Key:',
                        'id' => 'postopts-order-meta-key-' . $view_name . '',
                        'desc' => 'Set a meta key to order content by. The key will be the meta key set when adding a custom meta element.',
                        'type' => 'text',
                        'default' => ''
                    ) );

                    $section->createOption( array(
                        'name' => '',
                        'id' => 'postopts-order-' . $view_name . '',
                        'desc' => 'Set the order',
                        'type' => 'select',
                        'default' => 'DESC',
                        'options' => array(
                            'ASC' => 'ASC',
                            'DESC' => 'DESC'
                        )
                    ) );

                    $section->createOption( array(
                        'name' => 'Offset:',
                        'id' => 'postopts-post-offset-' . $view_name . '',
                        'desc' => 'Offset results by x number of posts',
                        'type' => 'text',
                        'default' => ''
                    ) );

                    $section->createOption( array(
                        'name' => '<a href="wp-admin/post.php?post='. $id .'&action=edit" target="_blank"><img src="'. views()->plugin_url .'/includes/admin/images/filter.png"/></a>',
                        'id' => 'postopts-builder-'.$id,
                        'type' => 'heading-infinity',
                        'desc' => '<strong>Filter Content</strong> <p>Takes you to the advanced filter page with more advanced options where you can select specific content to display.</p>',
                    ) );

                } else {
                    $section->createOption( array(
                        'name' => 'You are currently in default mode. <a href="wp-admin/post.php?post='. $id .'&action=edit" target="_blank">Enable custom query</a> to show specific content.',
                        'id' => 'postopts-disabled-query-'.$id,
                        'type' => 'heading-infinity',
                        'desc' => '<strong>Default Mode</strong>
                                    <p>When default mode is selected, the output will be like a normal blog template according to normal wordpress behaviour. For example, if you add this on a page, it will display that page\'s content. If you add it on the Blog Index layout, it will list the posts like a normal blog and if you add this box on a category or tag layout, it will list posts of that category or tag respectively.</p>
                                    <strong>Custom Query Mode</strong>
                                    <p>Enable a custom query to display specific content from your wordpress installation. This content will always show on all pages irrespective of the wordpress relative page. Used to display posts specific post types or taxonomies on all pages.</p>'
                    ) );
                }

                $section->createOption( array(
                    'name' => '1) Select a style',
                    'id' => 'toggle-heading-style-' . $view_name . '',
                    'type' => 'heading-infinity',
                    'default' => '20'
                ) );

                $section->createOption( array(
                    'name' => '',
                    'desc' => 'Set a preset style that is applied to each item in the view.',
                    'id' => 'style-name-' . $view_name . '',
                    'type' => 'select',
                    'options' => get_styles_list(),
                    'default' => 'boxed'
                ) );

                $section->createOption( array(
                    'name' => '2) Select a Layout',
                    'id' => 'toggle-heading-select-layout-' . $view_name . '',
                    'type' => 'heading-infinity',
                    'default' => '20'
                ) );

                $section->createOption( array(
                    'desc' => 'Choose from one of the layouts below to display your items.',
                    'id' => 'layout-heading-' . $view_name . '',
                    'type' => 'heading-infinity'
                ) );

                $section->createOption( array(
                    'name' => '',
                    'id' => 'view-layout-' . $id . '',
                    'type' => 'radio-image',
                    'options' => get_layouts_list(),
                    'default' => 'blog',
                ) );

                $section->createOption( array(
                    'name' => '3) Build Individual Layout',
                    'id' => 'toggle-heading-build-layout-' . $view_name . '',
                    'type' => 'heading-infinity',
                    'default' => '20'
                ) );

                $section->createOption( array(
                    'desc' => 'Use the builder below to show or hide & then configure the elements as needed.',
                    'id' => 'builder-heading-' . $view_name . '',
                    'type' => 'heading-infinity'
                ) );

                $section->createOption( array(
                    'name' => '',
                    'id' => 'builder_parts' . $view_name . '',
                    'description' => 'Choose from one of the layouts below to display your items.',
                    'type' => 'sortable-infinity',
                    'desc' => '',
                    'options' => get_builder_elements(),
                    'default' => array('title', 'image', 'excerpt', 'date', 'readmore'),
                ) );


                //Title Options

                $section->createOption( array(
                    'name' => 'Before title',
                    'id' => 'title-option-before-text-' . $view_name . '',
                    'type' => 'text',
                    'default' => ''
                ) );


                //Excerpt Options

                $section->createOption( array(
                    'name' => 'Content to show',
                    'id' => 'excerpt-option-content-to-show-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'excerpt' => 'Excerpt',
                        'content' => 'Full Content'
                    ),
                    'default' => 'excerpt'
                ) );

                $section->createOption( array(
                    'name' => 'Excerpt Length',
                    'id' => 'excerpt-option-length-' . $view_name . '',
                    'type' => 'text',
                    'default' => '140',
                    'livepreview' => ''
                ) );

                $section->createOption( array(
                    'name' => 'Excerpt more:',
                    'id' => 'excerpt-option-more-' . $view_name . '',
                    'type' => 'text',
                    'default' => '...',
                    'livepreview' => ''
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'excerpt-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => null,
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Date Options
                $section->createOption( array(
                    'name' => 'Before Text',
                    'id' => 'date-option-before-text-' . $view_name . '',
                    'type' => 'text',
                    'default' => ''
                ) );

                // $section->createOption( array(
                //     'name' => 'Before Text',
                //     'id' => 'date-styles-color-' . $view_name . '',
                //     'type' => 'color',
                //     'default' => '',
                //     'livepreview' => '$("#view-'.$id.' article").find(".date-part").css("color", value);',
                // ) );

                $section->createOption( array(
                    'name' => 'Date Format',
                    'type' => 'select',
                    'id' => 'date-option-meta-date-format-' . $view_name . '',
                    'options' => array(
                        'wordpress-default' => 'WordPress Default',
                        'F j, Y' => date('F j, Y'),
                        'm/d/y' => date('m/d/y'),
                        'd/m/y' => date('d/m/y'),
                        'M j' => date('M j'),
                        'M j, Y' => date('M j, Y'),
                        'F j' => date('F j'),
                        'F jS' => date('F jS'),
                        'F jS, Y' => date('F jS, Y')
                    ),
                    'default' => 'wordpress-default'
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'date-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Image Options

                $section->createOption( array(
                    'name' => 'Thumb Alignment',
                    'id' => 'image-option-thumb-align-' . $view_name . '',
                    'type' => 'radio-toggle-infinity',
                    'options' => array(
                        'left' => 'Left',
                        'none' => 'None',
                        'right' => 'Right'
                    ),
                    'default' => 'left'
                ) );

                $section->createOption( array(
                    'name' => 'Automatically Size Thumbnail',
                    'desc' => 'Auto resizes to fit the full width of the article column.',
                    'id' => 'image-option-auto-size-' . $view_name . '',
                    'type' => 'radio-toggle-infinity',
                    'options' => array(
                        'on' => 'Yes',
                        'off' => 'No'
                    ),
                    'default' => 'on'
                ) );

                $section->createOption( array(
                    'name' => 'Auto size container width',
                    'id' => 'image-option-autosize-container-width-' . $view_name . '',
                    'type' => 'text',
                    'default' => '940'

                ) );

                $section->createOption( array(
                    'name' => 'Dimensions if "Auto is Off"',
                    'id' => 'image-option-heading-dimensions-' . $view_name . '',
                    'type' => 'heading-infinity'
                ) );

                $section->createOption( array(
                    'name' => 'Thumb Width',
                    'id' => 'image-option-thumbnail-width-' . $view_name . '',
                    'type' => 'text',
                    'default' => '250'

                ) );

                $section->createOption( array(
                    'name' => 'Thumb Height',
                    'id' => 'image-option-thumbnail-height-' . $view_name . '',
                    'type' => 'text',
                    'default' => '200'

                ) );

                $section->createOption( array(
                    'name' => 'Crop thumb vertically',
                    'desc' => 'Makes the height of all images the same even if originals are not.',
                    'id' => 'image-option-crop-vertically-' . $view_name . '',
                    'type' => 'radio-toggle-infinity',
                    'options' => array(
                        'on' => 'Yes',
                        'off' => 'No'
                    ),
                    'default' => 'on'
                ) );

                $section->createOption( array(
                    'name' => 'Crop vertical ratio',
                    'id' => 'image-option-crop-vertically-height-ratio-' . $view_name . '',
                    'type' => 'text',
                    'default' => '60'
                ) );

                $section->createOption( array(
                    'name' => 'Icon Animation',
                    'id' => 'image-icon-type-effect-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'StyleH' => 'Fade In',
                        'StyleHe' => 'Slide Top',
                        'StyleLi' => 'Slide Bottom',
                        'StyleBe' => 'Slide Right',
                        'StyleB' => 'Slide Left',
                        'StyleC' => 'Zoom In',
                        'StyleN' => 'Twirl In',
                        'StyleO' => 'Rotate In',
                        'StyleF' => 'Flip In',
                        'StyleNe' => 'Zoom Twirl',
                        'StyleMg' => 'Left Right In',
                        'StyleAl' => 'Top Bottom In',
                        'StyleP' => 'Corners In 1',
                        'StyleS' => 'Corners In 2',
                        'StyleSc' => 'Left Right & Top',
                        'StyleTi' => 'Left Right & Center',
                        'StyleV' => 'Drop from top',
                        'CStyleH' => 'Cubic Top',
                        'CStyleHe' => 'Cubic Bottom',
                        'CStyleLi' => 'Cubic Right',
                        'CStyleBe' => 'Cubic Left',
                        'CStyleB' => 'Cubic Zoom In',
                        'CStyleC' => 'Cubic Center Zoom Left & Right'
                    ),
                    'default' => 'StyleH'
                ) );

                $section->createOption( array(
                    'name' => 'Icon Spotlight Effect',
                    'id' => 'image-icon-type-spotlight-effect-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'ImageFade' => 'Fade In',
                        'ImageFadeExpand' => 'Expand Outwards Top & Bottom',
                        'ImageOverlayLi' => 'Expand Inwards Top & Bottom',
                        'ImageOverlayB' => 'Expand Outwards Left & Right',
                        'ImageOverlayC' => 'Expand Inwards Left & Right',
                        'ImageOverlayO' => 'In from top left',
                        'ImageOverlayF' => 'In from top right',
                        'ImageOverlayNe' => 'In from bottom left',
                        'ImageOverlayNa' => 'In from bottom right',
                        'ImageOverlayMg' => 'Expand In from Top',
                        'ImageOverlayAl' => 'Expand In from Bottom',
                        'ImageOverlaySi' => 'Expand In from Right',
                        'ImageOverlayP' => 'Expand In from Left',
                        'ImageOverlayS' => 'Twirl In',
                        'ImageOverlayCl' => 'Twirl In Bottom Right',
                        'ImageOverlayK' => 'Flip In Bottom Left',
                        'ImageOverlayCa' => 'Flip In Top',
                        'ImageOverlaySc' => 'Flip In Bottom',
                        'ImageOverlayTi' => 'Flip In Top Right',
                    ),
                    'default' => 'ImageFade'
                ) );
                $section->createOption( array(
                    'name' => 'Icon Style',
                    'id' => 'image-icon-type-style-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'Headway' => 'Style in Headway',
                        'WhiteRounded' => 'White Rounded',
                        'BlackRounded' => 'Black Rounded',
                        'WhiteHollowRounded' => 'White Hollow Rounded',
                        'BlackHollowRounded' => 'Black Hollow Rounded',
                        'WhiteSquare' => 'White Square',
                        'BlackSquare' => 'Black Square',
                        'WhiteHollowSquare' => 'White Hollow Square',
                        'BlackHollowSquare' => 'Black Hollow Square',

                    ),
                    'default' => 'WhiteRounded',
                    'livepreview' => '
                        var element = $("article .cover-button");
                        var elClass = element.attr("class");
                        var lastword = elClass.split(" ").pop();
                        element.removeClass(lastword).addClass(value);
                        showHideLoader();
                    '
                ) );

                $section->createOption( array(
                    'name' => 'Spotlight Content Effect',
                    'id' => 'image-content-type-hover-effect-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'H' => 'Fade In',
                        'He' => 'Scale In',
                        'Li' => 'Scale Out & In',
                        'Be' => 'Zoom In',
                        'B' => 'Slide from Top',
                        'C' => 'Slide from Bottom',
                        'N' => 'Slide from Right',
                        'O' => 'Slide from Left',
                        'F' => 'Flip half Left',
                        'Ne' => 'Flip half Top',
                        'Na' => 'Flip half Right',
                        'Mg' => 'Flip half Bottom',
                        'Al' => 'Spin Zoom Horizontal',
                        'Si' => 'Spin Zoom Vertical',
                        'P' => 'Spin Horizontal',
                        'S' => 'Spin Vertical',
                        'Cl' => 'Push Up',
                        'Ar' => 'Push Top',
                        'K' => 'Push Right',
                        'Ca' => 'Push Left',
                        'Sc' => 'Slide Down',
                        'Ti' => 'Slide Left',
                        'V' => 'Slide Right',
                        'Cr' => 'Slide Bottom',
                        'Mn' => 'Rotate in Bottom Left',
                        'Fe' => 'Rotate in Top Right',
                        'Cu' => 'Flip Horizontal',
                        'Zn' => 'Flip Vertical'
                    ),
                    'default' => 'H'
                ) );

                $section->createOption( array(
                    'name' => 'Lightbox Image Width',
                    'id' => 'image-icon-type-lightbox-width-' . $view_name . '',
                    'type' => 'text',
                    'default' => '1024'

                ) );

                $section->createOption( array(
                    'name' => 'Lightbox Image Height',
                    'id' => 'image-icon-type-lightbox-height-' . $view_name . '',
                    'type' => 'text',
                    'default' => '768'

                ) );

                $section->createOption( array(
                    'name' => 'Show Hover Spotlight',
                    'id' => 'image-show-spotlight-hide-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Spotlight Type',
                    'id' => 'thumb-spotlight-type-hide-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'icons' => 'Icons',
                        'content' => 'Content'
                    ),
                    'default' => 'icons'
                ) );

                $section->createOption( array(
                    'name' => '4) Build Image Spotlight',
                    'id' => 'toggle-heading-build-spotlight-' . $view_name . '',
                    'type' => 'heading-infinity'
                ) );

                $section->createOption( array(
                    'desc' => 'Use the builder below to setup the content and effect that happens when a user hovers over a thumbnail.',
                    'id' => 'spotlight-builder-heading-' . $view_name . '',
                    'type' => 'heading-infinity'
                ) );

                $section->createOption( array(
                    'name' => 'Spotlight Content Builder',
                    'id' => 'image-parts-content-type-' . $view_name . '',
                    'desc' => 'Add <b>Icons</b> or <b>Content Parts</b> to the Image spotlight using the builder below. Then set the animation effect.',
                    'type' => 'sortable-infinity',
                    'options' => get_cover_builder_elements(),
                    'default' => array('title', 'categories')

                ) );

                $section->createOption( array(
                    'name' => 'Spotlight Icon Builder',
                    'id' => 'image-spotlight-icon-type-icons-' . $view_name . '',
                    'type' => 'sortable-infinity',
                    'options' => array(
                        'btn1' => 'Icon 1',
                        'btn2' => 'Icon 2',
                        'btn3' => 'Icon 3',
                        'btn4' => 'Icon 4'
                    ),
                    'default' => array('btn1', 'btn2')

                ) );

                $section->createOption( array(
                    'name' => 'Icon 1',
                    'id' => 'btn1-option-icon-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'search',
                    'options' => font_awesome_icons(),                    

                ) );

                $section->createOption( array(
                    'name' => 'Link 1',
                    'id' => 'btn1-option-link-' . $view_name . '',
                    'desc' => 'Add an http:// link or use <strong>lightbox</strong> or content (links to the full content item)',
                    'type' => 'text',
                    'default' => 'lightbox'

                ) );

                $section->createOption( array(
                    'name' => 'Icon 2',
                    'id' => 'btn2-option-icon-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'link',
                    'options' => font_awesome_icons(),                    
                ) );

                $section->createOption( array(
                    'name' => 'Link 2',
                    'id' => 'btn2-option-link-' . $view_name . '',
                    'type' => 'text',
                    'default' => 'content'

                ) );

                $section->createOption( array(
                    'name' => 'Icon 3',
                    'id' => 'btn3-option-icon-' . $view_name . '',
                    'type' => 'select',
                    'options' => font_awesome_icons(),                    
                    'default' => null

                ) );

                $section->createOption( array(
                    'name' => 'Link 3',
                    'id' => 'btn3-option-link-' . $view_name . '',
                    'type' => 'text',
                    'default' => null

                ) );

                $section->createOption( array(
                    'name' => 'Icon 4',
                    'id' => 'btn4-option-icon-' . $view_name . '',
                    'type' => 'select',
                    'options' => font_awesome_icons(),                    
                    'default' => 'check'

                ) );

                $section->createOption( array(
                    'name' => 'Link 4',
                    'id' => 'btn4-option-link-' . $view_name . '',
                    'type' => 'text',
                    'default' => 'check'

                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'image-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'B',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Read more options

                $section->createOption( array(
                    'name' => 'Read more text',
                    'id' => 'readmore-option-more-text-' . $view_name . '' ,
                    'type' => 'text',
                    'default' => 'Read More'
                ) );

                $section->createOption( array(
                    'name' => 'Show Always',
                    'id' => 'readmore-option-show-always-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => false
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'readmore-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Time since options

                $section->createOption( array(
                    'name' => 'Before time text',
                    'id' => 'time-option-time-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => null
                ) );

                $section->createOption( array(
                    'name' => 'Time format',
                    'id' => 'time-option-time-format-' . $view_name . '',
                    'type' => 'Select',
                    'options' => array(
                        'wordpress-default' => 'WordPress Default',
                        'g:i A' => date('g:i A'),
                        'g:i A T' => date('g:i A T'),
                        'g:i:s A' => date('g:i:s A'),
                        'G:i' => date('G:i'),
                        'G:i T' => date('G:i T')
                    ),
                    'default' => 'wordpress-default'
                ) );

                $section->createOption( array(
                    'name' => 'Show as Time Since',
                    'id' => 'time-option-time-since-' . $view_name . '',
                    'type' => 'radio-toggle-infinity',
                    'options' => array(
                        'on' => 'Yes',
                        'off' => 'No'
                    ),
                    'default' => 'on'
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'time-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Categories options
                $section->createOption( array(
                    'name' => 'Before categories text',
                    'id' => 'categories-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => null
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'categories-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Tags options
                $section->createOption( array(
                    'name' => 'Before tags text',
                    'id' => 'tags-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => null
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'tags-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Post Format options
                $section->createOption( array(
                    'name' => 'Before post format text',
                    'id' => 'post-format-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => null
                ) );

                $section->createOption( array(
                    'name' => 'Display format as',
                    'id' => 'post-format-option-type-' . $view_name . '',
                    'type' => 'select',
                    'options' => array(
                        'text' => 'Text',
                        'icon' => 'Icon'
                    ),
                    'default' => 'icon'
                ) );

                $section->createOption( array(
                    'name' => 'Icon Size',
                    'id' => 'post-format-option-icon-size-' . $view_name . '',
                    'type' => 'text',
                    'default' => '32'
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'post-format-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Author options
                $section->createOption( array(
                    'name' => 'Link to author page?',
                    'id' => 'author-option-linked-' .  $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Before author text',
                    'id' => 'author-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => ''
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'author-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Avatar options
                $section->createOption( array(
                    'name' => 'Link to author page?',
                    'id' => 'avatar-option-linked-' .  $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Before avatar text',
                    'id' => 'avatar-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => ''
                ) );

                $section->createOption( array(
                    'name' => 'Avatar size',
                    'id' => 'avatar-option-size-' . $view_name . '',
                    'type' => 'text',
                    'default' => 32
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'avatar-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //Comments Options
                $section->createOption( array(
                    'name' => 'If Several Comments',
                    'id' => 'comments-option-comments-format-' . $view_name . '',
                    'type' => 'text',
                    'default' => '%num% Comments'
                ) );

                $section->createOption( array(
                    'name' => 'If 1 Comment',
                    'id' => 'comments-option-comments-format-1-' . $view_name . '',
                    'type' => 'text',
                    'default' => '%num% Comment'
                ) );

                $section->createOption( array(
                    'name' => 'If no Comments',
                    'id' => 'comments-option-comments-format-0-' . $view_name . '',
                    'type' => 'text',
                    'default' => '%num% Comments'
                ) );

                $section->createOption( array(
                    'name' => 'Before comments text',
                    'id' => 'comments-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => ''
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'comments-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'inline-block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                //share options
                $section->createOption( array(
                    'name' => 'Before share text',
                    'id' => 'share-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => 'Share this: '
                ) );
                $section->createOption( array(
                    'name' => 'Share Icon Width',
                    'id' => 'share-option-icon-w-' . $view_name . '',
                    'type' => 'text',
                    'default' => '32'
                ) );

                $section->createOption( array(
                    'name' => 'Share Icon Height',
                    'id' => 'share-option-icon-h-' . $view_name . '',
                    'type' => 'text',
                    'default' => '32'
                ) );

                $section->createOption( array(
                    'name' => 'Twitter Share Icon',
                    'id' => 'share-option-twitter-share-icon-' . $view_name . '',
                    'type' => 'upload',
                    'default' => views()->plugin_url.'parts/content/images/social/share-twitter.png'
                ) );

                $section->createOption( array(
                    'name' => 'Twitter New Window',
                    'id' => 'share-option-twitter-target-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Facebook Share Icon',
                    'id' => 'share-option-facebook-share-icon-' . $view_name . '',
                    'type' => 'upload',
                    'default' => views()->plugin_url.'parts/content/images/social/share-facebook.png'
                ) );

                $section->createOption( array(
                    'name' => 'Facebook New Window',
                    'id' => 'share-option-facebook-target-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Google Plus Share Icon',
                    'id' => 'share-option-googleplus-share-icon-' . $view_name . '',
                    'type' => 'upload',
                    'default' => views()->plugin_url.'parts/content/images/social/share-googleplus.png'
                ) );

                $section->createOption( array(
                    'name' => 'Google Plus New Window',
                    'id' => 'share-option-googleplus-target-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'LinkedIn Share Icon',
                    'id' => 'share-option-linkedin-share-icon-' . $view_name . '',
                    'type' => 'upload',
                    'default' => views()->plugin_url.'parts/content/images/social/share-linkedin.png'
                ) );

                $section->createOption( array(
                    'name' => 'LinkedIn New Window',
                    'id' => 'share-option-linkedin-target-' . $view_name . '',
                    'type' => 'checkbox',
                    'default' => true
                ) );

                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'share-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );
                
                //likes options
                $section->createOption( array(
                    'name' => 'Before likes text',
                    'id' => 'likes-option-before-' . $view_name . '',
                    'type' => 'text',
                    'default' => 'Likes: '
                ) );
                $section->createOption( array(
                    'name' => 'Display As',
                    'id' => 'likes-styles-display-as-' . $view_name . '',
                    'type' => 'select',
                    'default' => 'block',
                    'options' => array(
                        '' => 'None',
                        'block' => 'Block',
                        'inline' => 'Inline',
                        'inline-block' => 'Inline Block'
                    )
                ) );

                if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

                    //Woocommerce Price options
                    $section->createOption( array(
                        'name' => 'Before Price text',
                        'id' => 'wc-price-option-before-' . $view_name . '',
                        'type' => 'text',
                        'default' => null
                    ) );

                    $section->createOption( array(
                        'name' => 'Display As',
                        'id' => 'wc-price-styles-display-as-' . $view_name . '',
                        'type' => 'select',
                        'default' => 'inline-block',
                        'options' => array(
                            '' => 'None',
                            'block' => 'Block',
                            'inline' => 'Inline',
                            'inline-block' => 'Inline Block'
                        )
                    ) );

                    //Woocommerce Rating options
                    $section->createOption( array(
                        'name' => 'Show as stars',
                        'id' => 'wc-rating-option-show-as-stars-' . $view_name . '',
                        'type' => 'checkbox',
                        'default' => true
                    ) );
                    $section->createOption( array(
                        'name' => 'Show review count',
                        'id' => 'wc-rating-option-show-review-count-' . $view_name . '',
                        'type' => 'checkbox',
                        'default' => false
                    ) );
                    $section->createOption( array(
                        'name' => 'Before Rating text',
                        'id' => 'wc-rating-option-before-' . $view_name . '',
                        'type' => 'text',
                        'default' => null
                    ) );
                    $section->createOption( array(
                        'name' => 'Display As',
                        'id' => 'wc-rating-styles-display-as-' . $view_name . '',
                        'type' => 'select',
                        'default' => 'inline-block',
                        'options' => array(
                            '' => 'None',
                            'block' => 'Block',
                            'inline' => 'Inline',
                            'inline-block' => 'Inline Block'
                        )
                    ) );

                    //Woocommerce Sale Flash
                    $section->createOption( array(
                        'name' => 'Before Sale Flash text',
                        'id' => 'wc-sale-flash-option-before-' . $view_name . '',
                        'type' => 'text',
                        'default' => null
                    ) );

                    $section->createOption( array(
                        'name' => 'After Sale Flash text',
                        'id' => 'wc-sale-flash-option-after-' . $view_name . '',
                        'type' => 'text',
                        'default' => null
                    ) );

                    $section->createOption( array(
                        'name' => 'On Sale Flash text',
                        'id' => 'wc-sale-flash-option-text-' . $view_name . '',
                        'type' => 'text',
                        'default' => 'On Sale!'
                    ) );

                    $section->createOption( array(
                        'name' => 'Show sale as percent',
                        'desc' => 'Replaces on sale flash text',
                        'id' => 'wc-sale-flash-option-as-percent-off-' . $view_name . '',
                        'type' => 'checkbox',
                        'default' => false
                    ) );

                    $section->createOption( array(
                        'name' => 'Display As',
                        'id' => 'wc-sale-flash-styles-display-as-' . $view_name . '',
                        'type' => 'select',
                        'default' => 'inline-block',
                        'options' => array(
                            '' => 'None',
                            'block' => 'Block',
                            'inline' => 'Inline',
                            'inline-block' => 'Inline Block'
                        )
                    ) );

                    //Woocommerce Add Cart options
                    $section->createOption( array(
                        'name' => 'Add to cart text',
                        'id' => 'wc-add-to-cart-option-add-text-' . $view_name . '',
                        'type' => 'text',
                        'default' => 'Add to cart'
                    ) );

                    $section->createOption( array(
                        'name' => 'Display As',
                        'id' => 'wc-add-to-cart-styles-display-as-' . $view_name . '',
                        'type' => 'select',
                        'default' => 'inline-block',
                        'options' => array(
                            '' => 'None',
                            'block' => 'Block',
                            'inline' => 'Inline',
                            'inline-block' => 'Inline Block'
                        )
                    ) );

                } //end woocommerce check

                //Pagination and infinite scroll
                $section->createOption( array(
                    'name' => 'Next',
                    'id' => 'pagination-next-page-text-' . $view_name . '',
                    'desc' => 'Text for button that goes to next page.',
                    'type' => 'text',
                    'default' => '&rsaquo;'
                ) );
                $section->createOption( array(
                    'name' => 'Previous',
                    'id' => 'pagination-prev-page-text-' . $view_name . '',
                    'type' => 'text',
                    'desc' => 'Text for button that goes to the previous page.',
                    'default' => '&lsaquo;'
                ) );
                $section->createOption( array(
                    'name' => 'Last:',
                    'id' => 'pagination-last-page-text-' . $view_name . '',
                    'desc' => 'Text for button that goes to last page.',
                    'type' => 'text',
                    'default' => '&raquo;'
                ) );
                $section->createOption( array(
                    'name' => 'First:',
                    'id' => 'pagination-first-page-text-' . $view_name . '',
                    'type' => 'text',
                    'desc' => 'Text for button that goes to first page.',
                    'default' => '&laquo;'
                ) );
                $section->createOption( array(
                    'name' => 'Pages:',
                    'id' => 'pagination-pages-text-' . $view_name . '',
                    'type' => 'text',
                    'desc' => 'Text that displays at the beginning of the pagination.',
                    'default' => 'Pages'
                ) );
                $section->createOption( array(
                    'name' => 'Align:',
                    'id' => 'pagination-alignment-' . $view_name . '',
                    'type' => 'select',
                    'desc' => 'Sets where to align the pagination if not using infinite.',
                    'options' => array(
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right'
                    ),
                    'default' => 'center'
                ) );
                $section->createOption( array(
                    'name' => 'Range:',
                    'id' => 'pagination-range-' . $view_name . '',
                    'desc' => 'The number of page links to show before and after the current page.',
                    'type' => 'text',
                    'default' => '3'
                ) );
                $section->createOption( array(
                    'name' => 'Effect:',
                    'id' => 'pagination-infinite-effect-' . $view_name . '',
                    'type' => 'select',
                    'desc' => 'Only applies to masonry layouts. Sets the effect for new articles added to the page.',
                    'options' => array(
                        'fade' => 'Fade In',
                        'flyin' => 'Fly In'
                    ),
                    'default' => 'fade'
                ) );
                $section->createOption( array(
                    'name' => 'Infinite:',
                    'id' => 'pagination-infinite-' . $view_name . '',
                    'desc' => 'Infinite pagination will automatically fetch and load posts from other pages without the user needing to navigate to another page.',
                    'type' => 'checkbox',
                    'default' => false
                ) );
                $section->createOption( array(
                    'name' => 'Pagination:',
                    'id' => 'pagination-show-' . $view_name . '',
                    'desc' => 'Set whether to show post pagination or not.',
                    'type' => 'checkbox',
                    'default' => true
                ) );

            }

    endwhile; 

    endif;

wp_reset_postdata();
}

add_action('wp_loaded', 'builder_options');

