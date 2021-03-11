<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_Order_Tracking extends ET_Builder_Module {
	public $vb_support = 'on';

    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	function init() {
		$this->name       = esc_html__( 'Woo Order Tracking', 'wc-builder-divi' );
		$this->slug       = 'et_pb_wcbd_order_tracking';

		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'p'   => array(
					'label'    => esc_html__( 'Paragraph', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} p:not(.form-row):not(.woocommerce-customer-details--phone):not(.woocommerce-customer-details--email):not(.order-again)",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
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
						'main' => "%%order_class%% form .form-row input, body.et_extra %%order_class%% form .form-row input, body.et_extra %%order_class%% form .form-row input::placeholder",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'headers'   => array(
					'label'    => esc_html__( 'Headers', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% h2, %%order_class%% h3, body.et_extra %%order_class%% h2, body.et_extra %%order_class%% h3",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '26px',
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
						'main' => "{$this->main_css_element} .woocommerce-customer-details address, {$this->main_css_element} .woocommerce-customer-details .woocommerce-customer-details--email,{$this->main_css_element}  .woocommerce-customer-details--phone",
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'font_size' => array(
						'default' => '14px',
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
				'inputs' => array(
					'label_prefix' => esc_html__( 'Inputs', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% form .form-row input",
							'border_styles' => "%%order_class%% form .form-row input",
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
				'track_button' => array(
					'label' => esc_html__( 'Track Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .button[name='track']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .button[name='track']",
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

	function get_fields(){

		$fields = array(
			'computed_depends_on_field' => array(
				'label' => 'Hidden',
				'type' => 'hidden',
				'default' => 'on',
				'computed_affects' => array(
					'__order_tracking',
				),
			),
			'__order_tracking' => array(
				'type' => 'computed',
				'computed_callback' => array( 'Divi_WC_Builder_Module_Order_Tracking', 'get_order_tracking' ),
				'computed_depends_on' => array(
					'computed_depends_on_field',
				),
			),
		);
		return $fields;
	}

	static function get_order_tracking( $args = array(), $conditional_tags = array(), $current_page = array() ){

		$output = '';
		global $post;

		if ( ! isset( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['et_post_id'] ) ) {
			$post_id = sanitize_text_field( $_POST['et_post_id'] );
		} else if ( isset( $current_page['id'] ) ) {
			// Overwrite global $post value in this scope
			$post_id = intval( $current_page['id'] );
		} else if ( is_object( $post ) && isset( $post->ID ) ) {
			$post_id = $post->ID;
		}else{
			$post_id = false;
		}

		/**
		 * This shortcode needs the $post global variable
		 */
		if( $post_id ){
			$post = get_post( $post_id );
			ob_start();
			echo do_shortcode( '[woocommerce_order_tracking]' );
			$output = ob_get_clean();
		}

		return $output;
	}

	function render( $attrs, $content = null, $render_slug ) {

		$track_button_custom        	= $this->props['custom_track_button'];
		$track_button_bg_color       	= $this->props['track_button_bg_color'];
		$track_button_icon       		= $this->props['track_button_icon'];
		$track_button_use_icon       		= $this->props['track_button_use_icon'];
		$track_button_icon_placement    = $this->props['track_button_icon_placement'];
		
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

		$this->add_classname( 'wcbd_module' );
		
		if( is_admin() ) return;

		// text orientation class
		$text_orientation = isset( $this->props['text_orientation'] ) ? esc_attr( $this->props['text_orientation'] ) : '';
		if( $text_orientation ){
			$this->add_classname( "et_pb_text_align_{$text_orientation}" );
		}
		/**
		 * track Button
		 */
		WCBD_HELPERS::set_button_style( 
			array(
				'render_slug' => $render_slug, 
				'custom_button' => $track_button_custom, 
				'button_use_icon' => $track_button_use_icon, 
				'button_icon' => $track_button_icon, 
				'button_icon_placement' => $track_button_icon_placement, 
				'button_bg_color' => $track_button_bg_color, 
				'button_selector' => "body #page-container %%order_class%% .button[name='track']" 			
			)
		);

		/**
		 * download Button
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
		 * order_again Button
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
		echo do_shortcode( '[woocommerce_order_tracking]' );
		return ob_get_clean();

	}
}
new Divi_WC_Builder_Module_Order_Tracking;
