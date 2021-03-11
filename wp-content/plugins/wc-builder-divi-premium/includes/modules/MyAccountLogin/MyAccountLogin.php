<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class Divi_WC_Builder_Module_MyAccount_Login extends ET_Builder_Module {

	public $vb_support = 'on';

    protected $module_credits = array(
		'module_uri' => WCBD_PRODUCT_URL,
		'author'     => WCBD_AUTHOR,
		'author_uri' => DIVIKINGDOM_URL,
	);
		
	function init() {
		$this->name       = esc_html__( 'Woo Login/Register', 'wc-builder-divi' );
		$this->slug       = 'et_pb_wcbd_myaccount_login';

		$this->settings_modal_toggles = array(

			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'tabs'	=> esc_html__( 'Tabs', 'wc-builder-divi' ),
					'forms' => esc_html__( 'Forms', 'wc-builder-divi' ),
				),
			),
			
		);
				
		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(
				'default_headers'   => array(
					'label'    => esc_html__( 'Default Headers', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% h2, body.et_extra %%order_class%% h2",
					),
					'font_size' => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'tabs_titles'   => array(
					'label'    => esc_html__( 'Tabs', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tabs .tab_heading",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'active_tab_title'   => array(
					'label'    => esc_html__( 'Active Tab', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tabs .tab_heading.active, {$this->main_css_element} .tabs .tab_heading.active:hover",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'hover_tab_title'   => array(
					'label'    => esc_html__( 'Hover On Tab', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .tabs .tab_heading:hover",
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
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
						'main' => "%%order_class%% form .form-row input",
						'important' => 'all',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'lost_password_link'   => array(
					'label'    => esc_html__( 'Lost Password', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% p.woocommerce-LostPassword, %%order_class%% p.woocommerce-LostPassword a",
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
				'tabs_head' => array(
					'label_prefix' => esc_html__( 'Tabs Head', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .tabs",
							'border_styles' => "%%order_class%% .tabs",
						),
						'important' => 'all',
					),
				),
				'single_tab' => array(
					'label_prefix' => esc_html__( 'Single Tab', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .tabs .tab_heading",
							'border_styles' => "%%order_class%% .tabs .tab_heading",
						),
						'important' => 'all',
					),
				),
				'active_tab' => array(
					'label_prefix' => esc_html__( 'Active Tab', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .tabs .tab_heading.active, %%order_class%% .tabs .tab_heading.active:hover",
							'border_styles' => "%%order_class%% .tabs .tab_heading.active, %%order_class%% .tabs .tab_heading.active:hover",
						),
						'important' => 'all',
					),
				),
				'inputs' => array(
					'label_prefix' => esc_html__( 'Inputs', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% form .form-row input.input-text",
							'border_styles' => "%%order_class%% form .form-row input.input-text",
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
				'forms' => array(
					'label_prefix' => esc_html__( 'Forms', 'wc-builder-divi' ),
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% form.woocommerce-ResetPassword, %%order_class%% form.register, %%order_class%% form.login",
							'border_styles' => "%%order_class%% form.woocommerce-ResetPassword, %%order_class%% form.register, %%order_class%% form.login",
						),
						'important' => 'all',
					),
					'defaults'        => array(
						'border_radii'  => 'on|5px|5px|5px|5px',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d3ced2',
							'style' => 'solid',
						),
					),
				),
			),
			'button' => array(
				'login_button' => array(
					'label' => esc_html__( 'Login Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .button[name='login']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .button[name='login']",
						),
					),
				),
				'register_button' => array(
					'label' => esc_html__( 'Register Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .button[name='register']",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .button[name='register']",
						),
					),
				),
				'reset_password_button' => array(
					'label' => esc_html__( 'Reset Password Button', 'wc-divi-builder' ),
					'css' => array(
						'main' => "%%order_class%% .woocommerce-ResetPassword .button",
						'important' => 'all',
					),
					'box_shadow' => array(
						'css' => array(
							'main' => "%%order_class%% .woocommerce-ResetPassword .button",
						),
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);

		$this->custom_css_fields = array(
			'login_button_css' => array(
				'label' => esc_html__( 'Login Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .button[name='login']",
			),
			'register_button_css' => array(
				'label' => esc_html__( 'Register Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .button[name='register']",
			),
			'reset_password_button_css' => array(
				'label' => esc_html__( 'Reset Password Button', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .woocommerce-ResetPassword .button",
			),
		);
	}

	function get_fields(){
		$fields = array(
			'display_type' => array(
				'label' => esc_html__( 'Display Type', 'wc-builder-divi' ),
				'type'	=> 'select',
				'options' => array(
					'default' => esc_html__( 'Default', 'wc-builder-divi' ),
					'tabs' => esc_html__( 'Tabs', 'wc-builder-divi' ),
				),
				'default' => 'default',
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'show_heading' => array(
				'label' => esc_html__( 'Show Heading', 'wc-builder-divi' ),
				'type' => 'yes_no_button',
				'options' => array(
					'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default' => 'on',
				'show_if' => array(
					'display_type' => 'default',
				),
				'tab_slug' => 'general',
				'toggle_slug' => 'main_content',
			),
			'tabs_alignment' => array(
				'label' => esc_html__( 'Tabs Alignment', 'wc-builder-divi' ),
				'type' => 'text_align',
				'option_category'  => 'configuration',
				'options' => et_builder_get_text_orientation_options(),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'tabs',
				'show_if'	=> array(
					'display_type' => 'tabs',
				),
			),
			'tabs_bg' => array(
				'label'             => esc_html__( 'Tabs Background', 'wc-builder-divi' ),
				'type'     => 'color-alpha',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'tabs',
				'show_if'	=> array(
					'display_type' => 'tabs',
				),
			),
			'tab_bg' => array(
				'label'             => esc_html__( 'Single Tab Background', 'wc-builder-divi' ),
				'type'     => 'color-alpha',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'tabs',
				'show_if'	=> array(
					'display_type' => 'tabs',
				),
			),
			'active_tab_bg' => array(
				'label'             => esc_html__( 'Active Tab Background', 'wc-builder-divi' ),
				'type'     => 'color-alpha',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'tabs',
				'show_if'	=> array(
					'display_type' => 'tabs',
				),
			),
			'hover_tab_bg' => array(
				'label'             => esc_html__( 'Hover On Tab Background', 'wc-builder-divi' ),
				'type'     => 'color-alpha',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'tabs',
				'show_if'	=> array(
					'display_type' => 'tabs',
				),
			),
			'inputs_bg' => array(
				'label' => esc_html__( 'Inputs Background', 'et_builder' ),
				'type' => 'color-alpha',
				'custom_color' => true,
				'tab_slug' => 'advanced',
				'toggle_slug' => 'forms',
			),
			'form_padding' => array(
				'label' => esc_html__( 'Form Padding', 'et_builder' ),
				'type' => 'custom_margin',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'forms',
			),
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {

		$display_type = $this->props['display_type'];
		$show_heading = $this->props['show_heading'];
		$tabs_alignment = $this->props['tabs_alignment'];
		$tabs_bg = $this->props['tabs_bg'];
		$tab_bg = $this->props['tab_bg'];
		$active_tab_bg = $this->props['active_tab_bg'];
		$hover_tab_bg = $this->props['hover_tab_bg'];
		$inputs_bg = $this->props['inputs_bg'];
		$form_padding = $this->props['form_padding'];

		$login_button_custom        		= $this->props['custom_login_button'];
		$login_button_bg_color       	= $this->props['login_button_bg_color'];
		$login_button_icon       		= $this->props['login_button_icon'];
		$login_button_use_icon       		= $this->props['login_button_use_icon'];
		$login_button_icon_placement     = $this->props['login_button_icon_placement'];

		$register_button_custom        		= $this->props['custom_register_button'];
		$register_button_bg_color       	= $this->props['register_button_bg_color'];
		$register_button_icon       		= $this->props['register_button_icon'];
		$register_button_use_icon       		= $this->props['register_button_use_icon'];
		$register_button_icon_placement     = $this->props['register_button_icon_placement'];

		$reset_password_button_custom        		= $this->props['custom_reset_password_button'];
		$reset_password_button_bg_color       	= $this->props['reset_password_button_bg_color'];
		$reset_password_button_icon       		= $this->props['reset_password_button_icon'];
		$reset_password_button_use_icon       		= $this->props['reset_password_button_use_icon'];
		$reset_password_button_icon_placement     = $this->props['reset_password_button_icon_placement'];

		$this->add_classname( 'wcbd_module' );
		$content = '';
		if( function_exists( 'is_account_page' ) && is_account_page() && !is_user_logged_in() ){

			// text orientation class
			$text_orientation = isset( $this->props['text_orientation'] ) ? esc_attr( $this->props['text_orientation'] ) : '';
			if( $text_orientation ){
				$this->add_classname( "et_pb_text_align_{$text_orientation}" );
			}

			if( $display_type == 'tabs' ){

				// tabs class
				$this->add_classname('wcbd_login_tabs');

				// tabs alignment
				$tabs_alignment != '' ? $this->add_classname('tabs-head-' . esc_attr( $tabs_alignment ) ) : '';

				// tabs bg color
				if( $tabs_bg != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .tabs',
						'declaration' => "background:" . esc_attr( $tabs_bg ) . ";",
					) );
				}

				// single tab bg color
				if( $tab_bg != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .tabs .tab_heading',
						'declaration' => "background:" . esc_attr( $tab_bg ) . ";",
					) );
				}

				// active tab bg color
				if( $active_tab_bg != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .tabs .tab_heading.active, %%order_class%% .tabs .tab_heading.active:hover',
						'declaration' => "background:" . esc_attr( $active_tab_bg ) . ";",
					) );
				}

				// hover on tab bg color
				if( $hover_tab_bg != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% .tabs .tab_heading:hover',
						'declaration' => "background:" . esc_attr( $hover_tab_bg ) . ";",
					) );
				}

				// add the tabs headers
				if( WCBD_Helpers::body_has_class( 'wcbd_account_logged_out_layout' ) && ! WCBD_Helpers::body_has_class( 'woocommerce-lost-password' ) ){
					ob_start();
					?>
						<div class='tabs'>
							<div class="tab_heading active" data-target=".col-1"><?php esc_html_e( 'Login', 'woocommerce' ); ?></div>
							<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
								<div class="tab_heading" data-target=".col-2"><?php esc_html_e( 'Register', 'woocommerce' ); ?></div>
							<?php endif; ?>
						</div>
					<?php
					$content .= ob_get_clean();
				}
			}
			if( $show_heading == 'off' && $display_type == 'default' ){
				$this->add_classname('wcbd_myaccount_login_hide_heading');
			}

			// inputs bg
			if( !empty( $inputs_bg ) ){
				self::set_style( $render_slug, array(
					'selector' => "%%order_class%% form .form-row input",
					'declaration' => "background:". esc_attr( $inputs_bg ) ." !important;"
				) );
			}

			// form padding
			if( !empty( $form_padding ) ){
				$m = explode( '|', $form_padding );
				
				// top padding
				if( isset( $m[0] ) && $m[0] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% form',
						'declaration' => "padding-top:". esc_attr( $m[0] ) ."!important;",
					) );					
				}
				
				// right padding
				if( isset( $m[1] ) && $m[1] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% form',
						'declaration' => "padding-right:". esc_attr( $m[1] ) ."!important;",
					) );					
				}
				
				// bottom padding
				if( isset( $m[2] ) && $m[2] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% form',
						'declaration' => "padding-bottom:". esc_attr( $m[2] ) ."!important;",
					) );					
				}
				
				// left padding
				if( isset( $m[3] ) && $m[3] != '' ){
					ET_Builder_Element::set_style( $render_slug, array(
						'selector'    => '%%order_class%% form',
						'declaration' => "padding-left:". esc_attr( $m[3] ) ."!important;",
					) );					
				}
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
					'button_selector' => "body #page-container %%order_class%% .button[name='login']" 			
				)
			);
			// register button
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $register_button_custom, 
					'button_use_icon' => $register_button_use_icon, 
					'button_icon' => $register_button_icon, 
					'button_icon_placement' => $register_button_icon_placement, 
					'button_bg_color' => $register_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .button[name='register']" 			
				)
			);
			// reset_password button
			WCBD_HELPERS::set_button_style( 
				array(
					'render_slug' => $render_slug, 
					'custom_button' => $reset_password_button_custom, 
					'button_use_icon' => $reset_password_button_use_icon, 
					'button_icon' => $reset_password_button_icon, 
					'button_icon_placement' => $reset_password_button_icon_placement, 
					'button_bg_color' => $reset_password_button_bg_color, 
					'button_selector' => "body #page-container %%order_class%% .woocommerce-ResetPassword .button" 			
				)
			);

			ob_start();
			echo do_shortcode( '[woocommerce_my_account]' );
			$content .= ob_get_clean();
		}
		return $content;

	}
}
new Divi_WC_Builder_Module_MyAccount_Login;
