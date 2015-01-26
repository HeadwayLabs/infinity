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

