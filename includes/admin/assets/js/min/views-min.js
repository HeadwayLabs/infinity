function showHideQueryBox(e,t){0==e.find("select").val()?t.addClass("closed"):t.removeClass("closed")}!function($){$(document).ready(function(){if($("body").is(".post-new-php")&&$(".create-view").click(function(){$(this).parents(".view-create-new-notice-wrapper").fadeOut()}),$("body").is(".post-php")){setTimeout(function(){$("#message, .update-nag").fadeOut()},3500);var e=$("#titlediv").find("input").val(),t="IntroTip-"+e,o=localStorage.getItem(t);1==o&&$(".start-message").hide(),$(".close-start-message").click(function(){$(".start-message").fadeOut("slow"),localStorage.setItem(t,1)});var a=$("#wpbody-content"),d=$("#publish");d.clone(!0).addClass("cloned-save top").prependTo(a),d.clone(!0).addClass("cloned-save bottom").appendTo(a),d.hide(),$(".cloned-save").click(function(){d.trigger("click")});var i=$("#to-move").find(".field-type-raw").text(),s='<div id="shortcode-preview" class="toolbar-wrap">'+i+"</div>";$("body.post-php #views-toolbar").prepend(s);var n=$("#to-move").find(".field-type-switch"),c=$("#advanced-query-params");c.find(".handlediv").hide();var l=n.clone(!0).prependTo(c).addClass("query-switch");c.find(".hndle").off(),showHideQueryBox(l,c),l.change(function(){n.find("select").val($(this).val()).trigger("change"),showHideQueryBox($(this),c)}),$("#to-move").hide()}})}(jQuery);