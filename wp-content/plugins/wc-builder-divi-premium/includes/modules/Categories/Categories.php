<?php
if (!defined('ABSPATH')) exit; // exit if accessed directly

class WCBD_Product_Categories_Module extends ET_Builder_Module
{

    public $vb_support = 'on';
    static $view_products_button_text;

    function init()
    {
        $this->name         = esc_html__('Woo Categories', 'wc-divi-builder');
        $this->slug         = 'et_pb_wcbd_categories';

        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Main Options', 'wc-builder-divi'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'category' => esc_html__('Category', 'wc-builder-divi'),
                ),
            ),
        );

        $this->main_css_element = '%%order_class%%';
        $this->advanced_fields = array(
            'fonts' => array(
                'cat_title' => array(
                    'label'    => esc_html__('Title', 'wc-builder-divi'),
                    'css'      => array(
                        'main' => "%%order_class%% .woocommerce .products .product .woocommerce-loop-category__title",
                    ),
                    'font_size' => array(
                        'default' => '14px',
                    ),
                    'line_height' => array(
                        'default' => '1em',
                    ),
                ),
                'cat_description' => array(
                    'label'    => esc_html__('Description', 'wc-builder-divi'),
                    'css'      => array(
                        'main' => "%%order_class%% .cat_description",
                    ),
                    'font_size' => array(
                        'default' => '14px',
                    ),
                    'line_height' => array(
                        'default' => '1.6em',
                    ),
                ),
                'products_count' => array(
                    'label'    => esc_html__('Products Count', 'wc-builder-divi'),
                    'css'      => array(
                        'main' => "%%order_class%% mark",
                    ),
                    'font_size' => array(
                        'default' => '14px',
                    ),
                    'line_height' => array(
                        'default' => '1em',
                    ),
                ),
            ),
            'button' => array(
                'view_products_button' => array(
                    'label' => esc_html__('View Products Button', 'wc-divi-builder'),
                    'css' => array(
                        'main' => "%%order_class%% .cat_view_products .button",
                        'important' => 'all',
                    ),
                    'box_shadow' => array(
                        'css' => array(
                            'main' => "%%order_class%% .cat_view_products .button",
                        ),
                    ),
                ),
            ),
            'box_shadow' => array(
                'default' => array(),
                'product' => array(
                    'label' => esc_html__('Category Box Shadow', 'wc-builder-divi'),
                    'css' => array(
                        'main' => "body.et_divi_theme %%order_class%% .products .product, body.et_extra %%order_class%% .products .product .product-wrapper",
                    ),
                    'option_category' => 'layout',
                    'tab_slug'        => 'advanced',
                    'toggle_slug'     => 'category',
                ),
            ),
        );
        $this->custom_css_fields = array(
            'category' => array(
                'label'    => esc_html__('Category', 'wc-builder-divi'),
                'selector' => '%%order_class%% li.product',
            ),
            'image' => array(
                'label'    => esc_html__('Image', 'wc-builder-divi'),
                'selector' => '%%order_class%% img',
            ),
            'cat_title' => array(
                'label'    => esc_html__('Title', 'wc-builder-divi'),
                'selector' => '%%order_class%% .woocommerce .products .product .woocommerce-loop-category__title',
            ),
            'cat_description' => array(
                'label'    => esc_html__('Description', 'wc-builder-divi'),
                'selector' => '%%order_class%% .cat_description',
            ),
            'products_count' => array(
                'label'    => esc_html__('Products Count', 'wc-builder-divi'),
                'selector' => '%%order_class%% mark',
            ),
            'view_products' => array(
                'label'    => esc_html__('View Products Button', 'wc-builder-divi'),
                'selector' => '%%order_class%% .cat_view_products .button',
            ),
        );
    }

    function get_fields()
    {
        $fields = array(
            'columns' => array(
                'label'             => esc_html__('Columns Number', 'wc-builder-divi'),
                'type'              => 'select',
                'options'           => array(
                    '0' => esc_html__('-- Default --', 'wc-builder-divi'),
                    '6' => sprintf(esc_html__('%1$s Columns', 'wc-builder-divi'), esc_html('6')),
                    '5' => sprintf(esc_html__('%1$s Columns', 'wc-builder-divi'), esc_html('5')),
                    '4' => sprintf(esc_html__('%1$s Columns', 'wc-builder-divi'), esc_html('4')),
                    '3' => sprintf(esc_html__('%1$s Columns', 'wc-builder-divi'), esc_html('3')),
                    '2' => sprintf(esc_html__('%1$s Columns', 'wc-builder-divi'), esc_html('2')),
                    '1' => esc_html__('1 Column', 'wc-builder-divi'),
                ),
                'computed_affects' => array(
                    '__categories',
                ),
                'default' => '3',
                'description'       => esc_html__('Choose how many columns to display. Default is 3.', 'wc-builder-divi'),
                'toggle_slug'       => 'main_content',
            ),
            'hide_empty' => array(
                'label' => esc_html__('Hide Empty', 'wc-builder-divi'),
                'description'       => esc_html__('Hide categories that have no products.', 'wc-builder-divi'),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__('Yes', 'wc-builder-divi'),
                    'off' => esc_html__('No', 'wc-builder-divi'),
                ),
                'default' => 'off',
                'computed_affects' => array(
                    '__categories',
                ),
                'toggle_slug'       => 'main_content',
            ),
            'hide_subcategories' => array(
                'label' => esc_html__('Hide Subcategories', 'wc-builder-divi'),
                'description'       => esc_html__('Show top level categories only.', 'wc-builder-divi'),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__('Yes', 'wc-builder-divi'),
                    'off' => esc_html__('No', 'wc-builder-divi'),
                ),
                'default' => 'off',
                'computed_affects' => array(
                    '__categories',
                ),
                'toggle_slug'       => 'main_content',
            ),
            'hide_products_count' => array(
                'label' => esc_html__('Hide Products Count', 'wc-builder-divi'),
                'description'       => esc_html__('Hide the products count.', 'wc-builder-divi'),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__('Yes', 'wc-builder-divi'),
                    'off' => esc_html__('No', 'wc-builder-divi'),
                ),
                'default' => 'off',
                'toggle_slug'       => 'main_content',
            ),
            'show_description' => array(
                'label' => esc_html__('Show Category Description', 'wc-builder-divi'),
                'description'       => esc_html__('Show the category description under the title.', 'wc-builder-divi'),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__('Yes', 'wc-builder-divi'),
                    'off' => esc_html__('No', 'wc-builder-divi'),
                ),
                'default' => 'off', 'computed_affects' => array(
                    '__categories',
                ),
                'toggle_slug'       => 'main_content',
            ),
            'view_products_button' => array(
                'label' => esc_html__('View Products Button', 'wc-builder-divi'),
                'description'       => esc_html__('Add a button to take the user to the category page.', 'wc-builder-divi'),
                'type' => 'yes_no_button',
                'options' => array(
                    'on' => esc_html__('Yes', 'wc-builder-divi'),
                    'off' => esc_html__('No', 'wc-builder-divi'),
                ),
                'default' => 'on',
                'computed_affects' => array(
                    '__categories',
                ),
                'toggle_slug'       => 'main_content',
            ),
            'view_products_button_text' => array(
                'label' => esc_html__('Button Text', 'wc-builder-divi'),
                'description'       => esc_html__('View products button text.', 'wc-builder-divi'),
                'type' => 'text',
                'default' => esc_html__('View Products', 'wc-builder-divi'),
                'computed_affects' => array(
                    '__categories',
                ),
                'show_if' => array(
                    'view_products_button' => 'on',
                ),
                'toggle_slug'       => 'main_content',
            ),
            'products_count_bg' => array(
                'label' => esc_html__('Count Background', 'wc-builder-divi'),
                'type' => 'color-alpha',
                'custom_color'      => true,
                'default' => '#FFFF00',
                'tab_slug' => 'advanced',
                'toggle_slug'       => 'products_count',
            ),
            'category_bg' => array(
                'label'             => esc_html__('Category Background', 'wc-builder-divi'),
                'type'              => 'color-alpha',
                'custom_color'      => true,
                'tab_slug'          => 'advanced',
                'toggle_slug'       => 'category',
            ),
            'category_padding' => array(
                'label'             => esc_html__('Category Padding', 'wc-builder-divi'),
                'type'              => 'range',
                'default'            => '0px',
                'tab_slug'          => 'advanced',
                'toggle_slug'       => 'category',
            ),
            '__categories' => array(
                'type' => 'computed',
                'computed_callback' => array('WCBD_Product_Categories_Module', 'get_categories'),
                'computed_depends_on' => array(
                    'columns',
                    'hide_empty',
                    'hide_subcategories',
                    'show_description',
                    'view_products_button',
                    'view_products_button_text',
                ),
            ),
        );

        return $fields;
    }

    static function get_categories($args = array(), $conditional_tags = array(), $current_page = array())
    {
        $columns = isset($args['columns']) ? absint($args['columns']) : 3;
        $hide_empty = $args['hide_empty'] == 'on' ? "true" : "false";

        // module options
        $options = "columns='{$columns}'";
        $options .= " hide_empty='{$hide_empty}'";

        // top level only
        if ($args['hide_subcategories'] == 'on') {
            $options .= " parent='0'";
        }

        // show description
        if ($args['show_description'] == 'on') {
            add_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'category_description'), 12);
        }

        // show view products button
        if ($args['view_products_button'] == 'on') {

            // set the text
            self::$view_products_button_text = $args['view_products_button_text'];

            // add the button
            add_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'view_products_button'), 13);
        }

        $output = do_shortcode("[product_categories {$options}]");

        return $output;
    }

    /**
     * display the category description
     */
    static function category_description($category)
    {
        $description = term_description($category->term_id, $category->taxonomy);

        if (!empty($description)) {
            echo '<div class="cat_description">' . $description . '</div>';
        }
    }

    /**
     * display the view products button
     */
    static function view_products_button($category)
    {
        $text = esc_html(self::$view_products_button_text);

        $link = get_term_link($category);
        if (!is_wp_error($link)) {

            echo "<div class='cat_view_products'>
                <a href='" . esc_url($link) . "' class='button'>" . $text . "</a>
            </div>";
        }
    }

    function render($attrs, $content = null, $render_slug)
    {
        $columns = absint($this->props['columns']);
        $hide_empty = $this->props['hide_empty'] == 'on' ? "true" : "false";
        $hide_products_count = $this->props['hide_products_count'];
        $show_description = $this->props['show_description'];

        $view_products_button = $this->props['view_products_button'];
        self::$view_products_button_text = $this->props['view_products_button_text'];

        $products_count_bg = $this->props['products_count_bg'];

        $category_bg = $this->props['category_bg'];
        $category_padding = $this->props['category_padding'];

        $view_products_button_custom            = $this->props['custom_view_products_button'];
        $view_products_button_bg_color           = $this->props['view_products_button_bg_color'];
        $view_products_button_use_icon           = $this->props['view_products_button_use_icon'];
        $view_products_button_icon               = $this->props['view_products_button_icon'];
        $view_products_button_icon_placement    = $this->props['view_products_button_icon_placement'];

        $classes = array('wcbd_module');

        // module options
        $options = "columns='{$columns}'";
        $options .= " hide_empty='{$hide_empty}'";

        // top level only
        if ($this->props['hide_subcategories'] == 'on') {
            $options .= " parent='0'";
        }

        // products count
        if ($hide_products_count == 'on') {
            $classes[] = 'hide_count';
        }

        // show description
        if ($show_description == 'on') {
            add_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'category_description'), 12);
        }

        // show view products button
        if ($view_products_button == 'on') {
            add_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'view_products_button'), 13);
        }

        // view_products button
        WCBD_HELPERS::set_button_style(
            array(
                'render_slug' => $render_slug,
                'custom_button' => $view_products_button_custom,
                'button_use_icon' => $view_products_button_use_icon,
                'button_icon' => $view_products_button_icon,
                'button_icon_placement' => $view_products_button_icon_placement,
                'button_bg_color' => $view_products_button_bg_color,
                'button_selector' => "body #page-container %%order_class%% .cat_view_products .button"
            )
        );

        // text orientation class
        $text_orientation = isset($this->props['text_orientation']) ? esc_attr($this->props['text_orientation']) : '';
        if ($text_orientation) {
            $this->add_classname("et_pb_text_align_{$text_orientation}");
        }

        // count bg
        if ($hide_products_count != 'on' & !empty($products_count_bg)) {
            ET_Builder_Element::set_style($render_slug, array(
                'selector'    => '%%order_class%% mark',
                'declaration' => "background:" . esc_attr($products_count_bg) . ";",
            ));
        }

        if ('' !== $category_bg) {
            ET_Builder_Element::set_style($render_slug, array(
                'selector'    => "body.et_divi_theme %%order_class%% .products .product, body.et_extra %%order_class%% .products .product .product-wrapper",
                'declaration' => sprintf(
                    'background-color: %1$s !important;',
                    esc_html($category_bg)
                ),
            ));
        }

        if ('' !== $category_padding) {
            ET_Builder_Element::set_style($render_slug, array(
                'selector'    => "body.et_divi_theme %%order_class%% .products .product, body.et_extra %%order_class%% .products .product .product-wrapper",
                'declaration' => sprintf(
                    'padding: %1$s !important;',
                    esc_html($category_padding)
                ),
            ));
        }

        // get the categories
        $this->add_classname($classes);
        $output = do_shortcode("[product_categories {$options}]");

        /**
         * Reset some filters in case the module used twice
         */

        // show description
        if ($show_description == 'on') {
            remove_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'category_description'), 12);
        }
        // show view products button
        if ($view_products_button == 'on') {
            remove_action('woocommerce_after_subcategory', array('WCBD_Product_Categories_Module', 'view_products_button'), 13);
        }

        return $output;
    }
}
new WCBD_Product_Categories_Module;
