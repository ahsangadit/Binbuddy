function wcbd_load_product_carousel(){
    jQuery(function($){

        /**
         * Both the "first" and "products" classes cause issues, because of the default styling from
         * Divi and WooCommerce, so, removing them will save you a lot of css styling conflicts
         */
        $('.et_pb_wcbd_product_carousel .product').removeClass('first');
        // $('.et_pb_wcbd_product_carousel .products').removeClass('products').addClass('carousel-products');

        $('.et_pb_wcbd_product_carousel').each(function(index){
            var carousel = $(this),
                container = carousel.find('ul.products').removeClass('products').addClass('carousel-products carousel-products-' + index),
                settings = carousel.find('.dk-carousel-wrapper').data('settings');

            if(!container.length || typeof settings !== 'object') return;

            // slides to show
            var slides_desktop = parseInt(settings.slides_desktop) ? parseInt(settings.slides_desktop) : 4,
                slides_tablet = parseInt(settings.slides_tablet) ? parseInt(settings.slides_tablet) : slides_desktop,
                slides_phone = parseInt(settings.slides_phone) ? parseInt(settings.slides_phone) : slides_tablet;

            // slides to scroll
            var scroll_desktop = parseInt(settings.scroll_desktop) ? parseInt(settings.scroll_desktop) : 1,
                scroll_tablet = parseInt(settings.scroll_tablet) ? parseInt(settings.scroll_tablet) : scroll_desktop,
                scroll_phone = parseInt(settings.scroll_phone) ? parseInt(settings.scroll_phone) : scroll_tablet;

            // autoplay speed
            var autoplay_speed = parseInt(settings.autoplay_speed) ? parseInt(settings.autoplay_speed) : 3000;

                var args = {
                slidesToShow: slides_desktop,
                slidesToScroll: scroll_desktop,
                dots: settings.dots === 'off' ? false : true,
                arrows: settings.arrows === 'off' ? false : true,
                infinite: settings.infinite === 'off' ? false : true,
                autoplay: settings.autoplay === 'off' ? false : true,
                autoplaySpeed: autoplay_speed,
                pauseOnHover: settings.pause_on_hover === 'off' ? false : true,
                prevArrow: '<span class="carousel-arrow carousel-prev"></span>',
                nextArrow: '<span class="carousel-arrow carousel-next"></span>',
                responsive: [
                    {
                      breakpoint: 768,
                      settings: {
                        slidesToShow: slides_tablet,
                        slidesToScroll: scroll_tablet,
                      }
                    },
                    {
                      breakpoint: 480,
                      settings: {
                        slidesToShow: slides_phone,
                        slidesToScroll: scroll_phone,
                      }
                    }
                ]
            };

            // start the carousel
            container.slick(args).animate({ opacity: 1 }, 300);
        });
    });
}
wcbd_load_product_carousel();