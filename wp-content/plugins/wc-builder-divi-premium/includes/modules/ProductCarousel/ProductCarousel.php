<?php
class WCBD_Divi_Wc_Product_Carousel extends ET_Builder_Module {

    /**
     * The current custom taxonomy name
     *
     * @var string
     */
    private $custom_taxonomy    = null;

    /**
     * Store the id of the current custom taxonomy
     *
     * @var int
     */
    private $custom_term_id     = 0;

	/**
	 * Module properties initialization
	 *
	 * @since 3.2.0
	 */
	function init() {
		$this->name         = esc_html__( 'Woo Product Carousel', 'wc-builder-divi' );
        $this->slug         = 'et_pb_wcbd_product_carousel';
        $this->vb_support   = 'on';
        $this->icon_path    = plugin_dir_path( __FILE__ ). 'icon.svg';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'wc-builder-divi' ),
					'carousel' => esc_html__( 'Carousel Settings', 'wc-builder-divi' ),
					'elements' => esc_html__( 'Elements', 'wc-builder-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
                    'text'    => array(
                        'title' => esc_html__( 'Text', 'wc-builder-divi' ),
                        'priority' => 1
                    ),
					'arrows' => array(
                        'title' => esc_html__( 'Arrows', 'wc-builder-divi' ),
                        'priority' => 38
                    ),
					'dots' => array(
                        'title' => esc_html__( 'Dots', 'wc-builder-divi' ),
                        'priority' => 39
                    ),
					'product' => array(
                        'title' => esc_html__( 'Product', 'wc-builder-divi' ),
                        'priority' => 40
                    ),
					'sale_badge' => array(
                        'title' => esc_html__( 'Sale Badge', 'wc-builder-divi' ),
                        'priority' => 42,
                    ),
					'image'   => array(
                        'title' => esc_html__( 'Product Image', 'wc-builder-divi' ),
                        'priority' => 43
                    ),
					'star'    => array(
                        'title' => esc_html__( 'Star Rating', 'wc-builder-divi' ),
                        'priority' => 54
                    )
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
                'arrows'     => array(
					'label'            => esc_html__( 'Arrows', 'wc-builder-divi' ),
					'css'              => array(
						'main'                 => "%%order_class%% .slick-arrow",
					),
					'hide_font'        => true,
					'hide_text_shadow' => true,
					'hide_letter_spacing' => true,
					'hide_text_align' => true,
					'font_size'        => array(
                        'label' => esc_html__( 'Arrows Size', 'wc-builder-divi' ),
                        'default' => '20px'
					),
					'text_color'       => array(
                        'label' => esc_html__( 'Arrows Color', 'wc-builder-divi' ),
                        'default' => '#ffffff'
                    ),
                    'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
                        ),
                        'default' => '1'
					),
					'toggle_slug'      => 'arrows',
                ),
                'product_title' => array(
					'label'    => esc_html__( 'Product Title', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% ul.carousel-products li.product .woocommerce-loop-product__title",
						'important' => 'plugin_only',
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce ul.carousel-products li.product .price, %%order_class%% .woocommerce ul.carousel-products li.product .price .amount",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'sale_price' => array(
					'label'           => esc_html__( 'Sale Price', 'wc-builder-divi' ),
					'css'             => array(
						'main' => "%%order_class%% .woocommerce ul.carousel-products li.product .price ins .amount",
					),
					'hide_text_align' => true,
					'font'            => array(
						'default' => '|700|||||||',
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'sale_badge' => array(
					'label'           => esc_html__( 'Sale Badge', 'wc-builder-divi' ),
					'css'             => array(
						'main'      => "%%order_class%% .woocommerce ul.carousel-products li.product .onsale",
						'important' => array( 'line-height', 'font', 'text-shadow' ),
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => '1.3em',
					),
					'font_size'       => array(
						'default' => '20px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'rating'     => array(
					'label'            => esc_html__( 'Star Rating', 'wc-builder-divi' ),
					'css'              => array(
						'main'                 => "%%order_class%% .star-rating",
						'hover'                => "%%order_class%% li.product:hover .star-rating",
						'color'                => "%%order_class%% .star-rating > span:before",
						'color_hover'          => "%%order_class%% li.product:hover .star-rating > span:before",
						'letter_spacing_hover' => "%%order_class%% li.product:hover .star-rating",
						'important'            => array( 'size' ),
					),
					'font_size'        => array(
						'default' => '14px',
					),
					'hide_font'        => true,
					'hide_line_height' => true,
					'hide_text_shadow' => true,
					'hide_letter_spacing' => true,
					'text_align'       => array(
						'label' => esc_html__( 'Star Rating Alignment', 'wc-builder-divi' ),
					),
					'font_size'        => array(
						'label' => esc_html__( 'Star Rating Size', 'wc-builder-divi' ),
					),
					'text_color'       => array(
						'label' => esc_html__( 'Star Rating Color', 'wc-builder-divi' ),
					),
					'toggle_slug'      => 'star',
                ),
                'excerpt' => array(
					'label'    => esc_html__( 'Short Description', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .woocommerce-product-details__short-description p",
						'important' => 'plugin_only',
					),
                ),
                'view_cart' => array(
					'label'    => esc_html__( 'View Cart', 'wc-builder-divi' ),
					'css'      => array(
						'main' => "%%order_class%% .added_to_cart",
						'important' => 'plugin_only',
					),
                ),               
            ),
			'borders'               => array(
                'default' => array(),
                'arrows' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .carousel-arrow",
							'border_styles' => "%%order_class%% .carousel-arrow",
						),
						'important' => 'all',
                    ),
                    'defaults' => array(
						'border_radii' => 'on|3px|3px|3px|3px',
					),
					'label_prefix' => esc_html__( 'Arrow', 'wc-builder-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'arrows',
				),
                'product' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .product",
							'border_radii_hover'  => "%%order_class%% .product:hover",
							'border_styles' => "%%order_class%% .product",
						),
						'important' => 'all',
					),
					'label_prefix' => esc_html__( 'Product', 'wc-builder-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'product',
				),
				'image' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "%%order_class%% img",
							'border_styles' => "%%order_class%% img",
						),
						'important' => 'all',
					),
					'label_prefix' => esc_html__( 'Image', 'wc-builder-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'            => array(
				'default' => array(),
				'product'   => array(
					'label'           => esc_html__( 'Product Box Shadow', 'wc-builder-divi' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'product',
					'css'             => array(
						'main'         => "%%order_class%% .product",
						'important' => true,
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'wc-builder-divi' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'         => "%%order_class%%.et_pb_module .woocommerce .et_shop_image > img",
						'important' => true,
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => "%%order_class%%",
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'filters'               => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => "%%order_class%% .et_shop_image",
				),
			),
			'scroll_effects'        => array(
				'grid_support' => 'yes',
			),
            'button'         => array(
				'add_to_cart' => array(
					'label'          => esc_html__('Add To Cart', 'wc-builder-divi'),
					'css'            => array(
						'main'         => "%%order_class%% .button",
						'important'    => 'all',
					),
					'use_alignment'  => false,
					'box_shadow'     => array(
						'css' => array(
							'main'      => "%%order_class%% .button",
							'important' => true,
						),
                    ),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
            'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
		);

		$this->custom_css_fields = array(
			'prev_arrow' => array(
				'label'    => esc_html__( 'Previous Arrow', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .carousel-prev",
			),
			'next_arrow' => array(
				'label'    => esc_html__( 'Next Arrow', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .carousel-next",
			),
			'dots' => array(
				'label'    => esc_html__( 'Dots', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .slick-dots li",
			),
			'active_dot' => array(
				'label'    => esc_html__( 'Active Dot', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .slick-dots .slick-active",
			),
			'product' => array(
				'label'    => esc_html__( 'Product', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product",
			),
			'onsale' => array(
				'label'    => esc_html__( 'Sale Badge', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product .onsale",
			),
			'image' => array(
				'label'    => esc_html__( 'Image', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .et_shop_image",
			),
			'title' => array(
				'label'    => esc_html__( 'Product Title', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product .woocommerce-loop-product__title",
			),
			'rating' => array(
				'label'    => esc_html__( 'Star Rating', 'wc-builder-divi' ),
				'selector' => "%%order_class%% .star-rating",
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product .price",
			),
			'price_old' => array(
				'label'    => esc_html__( 'Old Price', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product .price del span.amount",
            ),
            'excerpt' => array(
				'label'    => esc_html__( 'Short Description', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product .woocommerce-product-details__short-description",
            ),
            'button' => array(
				'label'    => esc_html__( 'Add To Cart', 'wc-builder-divi' ),
				'selector' => "%%order_class%% li.product button",
			),
		);
	}

	/**
	 * Module's specific fields
	 *
	 * @since 3.2.0
	 *
	 * @return array
	 */
	function get_fields() {
		return array(
			'type' => array(
				'label'           => esc_html__( 'Products Type', 'wc-builder-divi' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
                'description'     => esc_html__( 'The type of products to display.', 'wc-builder-divi' ),
                'options' => array(
                    'recent'           => esc_html__( 'Recent Products', 'wc-builder-divi' ),
					'featured'         => esc_html__( 'Featured Products', 'wc-builder-divi' ),
					'sale'             => esc_html__( 'Sale Products', 'wc-builder-divi' ),
					'best_selling'     => esc_html__( 'Best Selling Products', 'wc-builder-divi' ),
					'top_rated'        => esc_html__( 'Top Rated Products', 'wc-builder-divi' ),
                ),
                'tab_slug' => 'general',
				'toggle_slug'     => 'main_content',
				'computed_affects' => array(
					'__product_carousel',
				),
            ),
            'from' => array(
				'label'           => esc_html__( 'Products From', 'wc-builder-divi' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
                'description'     => esc_html__( 'Display products from all categories, specific categories or dynamically based on the current archive page.', 'wc-builder-divi' ),
                'options' => array(
                    'shop'           => esc_html__( 'The Entire Shop', 'wc-builder-divi' ),
                    'categories' => esc_html__( 'Specific Categories', 'wc-builder-divi' ),
                    'current_page' => esc_html__( 'Current Archive Page', 'wc-builder-divi' ),
                ),
                'tab_slug' => 'general',
				'toggle_slug'     => 'main_content',
				'computed_affects' => array(
					'__product_carousel',
				),
            ),
			'include_categories'  => array(
				'label'            => esc_html__( 'Categories', 'wc-builder-divi' ),
				'type'             => 'categories',
				'renderer_options' => array(
					'use_terms' => true,
					'term_name' => 'product_cat',
				),
				'show_if'  => array(
                    'from' => 'categories',
                ),
                'computed_affects' => array(
					'__product_carousel',
				),
				'description'      => esc_html__( 'Choose which categories you would like to include.', 'wc-builder-divi' ),
				'taxonomy_name'    => 'product_cat',
                'tab_slug' => 'general',
				'toggle_slug'      => 'main_content',
			),
            'posts_number'        => array(
				'label'            => esc_html__( 'Total Products Count', 'wc-builder-divi' ),
				'type'             => 'text',
				'default'          => '12',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'How many products to be included in the carousel.', 'wc-builder-divi' ),
				'computed_affects' => array(
					'__product_carousel',
				),
                'tab_slug' => 'general',
				'toggle_slug'      => 'carousel',
            ),
            'slides_to_show' => array(
                'label' => esc_html__( 'Slide To Show', 'wc-builder-divi' ),
                'description' => esc_html__( 'How many products to display on desktop, tablet and mobile.', 'wc-builder-divi' ),
                'type' => 'text',
                'default' => '4',
                'mobile_options' => true,
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'slides_to_scroll' => array(
                'label' => esc_html__( 'Slide To Scroll', 'wc-builder-divi' ),
                'description' => esc_html__( 'Number of products to scroll on desktop, tablet and mobile.', 'wc-builder-divi' ),
                'type' => 'text',
                'default' => '1',
                'mobile_options' => true,
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'autoplay' => array(
                'label' => esc_html__( 'Autoplay', 'wc-builder-divi' ),
                'description' => esc_html__( 'Enables Autoplay.', 'wc-builder-divi' ),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
                    'off' => esc_html__( 'No', 'wc-builder-divi' ),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel'
            ),
            'autoplay_speed' => array(
                'label' => esc_html__( 'Autoplay Speed', 'wc-builder-divi' ),
                'description' => esc_html__( 'Autoplay Speed in milliseconds, 3000 means 3 seconds.', 'wc-builder-divi' ),
                'type' => 'text',
                'default' => '3000',
                'show_if' => array(
                    'autoplay' => 'on',
                ),
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'arrows' => array(
                'label' => esc_html__( 'Show Arrows', 'wc-builder-divi' ),
                'description' => esc_html__( 'Show Prev/Next Arrows.', 'wc-builder-divi' ),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
                    'off' => esc_html__( 'No', 'wc-builder-divi' ),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'arrow_bg' => array(
                'label' => esc_html__( 'Arrow Background', 'wc-builder-divi' ),
                'description' => esc_html__( 'Set the arrows background color.', 'wc-builder-divi' ),
                'type' => 'color-alpha',
                'default' => '#333333',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'arrows',
            ),
            'dots' => array(
                'label' => esc_html__( 'Show Dots', 'wc-builder-divi' ),
                'description' => esc_html__( 'Show dot indicators.', 'wc-builder-divi' ),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
                    'off' => esc_html__( 'No', 'wc-builder-divi' ),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'dots_color' => array(
                'label' => esc_html__( 'Dots Color', 'wc-builder-divi' ),
                'description' => esc_html__( 'Set the color of the dots.', 'wc-builder-divi' ),
                'type' => 'color-alpha',
                'default' => '#000000',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'dots',
                'show_if' => array(
                    'dots' => 'on',
                ),
            ),
            'infinite' => array(
                'label' => esc_html__( 'Infinite Loop', 'wc-builder-divi' ),
                'description' => esc_html__( 'Infinite loop sliding.', 'wc-builder-divi' ),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
                    'off' => esc_html__( 'No', 'wc-builder-divi' ),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'pause_on_hover' => array(
                'label' => esc_html__( 'Pause On Hover', 'wc-builder-divi' ),
                'description' => esc_html__( 'Pause autoplay on hover.', 'wc-builder-divi' ),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__( 'Yes', 'wc-builder-divi' ),
                    'off' => esc_html__( 'No', 'wc-builder-divi' ),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'slides_space' => array(
                'label' => esc_html__( 'Space Between Slides', 'wc-builder-divi' ),
                'description' => esc_html__( 'Set the space between slides.', 'wc-builder-divi' ),
                'type' => 'range',
                'default' => '20px',
                'tab_slug' => 'general',
                'toggle_slug' => 'carousel',
            ),
            'show_sale_badge'     => array(
				'label'            => esc_html__( 'Show Sale Badge', 'wc-builder-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default'          => 'on',
                'tab_slug' => 'general',
				'toggle_slug'      => 'elements',
            ),
            'show_rating'     => array(
				'label'            => esc_html__( 'Show Rating', 'wc-builder-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default'          => 'on',
                'tab_slug' => 'general',
				'toggle_slug'      => 'elements',
            ),
            'show_price'     => array(
				'label'            => esc_html__( 'Show Price', 'wc-builder-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
				'default'          => 'on',
                'tab_slug' => 'general',
				'toggle_slug'      => 'elements',
            ),
            'show_excerpt'     => array(
				'label'            => esc_html__( 'Show Short Description', 'wc-builder-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
                'default'          => 'off',
                'computed_affects' => array(
					'__product_carousel',
				),
                'tab_slug' => 'general',
				'toggle_slug'      => 'elements',
            ),
            'show_add_to_cart'     => array(
				'label'            => esc_html__( 'Show Add To Cart Button', 'wc-builder-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'wc-builder-divi' ),
					'off' => esc_html__( 'No', 'wc-builder-divi' ),
				),
                'default'          => 'off',
                'computed_affects' => array(
					'__product_carousel',
				),
                'tab_slug' => 'general',
				'toggle_slug'      => 'elements',
            ),
            'orderby'             => array(
				'label'            => esc_html__( 'Order By', 'wc-builder-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'menu_order' => esc_html__( 'Menu Order', 'wc-builder-divi' ),
					'popularity' => esc_html__( 'Popularity', 'wc-builder-divi' ),
					'rating'     => esc_html__( 'Rating', 'wc-builder-divi' ),
					'date'       => esc_html__( 'Date: Oldest To Newest', 'wc-builder-divi' ),
					'date-desc'  => esc_html__( 'Date: Newest To Oldest', 'wc-builder-divi' ),
					'price'      => esc_html__( 'Price: Low To High', 'wc-builder-divi' ),
					'price-desc' => esc_html__( 'Price: High To Low', 'wc-builder-divi' ),
				),
				'default_on_front' => 'menu_order',
				'description'      => esc_html__( 'Choose how your products should be ordered.', 'wc-builder-divi' ),
				'computed_affects' => array(
					'__product_carousel',
				),
                'tab_slug' => 'general',
				'toggle_slug'      => 'main_content',
            ),
            'product_bg' => array(
                'label' => esc_html__( 'Product Background', 'wc-builder-divi' ),
                'type' => 'color-alpha',
                'custom_color'   => true,
                'description'    => esc_html__( 'Individual products background color.', 'wc-builder-divi' ),
                'tab_slug' => 'advanced',
                'toggle_slug' => 'product',
            ),
            'product_padding' => array(
                'label' => esc_html__( 'Product Padding', 'wc-builder-divi' ),
                'type' => 'custom_margin',
                'description'    => esc_html__( 'Add a space around the product.', 'wc-builder-divi' ),
                'tab_slug' => 'advanced',
                'toggle_slug' => 'product',
            ),
            'sale_badge_bg'    => array(
				'label'          => esc_html__( 'Sale Badge Background', 'wc-builder-divi' ),
				'description'    => esc_html__( 'Pick a color to use for the sales bade that appears on products that are on sale.', 'wc-builder-divi' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'sale_badge',
            ),
            '__product_carousel'              => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'WCBD_Divi_Wc_Product_Carousel', 'get_carousel_html' ),
				'computed_depends_on' => array(
					'type',
					'from',
                    'include_categories',
                    'show_excerpt',
                    'show_add_to_cart',
					'posts_number',
					'orderby',
					'columns_number',
					'show_pagination',
				),
            ),
		);
	}

    /**
     * check if this is a wc product taxonomy page
     *
     * @return boolean
     */
    function is_product_taxonomy() {
        return function_exists( 'is_product_taxonomy' ) && is_product_taxonomy();
    }

    /**
     * returns the slugs of the selected categories
     *
     * @param string $ids
     * @return mixed
     */
    function get_terms_slugs($ids = '', $taxonomy = 'product_cat'){

        if(empty($ids)){
            return false;
        }

        $ids = explode(',', $ids);

        if(in_array('all', $ids)){
            return false;
        }

        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'include' => $ids,
            'fields' => 'slugs'
        ));

        if(!empty($terms)){
            return implode(',', $terms);
        }

        return false;
    }

    /**
	 * Get the carousel HTML for the visual builder
	 *
	 * @param array   arguments that affect the output
	 * @param array   passed conditional tag for update process
	 * @param array   passed current page params
	 * @return string HTML markup for carousel module
	 */
    static function get_carousel_html( $args = array(), $conditional_tags = array(), $current_page = array() ){

        $carousel = new self();

		do_action( 'dk_wc_before_get_carousel' );

		$carousel->props = $args;

		// Get the carousel
		$output = $carousel->get_carousel_content( array(), array(), $current_page );

		do_action( 'dk_wc_after_get_carousel' );

		return $output;
    }

    /**
     * Get the carousel HTML for the visual builder and the frontend
     *
     * @param array $args
     * @param array $conditional_tags
     * @param array $current_page
     * @return string
     */
	function get_carousel_content( $args = array(), $conditional_tags = array(), $current_page = array() ) {

        $type               = $this->props['type'];
		$from               = $this->props['from'];
        $posts_number       = $this->props['posts_number'];
        $excerpt            = $this->props['show_excerpt'];
        $add_to_cart        = $this->props['show_add_to_cart'];
		$orderby            = 'recent' === $type ? 'id' : $this->props['orderby'];
		$order              = 'recent' === $type ? 'DESC' : 'ASC';
        $include_categories = $this->props['include_categories'];
		$use_current_loop   = $from == 'current_page' && ( is_post_type_archive( 'product' ) || is_search() || $this->is_product_taxonomy() );
        
        $product_categories = '';
		$product_tags       = array();
		$product_attribute  = '';
        $product_terms      = array();

		if ( $use_current_loop ) {

			if ( is_product_category() ) {
				$product_categories = (string) get_queried_object()->slug;
			} else if ( is_product_tag() ) {
				$product_tags = array( get_queried_object()->slug );
			} else if ( $this->is_product_taxonomy() ) {
                $term = get_queried_object();
                
				// Product attribute taxonomy slugs start with pa_
				if ( strpos( $term->taxonomy, 'pa_' ) === 0 ) {
					$product_attribute = $term->taxonomy;
					$product_terms[]   = $term->slug;
				}else{
                    // custom taxonomy
                    $this->custom_taxonomy = esc_attr( $term->taxonomy );
                    $this->custom_term_id = absint( $term->term_id );
                }
			}
		}

		if ( 'categories' === $from && ! empty( $this->props['include_categories'] ) ) {
			$product_categories = $this->get_terms_slugs($include_categories, 'product_cat');
		}

		if ( in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ) {
			$orderby = str_replace( '-desc', '', $orderby );
			$order = 'DESC';
		}

		$ids             = array();
		$wc_custom_view  = '';
		$wc_custom_views = array(
			'sale'         => array( 'on_sale', 'true' ),
			'best_selling' => array( 'best_selling', 'true' ),
			'top_rated'    => array( 'top_rated', 'true' ),
			'featured'     => array( 'visibility', 'featured' ),
		);
       
        if(in_array($type, array_keys($wc_custom_views))){
			$custom_view_data = $wc_custom_views[ $type ];
			$wc_custom_view   = sprintf( '%1$s="%2$s"', esc_attr( $custom_view_data[0] ), esc_attr( $custom_view_data[1] ) );            
        }

		$shortcode = sprintf(
			'[products paginate="false" columns="4" %1$s limit="%2$s" orderby="%3$s" %4$s order="%5$s" %6$s %7$s %8$s %9$s]',
			$wc_custom_view,
            esc_attr( $posts_number ),
			esc_attr( $orderby ),
			$product_categories ? sprintf( 'category="%s"', esc_attr($product_categories)) : '',
			esc_attr( $order ),
			$ids ? sprintf( 'ids="%s"', esc_attr( implode( ',', $ids ) ) ) : '',
			$product_tags ? sprintf( 'tag="%s"', esc_attr( implode( ',', $product_tags ) ) ) : '',
			$product_attribute ? sprintf( 'attribute="%s"', esc_attr( $product_attribute ) ) : '',
			$product_terms ? sprintf( 'terms="%s"', esc_attr( implode( ',', $product_terms ) ) ) : ''
        );
        
        $shortcode = apply_filters('dk_wc_carousel_shortcode', $shortcode);

        do_action( 'dk_wc_before_print_carousel' );

		global $wp_the_query;

		$query_backup = $wp_the_query;

        if ( $use_current_loop ) {
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'filter_products_query' ) );
			add_action( 'pre_get_posts', array( $this, 'apply_woo_widget_filters' ), 10 );
        }

        // excerpt
        if($excerpt == 'on'){
            add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt');
        }

        // add to cart
        if($add_to_cart == 'on'){
            add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
        }

        // load the carousel assets
        wp_enqueue_style('wcbd-carousel-lib-css');
        wp_enqueue_style('wcbd-carousel-lib-default-css');
        wp_enqueue_script('wcbd-carousel-lib-js');
        wp_enqueue_script('wcbd-carousel-init-js');

		$carousel = do_shortcode( $shortcode );

		if ( $use_current_loop ) {
			remove_action( 'pre_get_posts', array( $this, 'apply_woo_widget_filters' ), 10 );
            remove_filter( 'woocommerce_shortcode_products_query', array( $this, 'filter_products_query' ) );
            
            $this->custom_taxonomy = $this->custom_term_id = null;
        }

		$wp_the_query = $query_backup;

        do_action( 'dk_wc_after_print_carousel' );

        // excerpt
        if($excerpt == 'on'){
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt');
        }

        // add to cart
        if($add_to_cart == 'on'){
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
        }

        $is_shop_empty = preg_match( '/<div class="woocommerce columns-([0-9 ]+)"><\/div>+/', $carousel );

		if ( $is_shop_empty || strpos( $carousel, $shortcode ) ) {
			$carousel = "";
        }
        
		return apply_filters( 'dk_wc_product_carousel_html', $carousel, $shortcode );
    }

    /**
	 * Render module output
	 *
	 * @since 3.2.0
	 *
	 * @param array  $attrs       List of unprocessed attributes
	 * @param string $content     Content being processed
	 * @param string $render_slug Slug of module that is used for rendering output
	 *
	 * @return string module's rendered output
	 */ 
    function render( $attrs, $content = null, $render_slug ) {
        $dots_color                 = $this->props['dots_color'];
        $arrow_bg                   = $this->props['arrow_bg'];
        $slides_space               = $this->props['slides_space'];

        $sale_badge         = $this->props['show_sale_badge'];
        $rating             = $this->props['show_rating'];
        $price              = $this->props['show_price'];
        
        $product_bg                 = $this->props['product_bg'];
        $product_padding            = $this->props['product_padding'];

        $sale_badge_bg              = $this->props['sale_badge_bg'];

        $rating_text_align          = $this->props['rating_text_align'];
        
        $show_add_to_cart           = $this->props['show_add_to_cart'];
        $add_to_cart_icon           = $this->props['add_to_cart_icon'];
        $custom_add_to_cart         = $this->props['custom_add_to_cart'];
        $add_to_cart_use_icon       = $this->props['add_to_cart_use_icon'];
        $add_to_cart_icon_placement = $this->props['add_to_cart_icon_placement'];

        $this->add_classname('wcbd_module');

        // arrow background
        if(!empty($arrow_bg)){
            ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "%%order_class%% .carousel-arrow",
				'declaration' => 'background:'. esc_attr($arrow_bg) .';',
			) );
        }

        // dots color
        if(!empty($dots_color)){
            ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "%%order_class%% .slick-dots li",
				'declaration' => 'background:'. esc_attr($dots_color) .';',
			) );
        }

        // space between slides
        if(!empty($slides_space)){
            ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "%%order_class%% ul.carousel-products .product",
				'declaration' => 'margin:'. esc_attr($slides_space) .';',
			) );
        }

        // product background
        if(!empty($product_bg)){
            ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "%%order_class%% .product",
				'declaration' => 'background:'. esc_attr($product_bg) .';',
			) );
        }

        // product padding
        WCBD_HELPERS::render_padding_margin(
            'padding', 
            $render_slug, 
            "%%order_class%% .product", 
            $product_padding
        );

		// Sale Badge Color.
		if ( !empty($sale_badge_bg) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "%%order_class%% span.onsale",
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_bg )
				),
			) );
		}

        // star rating align
        if(!empty($rating_text_align)){
            $this->add_classname( 'dk_wc_rating_' . esc_attr($rating_text_align) );
        }

		// text align classnames
		$this->add_classname( array(
			$this->get_text_orientation_classname(),
        ) );
        
        // sale badge
        if($sale_badge == 'off'){
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
        }

        // rating
        if($rating == 'off'){
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        }

        // price
        if($price == 'off'){
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
        }

        // add to cart button
        if($show_add_to_cart == 'on' && $custom_add_to_cart == 'on'){

            // button icon
            if ( $add_to_cart_use_icon != 'off' && $add_to_cart_icon != '' ) {

                    // button icon
                    $button_icon_content = WCBD_INIT::et_icon_css_content(esc_attr($add_to_cart_icon));

                    if (!empty($button_icon_content)) {

                        $icon_selector = "%%order_class%% li.product .button";

                        switch ($add_to_cart_icon_placement) {
                            case 'right':
                                $icon_selector .= ':after';
                                break;
                            case 'left':
                                $icon_selector .= ':before';
                                break;

                            default:
                                $icon_selector .= ':after';
                                break;
                        }

                        ET_Builder_Element::set_style(
                            $render_slug,
                            array(
                                'selector' => $icon_selector,
                                'declaration' => "content: '{$button_icon_content}'!important;font-family:ETmodules!important;"
                            )
                        );
                    }
            }
        }

        // slides to show with their default values
        $slides_desktop = !empty($this->props['slides_to_show']) ? absint($this->props['slides_to_show']) : '4';
        $slides_tablet  = !empty($this->props['slides_to_show_tablet']) ? absint($this->props['slides_to_show_tablet']) : $slides_desktop;
        $slides_phone   = !empty($this->props['slides_to_show_phone']) ? absint($this->props['slides_to_show_phone']) : $slides_tablet;

        // slides to scroll with their default values
        $scroll_desktop = !empty($this->props['slides_to_scroll']) ? absint($this->props['slides_to_scroll']) : '1';
        $scroll_tablet  = !empty($this->props['slides_to_scroll_tablet']) ? absint($this->props['slides_to_scroll_tablet']) : $scroll_desktop;
        $scroll_phone  = !empty($this->props['slides_to_scroll_phone']) ? absint($this->props['slides_to_scroll_phone']) : $scroll_tablet;

        // carousel settings with default values
        $carousel_settings = array(
            'slides_desktop'            => $slides_desktop,
            'slides_tablet'             => $slides_tablet,
            'slides_phone'              => $slides_phone,
            'scroll_desktop'            => $scroll_desktop,
            'scroll_tablet'             => $scroll_tablet,
            'scroll_phone'              => $scroll_phone,
            'autoplay'                  => esc_html($this->props['autoplay']),
            'autoplay_speed'            => !empty($this->props['autoplay_speed']) ? absint($this->props['autoplay_speed']) : 3000,
            'arrows'                    => esc_html($this->props['arrows']),
            'dots'                      => esc_html($this->props['dots']),
            'infinite'                  => esc_html($this->props['infinite']),
            'pause_on_hover'            => esc_html($this->props['pause_on_hover'] ),
        );

        $carousel_content = $this->get_carousel_content( array(), array(), array( 'id' => $this->get_the_ID() ) );
        $carousel = sprintf(
            '<div class="dk-carousel-wrapper" data-settings="%2$s">%1$s</div>', 
            $carousel_content,
            esc_html( wp_json_encode($carousel_settings) )
        );

        /**
         * Re-add the removed elements
         */
        // sale badge
        if($sale_badge == 'off'){
            add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
        }

        // rating
        if($rating == 'off'){
            add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        }

        // price
        if($price == 'off'){
            add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
        }
        
        return $carousel;
    }

	/**
	 * Filter the products query arguments.
	 *
	 * @param array $query_args
	 *
	 * @return array
	 */
	public function filter_products_query( $query_args ) {
		if ( is_search() ) {
			$query_args['s'] = get_search_query();
		}
		if ( function_exists( 'WC' ) ) {
			$query_args['meta_query'] = WC()->query->get_meta_query($query_args['meta_query'], true);
			$query_args['tax_query']  = WC()->query->get_tax_query($query_args['tax_query'], true);

			// Add fake cache-busting argument as the filtering is actually done in self::apply_woo_widget_filters().
            $query_args['nocache'] = microtime( true );
            
            // custom taxonomy
            if($this->custom_taxonomy && $this->custom_term_id){
                $query_args['tax_query'][] = array(
                    'taxonomy' => $this->custom_taxonomy,
                    'terms' => $this->custom_term_id
                );               
            }
           
		}
		return $query_args;
	}
    
	/**
	 * Filter the products shortcode query so Woo widget filters apply.
	 *
	 * @param WP_Query $query
	 */
	public function apply_woo_widget_filters( $query ) {
		global $wp_the_query;

		// Trick Woo filters into thinking the products shortcode query is the
		// main page query as some widget filters have is_main_query checks.
		$wp_the_query = $query;

        // Set a flag to track that the main query is falsified.
		$wp_the_query->wcbd_products_carousel_query = true;

		if ( function_exists( 'WC' ) ) {
			add_filter( 'posts_clauses', array( WC()->query, 'price_filter_post_clauses' ), 10, 2 );
		}
    }
}

new WCBD_Divi_Wc_Product_Carousel;