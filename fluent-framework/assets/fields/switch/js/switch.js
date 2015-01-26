/**
 * Switch Field
 */
(function($) {
    'use strict';
    
    $.fluent = $.fluent || {};
    
    $.fluent.switch = {
        
        $el: null,
        
        set: function( o ){
            $.extend( this, o );
            return this;
        },
        
        init: function(){
            if($.fluent.is_clone_field(this.$el)){
                return false;
            }
            $(this.$el).toggleSwitch({
                highlight: true,
                width: 25
            });
        }
    };
    
    $(document).on('fluent/create_fields', function(e, el){
		
		$(el).find('.field-type-switch .toggle-switch').each(function(){
			
			$.fluent.switch.set({ $el : $(this) }).init();
			
		});
		
	});

    $(document).on('change', '.field-type-switch select', function(){
        $(document).trigger('fluent/field/change', $(this));
    });

    $.fluent.filter.add('fluent/field/switch/value', function(id){
        return $('#field-'+id).find('select').first().val();
    });
    
    //toggle switch plugin - will replace eventually
    jQuery.fn.toggleSwitch = function (params) {
    
        var defaults = {
            highlight: true,
            width: 40,
            change: null,
            stop: null
        };
    
        var options = $.extend({}, defaults, params);
    
        return $(this).each(function (i, item) {
            generateToggle(item);
        });
    
        function generateToggle(selectObj) {
    
            // create containing element
            var $contain = $("<div />").addClass("ui-toggle-switch");
    
            // generate labels
            $(selectObj).find("option").each(function (i, item) {
                $contain.append("<label>" + $(item).text() + "</label>");
            }).end().addClass("ui-toggle-switch");
    
            // generate slider with established options
            var $slider = $("<div />").slider({
                min: 0,
                max: 100,
                animate: "fast",
                change: options.change,
                stop: function (e, ui) {
                    var roundedVal = Math.round(ui.value / 100);
                    var self = this;
                    window.setTimeout(function () {
                        toggleValue(self.parentNode, roundedVal);
                    }, 11);
    
                    if(typeof options.stop === 'function') {
                        options.stop.call(this, e, roundedVal);
                    }
                },
                range: (options.highlight && !$(selectObj).data("hideHighlight")) ? "max" : null
            }).width(options.width);
    
            // put slider in the middle
            $slider.insertAfter(
                $contain.children().eq(0)
            );
    
            // bind interaction
            $contain.on("click", "label", function () {
                if ($(this).hasClass("ui-state-active")) {
                    return;
                }
                var labelIndex = ($(this).is(":first-child")) ? 0 : 1;
                toggleValue(this.parentNode, labelIndex);
            });
    
            function toggleValue(slideContain, index) {
                var $slideContain = $(slideContain), $parent = $slideContain.parent();
                $slideContain.find("label").eq(index).addClass("ui-state-active").siblings("label").removeClass("ui-state-active");
                $parent.find("option").prop("selected", false).eq(index).prop("selected", true);
                $parent.find("select").trigger("change");
                $slideContain.find(".ui-slider").slider("value", index * 100);
            }
    
            // initialise selected option
            $contain.find("label").eq(selectObj.selectedIndex).click();
    
            // add to DOM
            $(selectObj).parent().append($contain);
    
        }
    };
    
})(jQuery);