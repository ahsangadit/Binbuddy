<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class EventsManager {
	
	public $doingAMP = false;
	
	private $staticEvents = array();
    private $dynamicEvents = array();
    private $triggerEvents = array();
    private $triggerEventTypes = array();

	private $facebookServerEvents = array();
    private $standardParams = array();
    private $wooCustomerTotals = array();
    private $eddCustomerTotals = array();

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

		add_action( 'wp_head', array( $this, 'setupEventsParams' ), 3 );
		add_action( 'wp_head', array( $this, 'outputData' ), 4 );
		add_action( 'wp_footer', array( $this, 'outputNoScriptData' ), 10 );

	}

	public function enqueueScripts() {
		
		wp_register_script( 'vimeo', PYS_URL . '/dist/scripts/vimeo.min.js' );
		wp_register_script( 'jquery-bind-first', PYS_URL . '/dist/scripts/jquery.bind-first-0.2.3.min.js', array( 'jquery' ) );
		wp_register_script( 'js-cookie', PYS_URL . '/dist/scripts/js.cookie-2.1.3.min.js', array(), '2.1.3' );
		
		wp_enqueue_script( 'js-cookie' );
		wp_enqueue_script( 'jquery-bind-first' );

		if ( isEventEnabled( 'watchvideo_event_enabled' ) ) {
			wp_enqueue_script( 'vimeo' );
		}
		
		wp_enqueue_script( 'pys', PYS_URL . '/dist/scripts/public.js',
			array( 'jquery', 'js-cookie', 'jquery-bind-first' ), PYS_VERSION );

	}

	public function outputData() {

		$data = array(
			'staticEvents'          => $this->staticEvents,
            'dynamicEvents'          => $this->dynamicEvents,
			'triggerEvents'         => $this->triggerEvents,
			'triggerEventTypes'     => $this->triggerEventTypes,
		);

		// collect options for configured pixel
		foreach ( PYS()->getRegisteredPixels() as $pixel ) {
			/** @var Pixel|Settings $pixel */
			
			if ( $pixel->configured() ) {
				$data[ $pixel->getSlug() ] = $pixel->getPixelOptions();
			}
			
		}
		
		$options = array(
			'debug'                => PYS()->getOption( 'debug_enabled' ),
			'siteUrl'              => site_url(),
			'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
			'trackUTMs'            => PYS()->getOption( 'track_utms' ),
			'trackTrafficSource'   => PYS()->getOption( 'track_traffic_source' ),
            'user_id'              => GA()->enabled() && GA()->getOption("track_user_id") ? get_current_user_id() : 0,
		);
		
		$options['gdpr'] = array(
			'ajax_enabled'              => PYS()->getOption( 'gdpr_ajax_enabled' ),
			'all_disabled_by_api'       => apply_filters( 'pys_disable_by_gdpr', false ),
			'facebook_disabled_by_api'  => apply_filters( 'pys_disable_facebook_by_gdpr', false ),
			'analytics_disabled_by_api' => apply_filters( 'pys_disable_analytics_by_gdpr', false ),
			'google_ads_disabled_by_api' => apply_filters( 'pys_disable_google_ads_by_gdpr', false ),
			'pinterest_disabled_by_api' => apply_filters( 'pys_disable_pinterest_by_gdpr', false ),
			'bing_disabled_by_api' => apply_filters( 'pys_disable_bing_by_gdpr', false ),
			
			'facebook_prior_consent_enabled'   => PYS()->getOption( 'gdpr_facebook_prior_consent_enabled' ),
			'analytics_prior_consent_enabled'  => PYS()->getOption( 'gdpr_analytics_prior_consent_enabled' ),
			'google_ads_prior_consent_enabled' => PYS()->getOption( 'gdpr_google_ads_prior_consent_enabled' ),
			'pinterest_prior_consent_enabled'  => PYS()->getOption( 'gdpr_pinterest_prior_consent_enabled' ),
			'bing_prior_consent_enabled' => PYS()->getOption( 'gdpr_bing_prior_consent_enabled' ),
			
			'cookiebot_integration_enabled'         => isCookiebotPluginActivated() && PYS()->getOption( 'gdpr_cookiebot_integration_enabled' ),
			'cookiebot_facebook_consent_category'   => PYS()->getOption( 'gdpr_cookiebot_facebook_consent_category' ),
			'cookiebot_analytics_consent_category'  => PYS()->getOption( 'gdpr_cookiebot_analytics_consent_category' ),
			'cookiebot_google_ads_consent_category' => PYS()->getOption( 'gdpr_cookiebot_google_ads_consent_category' ),
			'cookiebot_pinterest_consent_category'  => PYS()->getOption( 'gdpr_cookiebot_pinterest_consent_category' ),
			'cookiebot_bing_consent_category' => PYS()->getOption( 'gdpr_cookiebot_bing_consent_category' ),
			
			'ginger_integration_enabled' => isGingerPluginActivated() && PYS()->getOption( 'gdpr_ginger_integration_enabled' ),
			'cookie_notice_integration_enabled' => isCookieNoticePluginActivated() && PYS()->getOption( 'gdpr_cookie_notice_integration_enabled' ),
			'cookie_law_info_integration_enabled' => isCookieLawInfoPluginActivated() && PYS()->getOption( 'gdpr_cookie_law_info_integration_enabled' ),
		);

		$options['edd'] = EventsEdd()->getOptions();
        $options['woo'] = EventsWoo()->getOptions();


		$data = array_merge( $data, $options );

		wp_localize_script( 'pys', 'pysOptions', $data );

	}
	
	public function outputNoScriptData() {
		
		foreach ( PYS()->getRegisteredPixels() as $pixel ) {
			/** @var Pixel|Settings $pixel */
			$pixel->outputNoScriptEvents();
		}
		
	}

    public function getWooCustomerTotals() {

        // setup and cache params
        if ( empty( $this->wooCustomerTotals ) ) {
            $this->wooCustomerTotals = getWooCustomerTotals();
        }

        return $this->wooCustomerTotals;

    }

    public function getEddCustomerTotals() {

        // setup and cache params
        if ( empty( $this->eddCustomerTotals ) ) {
            $this->eddCustomerTotals = getEddCustomerTotals();
        }

        return $this->eddCustomerTotals;

    }

	public function setupEventsParams() {
        $this->standardParams = getStandardParams();

        $this->facebookServerEvents = array();
		// initial event
        foreach ( PYS()->getRegisteredPixels() as $pixel ) {
            $event = new SingleEvent('init_event',EventTypes::$STATIC);
            $params = array();
            if(get_post_type() == "post") {
                global $post;
                $catIds = wp_get_object_terms( $post->ID, 'category', array( 'fields' => 'names' ) );
                $params['post_category'] = implode(", ",$catIds) ;
            }
            $event->addParams($params);
            $this->addEvent($event,$pixel);
        }

        // Search event
		if ( PYS()->getOption('search_event_enabled' ) && is_search() ) {
            foreach ( PYS()->getRegisteredPixels() as $pixel ) {
                $event = new SingleEvent('search_event',EventTypes::$STATIC);
                $this->addEvent($event,$pixel);
            }
		}

		if ( EventsCustom()->isEnabled() ) {
			add_filter( 'the_content', 'PixelYourSite\filterContentUrls', 1000 );
			add_filter( 'widget_text', 'PixelYourSite\filterContentUrls', 1000 );
		}

        if(EventsEdd()->isEnabled()) {
            // AddToCart on button
            if ( isEventEnabled( 'edd_add_to_cart_enabled') && PYS()->getOption( 'edd_add_to_cart_on_button_click' ) ) {
                add_action( 'edd_purchase_link_end', array( $this, 'setupEddSingleDownloadData' ) );
            }
        }

        if(EventsWoo()->isEnabled()){
            // AddToCart on button and Affiliate
            if ( isEventEnabled( 'woo_add_to_cart_enabled') && PYS()->getOption( 'woo_add_to_cart_on_button_click' )
                || isEventEnabled( 'woo_affiliate_enabled') ) {

                add_action( 'woocommerce_after_shop_loop_item', array( $this, 'setupWooLoopProductData' ) );
                add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'setupWooBlocksProductData' ), 10, 3 );

                if(is_product()) {
                    if(PYS()->getOption('woo_add_to_cart_on_single_product') == 'add_cart_hook') {
                        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'setupWooSingleProductData' ) );
                    } else {
                        $this->setupWooSingleProductData();
                    }
                } else {
                    add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'setupWooSingleProductData' ) );
                }
            }
        }


        /**
        * @var EventsFactory[] $eventsFactory
         **/
        $eventsFactory = array(EventsFdp(),EventsEdd(),EventsCustom(),EventsSignal(),EventsWoo());

        foreach ($eventsFactory as $factory) {

            if(!$factory->isEnabled())  continue;

            foreach ($factory->getEvents() as $eventName) {
                if($factory->isReadyForFire($eventName)) {

                    if($eventName == 'woo_purchase' ) {
                        $order_key = sanitize_key($_REQUEST['key']);
                        $order_id = (int) wc_get_order_id_by_order_key( $order_key );
                        if (PYS()->getOption( 'woo_purchase_on_transaction' ) && get_post_meta( $order_id, '_pys_purchase_event_fired', true ) ) {
                            continue;  // skip woo_purchase if this transaction was fired
                        }
                        update_post_meta( $order_id, '_pys_purchase_event_fired', true );
                    }

                    if($eventName == 'edd_purchase' ) {
                        $payment_key = getEddPaymentKey();
                        $order_id = (int) edd_get_purchase_id_by_key( $payment_key );

                        if ( PYS()->getOption( 'edd_purchase_on_transaction' ) && get_post_meta( $order_id, '_pys_purchase_event_fired', true ) ) {
                            continue; // skip woo_purchase if this transaction was fired
                        }
                        update_post_meta( $order_id, '_pys_purchase_event_fired', true );
                    }

                    foreach ( PYS()->getRegisteredPixels() as $pixel ) {
                        $event = $factory->getEvent($eventName);
                        $this->addEvent($event,$pixel);
                    }
                }
            }
        }

        // add Facebook Server events for async sending
        if (count($this->facebookServerEvents) > 0 &&  Facebook()->enabled()) {
            FacebookServer()->addAsyncEvents($this->facebookServerEvents);
            $this->facebookServerEvents = array();
        }

        // remove new user mark
        if($user_id = get_current_user_id()) {
            if ( get_user_meta( $user_id, 'pys_complete_registration', true ) ) {
                delete_user_meta( $user_id, 'pys_complete_registration' );
            }
        }
	}



    function addEvent($events,$pixel){
	    if(!is_array($events)) {
            $events = array($events);
        }
        foreach ($events as $event) {
            if(!method_exists($pixel,"addParamsToEvent")) continue; // for old pixel addons
            $isSuccess = $pixel->addParamsToEvent( $event );
            if ( !$isSuccess ) {
                continue; // event is disabled or not supported for the pixel
            }
            if($event->getType() == EventTypes::$STATIC) {
                $this->addStaticEvent( $event,$pixel );
            } elseif($event->getType() == EventTypes::$TRIGGER) {
                $this->addTriggerEvent($event,$pixel);
            } else {
                $this->addDynamicEvent($event,$pixel);
            }
        }
    }

    function addDynamicEvent($event,$pixel) {

        if(is_a($event,GroupedEvent::class)) {
            foreach ($event->getEvents() as $child) {
                if($pixel->getSlug() != "ga" || $pixel->isUse4Version()) {
                    $child->addParams($this->standardParams);
                }
                $eventData = $child->getData();
                //save static event data
                $this->dynamicEvents[ $event->getId() ][ $child->getId() ][ $pixel->getSlug() ] = $eventData;
            }
        } else {
            if($pixel->getSlug() != "ga" || $pixel->isUse4Version()) {
                $event->addParams($this->standardParams);
            }
            $eventData = $event->getData();
            //save static event data
            $this->dynamicEvents[ $event->getId() ][ $pixel->getSlug() ] = $eventData;
        }
    }

    function addTriggerEvent($event,$pixel) {
        if($pixel->getSlug() != "ga" || $pixel->isUse4Version()) {
            $event->addParams($this->standardParams);
        }
        $eventData = $event->getData();
        //save static event data
        if($event->getId() == "custom_event") {
            $eventId = $event->args->getPostId();
        } else {
            $eventId = $event->getId();
        }
        $this->triggerEvents[ $eventId ][ $pixel->getSlug() ] = $eventData;
        $this->triggerEventTypes[ $eventData['trigger_type'] ][ $eventId ][] = $eventData['trigger_value'];
    }
    /**
     * Create stack event, they fire when page loaded
     * @param Event $event
     */
    function addStaticEvent($event, $pixel) {

        if(is_a($event,GroupedEvent::class)) {
            foreach ($event->getEvents() as $child) {
                if($pixel->getSlug() != "ga" || $pixel->isUse4Version()) {
                    $child->addParams($this->standardParams);
                }

                $eventData = $child->getData();

                // send only for FB Server events
                if($pixel->getSlug() == "facebook" &&
                    ($event->getId() == "complete_registration" || $event->getId() == "complete_registration_category") &&
                    Facebook()->isServerApiEnabled() &&
                    Facebook()->getOption("woo_complete_registration_send_from_server") &&
                    !$this->isGdprPluginEnabled() )
                {
                    if($eventData['delay'] == 0) {
                        $this->addEventToFacebookServerApi($child->payload["pixelIds"],null,$eventData);
                    }
                    continue;
                }


                //save static event data
                $this->staticEvents[ $pixel->getSlug() ][ $eventData['name'] ][] = $eventData;
                // fire fb server api event
                if($pixel->getSlug() == "facebook") {
                    if( $eventData['delay'] == 0 && !Facebook()->getOption( "server_event_use_ajax" )) {
                        $this->addEventToFacebookServerApi($child->payload["pixelIds"],$child,$eventData);
                    }
                }
            }
        } else {
            if($pixel->getSlug() != "ga" || $pixel->isUse4Version()) {
                $event->addParams($this->standardParams);
            }
            $eventData = $event->getData();

            // send only for FB Server events
            if($pixel->getSlug() == "facebook" &&
                ($event->getId() == "complete_registration" || $event->getId() == "complete_registration_category") &&
                Facebook()->isServerApiEnabled() &&
                Facebook()->getOption("woo_complete_registration_send_from_server") &&
                !$this->isGdprPluginEnabled() )
            {
                if($eventData['delay'] == 0) {
                    $this->addEventToFacebookServerApi($event->payload["pixelIds"],null,$eventData);
                }
                return;
            }

            //save static event data
            $this->staticEvents[ $pixel->getSlug() ][ $eventData['name'] ][] = $eventData;
            // fire fb server api event
            if($pixel->getSlug() == "facebook") {
                if( $eventData['delay'] == 0 && !Facebook()->getOption( "server_event_use_ajax" )) {
                    $this->addEventToFacebookServerApi($event->payload["pixelIds"],$event,$eventData);
                }
            }
        }
    }
	
	public function getStaticEvents( $context ) {
		return isset( $this->staticEvents[ $context ] ) ? $this->staticEvents[ $context ] : array();
	}

	function addEventToFacebookServerApi($pixelIds,$eventType,$eventData) {

        if(!Facebook()->isServerApiEnabled()) return;
            $isDisabled = $this->isGdprPluginEnabled();

            if( !$isDisabled ) {
                $name = $eventData['name'];
                $data = $eventData['params'];
                $eventID = isset($eventData['eventID']) ? $eventData['eventID'] : false;
                $serverEvent = FacebookServer()->createEvent($eventID,$name,$data);
                $this->facebookServerEvents[] = array("pixelIds" => $pixelIds, "event" => $serverEvent );
            }
    }


    public function setupWooLoopProductData()
    {
        global $product;
        $this->setupWooProductData($product);
    }

    public function setupWooBlocksProductData($html, $data, $product)
    {
        $this->setupWooProductData($product);
        return $html;
    }

    public function setupWooProductData($product) {
	    if ( wooProductIsType( $product, 'variable' ) || wooProductIsType( $product, 'grouped' ) ) {
			return; // skip variable products
		} elseif ( wooProductIsType( $product, 'external' ) ) {
			$eventType = 'woo_affiliate';
		} else {
			$eventType = 'woo_add_to_cart_on_button_click';
		}

        $product_id = $product->get_id();

		$params = array();
		
		foreach ( PYS()->getRegisteredPixels() as $pixel ) {
			/** @var Pixel|Settings $pixel */

			$eventData = $pixel->getEventData( $eventType, $product_id );

			if ( false === $eventData ) {
				continue; // event is disabled or not supported for the pixel
			}
			if(isset($eventData['params']))
                $eventData['params'] = sanitizeParams($eventData['params']);
			else $eventData['params'] = sanitizeParams($eventData['data']);

            $params[$pixel->getSlug()] = $eventData;
		}

		if ( empty( $params ) ) {
			return;
		}

		$params = json_encode( $params );

		?>

		<script type="text/javascript">
			/* <![CDATA[ */
			window.pysWooProductData = window.pysWooProductData || [];
			window.pysWooProductData[ <?php echo $product_id; ?> ] = <?php echo $params; ?>;
			/* ]]> */
		</script>

		<?php

	}

	public function setupWooSingleProductData() {
		global $product;

        if ( ! is_object( $product)) $product = wc_get_product( get_the_ID() );

        if(!$product) return;

		if ( wooProductIsType( $product, 'external' ) ) {
			$eventType = 'woo_affiliate';
		} else {
			$eventType = 'woo_add_to_cart_on_button_click';
		}
        $product_id = $product->get_id();

		// main product id
		$product_ids[] = $product_id;

		// variations ids
		if ( wooProductIsType( $product, 'variable' ) ) {

			/** @var \WC_Product_Variable $variation */
			foreach ( $product->get_available_variations() as $variation ) {

				$variation = wc_get_product( $variation['variation_id'] );
                if(!$variation) continue;

                $product_ids[] = $variation->get_id();
			}

		}

		$params = array();

		foreach ( $product_ids as $product_id ) {
			foreach ( PYS()->getRegisteredPixels() as $pixel ) {
				/** @var Pixel|Settings $pixel */

				$eventData = $pixel->getEventData( $eventType, $product_id );

				if ( false === $eventData ) {
					continue; // event is disabled or not supported for the pixel
				}
                if(isset($eventData['params']))
                    $eventData['params'] = sanitizeParams($eventData['params']);
                else $eventData['params'] = sanitizeParams($eventData['data']);

                $params[ $product_id ][ $pixel->getSlug() ] = $eventData;

			}
		}

		if ( empty( $params ) ) {
			return;
		}

		?>

		<script type="text/javascript">
			/* <![CDATA[ */
			window.pysWooProductData = window.pysWooProductData || [];
			<?php foreach ( $params as $product_id => $product_data ) : ?>
				window.pysWooProductData[<?php echo $product_id; ?>] = <?php echo json_encode( $product_data ); ?>;
			<?php endforeach; ?>
			/* ]]> */
		</script>

		<?php

	}




	public function setupEddSingleDownloadData() {
		global $post;

		$download_ids = array();

		if ( edd_has_variable_prices( $post->ID ) ) {

			$prices = edd_get_variable_prices( $post->ID );

			foreach ( $prices as $price_index => $price_data ) {
				$download_ids[] = $post->ID . '_' . $price_index;
			}

		} else {

			$download_ids[] = $post->ID;

		}

		$params = array();

		foreach ( $download_ids as $download_id ) {
			foreach ( PYS()->getRegisteredPixels() as $pixel ) {
				/** @var Pixel|Settings $pixel */

				$eventData = $pixel->getEventData( 'edd_add_to_cart_on_button_click', $download_id );

				if ( false === $eventData ) {
					continue; // event is disabled or not supported for the pixel
				}

                if(isset($eventData['params']))
                    $eventData['params'] = sanitizeParams($eventData['params']);
                else $eventData['params'] = sanitizeParams($eventData['data']);

                $params[ $download_id ][ $pixel->getSlug() ] = $eventData;

			}
		}

		if ( empty( $params ) ) {
			return;
		}

		/**
		 * Format is pysEddProductData[ id ][ id ] or pysEddProductData[ id ] [ id_1, id_2, ... ]
		 */

		?>

		<script type="text/javascript">
			/* <![CDATA[ */
			window.pysEddProductData = window.pysEddProductData || [];
			window.pysEddProductData[<?php echo $post->ID; ?>] = <?php echo json_encode( $params ); ?>;
			/* ]]> */
		</script>

		<?php

	}


	function isGdprPluginEnabled() {
        return apply_filters( 'pys_disable_by_gdpr', false ) ||
        apply_filters( 'pys_disable_facebook_by_gdpr', false ) ||
        isCookiebotPluginActivated() && PYS()->getOption( 'gdpr_cookiebot_integration_enabled' ) ||
        isGingerPluginActivated() && PYS()->getOption( 'gdpr_ginger_integration_enabled' ) ||
        isCookieNoticePluginActivated() && PYS()->getOption( 'gdpr_cookie_notice_integration_enabled' ) ||
        isCookieLawInfoPluginActivated() && PYS()->getOption( 'gdpr_cookie_law_info_integration_enabled' );
    }

}