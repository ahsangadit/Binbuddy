<?php
/**
 * Cart totals module
 * @since 2.2.0
 * @version 2.2.0
 */
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_Cart_Totals extends ET_Builder_Module {

	public $vb_support = 'on';

	function init() {
		$this->name         = esc_html__( 'Woo Cart Totals', 'wc-divi-builder' );
        $this->slug         = 'et_pb_wcbd_cart_totals';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'wc-divi-builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'backgrounds' => esc_html__( 'Backgrounds', 'wc-builder-divi' ),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'title'   => array(
					'label'    => esc_html__( 'Module Title', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2.module-title",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '23px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'table_head'   => array(
					'label'    => esc_html__( 'Table Head', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "%%order_class%% .cart-collaterals table.shop_table tbody th, %%order_class%% table.shop_table_responsive tr td::before",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'table_body'   => array(
					'label'    => esc_html__( 'Table Body', 'wc-divi-builder' ),
					'css'      => array(
						'main' => "%%order_class%% .cart-collaterals table.shop_table tbody tr td, %%order_class%% .cart-collaterals table.shop_table tbody tr p, %%order_class%% .cart-collaterals table.shop_table tbody tr strong, %%order_class%% ul#shipping_method .amount",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
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
							'border_radii'  => "%%order_class%% .cart-collaterals table.shop_table",
							'border_styles' => "%%order_class%% .cart-collaterals table.shop_table",
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
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'box_shadow' => array(
				'default' => array(
					'label' => esc_html__( 'Module Box Shadow', 'wc-builder-divi' ),
				),
				'table' => array(
					'label' => esc_html__( 'Table Box Shadow', 'wc-builder-divi' ),
					'css' => array(
						'main' => "%%order_class%% .cart-collaterals table.shop_table",
						'custom_style' => true,
					)
				)
			),
			'button' => array(
				'shipping_button' => array(
					'label' => esc_html__( 'Calculate Shipping Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .shipping-calculator-form .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "{$this->main_css_element} .shipping-calculator-form .button",
						),
					),
				),
				'checkout_button' => array(
					'label' => esc_html__( 'Proceed To Checkout Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .wc-proceed-to-checkout a.checkout-button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "{$this->main_css_element} .wc-proceed-to-checkout a.checkout-button",
						),
					),
				),
			),
		);
		$this->custom_css_fields = array(
			'module_title' => array(
				'label'    => esc_html__( 'Module Title', 'wc-builder-divi' ),
				'selector' => "%%order_class%% h2.module-title",
			),
			'products_table' => array(
				'label'    => esc_html__( 'Products Table', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart-collaterals table.shop_table",
			),
			'table_head' => array(
				'label'    => esc_html__( 'Table Head', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart-collaterals table.shop_table tbody th, %%order_class%% table.shop_table_responsive tr td::before",
			),
			'table_body' => array(
				'label'    => esc_html__( 'Table Body', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart-collaterals table.shop_table tbody tr td, %%order_class%% .cart-collaterals table.shop_table tbody tr p",
			),
			'shipping_inputs' => array(
				'label'    => esc_html__( 'Shipping Inputs', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-shipping-calculator .input-text, %%order_class%% .woocommerce-shipping-calculator #calc_shipping_country",
			),
			'total_price' => array(
				'label'    => esc_html__( 'Total Price', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .order-total",
			),
		);
	}

	function get_fields() {
		$fields = array(
			'enable_module_title' => array(
				'label' => esc_html__( 'Module Title', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'options' => array(
					'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Display/Hide the module title.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'module_title_text' => array(
				'label' => esc_html__( 'Module Title', 'wc-builder-divi' ),
				'type' => 'text',
				'default' => __( 'Cart totals', 'woocommerce' ),
				'description' => esc_html__( 'The module default title: Cart totals.', 'wc-divi-builder' ),
				'show_if' => array(
					'enable_module_title' => 'on',
				),
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'enable_checkout_button' => array(
				'label' => esc_html__( 'Checkout button', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'options' => array(
					'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'description' => esc_html__( 'Show/Hide the proceed to checkout button.', 'wc-divi-builder' ),
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'table_head_bg' => array(
				'label' => esc_html__( 'Table Head Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
			),
			'table_body_bg' => array(
				'label' => esc_html__( 'Table Body Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'backgrounds',
			),
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {

		if( is_admin() || !function_exists( 'is_cart' ) || !is_cart() ){
			return;
		}
		$output 				= '';
		$enable_module_title 	= $this->props['enable_module_title'];
		$module_title_text 		= $this->props['module_title_text'];
		$enable_checkout_button 		= $this->props['enable_checkout_button'];

		$table_head_bg 				= $this->props['table_head_bg'];
		$table_body_bg 				= $this->props['table_body_bg'];

		$shipping_button_custom        		= $this->props['custom_shipping_button'];
		$shipping_button_bg_color       	= $this->props['shipping_button_bg_color'];
		$shipping_button_use_icon       		= $this->props['shipping_button_use_icon'];
		$shipping_button_icon       		= $this->props['shipping_button_icon'];
		$shipping_button_icon_placement     = $this->props['shipping_button_icon_placement'];

		$checkout_button_custom        		= $this->props['custom_checkout_button'];
		$checkout_button_bg_color       	= $this->props['checkout_button_bg_color'];
		$checkout_button_use_icon       		= $this->props['checkout_button_use_icon'];
		$checkout_button_icon       		= $this->props['checkout_button_icon'];
		$checkout_button_icon_placement     = $this->props['checkout_button_icon_placement'];

		$this->add_classname('wcbd_module');
		if( WC()->cart->is_empty() ){
			return '';
		}
		// text orientation class
		$text_orientation = isset( $this->props['text_orientation'] ) ? esc_attr( $this->props['text_orientation'] ) : '';
		if( $text_orientation ){
			$this->add_classname( "et_pb_text_align_{$text_orientation}" );
		}
		
		if( !Divi_WC_Builder_Module_Cart_Products::$module_used ){
			// call the cart shortcode to calculate shipping and check for valid products
			do_shortcode( '[woocommerce_cart]' );
		}
		
		// procedd to checkout button
		if( $enable_checkout_button == 'off' ){
			remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
			$this->add_classname('wcbd_no_checkout_button');
		}
		ob_start();
		woocommerce_cart_totals();
		$totals = ob_get_clean();

		if( $totals && $enable_module_title == 'on' && !empty( $module_title_text ) ){
			$output .= "<h2 class='module-title'>". esc_html( $module_title_text ) ."</h2>";
		}		

		$output .= $totals;

		// Backgrounds
		if( !empty( $table_head_bg ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} .cart-collaterals table.shop_table tbody th",
				'declaration' => "background:". esc_attr( $table_head_bg ) .";"
			) );
		}
		if( !empty( $table_body_bg ) ){
			self::set_style( $render_slug, array(
				'selector' => "{$this->main_css_element} .cart-collaterals table.shop_table tbody tr",
				'declaration' => "background:". esc_attr( $table_body_bg ) .";"
			) );
		}

		// shipping button
		WCBD_HELPERS::set_button_style( 
			array(
				'render_slug' => $render_slug, 
				'custom_button' => $shipping_button_custom, 
				'button_use_icon' => $shipping_button_use_icon, 
				'button_icon' => $shipping_button_icon, 
				'button_icon_placement' => $shipping_button_icon_placement, 
				'button_bg_color' => $shipping_button_bg_color, 
				'button_selector' => "body #page-container %%order_class%% .shipping-calculator-form .button" 			
			)
		);

		// checkout button
		WCBD_HELPERS::set_button_style( 
			array(
				'render_slug' => $render_slug, 
				'custom_button' => $checkout_button_custom, 
				'button_use_icon' => $checkout_button_use_icon, 
				'button_icon' => $checkout_button_icon, 
				'button_icon_placement' => $checkout_button_icon_placement, 
				'button_bg_color' => $checkout_button_bg_color, 
				'button_selector' => "body #page-container %%order_class%% .wc-proceed-to-checkout a.checkout-button" 			
			)
		);

		// in case the module used twice
		if( $enable_checkout_button == 'off' ){
			add_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		}

		return '<div class="woocommerce"><div class="cart-collaterals">'. $output .'</div></div>';	
	}
}
new Divi_WC_Builder_Module_Cart_Totals;
