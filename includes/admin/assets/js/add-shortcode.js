jQuery(document).ready(function($) {
    tinymce.PluginManager.add( 'views', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'view_button_key', {

            text: '',
            icon: 'icon infinity-icon',
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'Select View to add:',
                    width: 600,
                    height: 200,
                    body: [{
                        type: 'listbox',
                        name: 'view',
                        label: 'View',
                        'values': infinityViews.views

                    }],
                    onsubmit: function( e ) {
                        editor.insertContent( '[infinity id=' + e.data.view + ']' );
                    }

                } );
            }

        } );

    } );

});