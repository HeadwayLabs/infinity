function showHideQueryBox(e,d){0==e.find("select").val()?d.addClass("closed"):d.removeClass("closed")}!function($){$(document).ready(function(){var e=$("#to-move").find(".field-type-raw").text(),d='<div id="shortcode-preview">'+e+"</div>";$("#post-body").prepend(d);var o=$("#to-move").find(".field-type-switch"),i=$("#advanced-query-params");i.find(".handlediv").hide();var n=o.clone().prependTo(i).addClass("query-switch");i.find(".hndle").off(),showHideQueryBox(n,i),n.change(function(){showHideQueryBox($(this),i)}),$("#to-move").hide()})}(jQuery);