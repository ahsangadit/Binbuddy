<?php
/**
 * Cross-sells products module
 * @since 2.2.0
 * @version 2.2.0
 */
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_Cart_Cross_Sells extends ET_Builder_Module {

	public $vb_support = 'on';

	function init() {
		$this->name         = esc_html__( 'Woo Cart Cross-Sells', 'wc-divi-builder' );
        $this->slug         = 'et_pb_wcbd_cart_cross_sells';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay' => esc_html__( 'Product Image Overlay', 'wc-builder-divi' ),
					'misc'	=> esc_html__( 'Miscellaneous', 'wc-builder-divi' ),
				),
			),
			
		);

		$this->advanced_fields = array(
			'fonts' => array(
				'module_title' => array(
					'label'    => esc_html__( 'Module Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2.module-title, body.et_extra {$this->main_css_element} h2.module-title",
						'important' => array( 'size', 'font-size', 'plugin_all' ),
					),
					'font_size' => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'product_title' => array(
					'label'    => esc_html__( 'Product Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "body.woocommerce {$this->main_css_element} ul.products li.product h2.woocommerce-loop-product__title, {$this->main_css_element} ul.products li.product h2.woocommerce-loop-product__title",
						'important' => array( 'size', 'font-size' ),
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'product_price' => array(
					'label'    => esc_html__( 'Price', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "body.woocommerce {$this->main_css_element} ul.products li.product .price, {$this->main_css_element} ul.products li.product .price",
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
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'add_to_cart_button' => array(
					'label' => esc_html__( 'Add To Cart Button', 'wc-builder-divi' ),
					'css' => array(
						'main' => "%%order_class%% ul.products li.product .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% ul.products li.product .button",
						),
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'module_title' => array(
				'label' => esc_html__( 'Module Title', 'wc-builder-divi' ),
				'selector' => "{$this->main_css_element} h2.module-title",
			),
			'product_title' => array(
				'label' => esc_html__( 'Product Title', 'wc-builder-divi' ),
				'selector' => "body.woocommerce {$this->main_css_element} ul.products li.product h2.woocommerce-loop-product__title, {$this->main_css_element} ul.products li.product h2.woocommerce-loop-product__title",
			),
			'product_price' => array(
				'label' => esc_html__( 'Product Price', 'wc-builder-divi' ),
				'selector' => "body.woocommerce {$this->main_css_element} ul.products li.product .price, {$this->main_css_element} ul.products li.product .price",
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
				'label' => esc_html__( 'Title', 'wc-builder-divi' ),
				'type' => 'text',
				'default' => __( 'You may be interested in...', 'wc-builder-divi' ),
				'description' => esc_html__( 'The module default title: You may be interested in...', 'wc-divi-builder' ),
				'show_if' => array(
					'enable_module_title' => 'on',
				),
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'products_count' => array(
				'label'			=> esc_html__( 'Products count', 'wc-builder-divi' ),
				'type'			=> 'number',
				'default' 		=> 3,
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Products Count: Default is 3', 'wc-builder-divi' ),
				'toggle_slug' => 'main_content',
				'computed_affects' => array(
					'__cross_sells',
				),
			),
			'products_columns' => array(
				'label'			=> esc_html__( 'Products Columns', 'wc-builder-divi' ),
				'type'			=> 'select',
				'option_category' => 'layout',
				'options' => array(
					'0' => esc_html__( '-- Columns --', 'wc-builder-divi' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'default' => '3',
				'description'     => esc_html__( 'Products Columns: Default is 3', 'wc-builder-divi' ),
				'toggle_slug' => 'main_content',
				'computed_affects' => array(
					'__cross_sells',
				),
			),
			'show_add_to_cart' => array(
				'label' => esc_html__( 'Show Add To Cart', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'options' => array(
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
					'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
				),
				'default' => 'off',
				'description'     => esc_html__( 'Enable this to display add to cart button under each product.', 'wc-builder-divi' ),
				'toggle_slug' => 'main_content',
				'computed_affects' => array(
					'__cross_sells',
				),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'wc-builder-divi' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on' => esc_html__( 'On', 'wc-builder-divi' ),
					'off'  => esc_html__( 'Off', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'overlay',
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'wc-builder-divi' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'wc-builder-divi' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'wc-builder-divi' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'wc-builder-divi' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'wc-builder-divi' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'wc-builder-divi' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'default' 				=> 'P',
				'class'               => array( 'et-pb-font-icon' ),
				'depends_show_if'     => 'on',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom icon for the overlay', 'wc-builder-divi' ),
			),
			'stars_color' => array(
				'label'             => esc_html__( 'Rating Stars Color', 'wc-builder-divi' ),
				'type'     => 'color-alpha',
				'toggle_slug' => 'misc',
				'tab_slug' => 'advanced',
			),
			'sale_badge_color' => array(
				'label'             => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug' => 'misc',
				'tab_slug'          => 'advanced',
			),
			'__cross_sells' => array(
				'type' => 'computed',
				'computed_callback' => array( 'Divi_WC_Builder_Module_Cart_Cross_Sells', 'get_cross_sells' ),
				'computed_depends_on' => array(
					'show_add_to_cart',
					'products_count',
					'products_columns',
				),
			),
		);

		return $fields;
	}

	static function get_cross_sells( $args = array(), $conditional_tags = array(), $current_page = array() ){

		// add to cart
		if( $args['show_add_to_cart'] == 'on' ){
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 9 );
		}
		
		$limit = is_numeric( $args['products_count'] ) && (int) $args['products_count'] > 0 ? (int) $args['products_count'] : 3;
		$columns = is_numeric( $args['products_columns'] ) && (int) $args['products_columns'] <= 6 ? (int) $args['products_columns'] : 3;

		ob_start();
		echo do_shortcode( "[products pagibate='false' limit='{$limit}' columns='{$columns}']" );
		return ob_get_clean();
	}

	function render( $attrs, $content = null, $render_slug ) {

		$enable_module_title 	= $this->props['enable_module_title'];
		$module_title_text 		= $this->props['module_title_text'];

		$products_count 		= $this->props['products_count'];
		$products_columns 			= $this->props['products_columns'];
		$sale_badge_color        	= $this->props['sale_badge_color'];
		$overlay_icon_color  		= $this->props['overlay_icon_color'];
		$hover_overlay_color 		= $this->props['hover_overlay_color'];
		$hover_icon          		= $this->props['hover_icon'];
		$use_overlay         		= $this->props['use_overlay'];
		$stars_color      			= $this->props['stars_color'];

		$show_add_to_cart 						= $this->props['show_add_to_cart'];
		$add_to_cart_button_custom  			= $this->props['custom_add_to_cart_button'];
		$add_to_cart_button_icon 				= $this->props['add_to_cart_button_icon'];
		$add_to_cart_button_use_icon 				= $this->props['add_to_cart_button_use_icon'];
		$add_to_cart_button_icon_placement 		= $this->props['add_to_cart_button_icon_placement'];
		$add_to_cart_button_bg_color 			= $this->props['add_to_cart_button_bg_color'];
		$add_to_cart_button_on_hover 			= $this->props['add_to_cart_button_on_hover'];

		if( is_admin() ){
			return;
		}
		
		$this->add_classname('wcbd_module');

		// text orientation class
		$text_orientation = isset( $this->props['text_orientation'] ) ? esc_attr( $this->props['text_orientation'] ) : '';
		if( $text_orientation ){
			$this->add_classname( "et_pb_text_align_{$text_orientation}" );
		}

		$output 	= '';
		$limit 		= is_numeric( $products_count ) ? (int) $products_count : 3;
		$columns 	= is_numeric( $products_columns ) ? (int) $products_columns : 3;

		/**
		 * add to cart button
		 */
		if( $show_add_to_cart == 'on' ){
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 9 );
		}

		if( function_exists( 'is_cart' ) && is_cart() ){

			ob_start();
			woocommerce_cross_sell_display( $limit, $columns);
			$crosssells = ob_get_clean();	
			
			if( $crosssells && $enable_module_title == 'on' && !empty( $module_title_text ) ){
				$output .= "<h2 class='module-title'>". esc_html( $module_title_text ) ."</h2>";
			}

			$output .= $crosssells;
		}

		/**
		 * images overlay
		 */
		if( $use_overlay == 'off' ){
			$this->add_classname( 'hide_overlay' );

		}elseif( $use_overlay == 'on' ){

			// icon
			if( !empty( $hover_icon ) ){

				$icon_color = !( empty( $overlay_icon_color ) ) ? 'color: ' . esc_attr( $overlay_icon_color ) . ';' : '';

				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay:before, %%order_class%% .et_pb_extra_overlay:before',
					'declaration' => "content: '". esc_attr( WCBD_INIT::et_icon_css_content( $hover_icon ) ) ."'; font-family: 'ETmodules' !important; {$icon_color}",
				) );

			}

			// hover background color
			if( !empty( $hover_overlay_color ) ){

				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay, %%order_class%% .et_pb_extra_overlay',
					'declaration' => "background: ". esc_attr( $hover_overlay_color ) .";",
				) );

			}
		}

		/**
		 * rating stars color
		 */
		if( !empty( $stars_color ) ){

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => 'body.woocommerce %%order_class%% .star-rating span:before, body.woocommerce-page %%order_class%% .star-rating span:before',
				'declaration' => "color: ". esc_attr( $stars_color ) ."!important;",
			) );
		}

		/**
		 * sale badge color
		 */
		if ( '' !== $sale_badge_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% span.onsale',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_color )
				),
			) );
		}

		/**
		 * add to cart button style
		 */
		if( $show_add_to_cart == 'on' ){
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $add_to_cart_button_custom, 
					'button_use_icon' => $add_to_cart_button_use_icon, 
					'button_icon' => $add_to_cart_button_icon, 
					'button_icon_placement' => $add_to_cart_button_icon_placement, 
					'button_bg_color' => $add_to_cart_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% ul.products li.product .button" 			
				)
			);
			
			// extra theme fix
			if( $add_to_cart_button_use_icon == 'on' && $add_to_cart_button_icon != '' ){
				if( $add_to_cart_button_on_hover == 'off' ){
					// show the icon
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => 'body.et_extra #page-container %%order_class%% ul.products li.product .button:after, body.et_extra #page-container %%order_class%% ul.products li.product .button:before',
						'declaration' => "opacity: 1 !important;",
					) );
					if( $add_to_cart_button_icon_placement == 'right' ){
						ET_Builder_Element::set_style( $render_slug, array(
							'selector'    => 'body.et_extra #page-container %%order_class%% ul.products li.product .button',
							'declaration' => "padding-right: 2em; padding-left:.7em;",
						) );				
					}elseif( $add_to_cart_button_icon_placement == 'left' ){
						ET_Builder_Element::set_style( $render_slug, array(
							'selector'    => 'body.et_extra #page-container %%order_class%% ul.products li.product .button',
							'declaration' => "padding-right: .7em; padding-left:2em;",
						) );				
					}
				}
			}
		}

		/**
		 * some cleaning in case the module used twice
		 */
		if( $show_add_to_cart == 'on' ){
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 9 );
		}

		return '<div class="woocommerce columns-'. $columns .'">'. $output .'</div>';	
	}
}
new Divi_WC_Builder_Module_Cart_Cross_Sells;
