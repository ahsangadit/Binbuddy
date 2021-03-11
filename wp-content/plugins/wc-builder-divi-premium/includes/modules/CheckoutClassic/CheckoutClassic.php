<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_Checkout_Classic extends ET_Builder_Module {

	public $vb_support = 'on';
	protected $order_review_title;

    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	function init() {
		$this->name       = esc_html__( 'Woo Checkout', 'wc-builder-divi' );
		$this->slug       = 'et_pb_wcbd_checkout_classic';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'components' => esc_html__( 'Checkout Components', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'customer_details_section' => esc_html__( 'Customer Details Section', 'wc-builder-divi' ),
					'order_review_section' => esc_html__( 'Order Review Section', 'wc-builder-divi' ),
					'more_options' => esc_html__( 'More Options', 'wc-builder-divi' ),
				),
			),
			
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'returning_customer_title'   => array(
					'label'    => esc_html__( 'Returning Customer?', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-form-login-toggle .woocommerce-info, %%order_class%% .woocommerce-form-login-toggle .woocommerce-info a",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'have_coupon_title'   => array(
					'label'    => esc_html__( 'Have a Coupon?', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-form-coupon-toggle .woocommerce-info, %%order_class%% .woocommerce-form-coupon-toggle .woocommerce-info a",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'billing_details_title'   => array(
					'label'    => esc_html__( 'Billing Details Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #customer_details .woocommerce-billing-fields h3",
					),
					'font_size' => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'shipping_details_title'   => array(
					'label'    => esc_html__( 'Shipping Details Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #customer_details .woocommerce-shipping-fields h3",
					),
					'font_size' => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'order_review_title_style'   => array(
					'label'    => esc_html__( 'Order Review Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #order_review h3.order_review_title",
					),
					'font_size' => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'inputs_labels'   => array(
					'label'    => esc_html__( 'Inputs Labels', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% form .form-row label",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '2em',
					),
				),
				'inputs_content'   => array(
					'label'    => esc_html__( 'Inputs Content', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% form .form-row input, %%order_class%% form .form-row textarea, %%order_class%% .select2-container--default .select2-selection--single, %%order_class%% form .form-row select, .select2-dropdown",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'order_summary_content'   => array(
					'label'    => esc_html__( 'Order Summary', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #order_review .woocommerce-checkout-review-order-table, %%order_class%% #order_review .woocommerce-checkout-review-order-table th, body.woocommerce-order-pay %%order_class%% #order_review .shop_table, body.woocommerce-order-pay %%order_class%% #order_review .shop_table th",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'payment_method_name'   => array(
					'label'    => esc_html__( 'Payment Name', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #order_review .wc_payment_method label",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'payment_method_desc'   => array(
					'label'    => esc_html__( 'Payment Description', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% #payment .payment_box",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'privacy_policy'   => array(
					'label'    => esc_html__( 'Privacy Policy', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-privacy-policy-text",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders' => array(
				'default' => array(
					'label_prefix' => esc_html__( 'Module', 'wc-builder-divi' ),
				),
				'customer_details' => array(
					'label_prefix' => esc_html__( 'Customer Details Section', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% #customer_details",
							'border_styles' => "%%order_class%% #customer_details",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '',
							'style' => 'solid',
						),
					),
				),
				'order_review' => array(
					'label_prefix' => esc_html__( 'Order Review Section', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% #order_review",
							'border_styles' => "%%order_class%% #order_review",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '',
							'style' => 'solid',
						),
					),
				),
				'order_summary' => array(
					'label_prefix' => esc_html__( 'Order Summary', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% #order_review .woocommerce-checkout-review-order-table, body.woocommerce-order-pay %%order_class%% #order_review .shop_table",
							'border_styles' => "%%order_class%% #order_review .woocommerce-checkout-review-order-table, body.woocommerce-order-pay %%order_class%% #order_review .shop_table",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|5px|5px|5px|5px',
						'border_styles' => array(
							'width' => '1px',
							'color' => 'rgba(0,0,0,.1)',
							'style' => 'solid',
						),
					),
				),
				'payments_box' => array(
					'label_prefix' => esc_html__( 'Payments Box', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% #order_review #payment",
							'border_styles' => "%%order_class%% #order_review #payment",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|5px|5px|5px|5px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#fff',
							'style' => 'solid',
						),
					),
				),
				'inputs' => array(
					'label_prefix' => esc_html__( 'Inputs', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% form .form-row input, %%order_class%% form .form-row textarea, %%order_class%% .select2-container--default .select2-selection--single, %%order_class%% form .form-row select, body.woocommerce-page %%order_class%% form .form-row input, body.woocommerce-page %%order_class%% form .form-row textarea, body.woocommerce-page %%order_class%% .select2-container--default .select2-selection--single, body.woocommerce-page %%order_class%% form .form-row select
							",
							'border_styles' => "%%order_class%% form .form-row input, %%order_class%% form .form-row textarea, %%order_class%% .select2-container--default .select2-selection--single, %%order_class%% form .form-row select, body.woocommerce-page %%order_class%% form .form-row input, body.woocommerce-page %%order_class%% form .form-row textarea, body.woocommerce-page %%order_class%% .select2-container--default .select2-selection--single, body.woocommerce-page %%order_class%% form .form-row select
							",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '1px',
							'color' => 'rgb(187, 187, 187)',
							'style' => 'solid',
						),
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'login_button' => array(
					'label' => esc_html__( 'Login Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-form-login .button[name='login']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .woocommerce-form-login .button[name='login']",
						),
					),
				),
				'apply_coupon_button' => array(
					'label' => esc_html__( 'Apply Coupon Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-form-coupon .button[name='apply_coupon']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .woocommerce-form-coupon .button[name='apply_coupon']",
						),
					),
				),
				'place_order_button' => array(
					'label' => esc_html__( 'Place Order Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% #payment #place_order",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% #payment #place_order",
						),
					),
				),
			),
		);
		$this->custom_css_fields = array(
			'place_order_button_css' => array(
				'label'    => esc_html__( 'Place Order Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% #payment #place_order",
			),
		);
	}

	function get_fields(){
		$fields = array(

			'show_coupon_form' => array(
				'label'           => esc_html__( 'Apply Coupon Form', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'show_shipping_fields' => array(
				'label'           => esc_html__( 'Shipping Fields', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'check_ship_different_address' => array(
				'label'           => esc_html__( 'Check "Ship to a different address?"', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => __( 'No', 'wc-builder-divi' ),
					'on' => __( 'Yes', 'wc-builder-divi' ),
				),
				'description' => esc_html__( 'If you enable this , the Ship to a different address? checkbox will be checked by default.', 'wc-builder-divi' ),
				'show_if' => array(
					'show_shipping_fields' => 'on',
				),
				'default' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'show_order_notes' => array(
				'label'           => esc_html__( 'Show Order Notes', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'show_order_review_title' => array(
				'label'           => esc_html__( 'Order Review Title', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'order_review_title' => array(
				'label'           => esc_html__( 'Custom Title', 'wc-builder-divi' ),
				'type'            => 'text',
				'default'		 => esc_html__( 'Your order', 'woocommerce' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
				'show_if' => array(
					'show_order_review_title' => 'on',
				),
			),
			'show_order_summary' => array(
				'label'           => esc_html__( 'Order Summary Table', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'components',
			),
			'fullwidth_billing_shipping' => array(
				'label'           => esc_html__( 'Fullwidth Billing & Shipping', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on' => __( 'Yes', 'wc-builder-divi' ),
					'off' => __( 'No', 'wc-builder-divi' ),
				),
				'default' => 'off',
				'description' => esc_html__( 'This includes BOTH the billing and the shipping details.', 'wc-builder-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'customer_details_section',
			),
			'customer_details_width' => array(
				'label'           => esc_html__( 'Section Width', 'wc-builder-divi' ),
				'type'            => 'range',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'default' => '100%',
				'fixed_unit'      => '%',
				'description' => esc_html__( 'This includes BOTH the billing and the shipping details.', 'wc-builder-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'customer_details_section',
			),
			'customer_details_float' => array(
				'label' => esc_html__( 'Section Float', 'wc-builder-divi' ),
				'type' => 'text_align',
				'option_category'  => 'configuration',
				'options' => et_builder_get_text_orientation_options( array( 'justified', 'center' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'customer_details_section',
			),
			'customer_details_bg' => array(
				'label' => esc_html__( 'Section Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'customer_details_section',
			),
			'customer_details_padding' => array(
				'label'             => esc_html__( 'Section Padding', 'et_builder' ),
				'type'              => 'custom_margin',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'customer_details_section',
			),
			'order_review_width' => array(
				'label'           => esc_html__( 'Section Width', 'wc-builder-divi' ),
				'type'            => 'range',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'default' => '100%',
				'fixed_unit'      => '%',
				'description' => esc_html__( 'This includes BOTH the order summary and the payment methods.', 'wc-builder-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'order_review_section',
			),
			'order_review_float' => array(
				'label' => esc_html__( 'Section Float', 'wc-builder-divi' ),
				'type' => 'text_align',
				'option_category'  => 'configuration',
				'options' => et_builder_get_text_orientation_options( array( 'justified', 'center' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'order_review_section',
			),
			'order_review_bg' => array(
				'label' => esc_html__( 'Section Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'order_review_section',
			),
			'order_review_padding' => array(
				'label'             => esc_html__( 'Section Padding', 'et_builder' ),
				'type'              => 'custom_margin',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'order_review_section',
			),
			'order_summary_background' => array(
				'label' => esc_html__( 'Order Summary Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'order_review_section',
				'show_if' => array(
					'show_order_summary' => 'on',
				),
			),
			'payments_bg' => array(
				'label' => esc_html__( 'Payments Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'default' => '#ebe9eb',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'order_review_section',
			),
			'payment_desc_bg' => array(
				'label' => esc_html__( 'Payment Description Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'default' => '#dfdcde',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'order_review_section',
			),
			'returning_customer_bg' => array(
				'label' => esc_html__( 'Returning Customer Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'more_options',
			),
			'have_coupon_bg' => array(
				'label' => esc_html__( 'Have a Coupon Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'more_options',
			),
			'inputs_bg' => array(
				'label' => esc_html__( 'Inputs Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'more_options',
			),
			'required_icon_color' => array(
				'label' => esc_html__( 'Required Icon Color', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'default' => '#ff0000',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'more_options',
			),
		);

		return $fields;
	}

	function change_order_review_title(){
		$title = '';
		if( !empty( $this->order_review_title ) ){
			$title = "<h3 class='order_review_title'>". esc_attr( $this->order_review_title ) ."</h3>";
		}
		echo $title;
	}
	function render( $attrs, $content = null, $render_slug ) {

		$show_coupon_form = $this->props['show_coupon_form'];		
		$check_ship_different_address = $this->props['check_ship_different_address'];		
		$show_shipping_fields = $this->props['show_shipping_fields'];	
		$show_order_notes = $this->props['show_order_notes'];	
		$show_order_review_title = $this->props['show_order_review_title'];	
		$this->order_review_title = $this->props['order_review_title'];	
		$show_order_summary = $this->props['show_order_summary'];

		$fullwidth_billing_shipping = $this->props['fullwidth_billing_shipping'];

		$customer_details_width = $this->props['customer_details_width'];
		$order_review_width = $this->props['order_review_width'];

		$customer_details_float = $this->props['customer_details_float'];
		$order_review_float = $this->props['order_review_float'];

		$customer_details_padding = $this->props['customer_details_padding'];
		$order_review_padding = $this->props['order_review_padding'];

		$returning_customer_bg = $this->props['returning_customer_bg'];
		$have_coupon_bg = $this->props['have_coupon_bg'];
		$customer_details_bg = $this->props['customer_details_bg'];
		$order_review_bg = $this->props['order_review_bg'];
		$order_summary_background = $this->props['order_summary_background'];

		$payments_bg = $this->props['payments_bg'];
		$payment_desc_bg = $this->props['payment_desc_bg'];

		$inputs_bg = $this->props['inputs_bg'];
		$required_icon_color = $this->props['required_icon_color'];

		$apply_coupon_button_custom        		= $this->props['custom_apply_coupon_button'];
		$apply_coupon_button_bg_color       	= $this->props['apply_coupon_button_bg_color'];
		$apply_coupon_button_icon       		= $this->props['apply_coupon_button_icon'];
		$apply_coupon_button_use_icon       		= $this->props['apply_coupon_button_use_icon'];
		$apply_coupon_button_icon_placement     = $this->props['apply_coupon_button_icon_placement'];

		$login_button_custom        		= $this->props['custom_login_button'];
		$login_button_bg_color       	= $this->props['login_button_bg_color'];
		$login_button_icon       		= $this->props['login_button_icon'];
		$login_button_use_icon       		= $this->props['login_button_use_icon'];
		$login_button_icon_placement     = $this->props['login_button_icon_placement'];

		$place_order_button_custom        		= $this->props['custom_place_order_button'];
		$place_order_button_bg_color       	= $this->props['place_order_button_bg_color'];
		$place_order_button_icon       		= $this->props['place_order_button_icon'];
		$place_order_button_use_icon       		= $this->props['place_order_button_use_icon'];
		$place_order_button_icon_placement     = $this->props['place_order_button_icon_placement'];

		$this->add_classname('wcbd_module');
		$data = '';
		if( function_exists( 'is_checkout' ) && is_checkout() ){

			/**
			 * elements
			 */
			if( $show_coupon_form == 'off' ){
				$this->add_classname('checkout_no_coupoun');
			}
			if( $show_shipping_fields == 'off' ){
				$this->add_classname('checkout_no_shipping');
			}
			if( $show_order_summary == 'off' ){
				$this->add_classname('checkout_no_summary');
			}
			if( $show_order_notes == 'off' ){
				$this->add_classname('checkout_no_order_notes');
			}
			if( $fullwidth_billing_shipping == 'on' ){
				$this->add_classname('fullwidth_billing_shipping');
			}

			// order review title
			if( $show_order_review_title == 'on' ){
				add_action( 'woocommerce_checkout_order_review', array( $this, 'change_order_review_title' ), 1 );
			}
			/**
			 * Ship to a different address
			 */
			if( $check_ship_different_address == 'on' && $show_shipping_fields == 'on' ){
				add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
			}

			// fix floating issue
			add_action( 'woocommerce_checkout_after_order_review', array( 'WCBD_INIT', 'add_clearfix' ));

			ob_start();
			echo do_shortcode( '[woocommerce_checkout]' );
			$data = ob_get_clean();

			// customer details width
			if( $customer_details_width != '100%' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% #customer_details',
					'declaration' => "width: " . esc_attr( $customer_details_width ) . ";",
				) );
			}

			// order review section width
			if( $order_review_width != '100%' ){
				ET_Builder_Element::set_style( $render_slug, array(
					'selector' => '%%order_class%% #order_review',
					'declaration' => "width: " . esc_attr( $order_review_width ) . ";",
				) );
			}

			// customer details float
			if( $customer_details_float != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% #customer_details',
					'declaration' => "float: " . esc_attr( $customer_details_float ) . ";",
				) );
			}
			// order review float
			if( $order_review_float != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% #order_review',
					'declaration' => "float: " . esc_attr( $order_review_float ) . ";",
				) );
			}
			// customer details padding
			if( !empty( $customer_details_padding ) ){
				$m = explode( '|', $customer_details_padding );
				
				// top padding
				if( isset( $m[0] ) && $m[0] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #customer_details',
						'declaration' => "padding-top:". esc_attr( $m[0] ) ."!important;",
					) );					
				}
				
				// right padding
				if( isset( $m[1] ) && $m[1] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #customer_details',
						'declaration' => "padding-right:". esc_attr( $m[1] ) ."!important;",
					) );					
				}
				
				// bottom padding
				if( isset( $m[2] ) && $m[2] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #customer_details',
						'declaration' => "padding-bottom:". esc_attr( $m[2] ) ."!important;",
					) );					
				}
				
				// left padding
				if( isset( $m[3] ) && $m[3] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #customer_details',
						'declaration' => "padding-left:". esc_attr( $m[3] ) ."!important;",
					) );					
				}
			}

			// order review padding
			if( !empty( $order_review_padding ) ){
				$m = explode( '|', $order_review_padding );
				
				// top padding
				if( isset( $m[0] ) && $m[0] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #order_review',
						'declaration' => "padding-top:". esc_attr( $m[0] ) ."!important;",
					) );					
				}
				
				// right padding
				if( isset( $m[1] ) && $m[1] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #order_review',
						'declaration' => "padding-right:". esc_attr( $m[1] ) ."!important;",
					) );					
				}
				
				// bottom padding
				if( isset( $m[2] ) && $m[2] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #order_review',
						'declaration' => "padding-bottom:". esc_attr( $m[2] ) ."!important;",
					) );					
				}
				
				// left padding
				if( isset( $m[3] ) && $m[3] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% #order_review',
						'declaration' => "padding-left:". esc_attr( $m[3] ) ."!important;",
					) );					
				}
			}

			// Backgrounds
			if( !empty( $returning_customer_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "{$this->main_css_element} .woocommerce-form-login-toggle .woocommerce-info",
					'declaration' => "background:". esc_attr( $returning_customer_bg ) ." !important;"
				) );
			}
			if( !empty( $have_coupon_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "{$this->main_css_element} .woocommerce-form-coupon-toggle .woocommerce-info",
					'declaration' => "background:". esc_attr( $have_coupon_bg ) ." !important;"
				) );
			}
			if( !empty( $customer_details_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #customer_details",
					'declaration' => "background:". esc_attr( $customer_details_bg ) ." !important;"
				) );
			}
			if( !empty( $order_review_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #order_review",
					'declaration' => "background:". esc_attr( $order_review_bg ) ." !important;"
				) );
			}
			if( !empty( $order_summary_background ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #order_review .woocommerce-checkout-review-order-table, body.woocommerce-order-pay %%order_class%% #order_review .shop_table",
					'declaration' => "background:". esc_attr( $order_summary_background ) ." !important;"
				) );
			}
			if( !empty( $payments_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #payment",
					'declaration' => "background:". esc_attr( $payments_bg ) ." !important;"
				) );
			}
			if( !empty( $payment_desc_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #payment .payment_box",
					'declaration' => "background:". esc_attr( $payment_desc_bg ) ." !important;"
				) );
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% #payment div.payment_box::before",
					'declaration' => "border-bottom-color:". esc_attr( $payment_desc_bg ) ." !important;"
				) );
			}
			if( !empty( $inputs_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% form .form-row input, %%order_class%% form .form-row textarea, %%order_class%% .select2-container--default .select2-selection--single, %%order_class%% form .form-row select, .select2-dropdown",
					'declaration' => "background:". esc_attr( $inputs_bg ) ." !important;"
				) );
			}
			if( !empty( $required_icon_color ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% .form-row .required",
					'declaration' => "color:". esc_attr( $required_icon_color ) ." !important;"
				) );
			}

			/**
			 * Buttons
			 */

			// apply coupon button
			if( $show_coupon_form == 'on' ){
				WCBD_HELPERS::set_button_style( 
					array(
						'render_slug' => $render_slug, 
						'custom_button' => $apply_coupon_button_custom, 
						'button_use_icon' => $apply_coupon_button_use_icon, 
						'button_icon' => $apply_coupon_button_icon, 
						'button_icon_placement' => $apply_coupon_button_icon_placement, 
						'button_bg_color' => $apply_coupon_button_bg_color, 
						'button_selector' => "body #page-container %%order_class%% .woocommerce-form-coupon .button[name='apply_coupon']" 			
					)
				);		
			}	
			
			// login button
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $login_button_custom, 
					'button_use_icon' => $login_button_use_icon, 
					'button_icon' => $login_button_icon, 
					'button_icon_placement' => $login_button_icon_placement, 
					'button_bg_color' => $login_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .woocommerce-form-login .button[name='login']" 			
				)
			);

			// place order button
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $place_order_button_custom, 
					'button_use_icon' => $place_order_button_use_icon, 
					'button_icon' => $place_order_button_icon, 
					'button_icon_placement' => $place_order_button_icon_placement, 
					'button_bg_color' => $place_order_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% #payment #place_order" 			
				)
			);

			/**
			 * In case the module used more than once
			 */

			if( $check_ship_different_address == 'on' && $show_shipping_fields == 'on' ){
				remove_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
			}
			// order review title
			if( $show_order_review_title == 'on' ){
				remove_action( 'woocommerce_checkout_order_review', array( $this, 'change_order_review_title' ), 0 );
			}
		}
		return $data;

	}
}
new Divi_WC_Builder_Module_Checkout_Classic;
