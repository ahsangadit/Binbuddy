<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_MyAccount_Classic extends ET_Builder_Module {

	public $vb_support = 'on';

    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	function init() {
		$this->name       = esc_html__( 'Woo My Account', 'wc-builder-divi' );
		$this->slug       = 'et_pb_wcbd_myaccount_classic';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'nav' => array(
						'title' => esc_html__( 'Navigation', 'wc-builder-divi' ),
						'priority' => 35,
					),
					'more_options' => array(
						'title' => esc_html__( 'More Options', 'wc-builder-divi' ),
						'priority' => 36,
					),
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 55,
						'tabbed_subtoggles' => true,
						'bb_icons_support' => true,
						'sub_toggles' => array(
							'p'     => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
							'ul'    => array(
								'name' => 'UL',
								'icon' => 'list',
							),
							'ol'    => array(
								'name' => 'OL',
								'icon' => 'numbered-list',
							),
						),
					),
					'headings' => array(
						'title'    => esc_html__( 'Headings', 'et_builder' ),
						'priority' => 60,
						'tabbed_subtoggles' => true,
						'sub_toggles' => array(
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),
						),
					),
				),
			),
			
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'nav_item'   => array(
					'label'    => esc_html__( 'Nav Item', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-navigation ul li, %%order_class%% .woocommerce-MyAccount-navigation ul li a, body.et_extra %%order_class%% .woocommerce-MyAccount-navigation ul li:after",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'active_nav_item'   => array(
					'label'    => esc_html__( 'Active Nav Item', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-navigation ul li.is-active a, %%order_class%% .woocommerce-MyAccount-navigation ul li.is-active a:hover, body.et_extra %%order_class%% .woocommerce-MyAccount-navigation ul li.is-active:after",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'hover_nav_item'   => array(
					'label'    => esc_html__( 'Nav Item On Hover', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-navigation ul li a:hover, body.et_extra %%order_class%% .woocommerce-MyAccount-navigation ul li:hover:after",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'module_text'   => array(
					'label'    => esc_html__( 'Text', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-content p, %%order_class%% .woocommerce-MyAccount-content address, %%order_class%% .woocommerce-MyAccount-content table, %%order_class%% .woocommerce-MyAccount-content .woocommerce-Message",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'p',
					'hide_text_align' => true,
				),
				'link'   => array(
					'label'    => esc_html__( 'Link', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-content a",
						'color' => "%%order_class%% .woocommerce-MyAccount-content a",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'a',
				),
				'ul'   => array(
					'label'    => esc_html__( 'Unordered List', 'et_builder' ),
					'css'      => array(
						'main'        => "%%order_class%% .woocommerce-MyAccount-content ul",
						'color'       => "%%order_class%% .woocommerce-MyAccount-content ul",
						'line_height' => "%%order_class%% .woocommerce-MyAccount-content ul li",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'ul',
				),
				'ol'   => array(
					'label'    => esc_html__( 'Ordered List', 'et_builder' ),
					'css'      => array(
						'main'        => "%%order_class%% .woocommerce-MyAccount-content ol",
						'color'       => "%%order_class%% .woocommerce-MyAccount-content ol",
						'line_height' => "%%order_class%% .woocommerce-MyAccount-content ol li",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'ol',
				),
				'header_2'   => array(
					'label'    => esc_html__( 'Heading 2', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-content h2",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'headings',
					'sub_toggle'  => 'h2',
				),
				'header_3'   => array(
					'label'    => esc_html__( 'Heading 3', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-MyAccount-content h3",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'headings',
					'sub_toggle'  => 'h3',
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
				'nav' => array(
					'label_prefix' => esc_html__( 'Nav Bar', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-MyAccount-navigation ul",
							'border_styles' => "%%order_class%% .woocommerce-MyAccount-navigation ul",
						),
						'important' => 'all',
					),
				),
				'nav_item' => array(
					'label_prefix' => esc_html__( 'Nav Item', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-MyAccount-navigation ul li",
							'border_styles' => "%%order_class%% .woocommerce-MyAccount-navigation ul li",
						),
						'important' => 'all',
					),
				),
				'active_nav_item' => array(
					'label_prefix' => esc_html__( 'Active Nav Item', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .woocommerce-MyAccount-navigation ul li.is-active, %%order_class%% .woocommerce-MyAccount-navigation ul li.is-active:hover",
							'border_styles' => "%%order_class%% .woocommerce-MyAccount-navigation ul li.is-active, %%order_class%% .woocommerce-MyAccount-navigation ul li.is-active:hover",
						),
						'important' => 'all',
					),
				),
				'tables' => array(
					'label_prefix' => esc_html__( 'Tables', 'wc-builder-divi' ),
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
				'all_buttons' => array(
					'label' => esc_html__( 'Buttons', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "body #page-container %%order_class%% .button",
						),
					),
				),
			),
		);
	}

	function get_fields(){
		$fields = array(
			/* 'ajax_tabs' => array(
				'label'           => esc_html__( 'Load Tabs With Ajax', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => __( 'No', 'wc-builder-divi' ),
					'on' => __( 'Yes', 'wc-builder-divi' ),
				),
				'default' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
			), */
			'horizontal_nav' => array(
				'label'           => esc_html__( 'Horizontal Nav', 'wc-builder-divi' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => __( 'No', 'wc-builder-divi' ),
					'on' => __( 'Yes', 'wc-builder-divi' ),
				),
				'default' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'nav',
			),
			'nav_items_alignment' => array(
				'label' => esc_html__( 'Nav Items Alignment', 'wc-builder-divi' ),
				'type' => 'text_align',
				'option_category'  => 'configuration',
				'options' => et_builder_get_text_orientation_options(),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'nav',
			),
			'nav_bg' => array(
				'label' => esc_html__( 'Navigation Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'nav',
			),
			'nav_item_bg' => array(
				'label' => esc_html__( 'Nav Item Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'nav',
			),
			'active_nav_item_bg' => array(
				'label' => esc_html__( 'Active Nav Item Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'nav',
			),
			'hover_nav_item_bg' => array(
				'label' => esc_html__( 'Nav Item Hover Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'nav',
			),
			'inputs_bg' => array(
				'label' => esc_html__( 'Inputs Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'more_options',
			),
			'computed_depends_on_field' => array(
				'label' => 'Hidden',
				'type' => 'hidden',
				'default' => 'on',
				'computed_affects' => array(
					'__myaccount_classic',
				),
			),
			'__myaccount_classic' => array(
				'type' => 'computed',
				'computed_callback' => array( 'Divi_WC_Builder_Module_MyAccount_Classic', 'get_myaccount_classic' ),
				'computed_depends_on' => array(
					'computed_depends_on_field',
				),
			)
		);
		return $fields;
	}

	static function get_myaccount_classic( $args = array(), $conditional_tags = array(), $current_page = array() ){

		ob_start();
		echo do_shortcode( '[woocommerce_my_account]' );
		$output = ob_get_clean();
		return $output;
	}

	function render( $attrs, $content = null, $render_slug ) {

		//$ajax_tabs 				= $this->props['ajax_tabs'];

		$horizontal_nav 		= $this->props['horizontal_nav'];
		$nav_items_alignment 	= $this->props['nav_items_alignment'];
		$nav_bg 				= $this->props['nav_bg'];
		$nav_item_bg 			= $this->props['nav_item_bg'];
		$active_nav_item_bg 	= $this->props['active_nav_item_bg'];
		$hover_nav_item_bg 		= $this->props['hover_nav_item_bg'];
		$inputs_bg = $this->props['inputs_bg'];

		$all_buttons_custom        		= $this->props['custom_all_buttons'];
		$all_buttons_bg_color       	= $this->props['all_buttons_bg_color'];
		$all_buttons_icon       		= $this->props['all_buttons_icon'];
		$all_buttons_use_icon       		= $this->props['all_buttons_use_icon'];
		$all_buttons_icon_placement     = $this->props['all_buttons_icon_placement'];

		$this->add_classname( 'wcbd_module' );
		$data = '';
		if( function_exists( 'is_account_page' ) && is_account_page() && is_user_logged_in() ){

			if( $horizontal_nav == 'on' ){
				$this->add_classname( 'wcbd_myaccount_horizontal_nav' );
			}

			// $nav_items_alignment
			if( $nav_items_alignment == 'right' ){
				$this->add_classname ( 'right_nav' );
			}
			if( $nav_items_alignment == 'left' ){
				$this->add_classname ( 'left_nav' );
			}
			if( $nav_items_alignment == 'center' ){
				$this->add_classname ( 'center_nav' );
			}
			if( $nav_items_alignment == 'justified' ){
				$this->add_classname ( 'justified_nav' );
			}
			// nav_bg
			if( $nav_bg != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% .woocommerce-MyAccount-navigation ul',
					'declaration' => "background: " . esc_attr( $nav_bg ) . ";",
				) );
			}
			// nav_item_bg
			if( $nav_item_bg != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% .woocommerce-MyAccount-navigation ul li',
					'declaration' => "background: " . esc_attr( $nav_item_bg ) . ";",
				) );
			}
			// active_nav_item_bg
			if( $active_nav_item_bg != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% .woocommerce-MyAccount-navigation ul li.is-active, %%order_class%% .woocommerce-MyAccount-navigation ul li.is-active:hover',
					'declaration' => "background: " . esc_attr( $active_nav_item_bg ) . ";",
				) );
			}
			// hover_nav_item_bg
			if( $hover_nav_item_bg != '' ){
				ET_Builder_element::set_style( $render_slug, array(
					'selector' => '%%order_class%% .woocommerce-MyAccount-navigation ul li:hover',
					'declaration' => "background: " . esc_attr( $hover_nav_item_bg ) . ";",
				) );
			}

			// inputs bg
			if( !empty( $inputs_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% form .form-row input, %%order_class%% form .form-row textarea, %%order_class%% .select2-container--default .select2-selection--single, %%order_class%% form .form-row select, .select2-dropdown",
					'declaration' => "background:". esc_attr( $inputs_bg ) ." !important;"
				) );
			}

			// all_buttons
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $all_buttons_custom, 
					'button_use_icon' => $all_buttons_use_icon, 
					'button_icon' => $all_buttons_icon, 
					'button_icon_placement' => $all_buttons_icon_placement, 
					'button_bg_color' => $all_buttons_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .button" 			
				)
			);

			ob_start();
			echo do_shortcode( '[woocommerce_my_account]' );
			$data = ob_get_clean();

			// ajax tabs
			/* if( $ajax_tabs == 'on' ){
				$this->add_classname( 'wcbd_myaccount_ajax_tabs' );
			} */
		}
		return $data;

	}
}
new Divi_WC_Builder_Module_MyAccount_Classic;
