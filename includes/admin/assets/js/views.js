function showHideQueryBox(el, handle) {
	if ( el.find('select').val() == 0 ) {
		handle.addClass('closed');
	} else {
		handle.removeClass('closed');
	}
}

(function ($) {

	$(document).ready(function() {

		if ($('body').is('.post-new-php')) {

			$('.create-view').click(function() {
				$(this).parents('.view-create-new-notice-wrapper').fadeOut();
			});

		}

		if ($('body').is('.post-php')) {

			$('#masonry, #slider, #carousel, #grid').hide();

			var layout = $('#field-current-layouts .field-type-raw').text();

			$('#'+layout).show();

			setTimeout( function() {
				$('#message, .update-nag').fadeOut();
			}, 3500 );

			var view_name = $('#titlediv').find('input').val();

			var name = 'IntroTip-'+view_name;
			var hideIntro = localStorage.getItem(name);

			if(hideIntro == 1) {
				$('.start-message').hide();
			}

			$('.close-start-message').click(function() {
				$('.start-message').fadeOut('slow');
				localStorage.setItem(name, 1);
			});

			var wpbody = $('#wpbody-content');
			var pub = $('#publish');
			pub.clone(true).addClass('cloned-save top').prependTo(wpbody);
			pub.clone(true).addClass('cloned-save bottom').appendTo(wpbody);
			pub.hide();

			$('.cloned-save').click(function() {
				pub.trigger('click');
			});


			var shortcode = $('#to-move').find('#field-shortcode-info .field-type-raw').text();

			var html = '<div id="shortcode-preview" class="toolbar-wrap">'+ shortcode +'</div>';

			$('body.post-php #views-toolbar').prepend(html);

			//move switch
			var querySwitch = $('#to-move').find('.field-type-switch');
			var advQueryBox = $('#advanced-query-params');
			advQueryBox.find('.handlediv').hide();
			var theSwitch = querySwitch.clone(true).prependTo(advQueryBox).addClass('query-switch');

			advQueryBox.find('.hndle').off();

			showHideQueryBox(theSwitch, advQueryBox);

			theSwitch.change(function() {

				querySwitch.find('select').val($(this).val()).trigger('change');
				
				showHideQueryBox($(this), advQueryBox);

			});
			
			$('#to-move').hide();

		}
	
	});
})(jQuery);
