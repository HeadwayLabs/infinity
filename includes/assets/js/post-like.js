(function ($) {
	$(document).ready(function() {

		
		$('body').on('click','.vb-post-like',function(event){

				event.preventDefault();
				var heart = $(this);
				heart.find('.fa-heart').hide();
				var post_id = heart.data("post_id");
				heart.html("<i id='icon-like' class='fa fa-heart'></i><i id='icon-gear' class='fa fa-circle-o-notch fa-spin'></i>");
				$.ajax({
					type: "post",
					url: ajax_var.url,
					data: "action=vb-post-like&nonce="+ajax_var.nonce+"&vb_post_like=&post_id="+post_id,
					success: function(count){
						if( count.indexOf( "already" ) !== -1 )
						{
							var lecount = count.replace("already","");
							if (lecount === "0")
							{
								lecount = "Like";
							}
							heart.prop('title', 'Like');
							heart.removeClass("liked");
							heart.html("<i id='icon-unlike' class='fa fa-heart-o'></i>&nbsp;"+lecount);
						}
						else
						{
							heart.prop('title', 'Unlike');
							heart.addClass("liked");
							heart.html("<i id='icon-like' class='fa fa-heart'></i>&nbsp;"+count);
						}
					}
				});
			});


	});
})(jQuery);