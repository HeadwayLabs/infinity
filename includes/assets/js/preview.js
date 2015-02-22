function showHideLoader() {
	jQuery("body #whitewrap").addClass('loading').prepend('<div class="loading-overlay"><div><img src="moowp-content/plugins/views-builder/images/loading.gif" /><p>&nbsp;&nbsp;Working..</p></div></div>');
	setTimeout(function() {
		jQuery("#whitewrap").removeClass('loading');
		jQuery(".loading-overlay").remove();
	}, 500)
}

// //each view block/widget we initiate resizable
// //we have to match the id
// (function ($) {
// 	$(document).ready(function() {

// 		$("div[id*=view]").each(function(index, el) {

// 			var thisView = $(this).attr('data-view');

// 			$(this).find('.part.image img').resizable({

// 			  stop: function( event, ui ) {

// 			  	$wpc =  $("body",window.parent.document);

// 			  	var part = $(ui.element).parents('.part').attr('data-part');
// 				var width = $(event.target).width();
// 				var height = $(event.target).height();

// 				//$wpc.find('#accordion-section-'+ thisView +'').find('input[data-customize-setting-link=builder-options_'+ part +'-option-thumbnail-width-'+ thisView +']').val(width).trigger('change');
// 				//wp.customize.value('builder-options_image-option-thumbnail-height-another-view')('222');
// 				//console.log(wp.customize.value('builder-options_image-option-thumbnail-height-another-view')())
// 				//$wpc.find('#accordion-section-'+ thisView +'').find('input[data-customize-setting-link=builder-options_'+ part +'-option-thumbnail-height-'+ thisView +']').val(height).trigger('change');

// 				//_wpCustomizeSettings.values['builder-options_image-option-thumbnail-height-another-view'] = height;

// 			  }

// 			})

// 			// $(this).find('.article').each(function(index, el) {
				
// 			// 	$(this).find('.part').each(function(index, el) {
// 			// 		console.log($(this).attr('class'))
// 			// 	});

// 			// });

// 		});
// 	});
// })(jQuery);




