<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class ET_Builder_Module_WooPro_AddToCart extends ET_Builder_Module {

	public static $button_text, $p;
	public static function change_button_text( $btn_text ){
		if( !empty( self::$button_text ) ){
			$btn_text = esc_attr( self::$button_text );
		}
		return $btn_text;
	}

	public $vb_support = 'on';
	
	protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
	
	function init() {
		$this->name       = esc_html__( 'Woo Add To Cart Button', 'wc-builder-divi' );
		$this->slug       = 'et_pb_woopro_add_to_cart';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'add_to_cart_button' => esc_html__( 'Add to Cart Button', 'wc-builder-divi' ),
					'quantity_options' => esc_html__( 'Quantity Field', 'wc-builder-divi' ),
					'variations' => esc_html__( 'Variations', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'quantity_input' => esc_html__( 'Quantity Field', 'wc-builder-divi' ),
					'quantity_plus_minus' => esc_html__( '(+/-) Buttons', 'wc-builder-divi' ),
					'drop_down_menus' => esc_html__( 'Drop-down menus', 'wc-builder-divi' ),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
		$this->fields_defaults = array(
			'show_quantity' 		=> array( 'on' ),
			'hide_variation_price' 	=> array( 'off' ),
		);

		$this->advanced_fields = array(
			'fonts' => array(
				'quantity_input'   => array(
					'label'    => esc_html__( 'Quantity', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"body.woocommerce {$this->main_css_element} .quantity input.qty, {$this->main_css_element} .quantity input.qty",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '2em',
					),
				),
				'variation_description'   => array(
					'label'    => esc_html__( 'Variation Description', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"body.woocommerce {$this->main_css_element} .woocommerce-variation-description, {$this->main_css_element} .woocommerce-variation-description",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.3em',
					),
				),
				'variation_prices'   => array(
					'label'    => esc_html__( 'Variation Price', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"body.woocommerce {$this->main_css_element} .woocommerce-variation-price, body.woocommerce {$this->main_css_element} .woocommerce-variation-price .price, {$this->main_css_element} .woocommerce-variation-price, {$this->main_css_element} .woocommerce-variation-price .price",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '17px',
					),
					'line_height' => array(
						'default' => '1.3em',
					),
				),
				'variations_labels'   => array(
					'label'    => esc_html__( 'Variations Labels', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"{$this->main_css_element} .variations .label label",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.3em',
					),
				),
				'drop_down_menus'   => array(
					'label'    => esc_html__( 'Drop-down Menus', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"%%order_class%% .woocommerce .product form.cart .variations td select, %%order_class%% form.cart .variations td select, %%order_class%% select",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '12px',
					),
					'line_height' => array(
						'default' => '1.3em',
					),
				),
				'in_stock'   => array(
					'label'    => esc_html__( 'In Stock', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"{$this->main_css_element} .stock.in-stock",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'out_stock'   => array(
					'label'    => esc_html__( 'Out Of Stock', 'wc-builder-divi' ),
					'css'      => array(
						'main' => array(
							"{$this->main_css_element} .stock.out-of-stock",
						),
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders' => array(
				'default' => array(),
				'quantity_field' => array(
					'css'             => array(
						'main' => array(
							'border_radii' => "body.et-fb %%order_class%% .cart .quantity input.qty, body.woocommerce %%order_class%% .cart .quantity input.qty",
							'border_styles' => "body.et-fb %%order_class%% .cart .quantity input.qty, body.woocommerce %%order_class%% .cart .quantity input.qty",
						),
						'important' => 'all',
					),
					'defaults' => array(
						'border_radii' => 'on|0|0|0|0',
						'border_styles' => array(
							'width' => '',
							'color' => '#ababab',
							'style' => 'solid',
						),
					),
					'label_prefix'    => esc_html__( 'Quantity', 'wc-builder-divi' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'quantity_input',
				),
				'plus_border' => array(
					'css'             => array(
						'main' => array(
							'border_radii' => "%%order_class%%.qty-pm .quantity-up, body.et-fb %%order_class%% .qty-pm .quantity-up",
							'border_styles' => "%%order_class%%.qty-pm .quantity-up, body.et-fb %%order_class%% .qty-pm .quantity-up",
						),
						'important' => 'all',
					),
					'defaults' => array(
						'border_radii' => 'on|0|0|0|0',
						'border_styles' => array(
							'width' => '',
							'color' => '#ababab',
							'style' => 'solid',
						),
					),
					'label_prefix'    => esc_html__( '(+)', 'wc-builder-divi' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'quantity_plus_minus',
				),
				'minus_border' => array(
					'css'             => array(
						'main' => array(
							'border_radii' => "%%order_class%%.qty-pm .quantity-down, body.et-fb %%order_class%% .qty-pm .quantity-down",
							'border_styles' => "%%order_class%%.qty-pm .quantity-down, body.et-fb %%order_class%% .qty-pm .quantity-down",
						),
						'important' => 'all',
					),
					'defaults' => array(
						'border_radii' => 'on|0|0|0|0',
						'border_styles' => array(
							'width' => '',
							'color' => '#ababab',
							'style' => 'solid',
						),
					),
					'label_prefix'    => esc_html__( '(-)', 'wc-builder-divi' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'quantity_plus_minus',
				),
				'dropdow_menus_border' => array(
					'css'             => array(
						'main' => array(
							'border_styles' => ".woocommerce .product %%order_class%% form.cart .variations td select, %%order_class%% select, %%order_class%% .woocommerce .product form.cart .variations td select",
						),
						'important' => 'all',
					),
					'defaults' => array(
						'border_styles' => array(
							'width' => '0px',
							'color' => '',
							'style' => '',
						),
					),
					'use_radius'   => false,
					'label_prefix'    => esc_html__( 'Drop-down Menu', 'wc-builder-divi' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'drop_down_menus',
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'wc-builder-divi' ),
					'css' => array(
						'main' => "{$this->main_css_element} .cart .button",
						'important' => 'all',
					),
					'box_shadow'  => array(
						'css' => array(
							'main' => 'body #page-container %%order_class%% .cart .button',
						),
					),
				),
			),
		);
		$this->custom_css_fields = array(
			'quantity_container' => array(
				'label'    => esc_html__( 'Quantity Container', 'wc-builder-divi' ),
				'selector' => "body.woocommerce .product %%order_class%% .cart .quantity, body.et-fb %%order_class%% .woocommerce .product .cart .quantity",
			),
			'quantity_input' => array(
				'label'    => esc_html__( 'Quantity Field', 'wc-builder-divi' ),
				'selector' => "body.woocommerce .product {$this->main_css_element} .cart .quantity input.qty,,
				body.woocommerce-page .product {$this->main_css_element} .cart .quantity input.qty",
			),
			'plus_minus_container' => array(
				'label'    => esc_html__( '(+/-) Container', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart .quantity .quantity-nav",
			),
			'plus_button' => array(
				'label'    => esc_html__( '(+) Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart .quantity .quantity-nav .quantity-button.quantity-up",
			),
			'minus_button' => array(
				'label'    => esc_html__( '(-) Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .cart .quantity .quantity-nav .quantity-button.quantity-below",
			),
			'button' => array(
				'label'    => esc_html__( 'Add To Cart Button', 'wc-builder-divi' ),
				'selector' => "body #page-container {$this->main_css_element} .cart .button",
				'no_space_before_selector' => true,
			),
		);
	}

	function get_fields() {
		$fields = array(
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'wc-builder-divi' ),
				'type'            => 'text',
				'default'			=> esc_html__( 'Add to cart', 'woocommerce' ),
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text. Default is: Add to cart', 'wc-builder-divi' ),
				'toggle_slug'       => 'add_to_cart_button',
			),
			'show_quantity' => array(
				'label' => esc_html__( 'Show Quantity Field', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'toggle_slug'       => 'quantity_options',
				'tab_slug' => 'general',
			),
			'quantity_bg' => array(
				'label' => esc_html__( 'Quantity Background', 'wc-builder-divi' ),
				'type' => 'color-alpha',
				'custom_color'      => true,
				'default' => 'rgb(204, 204, 204)',
				'show_if' => array(
					'show_quantity' => 'on',
				),
				'toggle_slug'       => 'quantity_input',
				'tab_slug' => 'advanced',
			),
			'quantity_margin' => array(
				'label' => esc_html__( 'Quantity Margin', 'wc-builder-divi' ),
				'type' => 'custom_margin',
				'show_if' => array(
					'show_quantity' => 'on',
				),
				'toggle_slug'       => 'quantity_input',
				'tab_slug' => 'advanced',
			),
			'quantity_width' => array(
				'label' => esc_html__( 'Quantity Width', 'wc-builder-divi' ),
				'type' => 'range',
				'default' => '72px',
				'show_if' => array(
					'show_quantity' => 'on',
				),
				'toggle_slug'       => 'quantity_input',
				'tab_slug' => 'advanced',
			),
			'quantity_height' => array(
				'label' => esc_html__( 'Quantity Height', 'wc-builder-divi' ),
				'type' => 'range',
				'default' => '50px',
				'show_if' => array(
					'show_quantity' => 'on',
				),
				'toggle_slug'       => 'quantity_input',
				'tab_slug' => 'advanced',
			),
			'enable_plus_minus' => array(
				'label' => esc_html__( 'Show (+/-)', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
				),
				'default' => 'off',
				'show_if' => array(
					'show_quantity' => 'on',
				),
				'toggle_slug'       => 'quantity_options',
				'tab_slug' => 'general',
			),
			'plus_minus_style' => array(
				'label' => esc_html__( '(+/-) Style', 'wc-builder-divi' ),
				'type' => 'select',
				'options'           => array(
					'style_1' 	=> esc_html__( 'Style 1', 'wc-builder-divi' ),
					'style_2'  	=> esc_html__( 'Style 2', 'wc-builder-divi' ),
				),
				'default' => 'style_1',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_options',
				'tab_slug' => 'general',
			),
			'plus_bg' => array(
				'label' => esc_html__( '(+) Background', 'wc-builder-divi' ),
				'type' => 'color-alpha',
				'custom_color'      => true,
				'default' => '#d6d6d6',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'minus_bg' => array(
				'label' => esc_html__( '(-) Background', 'wc-builder-divi' ),
				'type' => 'color-alpha',
				'custom_color'      => true,
				'default' => '#d6d6d6',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'plus_minus_color' => array(
				'label' => esc_html__( '(+/-) Color', 'wc-builder-divi' ),
				'type' => 'color-alpha',
				'custom_color'      => true,
				'default' => '#6b6b6b',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'plus_minus_size' => array(
				'label' => esc_html__( '(+/-) Size', 'wc-builder-divi' ),
				'type' => 'range',
				'default' => '13px',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'plus_minus_width' => array(
				'label' => esc_html__( '(+/-) Width', 'wc-builder-divi' ),
				'type' => 'range',
				'default' => '25px',
				'show_if' => array(
					'enable_plus_minus' => 'on',
				),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'plus_minus_height' => array(
				'label' => esc_html__( '(+/-) Height', 'wc-builder-divi' ),
				'type' => 'range',
				'default' => '0px',
				// 'show_if' => array(
				// 	'plus_minus_style' => 'style_2',
				// ),
				'toggle_slug'       => 'quantity_plus_minus',
				'tab_slug' => 'advanced',
			),
			'drop_down_menus_bg' => array(
				'label' => esc_html__( 'Menu Background', 'wc-builder-divi' ),
				'type' => 'color-alpha',
				'custom_color'      => true,
				'default' => 'rgb(204, 204, 204)',
				'toggle_slug'       => 'drop_down_menus',
				'tab_slug' => 'advanced',
			),
			'drop_down_menus_height' => array(
				'label' => esc_html__( 'Menu Height', 'wc-builder-divi' ),
				'type' => 'range',
				'toggle_slug'       => 'drop_down_menus',
				'tab_slug' => 'advanced',
			),
			'hide_variation_price' => array(
				'label' => esc_html__( 'Hide Variation Price', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
				),
				'toggle_slug'       => 'variations',
			),
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {

		self::$button_text      	= $this->props['button_text'];

		$show_quantity				= $this->props['show_quantity'];
		$quantity_bg							= $this->props['quantity_bg'];
		$quantity_height							= $this->props['quantity_height'];
		$quantity_width							= $this->props['quantity_width'];
		$quantity_margin							= $this->props['quantity_margin'];

		$enable_plus_minus						= $this->props['enable_plus_minus'];
		$plus_bg											= $this->props['plus_bg'];
		$minus_bg											= $this->props['minus_bg'];
		$plus_minus_color							= $this->props['plus_minus_color'];
		$plus_minus_style							= $this->props['plus_minus_style'];
		$plus_minus_size							= $this->props['plus_minus_size'];
		$plus_minus_width							= $this->props['plus_minus_width'];
		$plus_minus_height							= $this->props['plus_minus_height'];
		
		$drop_down_menus_bg							= $this->props['drop_down_menus_bg'];
		$drop_down_menus_height							= $this->props['drop_down_menus_height'];

		$hide_variation_price		= $this->props['hide_variation_price'];
		$button_use_icon          	= $this->props['button_use_icon'];
		$custom_icon          		= $this->props['button_icon'];
		$button_custom        		= $this->props['custom_button'];
		$button_bg_color       		= $this->props['button_bg_color'];
		$button_icon_placement       		= $this->props['button_icon_placement'];

		$data = '';

		$this->add_classname( 'wcbd_module' );

		if( !is_product() ){
			return;
		}

		// quantity settings
		if( $show_quantity == 'off' ){
			$this->add_classname( 'hide-quantity' );
		}else{

			// quantity input background
			if( !empty( $quantity_bg ) ){
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .cart input.qty',
					'declaration' => "background-color:". esc_attr( $quantity_bg ) ."!important;",
				) );				
			}

			// quantity input width
			if( !empty( $quantity_width ) ){
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .cart input.qty',
					'declaration' => "width:". esc_attr( $quantity_width ) ."!important;",
				) );				
			}

			// quantity input height
			if( !empty( $quantity_height ) ){
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .cart input.qty',
					'declaration' => "height:". esc_attr( $quantity_height ) ."!important;",
				) );				
			}

			// quantity margin
			if( !empty( $quantity_margin ) ){
				$m = explode( '|', $quantity_margin );
				
				// top margin
				if( isset( $m[0] ) && $m[0] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity input.qty',
						'declaration' => "margin-top:". esc_attr( $m[0] ) ."!important;",
					) );					
				}
				
				// right margin
				if( isset( $m[1] ) && $m[1] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity input.qty',
						'declaration' => "margin-right:". esc_attr( $m[1] ) ."!important;",
					) );					
				}
				
				// bottom margin
				if( isset( $m[2] ) && $m[2] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity input.qty',
						'declaration' => "margin-bottom:". esc_attr( $m[2] ) ."!important;",
					) );					
				}
				
				// left margin
				if( isset( $m[3] ) && $m[3] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity input.qty',
						'declaration' => "margin-left:". esc_attr( $m[3] ) ."!important;",
					) );					
				}
			}

			// +/- buttons
			if( $enable_plus_minus == 'on' ){
				$this->add_classname( 'qty-pm' );

				// +/- style
				if( !empty( $plus_minus_style ) && in_array( $plus_minus_style, array( 'style_1', 'style_2' ) ) ){
					$this->add_classname( $plus_minus_style );
				}

				// + background
				if( !empty( $plus_bg ) ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-up',
						'declaration' => "background-color:". esc_attr( $plus_bg ) ."!important;",
					) );
				}	
				// + background
				if( !empty( $minus_bg ) ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-down',
						'declaration' => "background-color:". esc_attr( $minus_bg ) ."!important;",
					) );
				}	

				// +/- buttons color
				if( !empty( $plus_minus_color ) ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-button',
						'declaration' => "color:". esc_attr( $plus_minus_color ) .";",
					) );
				}	

				// +/- buttons width
				if( !empty( $plus_minus_width ) ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-button',
						'declaration' => "width:". esc_attr( $plus_minus_width ) ."!important;",
					) );
				}

				// +/- buttons size
				if( !empty( $plus_minus_size ) ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-button',
						'declaration' => "font-size:". esc_attr( $plus_minus_size ) .";",
					) );
				}	

				// height
				if( !empty( $plus_minus_height ) && $plus_minus_height != '0px' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .cart .quantity-button',
						'declaration' => "height:". esc_attr( $plus_minus_height ) ." !important;",
					) );
				}
			}
		}

		// drop down menus bg
		if( $drop_down_menus_bg != '' ){
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% form.cart .variations td select, %%order_class%% select',
				'declaration' => "background-color: ". esc_attr( $drop_down_menus_bg ) .";",
			) );			
		}
		// drop down menus height
		if( $drop_down_menus_height != '' ){
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% form.cart .variations td select, %%order_class%% select',
				'declaration' => "height: ". esc_attr( $drop_down_menus_height ) .";",
			) );			
		}

		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_button_text' ) );
		$data = WCBD_INIT::content_buffer( 'woocommerce_template_single_add_to_cart' );

		if( empty( $data ) ) return;

		if( $button_use_icon == 'on' && $custom_icon != '' && $button_custom == 'on' ){
			$custom_icon = 'data-icon="'. esc_attr( et_pb_process_font_icon( $custom_icon ) ) .'"';
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => 'body #page-container %%order_class%% .cart .button:after',
				'declaration' => "content: attr(data-icon);",
			) );

			// extra theme
			if( $button_icon_placement == 'right' ){
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => 'body #page-container %%order_class%% .cart .button:hover',
					'declaration' => "padding-right: 2em; padding-left:.7em;",
				) );				
			}

		}else{
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => 'body #page-container %%order_class%% .cart .button:hover',
				'declaration' => "padding-right:1em; padding-left:1em;",
			) );
		}

		if( $hide_variation_price == 'on' ){

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .woocommerce-variation-price',
				'declaration' => "display:none;",
			) );
		}

		if( !empty( $button_bg_color ) && $button_custom == 'on' ){

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => 'body #page-container %%order_class%% .cart .button',
				'declaration' => "background-color:". esc_attr( $button_bg_color ) ."!important;",
			) );
		}

		$output = str_replace(
			'class="single_add_to_cart_button button alt"',
			'class="single_add_to_cart_button button alt"' . $custom_icon 
			, $data
		);

		return $output;			
	}
}
new ET_Builder_Module_WooPro_AddToCart;
