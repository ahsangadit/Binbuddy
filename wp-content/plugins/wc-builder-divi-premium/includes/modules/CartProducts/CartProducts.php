<?php
/**
 * Cart products module
 * @since 2.2.0
 * @version 2.2.0
 */
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_Cart_Products extends ET_Builder_Module {

	public $vb_support = 'on';
	public static $module_used = false;

	function init() {
		$this->name         = esc_html__( 'Woo Cart Products', 'wc-divi-builder' );
    $this->slug         = 'et_pb_wcbd_cart_products';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'table_components' => esc_html__( 'Products Table Elements', 'wc-builder-divi' ),
					'extra_options' => esc_html__( 'Extra Options', 'wc-builder-divi' ),
					'empty_cart' => esc_html__( 'After Clearing The Cart', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'backgrounds' => esc_html__( 'Backgrounds', 'wc-builder-divi' ),
					'remove_icon' => esc_html__( 'Remove Product Icon', 'wc-builder-divi' ),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'table_head'   => array(
					'label'    => esc_html__( 'Table Head', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} table.shop_table thead tr th",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'product_title'   => array(
					'label'    => esc_html__( 'Product Title', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} table tbody .product-name, {$this->main_css_element} table tbody .product-name a",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'product_price'   => array(
					'label'    => esc_html__( 'Product Price', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} table tbody .product-price",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'quantity_input_text'   => array(
					'label'    => esc_html__( 'Quantity', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .quantity input.qty",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '2em',
					),
				),
				'subtotal'   => array(
					'label'    => esc_html__( 'Subtotal', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} table tbody .product-subtotal",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'apply_coupon_input_text' => array(
					'label' => esc_html__( 'Coupon Input', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} table #coupon_code, body.et_extra {$this->main_css_element} table #coupon_code, body.et_extra {$this->main_css_element} table #coupon_code::placeholder",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '2em',
					),
				),
				'empty_cart_text' => array(
					'label' => esc_html__( 'Empty Cart Message', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .cart-empty",
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
				'table' => array(
					'label_prefix' => esc_html__( 'Table', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-cart-form table.shop_table",
							'border_styles' => "%%order_class%% .woocommerce-cart-form table.shop_table",
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
				'table_th' => array(
					'label_prefix' => esc_html__( 'Table Head Cells', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-cart-form table.shop_table th",
							'border_styles' => "%%order_class%% .woocommerce-cart-form table.shop_table th",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'width' => '0px',
							'color' => 'rgba(0,0,0,.1)',
							'style' => 'solid',
						),
					),
				),
				'table_body_td' => array(
					'label_prefix' => esc_html__( 'Table Body Cells', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-cart-form table.shop_table td",
							'border_styles' => "%%order_class%% .woocommerce-cart-form table.shop_table td",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'width' => '0px',
							'color' => 'rgba(0,0,0,.1)',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow' => array(
				'default' => array(
					'label' => esc_html__( 'Module Box Shadow', 'wc-builder-divi' ),
				),
				'table' => array(
					'label' => esc_html__( 'Table Box Shadow', 'wc-builder-divi' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-cart-form table.shop_table",
						'custom_style' => true,
					)
				)
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'apply_coupon_button' => array(
					'label' => esc_html__( 'Apply Coupon Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .coupon .button[name='apply_coupon']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .coupon .button[name='apply_coupon']",
						),
					),
				),
				'update_cart_button' => array(
					'label' => esc_html__( 'Update Cart Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .button[name='update_cart']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "{$this->main_css_element} .button[name='update_cart']",
						),
					),
				),
				'return_to_shop_button' => array(
					'label' => esc_html__( 'Return To Shop Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .return-to-shop a.button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "{$this->main_css_element} .return-to-shop a.button",
						),
					),
				),
			),
		);
		$this->custom_css_fields = array(
			'products_table' => array(
				'label'    => esc_html__( 'Products Table', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table",
			),
			'table_head' => array(
				'label'    => esc_html__( 'Table Head', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table thead",
			),
			'table_body' => array(
				'label'    => esc_html__( 'Table Body', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody",
			),
			'table_rows' => array(
				'label'    => esc_html__( 'Table Rows', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody tr",
			),
			'product_remove' => array(
				'label'    => esc_html__( 'Remove Icon', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-remove",
			),
			'product_thumbnail' => array(
				'label'    => esc_html__( 'Product Thumbnail', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-thumbnail img",
			),
			'product_name' => array(
				'label'    => esc_html__( 'Product Title', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-name, %%order_class%% .woocommerce-cart-form table.shop_table tbody .product-name a",
			),
			'product_price' => array(
				'label'    => esc_html__( 'Product Price', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-price",
			),
			'product_quantity' => array(
				'label'    => esc_html__( 'Product Quantity', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-quantity input.qty",
			),
			'product_subtotal' => array(
				'label'    => esc_html__( 'Product Subtotal', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .product-subtotal",
			),
			'coupoun_input' => array(
				'label'    => esc_html__( 'Coupon Input', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-cart-form table.shop_table tbody .coupon #coupon_code",
			),
		);
	}

	function get_fields() {
		$fields = array(
 			'enable_x_icon' => array(
				'label' => esc_html__( 'Remove Product Icon', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show "remove product x" icon.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
				'affects' => array(
					'x_icon_color',
					'x_icon_hvr_bg_color',
				),
			),
			'enable_product_thumb' => array(
				'label' => esc_html__( 'Product Thumbnail', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Product Thumbnail.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
				'affects' => array(
					'thumb_size',
				)
			),
			'enable_title' => array(
				'label' => esc_html__( 'Product Title', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Product Title column.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
			),
			'enable_price' => array(
				'label' => esc_html__( 'Product Price', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Product Price column.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
			),
			'enable_quantity' => array(
				'label' => esc_html__( 'Product Quantity', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Product Quantity column.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
				'affects' => array(
					'quantity_bg',
				),
			),
			'enable_subtotal' => array(
				'label' => esc_html__( 'Product Subtotal Price', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Total Price column.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
			),
			'enable_apply_coupon' => array(
				'label' => esc_html__( 'Apply a Coupon', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Apply a Coupon input & button.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
				'affects' => array(
					'apply_coupon_input_bg',
				),
			),
			'enable_update_cart_button' => array(
				'label' => esc_html__( 'Update Cart Button', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Hide/Show Update Cart Button.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'table_components',
			),
			'empty_cart_message' => array(
				'label' => esc_html__( 'Empty Cart Message', 'wc-divi-builder' ),
				'type' => 'text',
				'description' => esc_html__( 'This message will appear if the client removed all products from the cart.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'empty_cart',
				'default' => esc_html__( 'Your cart is currently empty.', 'woocommerce' ),
			),
			'thumb_size' => array(
				'label' => esc_html__( 'Thumbnail Size', 'wc-builder-divi' ),
				'type' => 'select',
				'options' => array(
					'32' => esc_html__( 'Default', 'wc-builder-divi' ),
					'100' => esc_html__( 'Small', 'wc-builder-divi' ),
					'200' => esc_html__( 'Medium', 'wc-builder-divi' ),
					'300' => esc_html__( 'Large', 'wc-builder-divi' ),
				),
				'default' => '32',
				'description' => esc_html__( 'The width of Default: 32px, Small: 100px, Medium: 200px & Large: 300px', 'wc-builder-divi' ),
				'show_if' => array(
					'enable_product_thumb' => 'on',
				),
				'tab_slug' => 'general',
				'toggle_slug' => 'extra_options',
			),
			'product_link' => array(
				'label' => esc_html__( 'Product Link', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Enable or disable the product link on the product title and thumbnail.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'extra_options',
			),
			'after_clearing_cart_action' => array(
				'label' => esc_html__( 'Action', 'wc-divi-builder' ),
				'type' => 'select',
				'options' => array(
					'stay' => esc_html__( 'Stay on the page', 'wc-divi-builder' ),
					'reload' => esc_html__( 'Reload the page', 'wc-divi-builder' ),
					'redirect_to_url' => esc_html__( 'Redirect to URL', 'wc-divi-builder' ),
				),
				'default' => 'stay',
				'description' => esc_html__( 'If the customer removes all products from the cart: stay on the page, reload the page and display the empty cart layout ( set under the plugin settings page ) Or redirect to a specific page.', 'wc-divi-builder' ),
				'toggle_slug' => 'empty_cart',
				'tab_slug' => 'general',
				'affects' => array(
					'redirection_url',
				),
			),
			'redirection_url' => array(
				'label' => esc_html__( 'URL', 'wc-divi-builder' ),
				'type' => 'text',
				'tab_slug' => 'general',
				'toggle_slug' => 'empty_cart',
				'depends_show_if' => 'redirect_to_url',
			),
			'show_return_to_shop_button' => array(
				'label' => esc_html__( 'Return to shop button', 'wc-divi-builder' ),
				'type' => 'yes_no_button',
				'options' => array(
					'on' => esc_html__( 'Yes', 'wc-divi-builder' ),
					'off' => esc_html__( 'No', 'wc-divi-builder' ),
				),
				'default' => 'on',
				'show_if' => array(
					'after_clearing_cart_action' => 'stay',
				),
				'tab_slug' => 'general',
				'toggle_slug' => 'empty_cart',
			),
			'table_head_bg' => array(
				'label' => esc_html__( 'Table Head', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
			),
			'table_body_bg_odd' => array(
				'label' => esc_html__( 'Table Odd Rows', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
			),
			'table_body_bg_even' => array(
				'label' => esc_html__( 'Table Even Rows', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
			),
			'quantity_bg' => array(
				'label' => esc_html__( 'Quantity Input', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
				'depends_show_if' => 'on',
			),
			'x_icon_color' => array(
				'label' => esc_html__( 'Icon Color', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'default' => '#ff0000',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'remove_icon',
				'depends_show_if' => 'on',
			),
			'x_icon_hvr_bg_color' => array(
				'label' => esc_html__( 'Icon Hover Background Color', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'default' => '#ff0000',
				'toggle_slug' => 'remove_icon',
				'depends_show_if' => 'on',
			),
			'apply_coupon_input_bg' => array(
				'label' => esc_html__( 'Coupon Input', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
				'depends_show_if' => 'on',
			),
		);

		return $fields;
	}
	function render( $attrs, $content = null, $render_slug ) {

		// only use the module once to prevent ajax problems
		//static $n = 0; 
		//$n++;
		//if( $n >= 2 ) return '';
		//if( $this->render_count() > 0 ) return;

		$enable_x_icon 				= $this->props['enable_x_icon'];
		$enable_product_thumb 		= $this->props['enable_product_thumb'];
		$enable_title 				= $this->props['enable_title'];
		$enable_price 				= $this->props['enable_price'];
		$enable_quantity 			= $this->props['enable_quantity'];
		$enable_subtotal 			= $this->props['enable_subtotal'];
		$enable_apply_coupon 		= $this->props['enable_apply_coupon'];
		$enable_update_cart_button 	= $this->props['enable_update_cart_button'];

		$after_clearing_cart_action 	= $this->props['after_clearing_cart_action'];	
		$empty_cart_message 			= $this->props['empty_cart_message'];
		$redirection_url 				= $this->props['redirection_url'];
		$show_return_to_shop_button 				= $this->props['show_return_to_shop_button'];

		$table_head_bg 				= $this->props['table_head_bg'];
		$table_body_bg_odd 				= $this->props['table_body_bg_odd'];
		$table_body_bg_even 				= $this->props['table_body_bg_even'];
		$quantity_bg 				= $this->props['quantity_bg'];
		$apply_coupon_input_bg 		= $this->props['apply_coupon_input_bg'];

		$x_icon_color 				= $this->props['x_icon_color'];
		$x_icon_hvr_bg_color 		= $this->props['x_icon_hvr_bg_color'];

		$thumb_size = $this->props['thumb_size'];
		$product_link = $this->props['product_link'];


		$apply_coupon_button_custom        		= $this->props['custom_apply_coupon_button'];
		$apply_coupon_button_bg_color       	= $this->props['apply_coupon_button_bg_color'];
		$apply_coupon_button_use_icon       		= $this->props['apply_coupon_button_use_icon'];
		$apply_coupon_button_icon       		= $this->props['apply_coupon_button_icon'];
		$apply_coupon_button_icon_placement     = $this->props['apply_coupon_button_icon_placement'];

		$update_cart_button_custom        		= $this->props['custom_update_cart_button'];
		$update_cart_button_bg_color       		= $this->props['update_cart_button_bg_color'];
		$update_cart_button_icon       			= $this->props['update_cart_button_icon'];
		$update_cart_button_use_icon       			= $this->props['update_cart_button_use_icon'];
		$update_cart_button_icon_placement      = $this->props['update_cart_button_icon_placement'];

		$return_to_shop_button_custom   		= $this->props['custom_return_to_shop_button'];
		$return_to_shop_button_bg_color 		= $this->props['return_to_shop_button_bg_color'];
		$return_to_shop_button_icon     		= $this->props['return_to_shop_button_icon'];
		$return_to_shop_button_use_icon     		= $this->props['return_to_shop_button_use_icon'];
		$return_to_shop_button_icon_placement 	= $this->props['return_to_shop_button_icon_placement'];

		if( is_admin() || !function_exists( 'is_cart' ) || !is_cart() ){
			return;
		}
		$module_classes = array( 'wcbd_module' );
		
		$data = '';

		// cleaning unwanted stuff
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals' );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

		/**
		 * Remove items
		 */
		if( $enable_x_icon == 'off' ){
			//add_filter( 'woocommerce_cart_item_remove_link', '__return_false' );
			$module_classes[] = 'no_x_icon';
		}
		if( $enable_product_thumb == 'off' ){
			//add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );
			$module_classes[] = 'no_thumb';
		}
		if( $enable_title  == 'off' ){
			//add_filter( 'woocommerce_cart_item_name', '__return_false' );
			$module_classes[] = 'no_title';
		}
		if( $enable_price == 'off' ){
			//add_filter( 'woocommerce_cart_item_price', '__return_false' );
			$module_classes[] = 'no_price';
		}
		if( $enable_quantity == 'off' ){
			//add_filter( 'woocommerce_cart_item_quantity', '__return_false' );
			$module_classes[] = 'no_qty';
		}
		if( $enable_subtotal == 'off' ){
			//add_filter( 'woocommerce_cart_item_subtotal', '__return_false' );
			$module_classes[] = 'no_subtotal';
		}
		if( $enable_apply_coupon == 'off' ){
			//add_filter( 'woocommerce_coupons_enabled', '__return_false' );
			$module_classes[] = 'no_coupon';
		}
		if( $enable_update_cart_button == 'off' ){
			$module_classes[] = 'no_update_cart';
		}

		/**
		 * Thumbnail size
		 */
		if( $enable_product_thumb == 'on' && is_numeric( $thumb_size ) && (int)$thumb_size > 0 && $thumb_size != '32' ){
			$module_classes[] = 'wcbd_cart_thumb_' . (int)$thumb_size;
		}

		/**
		 * Product Link
		 */
		if( $product_link == 'off' && ( $enable_product_thumb == 'on' || $enable_title  == 'on' ) ){
			add_filter( 'woocommerce_cart_item_permalink', '__return_false' );
		}
	
		/**
		 * Get the cart
		 */
		ob_start();
		echo do_shortcode( '[woocommerce_cart]' );
		$data .= ob_get_clean();

		/**
		 * Return removed things back in case the module used twice 
		 */
		if( $product_link == 'off' && ( $enable_product_thumb == 'on' || $enable_title  == 'on' ) ){
			remove_filter( 'woocommerce_cart_item_permalink', '__return_false' );
		}

		// empty cart message
		if( !empty( esc_attr( $empty_cart_message ) ) ){
			$data .= "<p class='empty_cart_message'>". esc_attr( $empty_cart_message ) ."</p>";
		}

		// return to shop button
		if( $after_clearing_cart_action == 'stay' ){
			$module_classes[] = 'stay';

			if ( wc_get_page_id( 'shop' ) > 0 ) :
				ob_start();
				?>
					<p class="return-to-shop">
						<a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
							<?php esc_html_e( 'Return to shop', 'woocommerce' ); ?>
						</a>
					</p>
			<?php 
			$data .= ob_get_clean();	
		endif;
		}

		if( $after_clearing_cart_action == 'reload' ){
			$module_classes[] = 'reload';
		}

		if( $after_clearing_cart_action == 'redirect_to_url' && !empty( esc_attr( $redirection_url ) ) ){
			$module_classes[] = 'redirect';
			$data .= '<span class="empty_cart_content redirection_url" data-redirect-to="'. esc_url( $redirection_url ) .'"></span>';
		}

		// Backgrounds
		if( !empty( $table_head_bg ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} table thead",
				'declaration' => "background:". esc_attr( $table_head_bg ) .";"
			) );
		}
		if( !empty( $table_body_bg_odd ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} table tbody tr:nth-child(odd)",
				'declaration' => "background:". esc_attr( $table_body_bg_odd ) .";"
			) );
		}
		if( !empty( $table_body_bg_even ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} table tbody tr:nth-child(even)",
				'declaration' => "background:". esc_attr( $table_body_bg_even ) .";"
			) );
		}
		if( !empty( $quantity_bg ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} .quantity input.qty",
				'declaration' => "background:". esc_attr( $quantity_bg ) ."!important;"
			) );
		}
		if( !empty( $apply_coupon_input_bg ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} #coupon_code",
				'declaration' => "background:". esc_attr( $apply_coupon_input_bg ) ."!important;"
			) );
		}

		// Remove Icon Color
		if( $enable_x_icon == 'on' ){
			if( !empty( $x_icon_color ) ){
				self::set_style( $render_slug, array(
					'selector' => "{$this->main_css_element} a.remove",
					'declaration' => "color:". esc_attr( $x_icon_color ) ."!important;"
				) );
			}

			if( !empty( $x_icon_hvr_bg_color ) ){
					self::set_style( $render_slug, array(
						'selector' => "{$this->main_css_element} a.remove:hover",
						'declaration' => "background:". esc_attr( $x_icon_hvr_bg_color ) ."!important;"
				) );
			}
		}

		/**
		 * Buttons
		 */
		
		// apply coupon button
		if( $enable_apply_coupon == 'on' ){
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $apply_coupon_button_custom, 
					'button_use_icon' => $apply_coupon_button_use_icon, 
					'button_icon' => $apply_coupon_button_icon, 
					'button_icon_placement' => $apply_coupon_button_icon_placement, 
					'button_bg_color' => $apply_coupon_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .coupon .button[name='apply_coupon']" 			
				)
			);						
		}	

		// update cart button
		if( $enable_update_cart_button == 'on' ){
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $update_cart_button_custom, 
					'button_use_icon' => $update_cart_button_use_icon, 
					'button_icon' => $update_cart_button_icon, 
					'button_icon_placement' => $update_cart_button_icon_placement, 
					'button_bg_color' => $update_cart_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .button[name='update_cart']" 			
				)
			);			
		}
		// return to shop button
		WCBD_HELPERS::set_button_style( 
			array(
				'render_slug' => $render_slug, 
				'custom_button' => $return_to_shop_button_custom, 
				'button_use_icon' => $return_to_shop_button_use_icon, 
				'button_icon' => $return_to_shop_button_icon, 
				'button_icon_placement' => $return_to_shop_button_icon_placement, 
				'button_bg_color' => $return_to_shop_button_bg_color, 
				'button_selector' => "body #page-container %%order_class%% .return-to-shop a.button" 			
			)
		);

		self::$module_used = true;

		$this->add_classname( $module_classes );
		return $data;
	}
}
new Divi_WC_Builder_Module_Cart_Products;
