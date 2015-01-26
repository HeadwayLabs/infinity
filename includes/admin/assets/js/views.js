function showHideQueryBox(el, handle) {
	if ( el.find('select').val() == 0 ) {
		handle.addClass('closed');
	} else {
		handle.removeClass('closed');
	}
}

(function ($) {

	$(document).ready(function() {

		var shortcode = $('#to-move').find('.field-type-raw').text();

		var html = '<div id="shortcode-preview">'+ shortcode +'</div>';

		$('#post-body').prepend(html);

		//move switch
		var querySwitch = $('#to-move').find('.field-type-switch');
		var advQueryBox = $('#advanced-query-params');
		advQueryBox.find('.handlediv').hide();
		var theSwitch = querySwitch.clone().prependTo(advQueryBox).addClass('query-switch');

		advQueryBox.find('.hndle').off();

		showHideQueryBox(theSwitch, advQueryBox);

		theSwitch.change(function() {
			
			showHideQueryBox($(this), advQueryBox);

		});
		
		$('#to-move').hide();
	
	});
})(jQuery);