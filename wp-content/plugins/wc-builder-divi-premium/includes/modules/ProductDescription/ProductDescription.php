<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class ET_Builder_Module_WooPro_Description extends ET_Builder_Module {

	public $vb_support = 'on';
    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	public static $heading, $show_heading;
	public static function change_tab_heading( $hd ){
		
		if( self::$show_heading == 'off' ){
			$hd = '';
		}elseif( self::$show_heading == 'on' ){
			if( self::$heading != '' ){
				$hd = esc_attr( self::$heading );
			}
		}

		return $hd;
	}

	function init() {
		$this->name            	= esc_html__( 'Woo Product Description', 'wc-builder-divi' );
		$this->slug            	= 'et_pb_woopro_description';
		$this->post_types 		= apply_filters( 'woo_description_module_post_types', array( 'page', 'project' ) ); // this will remove the module from the product description builder

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'module_heading' => esc_html__( 'Module Title', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'module_title' => esc_html__( 'Module Heading', 'wc-builder-divi' ),
					'text' => array(
						'title'    => esc_html__( 'Description', 'et_builder' ),
						'priority' => 45,
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
							'quote' => array(
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							),
						),
					),
					'header' => array(
						'title'    => esc_html__( 'Description Heading', 'et_builder' ),
						'priority' => 49,
						'tabbed_subtoggles' => true,
						'sub_toggles' => array(
							'h1' => array(
								'name' => 'H1',
								'icon' => 'text-h1',
							),
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),
							'h4' => array(
								'name' => 'H4',
								'icon' => 'text-h4',
							),
							'h5' => array(
								'name' => 'H5',
								'icon' => 'text-h5',
							),
							'h6' => array(
								'name' => 'H6',
								'icon' => 'text-h6',
							),
						),
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			
		);
		
		$this->main_css_element = '%%order_class%%';
		$this->fields_defaults = array(
			'show_heading' => array( 'on' ),
		);
		$this->advanced_fields = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Module Heading', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2:first-of-type, body.et_extra {$this->main_css_element} h2:first-of-type",
					),
					'font_size' => array(
						'default' => '20px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_tab' => 'advanced',
					'toggle_slug'  => 'module_title',
				),
				'text'   => array(
					'label'    => esc_html__( 'Text', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'color' => "{$this->main_css_element} p",
					),
					'line_height' => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size' => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'p',
					'hide_text_align' => true,
				),
				'link'   => array(
					'label'    => esc_html__( 'Link', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} a",
						'color' => "{$this->main_css_element} a",
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
						'main'        => "{$this->main_css_element} ul",
						'color'       => "{$this->main_css_element} ul",
						'line_height' => "{$this->main_css_element} ul li",
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
						'main'        => "{$this->main_css_element} ol",
						'color'       => "{$this->main_css_element} ol",
						'line_height' => "{$this->main_css_element} ol li",
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
				'quote'   => array(
					'label'    => esc_html__( 'Blockquote', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} blockquote, {$this->main_css_element} blockquote p",
						'color' => "{$this->main_css_element} blockquote, {$this->main_css_element} blockquote p",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'toggle_slug' => 'text',
					'sub_toggle'  => 'quote',
				),
				'header_1'   => array(
					'label'    => esc_html__( 'Heading', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h1",
					),
					'font_size' => array(
						'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h1',
				),
				'header_2'   => array(
					'label'    => esc_html__( 'Heading 2', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
					),
					'font_size' => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h2',
				),
				'header_3'   => array(
					'label'    => esc_html__( 'Heading 3', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3",
					),
					'font_size' => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h3',
				),
				'header_4'   => array(
					'label'    => esc_html__( 'Heading 4', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4",
					),
					'font_size' => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h4',
				),
				'header_5'   => array(
					'label'    => esc_html__( 'Heading 5', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h5",
					),
					'font_size' => array(
						'default' => '16px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h5',
				),
				'header_6'   => array(
					'label'    => esc_html__( 'Heading 6', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h6",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h6',
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
		);
		$this->custom_css_fields = array(
			'header' => array(
				'label' => esc_html__( 'Heading', 'wc-builder-divi' ),
				'selector' => "{$this->main_css_element} h2:first-of-type, body.et_extra {$this->main_css_element} h2:first-of-type",
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_heading' => array(
				'label' => esc_html__( 'Show Heading', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'options_category' => 'configuration',
				'options' => array( 
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'affects' => array(
					'heading',
				),
				'toggle_slug' => 'module_heading',
			),
			'heading' => array(
				'label'           => esc_html__( 'Custom Heading', 'wc-builder-divi' ),
				'type'            => 'text',
				'default'		=> esc_html__( 'Description', 'woocommerce' ),
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The Heading of the module. If left empty, default is: Product Description', 'wc-builder-divi' ),
				'depends_show_if'   => 'on',
				'toggle_slug' => 'module_heading',
			),
			'ul_type' => array(
				'label'             => esc_html__( 'Unordered List Style Type', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'disc'    => esc_html__( 'Disc', 'et_builder' ),
					'circle'  => esc_html__( 'Circle', 'et_builder' ),
					'square'  => esc_html__( 'Square', 'et_builder' ),
					'none'    => esc_html__( 'None', 'et_builder' ),
				),
				'priority'          => 80,
				'default'           => 'disc',
				'default_on_front' => '',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'sub_toggle'        => 'ul',
			),
			'ul_position' => array(
				'label'             => esc_html__( 'Unordered List Style Position', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'outside' => esc_html__( 'Outside', 'et_builder' ),
					'inside'  => esc_html__( 'Inside', 'et_builder' ),
				),
				'priority'          => 85,
				'default'           => 'outside',
				'default_on_front'  => '',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'sub_toggle'        => 'ul',
			),
			'ul_item_indent' => array(
				'label'           => esc_html__( 'Unordered List Item Indent', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'sub_toggle'      => 'ul',
				'priority'        => 90,
				'default'         => '0px',
				'default_on_front'  => '',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'ol_type' => array(
				'label'             => esc_html__( 'Ordered List Style Type', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'decimal'              => 'decimal',
					'armenian'             => 'armenian',
					'cjk-ideographic'      => 'cjk-ideographic',
					'decimal-leading-zero' => 'decimal-leading-zero',
					'georgian'             => 'georgian',
					'hebrew'               => 'hebrew',
					'hiragana'             => 'hiragana',
					'hiragana-iroha'       => 'hiragana-iroha',
					'katakana'             => 'katakana',
					'katakana-iroha'       => 'katakana-iroha',
					'lower-alpha'          => 'lower-alpha',
					'lower-greek'          => 'lower-greek',
					'lower-latin'          => 'lower-latin',
					'lower-roman'          => 'lower-roman',
					'upper-alpha'          => 'upper-alpha',
					'upper-greek'          => 'upper-greek',
					'upper-latin'          => 'upper-latin',
					'upper-roman'          => 'upper-roman',
					'none'                 => 'none',
				),
				'priority'          => 80,
				'default'           => 'decimal',
				'default_on_front' => '',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'sub_toggle'        => 'ol',
			),
			'ol_position' => array(
				'label'             => esc_html__( 'Ordered List Style Position', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'outside' => esc_html__( 'Outside', 'et_builder' ),
					'inside'  => esc_html__( 'Inside', 'et_builder' ),
				),
				'priority'          => 85,
				'default'           => 'outside',
				'default_on_front' => '',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'sub_toggle'        => 'ol',
			),
			'ol_item_indent' => array(
				'label'           => esc_html__( 'Ordered List Item Indent', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'sub_toggle'      => 'ol',
				'priority'        => 90,
				'default'         => '0px',
				'default_on_front' => '',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'quote_border_weight' => array(
				'label'           => esc_html__( 'Blockquote Border Weight', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'sub_toggle'      => 'quote',
				'priority'        => 85,
				'default'         => '5px',
				'default_unit'    => 'px',
				'default_on_front' => '',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'quote_border_color' => array(
				'label'           => esc_html__( 'Blockquote Border Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'option_category' => 'configuration',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'sub_toggle'      => 'quote',
				'field_template'  => 'color',
				'priority'        => 90,
			),
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		self::$show_heading		= $this->props['show_heading'];
		self::$heading 			= $this->props['heading'];

		$ul_type              = $this->props['ul_type'];
		$ul_position          = $this->props['ul_position'];
		$ul_item_indent       = $this->props['ul_item_indent'];
		$ol_type              = $this->props['ol_type'];
		$ol_position          = $this->props['ol_position'];
		$ol_item_indent       = $this->props['ol_item_indent'];
		$quote_border_weight  = $this->props['quote_border_weight'];
		$quote_border_color   = $this->props['quote_border_color'];

		$this->add_classname( 'wcbd_module' );
		
		if( !is_product() ){
			return;
		}

		$data = '';
		// check if the content has the description module
		global $post;

		if( has_shortcode( $post->post_content, 'et_pb_woopro_description' ) ){
			return;
		}

		// only load description if the product has
		$all_tabs = woocommerce_default_product_tabs();
		
		if( isset( $all_tabs['description'] ) ){
			
			add_filter( 'woocommerce_product_description_heading', array( $this, 'change_tab_heading' ) );

			$data = WCBD_INIT::content_buffer( 'woocommerce_product_description_tab' );

			// remove the new title to not affect the tabs module if used
			remove_filter( 'woocommerce_product_description_heading', array( $this, 'change_tab_heading' ) );

		}

		if ( '' !== $ul_type || '' !== $ul_position || '' !== $ul_item_indent ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% ul',
				'declaration' => sprintf(
					'%1$s
					%2$s
					%3$s',
					'' !== $ul_type ? sprintf( 'list-style-type: %1$s !important;', esc_html( $ul_type ) ) : '',
					'' !== $ul_position ? sprintf( 'list-style-position: %1$s !important;', esc_html( $ul_position ) ) : '',
					'' !== $ul_item_indent ? sprintf( 'padding-left: %1$s !important;', esc_html( $ul_item_indent ) ) : ''
				),
			) );
		}

		if ( '' !== $ol_type || '' !== $ol_position || '' !== $ol_item_indent ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% ol',
				'declaration' => sprintf(
					'%1$s
					%2$s
					%3$s',
					'' !== $ol_type ? sprintf( 'list-style-type: %1$s !important;', esc_html( $ol_type ) ) : '',
					'' !== $ol_position ? sprintf( 'list-style-position: %1$s !important;', esc_html( $ol_position ) ) : '',
					'' !== $ol_item_indent ? sprintf( 'padding-left: %1$s !important;', esc_html( $ol_item_indent ) ) : ''
				),
			) );
		}

		if ( '' !== $quote_border_weight || '' !== $quote_border_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% blockquote',
				'declaration' => sprintf(
					'%1$s
					%2$s',
					'' !== $quote_border_weight ? sprintf( 'border-width: %1$s;', esc_html( $quote_border_weight ) ) : '',
					'' !== $quote_border_color ? sprintf( 'border-color: %1$s;', esc_html( $quote_border_color ) ) : ''
				),
			) );
		}

		return $data;			
	}
}
new ET_Builder_Module_WooPro_Description;