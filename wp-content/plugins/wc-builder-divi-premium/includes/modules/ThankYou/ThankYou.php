<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_ThankYou extends ET_Builder_Module {

	public $vb_support = 'on';

    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	function init() {
		$this->name       = esc_html__( 'Woo Thank You', 'wc-builder-divi' );
		$this->slug       = 'et_pb_wcbd_thankyou';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'wc-builder-divi' ),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts'                 => array(
				'order_received'   => array(
					'label'    => esc_html__( 'Order Received', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce-thankyou-order-received",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'order_failed'   => array(
					'label'    => esc_html__( 'Order Failed', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce-thankyou-order-failed",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'order_details'   => array(
					'label'    => esc_html__( 'Order Details', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce-thankyou-order-details",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'headers'   => array(
					'label'    => esc_html__( 'Headers', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% h2, %%order_class%% h3",
						'important' => 'all',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '26px',
					),
				),
				'p'   => array(
					'label'    => esc_html__( 'Paragraph', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} p:not(.form-row):not(.woocommerce-customer-details--phone):not(.woocommerce-customer-details--email):not(.order-again):not(.woocommerce-notice)",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'table_head'   => array(
					'label'    => esc_html__( 'Table Head', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .shop_table th",
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'table_body'   => array(
					'label'    => esc_html__( 'Table Body', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .shop_table td:not(.download-file), {$this->main_css_element} .shop_table td:not(.download-file) a",
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
				'address'   => array(
					'label'    => esc_html__( 'Address', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce-customer-details address, {$this->main_css_element} .woocommerce-customer-details p",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
				),
			),
			'background'            => array(
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
							'border_radii'  => "%%order_class%% table.shop_table, %%order_class%% .woocommerce-customer-details address",
							'border_styles' => "%%order_class%% table.shop_table, %%order_class%% .woocommerce-customer-details address",
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
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'pay_button' => array(
					'label' => esc_html__( 'Pay Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-thankyou-order-failed-actions .button:first-child",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .woocommerce-thankyou-order-failed-actions .button:first-child",
						),
					),
				),
				'myaccount_button' => array(
					'label' => esc_html__( 'My Account Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-thankyou-order-failed-actions .button:nth-child(2)",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .woocommerce-thankyou-order-failed-actions .button:nth-child(2)",
						),
					),
				),
				'download_button' => array(
					'label' => esc_html__( 'Download Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .download-file .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .download-file .button",
						),
					),
				),
				'order_again_button' => array(
					'label' => esc_html__( 'Order Again Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .order-again .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .order-again .button",
						),
					),
				),
			),
		);
	}

	function render( $attrs, $content = null, $render_slug ) {

		$this->add_classname( 'wcbd_module' );
		$data = '';
		if( function_exists( 'is_order_received_page' ) && is_order_received_page() ){

			$pay_button_custom        		= $this->props['custom_pay_button'];
			$pay_button_bg_color       		= $this->props['pay_button_bg_color'];
			$pay_button_icon       			= $this->props['pay_button_icon'];
			$pay_button_use_icon       			= $this->props['pay_button_use_icon'];
			$pay_button_icon_placement    	= $this->props['pay_button_icon_placement'];

			$myaccount_button_custom        	= $this->props['custom_myaccount_button'];
			$myaccount_button_bg_color       	= $this->props['myaccount_button_bg_color'];
			$myaccount_button_icon       		= $this->props['myaccount_button_icon'];
			$myaccount_button_use_icon       		= $this->props['myaccount_button_use_icon'];
			$myaccount_button_icon_placement    = $this->props['myaccount_button_icon_placement'];
			$pay_button_icon_placement    	= $this->props['pay_button_icon_placement'];

			$download_button_custom        	= $this->props['custom_download_button'];
			$download_button_bg_color       	= $this->props['download_button_bg_color'];
			$download_button_icon       		= $this->props['download_button_icon'];
			$download_button_use_icon       		= $this->props['download_button_use_icon'];
			$download_button_icon_placement    = $this->props['download_button_icon_placement'];

			$order_again_button_custom        	= $this->props['custom_order_again_button'];
			$order_again_button_bg_color       	= $this->props['order_again_button_bg_color'];
			$order_again_button_icon       		= $this->props['order_again_button_icon'];
			$order_again_button_use_icon       		= $this->props['order_again_button_use_icon'];
			$order_again_button_icon_placement    = $this->props['order_again_button_icon_placement'];

			// text orientation class
			$text_orientation = isset( $this->props['text_orientation'] ) ? esc_attr( $this->props['text_orientation'] ) : '';
			if( $text_orientation ){
				$this->add_classname( "et_pb_text_align_{$text_orientation}" );
			}
			/**
			 * Pay Button
			 * Appears if the order is failed
			 */
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $pay_button_custom, 
					'button_use_icon' => $pay_button_use_icon, 
					'button_icon' => $pay_button_icon, 
					'button_icon_placement' => $pay_button_icon_placement, 
					'button_bg_color' => $pay_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .woocommerce-thankyou-order-failed-actions .button:first-child" 			
				)
			);

			/**
			 * My Account Button
			 * Appears if the order is failed
			 */
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $myaccount_button_custom, 
					'button_use_icon' => $myaccount_button_use_icon, 
					'button_icon' => $myaccount_button_icon, 
					'button_icon_placement' => $myaccount_button_icon_placement, 
					'button_bg_color' => $myaccount_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .woocommerce-thankyou-order-failed-actions .button:nth-child(2)" 			
				)
			);

			/**
			 * Download Button
			 * Appears if the order has downloadable products
			 */
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $download_button_custom, 
					'button_use_icon' => $download_button_use_icon, 
					'button_icon' => $download_button_icon, 
					'button_icon_placement' => $download_button_icon_placement, 
					'button_bg_color' => $download_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .download-file .button" 			
				)
			);

			/**
			 * order again button
			 * This button appears if the order is completed
			 */
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $order_again_button_custom, 
					'button_use_icon' => $order_again_button_use_icon, 
					'button_icon' => $order_again_button_icon, 
					'button_icon_placement' => $order_again_button_icon_placement, 
					'button_bg_color' => $order_again_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .order-again .button" 			
				)
			);

			ob_start();
			echo do_shortcode( '[woocommerce_checkout]' );
			$data = ob_get_clean();
		}
		return $data;

	}
}
new Divi_WC_Builder_Module_ThankYou;
