(function ($) {
	$(document).ready(function() {

	//setup loading structure		
	var previewDiv = $('#customize-preview');
	previewDiv.prepend('<div class="loading-overlay" style="display: none"><div><img src="wp-content/plugins/views-builder/images/loading.gif" /><p>&nbsp;&nbsp;Working..</p></div></div>');
	var loadingOverlay = previewDiv.find('.loading-overlay');

	//not quote working how I want it yet.
	// setTimeout(function(){

	// 	$('.wp-full-overlay').change(function() {
	// 		loadingOverlay.show();
	//       previewDiv.addClass('loading');

	//       setTimeout(function(){

	// 			previewDiv.children('iframe').load(function() {
				    
	// 				loadingOverlay.hide();
	// 				$('#customize-preview iframe').contents().find('html').css('marginBottom', '55px');
	// 				previewDiv.removeClass('loading');
	// 				previewDiv.addClass('loaded');

	// 			});

	// 		}, 2000);

	// 	});

	// }, 2000);


		setInterval(function(){

			//if no iframe then show loader
			if( previewDiv.children('iframe').length !== 1 ) {

				loadingOverlay.show();
				previewDiv.addClass('loading');

			//if iframe remove loader
			} else {

				loadingOverlay.hide();
				var paginationToolbarHeight = $('.customizer-toolbar.bottom').outerHeight();
				$('#customize-preview iframe').contents().find('html').css('paddingBottom', paginationToolbarHeight+10);
				previewDiv.removeClass('loading');
				previewDiv.addClass('loaded');

			}

		}, 1000);


	var part_types = [
		"title",
		"image",
		"excerpt",
		"date",
		"time",
		"categories",
		"tags",
		"post-format",
		"author",
		"avatar",
		"comments",
		"share",
		"likes",
		"readmore",
		"wc-price",
		"wc-rating",
		"wc-sale-flash",
		"wc-add-to-cart"
	];

	var image_types = [
		"btn1",
		"btn2",
		"btn3",
		"btn4"
	];

	//image icon options modifications
	$("li[id*=accordion-section-builder]").each(function(index, el) {

		$.each(image_types, function(index, image) {

			$(el).find($("li[id*=customize-control-builder-options_" + image + "-option-]"))
				.addClass('control-section accordion-section')
				.wrapAll('<ul id="' + image + '-options" class="clearfix"></ul>');

			var imageSortable = $(el).find($("li[id*=customize-control-builder-options_image-spotlight-icon-type-icons-] li." + image + ""));

			$(el).find('#' + image + '-options')
				.remove()
				.clone(true)
				.addClass('image-options builder-options')
				.appendTo(imageSortable)
				.hide();

			imageSortable.find('.dashicons-admin-generic').on( 'click', function() {

				if (!$(this).attr('data-toggled') || $(this).attr('data-toggled') === 'off'){
					$(this).addClass('active');
					$(this).attr('data-toggled','on').nextAll('.image-options').fadeIn();
				}
				else if ($(this).attr('data-toggled') === 'on'){
					$(this).removeClass('active');
					$(this).attr('data-toggled','off').nextAll('.image-options').fadeOut();
				}

			});

		});
		
	});

	//parts options modifications
	$("li[id*=accordion-section-builder]").each(function(index, el) {


		//create top toolbar with post display settings

		//add heading to toolbar
		//var str = $(this).attr('id');
		// //replace - with spaces
		// str = str.replace(/-/g, " ");
		// //return last words to get title
		// str = str.split(/\s+/).slice(3,20).join(" ");
		// var headingText = str;
		
		var postsToolbar = $('<ul id="posts-toolbar-options-'+ $(this).attr('id') +'" class="customizer-toolbar top"></ul>');
		postsToolbar.prependTo('.wp-full-overlay').hide();

		$(this).find("li[id*=customize-control-builder-options_postopts]").each(function() {
			$(this).remove().clone(true).appendTo(postsToolbar);
		});

		//create bottom toolbar with pagination settings 
		var paginationToolbar = $('<ul id="pagination-toolbar-options-'+ $(this).attr('id') +'" class="customizer-toolbar bottom"></ul>');
		paginationToolbar.appendTo('.wp-full-overlay').hide();

		$(this).find("li[id*=customize-control-builder-options_pagination]").each(function() {
			$(this).remove().clone(true).prependTo(paginationToolbar);
		});

		$('.customizer-toolbar li').hover(function() {
			var desc = $(this).find('.description').not('.customize-control-description');
			if ( desc.text() !== '' ) {
				desc.show().addClass('vb-tooltip');
			}
		}, function() {
			var desc = $(this).find('.description').not('.customize-control-description');
			desc.hide().removeClass('vb-tooltip');
		});

		function toggleOrderingMetaVal(toggler, toolbar) {

			var input = toggler.find('select');

			if ( input.val() === 'meta_value' || input.val() === 'meta_value_num' ) {

				$(toggler).siblings("li[id*=customize-control-builder-options_postopts-order-meta-key-]").removeClass('hidden');

			} else {

				$(toggler).siblings("li[id*=customize-control-builder-options_postopts-order-meta-key-]").addClass('hidden');

			}

		}

		function toggleOnToolbarLoad(toggler, toolbar, toggleEl) {

			var input = toggler.find('.active input');

			if ( input.val() === 'carousel' || input.val() === 'grid' || input.val() === 'simple-masonry' ) {

				toolbar.find(toggleEl).removeClass('hidden');
			
			} else {

				toolbar.find(toggleEl).addClass('hidden');
			
			}

		}

		//show toolbars on click
		$(this).click(function() {

			$('.customizer-toolbar').hide();

			$('#posts-toolbar-options-'+ $(this).attr('id') +'').show();
			var postsToolbarHeight = $('#posts-toolbar-options-'+ $(this).attr('id') +'').outerHeight();
			$('#customize-preview').css('marginTop', postsToolbarHeight);

			$('#pagination-toolbar-options-'+ $(this).attr('id') +'').show();
			var paginationToolbarHeight = $('#posts-toolbar-options-'+ $(this).attr('id') +'').outerHeight();
			$('#customize-preview iframe').contents().find('html').css('paddingBottom', paginationToolbarHeight+10);

			var toolbar = $('#posts-toolbar-options-'+ $(this).attr('id') +'');

			var orderByEl = $('#posts-toolbar-options-'+ $(this).attr('id') +'').find("li[id*=customize-control-builder-options_postopts-order-by]");
			toggleOrderingMetaVal( orderByEl, toolbar );

			var viewLayout = $(this).find("li[id*=customize-control-builder-options_view-layout]");
			var columns = $("li[id*=customize-control-builder-options_postopts-columns]");
			toggleOnToolbarLoad( viewLayout, toolbar, columns );

		});

		$("li[id*=customize-control-builder-options_postopts-order-by]").change(function() {

			toggleOrderingMetaVal($(this));

		});

		$("li[id*=customize-control-builder-options_postopts-post-categories] .customize-control-title").toggle(function() {

			if ( $("li[id*=customize-control-builder-options_postopts-post-categories] .tf-multicheck-container").find('p:first').find('input').prop('checked') === true ) {
				$("li[id*=customize-control-builder-options_postopts-post-categories] .tf-multicheck-container").find('p:first').nextAll().addClass('hidden');
			}
			$(this).addClass('active').next('.tf-multicheck-container').removeClass('hidden');

		}, function() {

			$(this).removeClass('active').next('.tf-multicheck-container').addClass('hidden');

		});


		//hide other options if all categories is checked
		$("li[id*=customize-control-builder-options_postopts-post-categories] .tf-multicheck-container p").first().find('input').change(function() {
			if ( $(this).prop('checked') === true ) {
				$("li[id*=customize-control-builder-options_postopts-post-categories] .tf-multicheck-container").find('p:first').nextAll().addClass('hidden');
			} else{
				$("li[id*=customize-control-builder-options_postopts-post-categories] .tf-multicheck-container").find('p:first').nextAll().removeClass('hidden');
			}
		});



		//add headings - no way to do this with titanium currently
		$(this).find("li[id*=customize-control-builder-options_image-option-thumbnail-width]").before('<li id="customize-control-builder-options_image-option-sizing-heading" class="help"><h3>Dimensions</h3><p class="description">Set the thumbnail dimensions manually or use auto size which makes each image the width of the article (Needs container width).</p></li>');

		$.each(part_types, function(index, part_type) {

			/***** Image spotlight builders *****/
			if (part_type === 'image') {

				var tabs = $('<ul id="image-builder-tabs"><li class="icons"><span class="dashicons dashicons-welcome-view-site"></span></li><li class="content"><span class="dashicons dashicons-welcome-widgets-menus"></span></li><li class="show-hide-spotlight"><span class="dashicons dashicons-visibility"></span></li></ul>');

				//builderHeading.insertBefore($(el).find($("li[id*=customize-control-builder-options_image-icon-type-effect]")))
				var triggerTabs = tabs.insertBefore($(el).find($("li[id*=customize-control-builder-options_image-parts-content-type]")));

				var spotlightToggle = $(el).find('li[id*=customize-control-builder-options_image-show-spotlight]').addClass('hidden');

				/***** modify icon builder *****/
				$(el).find($("li[id*=customize-control-builder-options_image-icon-type-]"))
					.wrapAll('<ul id="' + part_type + '-icon-options" class="clearfix"></ul>');

				var iconSortable = $(el).find('li[id*=customize-control-builder-options_image-spotlight-icon-type]');

				$(el).find('#image-icon-options')
					.remove()
					.clone(true)
					.addClass('image-options-wrapper')
					.appendTo(iconSortable);

				iconSortable.find('> label > .customize-control-title').remove();
				iconSortable.find('p.description').remove();

				/***** modify content builder *****/
				$(el).find($("li[id*=customize-control-builder-options_image-content-type-]"))
					.wrapAll('<ul id="' + part_type + '-content-options" class="clearfix"></ul>');

				var contentSortable = $(el).find('li[id*=customize-control-builder-options_image-parts-content-type]');

				$(el).find('#image-content-options')
					.remove()
					.clone(true)
					.addClass('image-options-wrapper')
					.appendTo(contentSortable);

				contentSortable.find('> label > .customize-control-title').remove();
				contentSortable.find('p.description').remove();

				//modify both builders
				var spotlightLi = $(el).find('li[id*=customize-control-builder-options_thumb-spotlight-type]');
				var spotlightSelect = spotlightLi.find('select');

				spotlightLi.addClass('hidden');
				
				if (spotlightSelect.val() === 'icons') {

					triggerTabs.find('li.icons').addClass('active');
					iconSortable.removeClass('hidden');
					contentSortable.addClass('hidden');

				} else if (spotlightSelect.val() === 'content') {

					triggerTabs.find('li.content').addClass('active');
					iconSortable.addClass('hidden');
					contentSortable.removeClass('hidden');

				}

				if ( spotlightToggle.find('input').prop('checked') === true ) {
					
					$('.show-hide-spotlight').removeClass('active');
					$('.disabled-line').remove();
					spotlightToggle.find('input').prop('checked', true).val(1).trigger('change');
					contentSortable.add(triggerTabs.find('li.icons')).add(triggerTabs.find('li.content')).add(iconSortable).removeClass('spotlight-disabled');
					$('.show-hide-spotlight').attr('data-toggled','off');

				} else  {

					$('.show-hide-spotlight').addClass('active');
					spotlightToggle.find('input').prop('checked', false).val(0).trigger('change');
					contentSortable.add(iconSortable).append('<div class="disabled-line"></div>');
					contentSortable.add(triggerTabs.find('li.icons')).add(triggerTabs.find('li.content')).add(iconSortable).addClass('spotlight-disabled');
					$('.show-hide-spotlight').attr('data-toggled','on');
					
				}

				triggerTabs.find('li').click(function() {

					if ($(this).attr('class') === 'icons') {

						triggerTabs.find('li').removeClass('active');
						$(this).addClass('active');
						spotlightSelect.val('icons').trigger('change');
						iconSortable.removeClass('hidden');
						contentSortable.addClass('hidden');

					} else if ($(this).attr('class') === 'content') {

						triggerTabs.find('li').removeClass('active');
						$(this).addClass('active');
						spotlightSelect.val('content').trigger('change');
						iconSortable.addClass('hidden');
						contentSortable.removeClass('hidden');

					} else if ($(this).hasClass('show-hide-spotlight')) {

						if (!$(this).attr('data-toggled') || $(this).attr('data-toggled') === 'off') {

							$(this).addClass('active');
							spotlightToggle.find('input').prop('checked', false).val(0).trigger('change');
							contentSortable.add(iconSortable).append('<div class="disabled-line"></div>');
							contentSortable.add(triggerTabs.find('li.icons')).add(triggerTabs.find('li.content')).add(iconSortable).addClass('spotlight-disabled');
							$(this).attr('data-toggled','on');

						}
						else if ($(this).attr('data-toggled') === 'on') {

							$(this).removeClass('active');
							$('.disabled-line').remove();
							spotlightToggle.find('input').prop('checked', true).val(1).trigger('change');
							contentSortable.add(triggerTabs.find('li.icons')).add(triggerTabs.find('li.content')).add(iconSortable).removeClass('spotlight-disabled');
							$(this).attr('data-toggled','off');

						}

					}

				});

			}

			//setup part options
			$(el).find($("li[id*=customize-control-builder-options_" + part_type + "-option-]"))
				.addClass('control-section accordion-section')
				.wrapAll('<ul id="' + part_type + '-options" class="clearfix"></ul>');
				
			//$(this).find('.title-options').prepend('<h3 class="inner-accordion-section-title closed">Title Options</h3>');

			// Inner accordion
			// $('.inner-accordion-section-title').click(function(event) {
				
			// 	if ( ! $(this).hasClass('closed') ) {
			// 		$(this).removeClass('closed').nextAll('li').show();
			// 	};
			// 	$(this).addClass('closed').nextAll('li').hide();
			// });

			var builderSortable = $(el).find($("li[id*=customize-control-builder-options_builder_parts] li." + part_type + ""));

			$(el).find('#' + part_type + '-options')
				.remove()
				.clone(true)
				.addClass('part-options builder-options')
				.appendTo(builderSortable)
				.hide();

			builderSortable.find('.dashicons-admin-generic').on( 'click', function() {

				if (!$(this).attr('data-toggled') || $(this).attr('data-toggled') === 'off') {
					builderSortable.find('.style-options').fadeOut();
					$(this).siblings('.dashicons-admin-appearance').removeAttr('data-toggled').removeClass('active');
					$(this).addClass('active');
					$(this).attr('data-toggled','on').nextAll('.part-options').fadeIn();
				}
				else if ($(this).attr('data-toggled') === 'on') {
					$(this).removeClass('active');
					$(this).attr('data-toggled','off').nextAll('.part-options').fadeOut();
				}

			});


			//setup part styling options
			$(el).find($("li[id*=customize-control-builder-options_" + part_type + "-styles-]"))
				.addClass('control-section accordion-section')
				.wrapAll('<ul id="' + part_type + '-styles" class="clearfix"></ul>');
				
			//$(this).find('.title-options').prepend('<h3 class="inner-accordion-section-title closed">Title Options</h3>');

			// Inner accordion
			// $('.inner-accordion-section-title').click(function(event) {
				
			// 	if ( ! $(this).hasClass('closed') ) {
			// 		$(this).removeClass('closed').nextAll('li').show();
			// 	};
			// 	$(this).addClass('closed').nextAll('li').hide();
			// });

			$(el).find('#' + part_type + '-styles')
				.remove()
				.clone(true)
				.addClass('style-options builder-options')
				.appendTo(builderSortable)
				.hide();

			builderSortable.find('.dashicons-admin-appearance').on( 'click', function() {

				if (!$(this).attr('data-toggled') || $(this).attr('data-toggled') === 'off') {
					builderSortable.find('.part-options').fadeOut();
					$(this).siblings('.dashicons-admin-generic').removeAttr('data-toggled').removeClass('active');
					$(this).addClass('active');
					$(this).attr('data-toggled','on').nextAll('.style-options').fadeIn();
				}
				else if ($(this).attr('data-toggled') === 'on'){
					$(this).removeClass('active');
					$(this).attr('data-toggled','off').nextAll('.style-options').fadeOut();
				}

			});

			// var clonedPrimaryPart 	=	$(el).find('#' + part_type + '-options')
			// 	.find('li:first')
			// 	.remove()
			// 	.clone(true)
			// 	.addClass('primary-part-option')
			// 	.prependTo(builderSortable)
			// 	.addClass('hidden')
			// 	.hide()

			// 	builderSortable.find('.dashicons-edit').on( 'click', function() {

			// 		if (!$(this).attr('data-toggled') || $(this).attr('data-toggled') == 'off'){

			// 			$(this).attr('data-toggled','on').prevAll('.primary-part-option').removeClass('hidden').fadeIn()
						
			// 		}
			// 		else if ($(this).attr('data-toggled') == 'on'){

			// 			$(this).attr('data-toggled','off').prevAll('.primary-part-option').addClass('hidden').fadeOut();

			// 		}
				
			// 	});

			});

			$(this).find("li[id*=customize-control-builder-options_view-layout]").find('.tf-radio-image').each(function() {
				if ($(this).find('input').attr('checked')) {
					$(this).addClass('active');
				}
				$(this).click(function() {
					var layout = $(this).find('input').val();
					var id = $(this).parents('.accordion-section').attr('id');
					if ( layout == 'carousel' || layout == 'grid' || layout == 'simple-masonry' ) {
						$("#posts-toolbar-options-" + id + " li[id*=customize-control-builder-options_postopts-columns]").removeClass('hidden');
					} else {
						$("#posts-toolbar-options-" + id + " li[id*=customize-control-builder-options_postopts-columns]").addClass('hidden');
					}
					$(this).siblings('.tf-radio-image').removeClass('active');
					$(this).addClass('active');
				});
			});

			/* Toggle heading sections */
			$(this).find($("li[id*=toggle-heading-]")).each(function() {

				$(this).nextUntil($("li[id*=toggle-heading-]")).addClass('hidden');
				$(this).append('<span class="dashicons dashicons-plus"></span>');
				//$(this).nextUntil($("li[id*=toggle-heading-]")).addClass('hidden');
				
			});

			$(this).find($("li[id*=toggle-heading-]")).toggle(function() {

				$(this).find('.dashicons').removeClass('dashicons-plus').addClass('dashicons-minus');
				$(this).addClass('open').nextUntil($("li[id*=toggle-heading-]")).removeClass('hidden');

			}, function() {
				
				$(this).find('.dashicons').removeClass('dashicons-minus').addClass('dashicons-plus');
				$(this).removeClass('open').nextUntil($("li[id*=toggle-heading-]")).addClass('hidden');

			});

		
	});
	
	});
})(jQuery);