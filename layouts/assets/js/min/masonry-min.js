/**
 * BoxFish.js
 *
 * jQuery plugin to create dynamic resizing columnar boxes.
 *
 * @version: 0.2
 * @authors: Jeremy Harris
 * @license: MIT
 */

(function ($) {

    $.fn.boxfish = function( options ) {

        // Define settings
        var settings = $.extend({
            columns: 3,
            breakpoints: []
        }, options );

        // Initialize values on plugin load
        var boxes               = $(this);
        var boxCount            = boxes.length;
        var container           = boxes.parent();
        var containerWidth      = getContainerWidth();
        var containerHeight     = getContainerHeight();
        var horizontalMargin    = 0;
        var horizontalPadding   = 0;
        var resizeTimer;

        // Add boxfish classes
        boxes.addClass('boxfish-box');
        container.addClass('boxfish-container');

        // Perform initial on-load scaling
        scaleBoxes();

        /** 
         * Catch window resize event
         */
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeComplete, 100);
        });

        /**
         * Window resize event callback
         *
         * Triggered by setTimeout() so that it doesn't fire dozens
         * of times in a row, rather it will fire once after the resize
         * is complete (roughly).
         */
        function resizeComplete() {
            scaleBoxes();
        }

        /**
         * Scale Boxes
         */
        function scaleBoxes() {

            // Recalculate container size
            containerWidth = getContainerWidth();
            containerHeight = getContainerHeight();

            // Rescale box dimensions
            scaleWidth();
            //scaleHeight();
        }

        /**
         * Scale box widths
         */
        function scaleWidth() {

            // Init
            var maxWidth = getMaxBoxWidth();
            var firstBox = boxes.first();

            // Determine factors affecting width
            horizontalMargin = parseInt(firstBox.css('margin-left'), 10) + parseInt(firstBox.css('margin-right'), 10);
            horizontalPadding = parseInt(firstBox.css('padding-left'), 10) + parseInt(firstBox.css('padding-right'), 10);
            horizontalBorders = parseInt(firstBox.css('border-left-width'), 10) + parseInt(firstBox.css('border-right-width'), 10);

            // Process boxes
            boxes.each(function(index, element) {
                
                //modified to allow for advanced layouts which span multiple columns
                if (!$(this).hasClass('w2') ) {
                    $(this).width(maxWidth - (horizontalMargin + horizontalPadding + horizontalBorders));
                } else {
                    var span = $(this).attr('data-span');
                    $(this).width( (firstBox.outerWidth()*span+(horizontalMargin*(span-1)))-32 );
                }

            });

        }

        /**
         * Scale box heights
         */
        function scaleHeight() {

            // Init
            //var maxHeight = getMaxBoxHeight();

            // Process boxes
            // boxes.each(function(index, element) {
            //     $(this).css('height','auto');
            //     $(this).animate({ height: maxHeight }, 1, function() {
            //         scaleRowHeight();
            //     });
            // });

        }

        /**
         * Scale row heights
         *
         * Similar to the scaleHeight method, except this looks rows generated
         * by float wrapping and resizes the height of items in that row to the 
         * height of the largest item in the row.
         */
        function scaleRowHeight() {

            // Init
            var rows = [];
            
            // Determine rows based on top positions of boxes
            for (x = 0; x < boxes.length; x++) {
                topPos = boxes[x].offsetTop;
                if ( ! rows[topPos])
                    rows[topPos] = [];
                rows[topPos].push(boxes[x]);
            }
            for (var t in rows) {
                maxRowHeight = [];
                for(x=0;x<rows[t].length;x++) {
                    var rowHeight = $(rows[t][x]).outerHeight(true);
                    if ( ! maxRowHeight[t])
                        maxRowHeight[t] = 0;
                    if (maxRowHeight[t] < rowHeight)
                        maxRowHeight[t] = rowHeight;
                }
                for(x=0;x<rows[t].length;x++) {
                    $(rows[t][x]).animate({ height: maxRowHeight[t] }, 1);
                }
            }
        }

        function resetBoxClears() {
            return $(boxes).css('clear', '');
        }

        function clearWrappedFloats() {

            var firstBox = $(boxes).first();
            var lastBox = $(firstBox);
            var currentRow = $(firstBox);
            var offset = 0;
            var count = 0;

            // Reset clear
            resetBoxClears();
            $(boxes).css('border', '1px solid #DDD');

            boxes.each(function(index, box) {

                // Get offset            
                offset = $(box).offset();
                console.log('/=====================================\\');
                console.log($(box).attr('id') + ": LEFT = " + offset.left + " ... TOP = " + offset.top);
                console.log($(currentRow).offset());
                console.log('\\=====================================/');



                // If not first box and offset is equal to first box offset
                if(count > 0 && offset.left == firstBox.offset().left && offset.top >= currentRow.offset().top)
                {
                    console.log("BOX " + $(lastBox).attr('id') + " CLEARED");
                    console.log("COUNT: " + count);
                    console.log("LEFT OFFSET: " + offset.left);

                    //$(lastBox).css('clear', 'left');
                    $(lastBox).css('border', '1px solid red');
                    currentRow = null;
                    currentRow = $(box);
                }

               // Next box
               count++;

               lastBox = $(box);

            });
            console.log("--------------");
            console.log("--------------");
            console.log("--------------");
            console.log("--------------");
        }

        /**
         * Timer driven logic to recalculate the height if
         * images have not completed loading
         */
        var triggerHeightRecalc = function() {
            setTimeout(function() {
                if (imagesLoaded())
                    scaleHeight();
                else
                    triggerHeightRecalc();
            }, 100);
        }; triggerHeightRecalc();


        /**
         * Check if images in boxes are loaded as these affect
         * the height calculations.
         */
        function imagesLoaded() {
            var images = boxes.find('img');
            images.each(function(index, image) {
                if ( ! image.complete ) {
                    return false;
                }
                if (typeof image.naturalWidth !== "undefined" && image.naturalWidth === 0) {
                    return false;
                }
            });
            return true;
        }

        /**
         * Calculate container (parent) width
         *
         * @return integer
         */
        function getContainerWidth() {
            return parseInt(container.width(), 10);
        }

        /**
         * Calculate container (parent) height
         *
         * @return integer
         */
        function getContainerHeight() {
            return parseInt(container.height(), 10);
        }

        /**
         * Calculate maximum box width
         *
         * Uses column count defined by breakpoints to determine how many columns
         * and what their widths should be.
         *
         * @return float
         */
        function getMaxBoxWidth() {
            var match = [0, 0];
            var columns = settings.columns;
            for (i = 0; i < settings.breakpoints.length; i++) {
                if (settings.breakpoints[i][0] < containerWidth && settings.breakpoints[i][0] > match[0]) {
                    match = settings.breakpoints[i];
                }
            }
            if (match[1] > 0)
                columns = match[1];

            return containerWidth / columns;
        }

        /**
         * Calculate maximum box height
         *
         * @return float
         */
        function getMaxBoxHeight() {
            var maxBoxHeight = 0;
            var boxHeight = 0;
            boxes.each(function(index, element) {
                boxHeight = $(this).outerHeight(true);
                if (boxHeight > maxBoxHeight)
                    maxBoxHeight = boxHeight;
            });
            return maxBoxHeight;
        }

        /**
         * Add breakpoint to column calculations
         *
         * Public method for end user to add a breakpoint to be used
         * in determining number of columns.
         *
         * @param integer   Pixel width to break on
         * @param integer   Columns to use after break
         */
        this.addBreakpoint = function(pixels, columns) {

            // Format parameters
            pixels = parseInt(pixels, 10);
            columns = parseInt(columns, 10);

            // Variable to determine if matching breakpoint (pixels) was found in interation
            var hasMatch = false;

            // If already set, then update columns
            for (i = 0; i < settings.breakpoints.length; i++) {
                if (settings.breakpoints[i][0] == pixels) {
                    settings.breakpoints[i][1] = columns;
                    hasMatch = true;
                    break;
                }
            }

            // If no match was found, set custom breakpoint
            if ( ! hasMatch) {
                settings.breakpoints[settings.breakpoints.length] = [pixels, columns];
            }

            // Rearrange boxes
            scaleWidth();

            return this;

        };

        // Return plugin to end user so they can call public methods
        return this;
    };

}( jQuery ));

/* **********************************************
     Begin imagesloaded.pkgd.min.js
********************************************** */

/*!
 * imagesLoaded PACKAGED v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function f(e){this.img=e}function c(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);var i=n.nodeType;if(i&&(1===i||9===i||11===i))for(var r=n.querySelectorAll("img"),o=0,s=r.length;s>o;o++){var f=r[o];this.addImage(f)}}},s.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),f.prototype=new t,f.prototype.check=function(){var e=v[this.img.src]||new c(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return c.prototype=new t,c.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},c.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},c.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},c.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},c.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},c.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

/* **********************************************
     Begin magnet.js
********************************************** */

/*
 *  Magnet - v.1.3.0
 *  A jQuery plugin to create filterable layouts.
 *  http://haundodesign.com/magnet
 *
 *  Developed by Haundo Design
 */

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {
	// undefined is used here as the undefined global variable in ECMAScript 3 is
	// mutable (ie. it can be changed by someone else). undefined isn't really being
	// passed in so we can ensure the value of it is truly undefined. In ES5, undefined
	// can no longer be modified.

	// window and document are passed through as local variable rather than global
	// as this (slightly) quickens the resolution process and can be more efficiently
	// minified (especially when both are regularly referenced in your plugin).

	// Create the defaults once
	var pluginName = 'magnet',
		defaults = {
			animationType: 'scale',
			columns: {
				items: 8
			},
			containerSelector: '.magnet',
			containerStyle: { position: 'relative', overflow: 'hidden' },
			duration: 800,
			filter: '*',
			filterSelector: '.magnet-filter',
			gutter: null,
			hiddenClass: 'magnet-hidden',
			hiddenStyle: { opacity: 0 },
			itemSelector: '.magnet-item',
			itemStyle: { position: 'absolute' },
			layoutMode: 'masonry',
			rows: {
				items: 8
			},
			visibleStyle: { 'opacity': '1' }
		};

	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		this.$element = $(element);
		// jQuery has an extend method which merges the contents of two or
		// more objects, storing the result in the first object. The first object
		// is generally empty as we don't want to alter the default options for
		// future instances of the plugin
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;

		if(this[options]){
			this[options](this.element);
		}else{
			this.isInit = true;
			this.init();
		}
	}	

	Plugin.prototype = {
		init: function () {
			// Place initialization logic here
			// You already have access to the DOM element and
			// the options via the instance, e.g. this.element
			// and this.settings
			// you can add more functions like the one below and
			// call them like so: this.yourOtherFunction(this.element, this.settings).
			var plugin = this;

			plugin.$container = plugin.$element.find(plugin.settings.containerSelector);
			plugin.$item = plugin.$element.find(plugin.settings.itemSelector);
			plugin.$filter = plugin.$element.find(plugin.settings.filterSelector);
			
			plugin.containerWidth;
			plugin.items = {
				x: [],
				y: []
			};

			plugin.onInit();
			plugin.onResize();
			plugin.onClick();
		},

		getContainerWidth: function () {
			var plugin = this,
					$container = plugin.$container;

			plugin.containerWidth = $container.width();
		},

		setGlobalStyle: function () {
			var plugin = this,
					$container = plugin.$container,
					$item = plugin.$item;

			$container.css(plugin.settings.containerStyle);
			$item.css(plugin.settings.itemStyle);
			$item.css({
				'margin-right': plugin.settings.gutter + 'px',
				'margin-bottom': plugin.settings.gutter + 'px'
			});
		},

		fetchItems: function () {
			var plugin = this,
					$item = plugin.$item,
					element = [],
					index = [],
					width = [],
					height = [],
					i = 0;

			$item.each(function(){
				var $this = $(this),
					e = $this[0],
					w = $this.outerWidth(true);
					h = $this.outerHeight(true);

				element.push(e);
				index.push(i);
				width.push(w);
				height.push(h);

				plugin.items.element = element;
				plugin.items.index = index;
				plugin.items.width = width;
				plugin.items.height = height;

				i++;
			});
		},

		filterItems: function () {
			var plugin = this,
					$item = plugin.$item,
					visibility = [],
					i = 0;

			$item.each(function(){
				var $this = $(this),
						v = true;

				if(plugin.settings.filter === '*' || $this.hasClass(plugin.settings.filter)){
				 	$this.removeClass(plugin.settings.hiddenClass);
				}else{
					v = false;
					$this.addClass(plugin.settings.hiddenClass);
				}

				visibility.push(v);

				i++;
			});
			plugin.items.visibility = visibility;
		},

		layoutItems: function () {
			var plugin = this,
					layout = plugin.settings.layoutMode,
					items = plugin.items,
					$container = plugin.$container,
					$item = plugin.$item,
					i = 0, // Items
					c = 1, // Columns
					r = 1, // Rows
					n, // Next item
					height = 0,
					min_height = 0, // Minimum height of an item
					max_height = 0,	// Maximum height of an item
					row_height = 0, // Height of the row where items are placed
					width = 0,
					max_width = 0; // Maximum width of an item

			switch(plugin.settings.layoutMode){
				case 'tiled':
					$item.each(function(){
						var $this = $(this);

						if(items.visibility[i]){
							items.x[i] = width;
							items.y[i] = height;

							for(j = 0; j < i && items.visibility[i]; j++){ // Position the first item of the row and set row height.				
								if(items.x[i] == 0 && items.x[j] == 0){
									if(items.visibility[j]){ // If compared item is visible
										height = items.y[j];
										items.y[i] = height;
									}
								}
							}

							for(j = 0; j < i; j++){
								if(items.visibility[j]){
									var condition_1 = (items.x[i] + items.width[i] > items.x[j]) && (items.x[i] + items.width[i] < items.x[j] + items.width[j]);
									var condition_2 = (items.x[j] + items.width[j] > items.x[i]) && (items.x[j] + items.width[j] < items.x[i] + items.width[i]);
									var condition_3 = (items.x[i] == items.x[j]);
									var condition_4 = (items.x[i] + items.width[i] == items.x[j] + items.width[j]);
									var condition_5 = (items.y[j] + items.height[j] > items.y[i]);
									var condition = (condition_1 || condition_2 || condition_3 || condition_4) && condition_5;

									if(condition){
										items.y[i] = items.height[j] + items.y[j]; // Update item position			
									}
								}
							}

							n = i + 1; //Next item
							while(items.visibility[n] == false){ //Get next visible item
								n++;
							}
							
							if(width + items.width[i] + items.width[n] <= plugin.containerWidth){
								width += items.width[i];
							}else{
								width = 0;
							}
						}

						i++;
					});

					break;
				case 'rows':
					$item.each(function(){
						var $this = $(this);			

						if(items.visibility[i]){
							items.x[i] = width;
							items.y[i] = height;

							if(items.height[i] > max_height){
								max_height = items.height[i];
							}

							n = i + 1; //Next item
							while(items.visibility[n] == false){ //Get next visible item
								n++;
							}

							if(width + items.width[i] + items.width[n] <= plugin.containerWidth && c < plugin.settings.rows.items){
								width += items.width[i];
							}else{
								height += max_height;
								c = 0;
								width = 0;
								max_height = 0;
							}

							c++;
						}

						i++;
					});

					break;
				case 'columns':
					$item.each(function(){
						var $this = $(this);			

						if(items.visibility[i]){
							items.x[i] = width;
							items.y[i] = height;

							if(items.width[i] > max_width){
								max_width = items.width[i];
							}

							n = i + 1; //Next item
							while(items.visibility[n] == false){ //Get next visible item
								n++;
							}

							if(r < plugin.settings.columns.items){
								height += items.height[i];
							}else{
								width += max_width;
								r = 0;
								height = 0;
								max_width = 0;
							}

							r++;
						}

						i++;
					});

					break;
				case 'vertical':
					$item.each(function(){
						var $this = $(this);

						if(items.visibility[i]){
							items.x[i] = width;
					 		items.y[i] = height;

							height += items.height[i];
						}	

						i++;
					});

					break;
				case 'horizontal':
					$item.each(function(){
						var $this = $(this);

						if(items.visibility[i]){
							items.x[i] = width;
					 		items.y[i] = height;

							width += items.width[i];
						}	

						i++;
					});

					break;
				case 'static':
					$item.each(function(){
						var $this = $(this);

						items.x[i] = width;
						items.y[i] = height;

						if(min_height == 0 || min_height > items.height[i]){
							min_height = items.height[i];
						}

						do{
							var flag = false;

							for(j = 0; j < i; j++){ // Check item by item if current item overlaps any previous item.
								var condition_1 = (items.x[i] + items.width[i] > items.x[j]) && (items.x[i] + items.width[i] < items.x[j] + items.width[j]);
								var condition_2 = (items.x[j] + items.width[j] > items.x[i]) && (items.x[j] + items.width[j] < items.x[i] + items.width[i]);
								var condition_3 = (items.x[i] == items.x[j]);
								var condition_4 = (items.x[i] + items.width[i] == items.x[j] + items.width[j]);
								var condition_5 = (items.y[j] + items.height[j] > items.y[i]);
								var condition = (condition_1 || condition_2 || condition_3 || condition_4) && condition_5;

								if(condition){
									flag = true; // Check new item position again with previous items.

									if(items.x[j] + items.width[j] + items.width[i] <= plugin.containerWidth){ // Check if item fits inside container
										width = items.x[j] + items.width[j];
									}else{
										width = 0;
										height += row_height;
										items.y[i] = height; // Update item position.
									}

									items.x[i] = width // Update item position.
								}
							}
	
						}while(flag);

						n = i + 1; //Next item
							
						if(width + items.width[i] + items.width[n] <= plugin.containerWidth){
							width += items.width[i];
						}else{
							row_height = min_height; // Set row height to min height of the item of the previous row
							width = 0;
							min_height = 0;
						}

						i++;
					});

					break;
				default:
					$item.each(function(){
						var $this = $(this);

						if(items.visibility[i]){
							items.x[i] = width;
							items.y[i] = height;

							if(min_height == 0 || min_height > items.height[i]){
								min_height = items.height[i];
							}

							do{
								var flag = false;

								for(j = 0; j < i; j++){ // Check item by item if current item overlaps any previous item.
									if(items.visibility[j]){ // Check with visible items.
										var condition_1 = (items.x[i] + items.width[i] > items.x[j]) && (items.x[i] + items.width[i] < items.x[j] + items.width[j]);
										var condition_2 = (items.x[j] + items.width[j] > items.x[i]) && (items.x[j] + items.width[j] < items.x[i] + items.width[i]);
										var condition_3 = (items.x[i] == items.x[j]);
										var condition_4 = (items.x[i] + items.width[i] == items.x[j] + items.width[j]);
										var condition_5 = (items.y[j] + items.height[j] > items.y[i]);
										var condition = (condition_1 || condition_2 || condition_3 || condition_4) && condition_5;

										if(condition){
											flag = true; // Check new item position again with previous items.

											if(items.x[j] + items.width[j] + items.width[i] <= plugin.containerWidth){ // Check if item fits inside container
												width = items.x[j] + items.width[j];
											}else{
												width = 0;
												height += row_height;
												items.y[i] = height; // Update item position.
											}
											items.x[i] = width // Update item position.
										}
									}
								}
							}while(flag);

							n = i + 1; //Next item
							while(items.visibility[n] == false){ //Get next visible item
								n++;
							}
							
							if(width + items.width[i] + items.width[n] <= plugin.containerWidth){
								width += items.width[i];
							}else{
								row_height = min_height; // Set row height to min height of the item of the previous row
								width = 0;
								min_height = 0;
							}
						}

						i++;
					});
			}
		},

		animateItems: function () {
			var plugin = this,
					$item = plugin.$item,
					animation,
					opacity,
					i = 0;

			$item.each(function(){
				var $this = $(this);

				if(plugin.items.visibility[i]){
					opacity = plugin.settings.visibleStyle.opacity;

					switch(plugin.settings.animationType){
						case 'fade':
							animation =  '';
							break;
						case 'flip':
							animation =  'rotateX(0deg)';
							break;
						case 'turn':
							animation =  'rotateY(0deg)';
							break;
						case 'rotate':
							animation =  'rotateZ(0deg)';
							break;
						default:
							animation =  'scale3d(1, 1, 1)';
					}
				}else{
					opacity = plugin.settings.hiddenStyle.opacity;

					switch(plugin.settings.animationType){
						case 'fade':
							animation =  '';
							break;
						case 'flip':
							animation =  'rotateX(180deg)';
							break;
						case 'turn':
							animation =  'rotateY(180deg)';
							break;
						case 'rotate':
							animation =  'rotateZ(180deg)';
							break;
						default:
							animation =  'scale3d(0, 0, 0)';
					}
				}

				if(plugin.isInit){
					$this.css({
			 			'transform': 'translate3d(' + plugin.items.x[i] + 'px,' + plugin.items.y[i] + 'px, 0px)' + animation,
						'opacity': opacity
					});
				}else{
					$this.css({
						'transform': 'translate3d(' + plugin.items.x[i] + 'px,' + plugin.items.y[i] + 'px, 0px)' + animation,
						'opacity': opacity,	
						'transition': plugin.settings.duration + 'ms'
					});
				}

				i++;
			});

			plugin.isInit = false;
		},

		resizeContainer: function () {
			var plugin = this,
					layout = plugin.settings.layoutMode,
					items = plugin.items,
					$container = plugin.$container,
					$item = plugin.$item,
					i = 0,
					width = 0,
					height = 0;
			
			if(layout == 'columns' || layout == 'horizontal'){
				for(i = 0; i < items.visibility.length; i++){
					if(items.visibility[i]){
						if(items.width[i] + items.x[i] > width){
							width = items.width[i] + items.x[i];
						}
					}
				}

				width += $container.outerWidth() - plugin.containerWidth; // Add container border + padding + margin

				$container.css('width', width);
			}

			for(i = 0; i < items.visibility.length; i++){
				if(items.visibility[i]){
					if(items.height[i] + items.y[i] > height){
						height = items.height[i] + items.y[i];
					}
				}
			}

			height += $container.outerHeight() - $container.height();

			$container.css({
				'height': height,
				'transition': plugin.settings.duration + 'ms'
			});
		},

		onInit: function () {
			var plugin = this;

			plugin.getContainerWidth();
			plugin.setGlobalStyle();
			plugin.fetchItems();
			plugin.filterItems();
			plugin.layoutItems();
			plugin.animateItems();
			plugin.resizeContainer();
		},
		
		onClick: function () {
			var plugin = this,
					$filter = plugin.$filter;

			$filter.on('click', '[data-filter]', function () {
				var $this = $(this);

				if($this.parent().is($filter)) { // Check if clicked element is direct child of filterSelector
					$this.addClass('active').siblings().removeClass('active'); // Add active to clicked element, remove it from non-clicked elements
				}else{
					$this.parent().addClass('active').siblings().removeClass('active'); // Add active to parent of the clicked element, remove it from parent of non-clicked elements
				}

				plugin.settings.filter = $this.attr('data-filter'); // Get filter of clicked element

				plugin.getContainerWidth();
				plugin.filterItems();
				plugin.layoutItems();
				plugin.animateItems();
				plugin.resizeContainer();
			});
		},

		onResize: function () {
			var plugin = this;

			$(window).resize(function(){
				plugin.getContainerWidth();
				plugin.fetchItems();
				plugin.layoutItems();
				plugin.animateItems();
				plugin.resizeContainer();
			});
		}
	};

	Plugin.prototype.append = function (elem) {
		var plugin = this;

		plugin.$container.append(elem);
		plugin.refresh();
	}

	Plugin.prototype.destroy = function () {
		var plugin = this,
				$item = plugin.$item,
				$container = plugin.$container;
				$filter = plugin.$filter;
		
		$item.each(function(){
			var $this = $(this);

			$this.removeClass(plugin.settings.hiddenClass);
			$this.removeAttr('style');
		});
		$container.removeAttr('style');
		$filter.off('click', '[data-filter]');
		$(window).off('resize');
	}

	Plugin.prototype.get = function (elem) {
		var plugin = this,
				$elem = plugin.$container.find(elem);

		for(var key in plugin.items.element){
			var elem = plugin.items.element[key];

			if($elem.is(elem)){
				console.log(elem);
			}
		}
	}

	Plugin.prototype.getAll = function () {
		var plugin = this;

		for(var key in plugin.items.element){
			console.log(plugin.items.element[key]);
		}
	}

	Plugin.prototype.getAllData = function () {
		var plugin = this;

		console.log(plugin.items);
	}

	Plugin.prototype.hide = function (elem) {
		var plugin = this,
				$elem = plugin.$container.find(elem),
				i = 0;

		plugin.$item.each(function(){
			var $this = $(this);

			if($elem.is($this)){
				$elem.addClass(plugin.settings.hiddenClass);
				plugin.items.visibility[i] = false;
			}

			i++;
		});

		plugin.layoutItems();
		plugin.animateItems();
		plugin.resizeContainer();
	}

	Plugin.prototype.prepend = function (elem) {
		var plugin = this;

		plugin.$container.prepend(elem);
		plugin.refresh();
	}

	Plugin.prototype.refresh = function () {
		var plugin = this;

		plugin.init();
	}

	Plugin.prototype.remove = function (elem) {
		var plugin = this;

		plugin.$container.find(elem).remove();
		plugin.refresh();
	}

	Plugin.prototype.show = function (elem) {
		var plugin = this,
				$elem = plugin.$container.find(elem),
				i = 0;

		plugin.$item.each(function(){
			var $this = $(this);

			if($elem.is($this)){
				$elem.removeClass(plugin.settings.hiddenClass);
				plugin.items.visibility[i] = true;
			}

			i++;
		});

		plugin.layoutItems();
		plugin.animateItems();
		plugin.resizeContainer();
	}

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function ( options ) {
		var args = arguments;

   	// Is the first parameter an object (options), or was omitted,
    // instantiate a new instance of the plugin.
    if (options === undefined || typeof options === 'object') {
      return this.each(function () {
        if (!$.data(this, 'plugin_' + pluginName)) {
        	$.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
        }
      });
    } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
      // Cache the method call to make it possible to return a value
      var returns;

      this.each(function () {
        var instance = $.data(this, 'plugin_' + pluginName);

        if (instance instanceof Plugin && typeof instance[options] === 'function') {
          returns = instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );
        }

        if (options === 'destroy') {
          $.data(this, 'plugin_' + pluginName, null);
        }
    	});

    	// If the earlier cached method gives a value back return the value,
    	// otherwise return this to preserve chainability.
      return returns !== undefined ? returns : this;
    }
  };

})( jQuery, window, document );

/* **********************************************
     Begin imagesloaded.pkgd.min.js
********************************************** */

/*!
 * imagesLoaded PACKAGED v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function f(e){this.img=e}function c(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);var i=n.nodeType;if(i&&(1===i||9===i||11===i))for(var r=n.querySelectorAll("img"),o=0,s=r.length;s>o;o++){var f=r[o];this.addImage(f)}}},s.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),f.prototype=new t,f.prototype.check=function(){var e=v[this.img.src]||new c(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return c.prototype=new t,c.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},c.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},c.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},c.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},c.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},c.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});

/* **********************************************
     Begin magnet-addons.js
********************************************** */

/*
 *  Add ons for Magnet
 */

function filterSelect(wrapper) {

    var filter = wrapper.find('.magnet-filter');
    var item   = wrapper.find('.magnet-item');

    filter.change(function (){

      var value = jQuery(this).val();

      item.each(function() {

          var item = jQuery(this);

          if( item.hasClass(value) || value === 'all') {

             wrapper.magnet('show', item);

          } else {

             wrapper.magnet('hide', item);

          }

      });

    });

}

function initBoxfish( wrapper, cols, bp ) {
    wrapper.boxfish({
        columns: cols,
        breakpoints: bp
    });
}

function initInfiniteScroll(view, wrapper, cols, bp, loader, effect) {

    var lastItem = view.find('.item').last();

    view.infinitescroll({ // .magnet  contains items
        loading: {
            msgText: "<em>Loading the next set of posts...</em>",
            img: loader,
        },
        navSelector: '#pagenav', 
        nextSelector: '#pagenav a',
        itemSelector: '.item'
    }, function() {

        lastItem.nextAll().addClass('hidden');

        setTimeout(function(){

            var article = view.find('article');

            initBoxfish(article, cols, bp);

            if(jQuery().magnet)
              wrapper.magnet('refresh');

            if (effect == 'fade') {

                lastItem.nextAll().hide().removeClass('hidden');

                lastItem.nextAll().fadeIn();

            };

            lastItem.nextAll().removeClass('hidden');

        }, 700);

    });

}

function initMagnet(wrapper, duration, animation) {

    imagesLoaded( wrapper, function() {
        wrapper.magnet({
            layoutMode: 'tiled',//columns, rows, tiled,
            animationType: animation,//Values: 'scale' | 'fade' | 'flip' | 'turn' | 'rotate'
            duration: duration,
        });
    });

}

function addEvent(el, type, fn) {

     if (el.addEventListener) {
         el.addEventListener(type, fn, false);
     } else if (el.attachEvent) {
         el.attachEvent('on' + type, function(){
             fn.call(el);
        });
    } else {
         throw new Error('not supported or DOM not loaded');
    }

 }

 function addResizeEvent(fn){
     var timeout;
            
		addEvent(window, 'resize', function(){
		   if (timeout){
		       clearTimeout(timeout);
		   }
		   timeout = setTimeout(fn, 100);                        
		});
 } 



/* **********************************************
     Begin masonry.js
********************************************** */

