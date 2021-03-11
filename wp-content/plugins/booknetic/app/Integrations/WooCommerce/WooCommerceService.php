<?php

namespace BookneticApp\Integrations\WooCommerce;

use BookneticApp\Backend\Appointments\Helpers\AppointmentService;
use BookneticApp\Backend\Appointments\Model\Appointment;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomData;
use BookneticApp\Backend\Appointments\Model\AppointmentCustomer;
use BookneticApp\Backend\Appointments\Model\AppointmentExtra;
use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class WooCommerceService
{
	/**
	 * Woocommerce cart items cache
	 * @var array
	 */
	private static $woocommerceCartItems;

	private static $_appointment = [];
	private static $_customer = [];
	private static $_payabla_amount = [];
	private static $_sum_amount = [];
	private static $_appointments_count = [];
	private static $_appointment_extras = [];
	private static $_appointment_customers = [];
	private static $_appointments = [];

	/**
	 * Init WooCommerce services...
	 *
	 */
	public static function init()
	{
		if( !self::woocommerceIsEnabled() )
			return;

		$productId = self::bookneticProduct();

		add_filter( 'woocommerce_get_item_data',			            [self::class, 'getItemData'], 10, 2 );
		add_filter( 'woocommerce_cart_item_name',			            [self::class, 'productName'], 10, 2 );

		add_filter( 'woocommerce_cart_item_thumbnail',		            [self::class, 'productImage'], 10, 3 );
		add_filter( 'woocommerce_cart_item_price',      	            [self::class, 'productPrice'], 10, 3 );
		add_action( 'woocommerce_before_calculate_totals',              [self::class, 'beforeCalculateTotals'], 10, 1 );

		if ( version_compare( WC()->version, '3.0', '>=') )
		{
			add_action('woocommerce_checkout_create_order_line_item',   [self::class, 'checkoutCreateOrderLineItem'], 10, 4);
		}
		else
		{
			add_filter('woocommerce_add_order_item_meta',               [self::class, 'addOrderItemMeta'], 10, 3);
		}

		add_action( 'woocommerce_remove_cart_item',                     [self::class, 'removeCartItem'], 10, 2 );

		add_filter( 'woocommerce_checkout_get_value',                   [self::class, 'checkoutFields'], 10, 2 );

		add_action( 'woocommerce_order_status_completed',               [self::class, 'paymentComplete'], 10, 1 );
		//add_action( 'woocommerce_order_status_on-hold',                 [self::class, 'paymentComplete'], 10, 1 );
		add_action( 'woocommerce_order_status_processing',              [self::class, 'paymentComplete'], 10, 1 );


		add_filter( 'woocommerce_checkout_cart_item_quantity',			[self::class, 'checkoutCartItemQuantity'], 10, 2 );
		add_filter( 'woocommerce_order_item_quantity_html',		    	[self::class, 'checkoutCartItemQuantity'], 10, 2 );
		add_filter( 'woocommerce_order_item_name',			            [self::class, 'productName'], 10, 2 );
		add_filter( 'woocommerce_display_item_meta', 		            [self::class, 'displayItemMeta'], 10, 3 );

		add_action( 'woocommerce_after_order_itemmeta',                 [self::class, 'orderItemMeta'], 11, 1 );
		add_action( 'woocommerce_order_item_meta_end',                  [self::class, 'orderItemMeta'], 10, 1 );

	}

	/**
	 * @param $quantity
	 * @param $item
	 * @return string
	 */
	public static function checkoutCartItemQuantity( $quantity, $item )
	{
		if( isset( $item['booknetic_appointment_id'] ) )
		{
			$quantity = '';
		}

		return $quantity;
	}

	/**
	 * @param $html
	 * @param $item
	 * @param $args
	 * @return string
	 */
	public static function displayItemMeta( $html, $item, $args )
	{
		if( isset( $item['booknetic_appointment_id'] ) )
		{
			$html = '';
		}

		return $html;
	}

	/**
	 * @param $item_id
	 * @throws \Exception
	 */
	public static function orderItemMeta( $item_id )
	{
		$appointmentId = wc_get_order_item_meta( $item_id, 'booknetic_appointment_id' );

		if ( $appointmentId > 0 && self::appointmentExist( $appointmentId ) )
		{
			print '<div><strong>' . bkntc__('Appointment details') . ':</strong></div>';
			print self::productAdditionalData( $appointmentId );
		}
	}

	/**
	 * @param $order_id
	 * @throws \Exception
	 */
	public static function paymentComplete( $order_id )
	{
		$order = new \WC_Order( $order_id );

		foreach ( $order->get_items() as $item_id => $order_item )
		{
			$appointmentId = wc_get_order_item_meta( $item_id, 'booknetic_appointment_id' );

			if ( !empty( $appointmentId ) && $appointmentId > 0 && self::appointmentExist( $appointmentId ) )
			{
				$appointmentInf = AppointmentCustomer::get( $appointmentId );

				if( $appointmentInf && $appointmentInf['payment_status'] == 'pending' )
				{
					AppointmentService::confirmPayment( $appointmentId );
				}
			}
		}
	}

	/**
	 * @param $value
	 * @param $field_name
	 * @return string
	 */
	public static function checkoutFields( $value, $field_name )
	{
		$customerInfo = self::customerInfo();

		if( $customerInfo !== false )
		{
			switch( $field_name )
			{
				case 'billing_first_name':
					return $customerInfo->first_name;
					break;
				case 'billing_last_name':
					return $customerInfo->last_name;
					break;
				case 'billing_email':
					return $customerInfo->email;
					break;
				case 'billing_phone':
					return $customerInfo->phone_number;
					break;
			}
		}

		return $value;
	}

	/**
	 * @param $item_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param $unique
	 * @throws \Exception
	 */
	public static function addOrderItemMeta( $item_id, $meta_key, $meta_value, $unique )
	{
		if ( isset( $meta_value['booknetic_appointment_id'] ) )
		{
			wc_update_order_item_meta( $item_id, 'booknetic_appointment_id', $meta_value['booknetic_appointment_id'] );
		}
	}

	/**
	 * @param $cart_item_key
	 * @param $cart
	 */
	public static function removeCartItem( $cart_item_key, $cart )
	{
		$cart_item = $cart->removed_cart_contents[ $cart_item_key ];

		if ( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
		{
			self::removeAppointment( $cart_item );
		}
	}

	/**
	 * Empty cart action: Remove Appointments
	 */
	public static function emptyCart( )
	{
		foreach ( self::getCartItems() AS $cart_item )
		{
			if ( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
			{
				self::removeAppointment( $cart_item );
			}
		}
	}

	/**
	 * @param $item
	 * @param $cart_item_key
	 * @param $values
	 * @param $order
	 */
	public static function checkoutCreateOrderLineItem( $item, $cart_item_key, $values, $order )
	{
		if ( isset( $values['booknetic_appointment_id'] ) )
		{
			$item->update_meta_data('booknetic_appointment_id', $values['booknetic_appointment_id']);
		}
	}

	/**
	 * @param $product_price
	 * @param $cart_item
	 * @param $cart_item_key
	 * @return string
	 */
	public static function productPrice( $product_price, $cart_item, $cart_item_key )
	{
		if ( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
		{
			$product_price = wc_price( self::getPayableAmount( $cart_item['booknetic_appointment_id'] ) );
		}

		return $product_price;
	}

	/**
	 * @param $cart_object
	 */
	public static function beforeCalculateTotals( $cart_object )
	{
		foreach ( $cart_object->cart_contents AS $cart_item )
		{
			if ( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
			{
				$cart_item['data']->set_price( self::getPayableAmount( $cart_item['booknetic_appointment_id'] ) );
			}
		}
	}

	/**
	 * @param $item_name
	 * @param $cart_item
	 * @return string
	 */
	public static function productName( $item_name, $cart_item )
	{
		if( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
		{
			$item_name = esc_html( self::appointmentInfo( $cart_item['booknetic_appointment_id'] )->service()->fetch()->name );

			if( self::getAppoointmentsCount( $cart_item['booknetic_appointment_id'] ) > 1 )
			{
				$item_name .= ' <strong class="product-quantity">× ' . self::getAppoointmentsCount( $cart_item['booknetic_appointment_id'] ) . '</strong>';
			}
		}

		return $item_name;
	}

	/**
	 * @param $_product_img
	 * @param $cart_item
	 * @return string
	 */
	public static function productImage( $_product_img, $cart_item )
	{
		if( isset( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
		{
			$_product_img = '<img src="'.Helper::profileImage(self::appointmentInfo( $cart_item['booknetic_appointment_id'] )->service()->fetch()->image, 'Services').'" width="300" height="300">';
		}

		return $_product_img;
	}

	/**
	 * @param $other_data
	 * @param $cart_item
	 * @return array
	 */
	public static function getItemData( $other_data, $cart_item )
	{
		if ( isset ( $cart_item['booknetic_appointment_id'] ) && self::appointmentExist( $cart_item['booknetic_appointment_id'] ) )
		{
			$other_data[] = [
				'name'  =>  bkntc__('Appointment details'),
				'value' =>  '&nbsp;</dd></dl>' . self::productAdditionalData( $cart_item['booknetic_appointment_id'] ) . '<dl><dd>'
			];
		}

		return $other_data;
	}

	/**
	 * @param $appointmentId
	 * @return string
	 */
	private static function productAdditionalData( $appointmentId )
	{
		$additionalData = '';

		$woocommerde_order_details = Helper::getOption('woocommerde_order_details', "Date: {appointment_date}\nTime: {appointment_start_time}\nStaff: {staff_name}");
		$woocommerde_order_details = trim($woocommerde_order_details);
		if( !empty( $woocommerde_order_details ) )
		{
			$woocommerde_order_details = nl2br( htmlspecialchars( $woocommerde_order_details ) );
			$additionalData .= '<div>' . self::replaceShortTags( $appointmentId, $woocommerde_order_details ) . '</div>';
		}

		return $additionalData;
	}

	/**
	 * @param $appointmentCustomerId
	 * @param $body
	 * @return string
	 */
	private static function replaceShortTags( $appointmentCustomerId, $body )
	{
		$appointmentInfo 			= self::appointmentInfo( $appointmentCustomerId );
		$appointmentCustomerInfo	= self::appointmentCustomerInfo( $appointmentCustomerId );
		$serviceInfo				= $appointmentInfo->service()->fetch();
		$categoryInfo				= $serviceInfo->category()->fetch();
		$customerInfo				= $appointmentCustomerInfo->customer()->fetch();
		$staffInfo					= $appointmentInfo->staff()->fetch();
		$locationInfo				= $appointmentInfo->location()->fetch();

		$appointmentId				= $appointmentInfo->id;
		$customerId					= $customerInfo->id;

		$body = str_replace( [
			'{appointment_id}',
			'{appointment_date}',
			'{appointment_date_time}',
			'{appointment_start_time}',
			'{appointment_end_time}',
			'{appointment_duration}',
			'{appointment_buffer_before}',
			'{appointment_buffer_after}',
			'{appointment_status}',
			'{appointment_service_price}',
			'{appointment_extras_price}',
			'{appointment_discount_price}',
			'{appointment_sum_price}',
			'{appointment_paid_price}',
			'{appointment_payment_method}',

			'{service_name}',
			'{service_price}',
			'{service_duration}',
			'{service_notes}',
			'{service_color}',
			'{service_image_url}',
			'{service_category_name}',

			'{customer_full_name}',
			'{customer_first_name}',
			'{customer_last_name}',
			'{customer_phone}',
			'{customer_email}',
			'{customer_birthday}',
			'{customer_notes}',
			'{customer_profile_image_url}',

			'{staff_name}',
			'{staff_email}',
			'{staff_phone}',
			'{staff_about}',
			'{staff_profile_image_url}',

			'{location_name}',
			'{location_address}',
			'{location_image_url}',
			'{location_phone_number}',
			'{location_notes}',

			'{company_name}',
			'{company_image_url}',
			'{company_website}',
			'{company_phone}',
			'{company_address}',
		], [
			$appointmentCustomerInfo['id'],
			Date::datee( $appointmentInfo['date'] ),
			Date::dateTime($appointmentInfo['date'] . ' ' . $appointmentInfo['start_time'] ),
			Date::time( $appointmentInfo['date'] . ' ' . $appointmentInfo['start_time'] ),
			Date::time(Date::epoch( $appointmentInfo['date'] . ' ' . $appointmentInfo['start_time'] ) + $appointmentInfo['duration'] * 60),
			Helper::secFormat( $appointmentInfo['duration'] * 60 ),
			Helper::secFormat( $appointmentInfo['buffer_before'] * 60 ),
			Helper::secFormat( $appointmentInfo['buffer_after'] * 60 ),
			$appointmentCustomerInfo['status'],
			Helper::price( $appointmentCustomerInfo['service_amount'] ),
			Helper::price( $appointmentCustomerInfo['extras_amount'] ),
			Helper::price( $appointmentCustomerInfo['discount'] ),
			Helper::price( $appointmentCustomerInfo['service_amount'] + $appointmentCustomerInfo['extras_amount'] - $appointmentCustomerInfo['discount'] ),
			Helper::price( $appointmentCustomerInfo['paid_amount'] ),
			Helper::paymentMethod( $appointmentCustomerInfo['payment_method'] ),

			$serviceInfo['name'],
			Helper::price( $serviceInfo['price'] ),
			Helper::secFormat( $serviceInfo['duration'] * 60 ),
			$serviceInfo['notes'],
			$serviceInfo['color'],
			Helper::profileImage( $serviceInfo['image'], 'Services' ),
			$categoryInfo['name'],

			$customerInfo['first_name'] . ' ' . $customerInfo['last_name'],
			$customerInfo['first_name'],
			$customerInfo['last_name'],
			$customerInfo['phone_number'],
			$customerInfo['email'],
			$customerInfo['birthdate'],
			$customerInfo['notes'],
			Helper::profileImage( $customerInfo['profile_image'], 'Customers' ),

			$staffInfo['name'],
			$staffInfo['email'],
			$staffInfo['phone_number'],
			$staffInfo['about'],
			Helper::profileImage( $staffInfo['profile_image'], 'Staff' ),

			$locationInfo['name'],
			$locationInfo['address'],
			Helper::profileImage( $locationInfo['image'], 'Locations' ),
			$locationInfo['phone_number'],
			$locationInfo['notes'],

			Helper::getOption('company_name', ''),
			Helper::profileImage( Helper::getOption('company_image', ''), 'Settings'),
			Helper::getOption('company_website', ''),
			Helper::getOption('company_phone', ''),
			Helper::getOption('company_address', '')

		], $body );

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)}/', function ( $found ) use( $appointmentId, $customerId )
		{
			if( !isset( $found[1] ) )
				return $found[0];

			return self::getCustomFieldValue( $appointmentId, $customerId, $found[1] );

		}, $body);

		$body = preg_replace_callback('/{appointment_custom_field_([0-9]+)_url}/', function ( $found ) use ( $appointmentId, $customerId )
		{

			if( !isset( $found[1] ) )
				return $found[0];

			return self::getCustomFieldValue( $appointmentId, $customerId, $found[1], true );

		}, $body);

		return $body;
	}

	/**
	 * @param $appointmentId
	 * @param $customerId
	 * @param $cf_id
	 * @param bool $fileUrl
	 * @return string
	 */
	private static function getCustomFieldValue( $appointmentId, $customerId, $cf_id, $fileUrl = false )
	{
		$customData = DB::DB()->get_row(
			DB::DB()->prepare("
				SELECT 
					tb2.type, tb1.input_file_name,
					IF( tb2.type IN ('select', 'checkbox', 'radio'), (SELECT group_concat(' ', `title`) FROM `".DB::table('form_input_choices')."` WHERE FIND_IN_SET(id, tb1.`input_value`)), tb1.`input_value` ) AS real_value
				FROM `".DB::table('appointment_custom_data')."` tb1 
				LEFT JOIN `".DB::table('form_inputs')."` tb2 ON tb2.id=tb1.form_input_id
				WHERE appointment_id=%d AND customer_id=%d AND form_input_id=%d
				", [ $appointmentId, $customerId, $cf_id ]
			),
			ARRAY_A
		);

		if( !$customData )
		{
			return '';
		}

		if( $customData['type'] == 'file' )
		{
			if( $fileUrl )
			{
				return Helper::uploadedFileURL( htmlspecialchars($customData['real_value']), 'CustomForms');
			}
			else
			{
				return $customData['input_file_name'];
			}
		}
		else
		{
			return $customData['real_value'];
		}
	}

	/**
	 * @param $appointmentId
	 * @return array
	 */
	private static function appointmentExtras( $appointmentId )
	{
		if( !isset( self::$_appointment_extras[ $appointmentId ] ) )
		{
			$appointmentCustomerInf = self::appointmentCustomerInfo( $appointmentId );

			self::$_appointment_extras[ $appointmentId ] = AppointmentExtra::where( 'appointment_id', $appointmentCustomerInf->appointment_id )->where('customer_id', $appointmentCustomerInf->customer_id)->fetchAll();
		}

		return self::$_appointment_extras[ $appointmentId ];
	}

	/**
	 * Is WooCommerce plugin enabled?
	 *
	 * @return bool
	 */
	public static function woocommerceIsEnabled()
	{
		return class_exists( 'WooCommerce', false );
	}

	/**
	 * Is WooCommerce payment method enabled in Booknetic?
	 *
	 * @return bool
	 */
	public static function paymentMethodIsEnabled()
	{
		if( !self::woocommerceIsEnabled() )
		{
			return false;
		}

		$methodIsActive = Helper::getOption('woocommerce_enabled', 'off');

		return $methodIsActive == 'on';
	}

	/**
	 * Get Booknetic product ID
	 *
	 * @return int
	 */
	public static function bookneticProduct()
	{
		if( !self::paymentMethodIsEnabled() )
			return 0;

		$productId = Helper::getOption('woocommerce_product_id');

		if( $productId )
		{
			$productInf = wc_get_product( $productId );
		}

		if( !$productId || !$productInf || !$productInf->exists() || $productInf->get_status() != 'publish' )
		{
			$productId = wp_insert_post([
				'post_title'    => 'Booknetic',
				'post_type'     => 'product',
				'post_status'   => 'publish'
			]);

			Helper::setOption('woocommerce_product_id', $productId);

			// set product is simple/variable/grouped
			wp_set_object_terms( $productId, 'simple', 'product_type' );

			update_post_meta( $productId, '_visibility', 'hidden' );
			update_post_meta( $productId, '_stock_status', 'instock');
			update_post_meta( $productId, 'total_sales', '0' );
			update_post_meta( $productId, '_downloadable', 'no' );
			update_post_meta( $productId, '_virtual', 'yes' );
			update_post_meta( $productId, '_regular_price', '0' );
			update_post_meta( $productId, '_sale_price', '' );
			update_post_meta( $productId, '_purchase_note', '' );
			update_post_meta( $productId, '_featured', 'no' );
			update_post_meta( $productId, '_weight', '' );
			update_post_meta( $productId, '_length', '' );
			update_post_meta( $productId, '_width', '' );
			update_post_meta( $productId, '_height', '' );
			update_post_meta( $productId, '_sku', '' );
			update_post_meta( $productId, '_product_attributes', [] );
			update_post_meta( $productId, '_sale_price_dates_from', '' );
			update_post_meta( $productId, '_sale_price_dates_to', '' );
			update_post_meta( $productId, '_price', '0' );
			update_post_meta( $productId, '_sold_individually', 'yes' );
			update_post_meta( $productId, '_manage_stock', 'no' );
			update_post_meta( $productId, '_backorders', 'no' );
			wc_update_product_stock($productId, 0, 'set');
			update_post_meta( $productId, '_stock', '' );
		}

		return $productId;
	}

	/**
	 * Add Booknetic to cart
	 *
	 * @param $appointmentId
	 * @return bool
	 * @throws \Exception
	 */
	public static function addToWoocommerceCart( $appointmentId )
	{
		self::setCustomerCookie();

		WC()->cart->empty_cart();
		WC()->cart->add_to_cart( self::bookneticProduct(), 1, '', [], ['booknetic_appointment_id' => $appointmentId] );

		return true;
	}

	/**
	 * Set WC Customer session cookie
	 *
	 * @return bool
	 */
	private static function setCustomerCookie()
	{
		if ( WC()->session && WC()->session instanceof \WC_Session_Handler && WC()->session->get_session_cookie() === false )
		{
			WC()->session->set_customer_session_cookie( true );
		}

		return true;
	}

	/**
	 * Get WooCommerce Cart URL
	 *
	 * @return string
	 */
	public static function redirect()
	{
		return Helper::getOption('woocommerce_rediret_to', 'cart') == 'cart' ? wc_get_cart_url() : wc_get_checkout_url();
	}

	/**
	 * @param $id
	 * @return Appointment
	 */
	private static function appointmentInfo( $id )
	{
		$appointmentCustomerInfo = self::appointmentCustomerInfo( $id );

		if( !isset( self::$_appointments[ $appointmentCustomerInfo->appointment_id ] ) )
		{
			self::$_appointments[ $appointmentCustomerInfo->appointment_id ] = $appointmentCustomerInfo->appointment()->fetch();
		}

		return self::$_appointments[ $appointmentCustomerInfo->appointment_id ] ;
	}

	/**
	 * @param $id
	 * @return AppointmentCustomer
	 */
	private static function appointmentCustomerInfo( $id )
	{
		if( !isset( self::$_appointment_customers[ $id ] ) )
		{
			self::$_appointment_customers[ $id ] = AppointmentCustomer::get( $id );
		}

		return self::$_appointment_customers[ $id ];
	}

	/**
	 * @param $bookingId
	 * @return mixed
	 */
	private static function getPayableAmount( $bookingId )
	{
		if( !isset( self::$_payabla_amount[ $bookingId ] ) )
		{
			self::sumPayableAmountAndAppointmentsCount( $bookingId );
		}

		return self::$_payabla_amount[ $bookingId ];
	}

	/**
	 * @param $bookingId
	 * @return mixed
	 */
	private static function getSumAmount( $bookingId )
	{
		if( !isset( self::$_sum_amount[ $bookingId ] ) )
		{
			self::sumPayableAmountAndAppointmentsCount( $bookingId );
		}

		return self::$_sum_amount[ $bookingId ];
	}

	/**
	 * @param $bookingId
	 * @return mixed
	 */
	private static function getAppoointmentsCount( $bookingId )
	{
		if( !isset( self::$_appointments_count[ $bookingId ] ) )
		{
			self::sumPayableAmountAndAppointmentsCount( $bookingId );
		}

		return self::$_appointments_count[ $bookingId ];
	}

	/**
	 * @param $bookingId
	 * @return bool
	 */
	private static function appointmentExist( $bookingId )
	{
		$bookingInfo = AppointmentCustomer::get( $bookingId );

		return $bookingInfo && $bookingInfo->id > 0;
	}

	/**
	 * @param $bookingId
	 */
	private static function sumPayableAmountAndAppointmentsCount( $bookingId )
	{
		$bookingInfo = AppointmentCustomer::get( $bookingId );

		$customerId = $bookingInfo['customer_id'];
		$appointmentId = $bookingInfo['appointment_id'];

		$get_payable_amount = DB::DB()->get_row(
			DB::DB()->prepare(
				'SELECT SUM(`service_amount`+`extras_amount`-`discount`) as `sum_amount`, SUM(paid_amount) as `payable_amount`, COUNT(0) as `appointments_count` FROM `'.DB::table('appointment_customers').'` WHERE appointment_id IN (SELECT id FROM `'.DB::table('appointments').'` WHERE id=%d OR recurring_id=%d) AND customer_id=%d',
				[
					$appointmentId,
					$appointmentId,
					$customerId
				]
			),
			ARRAY_A
		);

		self::$_payabla_amount[ $bookingId ] = $get_payable_amount['payable_amount'];
		self::$_sum_amount[ $bookingId ] = $get_payable_amount['sum_amount'];
		self::$_appointments_count[ $bookingId ] = $get_payable_amount['appointments_count'];
	}

	/**
	 * @return array
	 */
	private static function getCartItems()
	{
		if( is_null( self::$woocommerceCartItems ) )
		{
			self::$woocommerceCartItems = WC()->cart->get_cart();
		}

		return self::$woocommerceCartItems;
	}

	/**
	 * @return bool|int
	 */
	private static function bookneticAppointmentId()
	{
		foreach ( self::getCartItems() AS $item )
		{
			if( isset( $item['booknetic_appointment_id'] ) )
			{
				if( AppointmentCustomer::get( $item['booknetic_appointment_id'] ) )
				{
					return $item['booknetic_appointment_id'];
				}
			}
		}

		return false;
	}

	/**
	 * @return Customer
	 */
	private static function customerInfo()
	{
		$appointmentId = self::bookneticAppointmentId();

		if( $appointmentId !== false )
		{
			return AppointmentCustomer::get( $appointmentId )->customer()->fetch();
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private static function showStaffData()
	{
		if( Staff::count() == 1 && Helper::getOption('hide_staff_step', 'off')=='on' )
			return false;

		return true;
	}

	/**
	 * @return bool
	 */
	private static function showLocationData()
	{
		if( Location::count() == 1 && Helper::getOption('hide_locations_step', 'off')=='on' )
			return false;

		return true;
	}

	private static function removeAppointment( $cart_item )
	{
		$appointmentCustomerInfo    = self::appointmentCustomerInfo( $cart_item['booknetic_appointment_id'] );
		$appointmentId              = $appointmentCustomerInfo->appointment_id;
		$customerId                 = $appointmentCustomerInfo->customer_id;

		AppointmentExtra::where('appointment_id', $appointmentId)->where('customer_id', $customerId)->delete();
		AppointmentCustomData::where('appointment_id', $appointmentId)->where('customer_id', $customerId)->delete();
		AppointmentCustomer::where('appointment_id', $appointmentId)->where('customer_id', $customerId)->delete();

		$checkSlotIsEmpty = AppointmentCustomer::where('appointment_id', $appointmentId)->count();
		if( $checkSlotIsEmpty == 0 )
		{
			Appointment::where('id', $appointmentId)->delete();
		}
	}

}