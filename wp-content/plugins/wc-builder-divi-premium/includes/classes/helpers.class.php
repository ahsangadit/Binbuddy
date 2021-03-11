<?php
if (!defined('ABSPATH')) exit; // exit if accessed directly

/**
 * This class contains some helper methods and 3rd party compatibility fixes
 * @since 2.1.7
 */

if (!class_exists('WCBD_HELPERS')) :
    class WCBD_HELPERS
    {

        public static $post_id, $item_id, $item_title;

        /**
         * Switch the site language to another language
         * @since 2.1.7
         * @param string $to the language code you want switch to, accepts languages codes, all & original
         * @see https://wpml.org/forums/topic/how-to-filter-get_terms-function-my-own-language/
         */
        public static function wpml_switch_lang($to = '')
        {
            global $sitepress;
            if ($sitepress) {
                $to = esc_html($to);
                // get the original language
                if ($to == 'original' && defined('ICL_LANGUAGE_CODE')) {
                    $to = ICL_LANGUAGE_CODE;
                }
                // Switch to new language
                if ($to) {
                    $sitepress->switch_lang($to);
                }
            }
        }

        /**
         * WPML
         * get language code
         * @since 2.1.7
         * @param int $object_id This is the object id of a post or a taxonomy
         * @param string $object_type This the object type; post type or taxonomy
         * @see https://wpml.org/wpml-hook/wpml_element_language_code/
         */
        public static function wpml_get_lang_code($element_id = '', $element_type = '')
        {
            $language_code = apply_filters(
                'wpml_element_language_code',
                null,
                array(
                    'element_id' => (int) $element_id,
                    'element_type' => esc_html($element_type)
                )
            );

            return $language_code;
        }

        /**
         * Check is the body has a specific class
         * @since 2.2
         */
        public static function body_has_class($class)
        {
            $body_classes = get_body_class();

            if (is_array($body_classes) && in_array(esc_html($class), $body_classes)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Fix WC issue with iPads by changing the small screen breakpoint from 768px to 767px
         * @see https://github.com/woocommerce/woocommerce/issues/14813
         * @since 2.1.10  
         * @param string $breakpoint
         * @return string
         */
        public static function change_wc_smallscreen_breakpoint($breakpoint)
        {
            return '767px';
        }

        /**
         * set buttons styles
         * @since 2.2.0
         */
        public static function set_button_style($args = array())
        {

            $defaults = array(
                'render_slug'               => '',
                'custom_button'             => 'off',
                'button_use_icon'           => 'on',
                'button_icon'               => '5',
                'button_icon_placement'     => 'right',
                'button_bg_color'           => 'transparent',
                'button_selector'           => ''
            );
            $args = wp_parse_args($args, $defaults);

            // some cleaning
            $render_slug             = esc_html($args['render_slug']);
            $custom_button             = esc_html($args['custom_button']);
            $button_icon             = esc_html($args['button_icon']);
            $button_use_icon         = esc_html($args['button_use_icon']);
            $button_bg_color         = esc_html($args['button_bg_color']);
            $button_icon_placement     = esc_html($args['button_icon_placement']);
            $button_selector         = $args['button_selector'];

            if ($custom_button == 'on') {

                // button icon
                if ($button_use_icon == 'on' && $button_icon !== '') {
                    $icon_content = WCBD_INIT::et_icon_css_content($button_icon);

                    $icon_selector = '';
                    if ($button_icon_placement == 'right') {
                        $icon_selector = $button_selector . ':after';
                    } elseif ($button_icon_placement == 'left') {
                        $icon_selector = $button_selector . ':before';
                    }

                    if (!empty($icon_content) && !empty($icon_selector)) {
                        ET_Builder_Element::set_style(
                            $render_slug,
                            array(
                                'selector' => $icon_selector,
                                'declaration' => "content: '{$icon_content}'!important;font-family:ETmodules!important;"
                            )
                        );
                    }
                } else {
                    ET_Builder_Element::set_style($render_slug, array(
                        'selector'    => $button_selector . ':hover',
                        'declaration' => "padding-right:1em; padding-left:1em;",
                    ));
                }

                // button background
                if (!empty($button_bg_color)) {
                    ET_Builder_Element::set_style($render_slug, array(
                        'selector'    => $button_selector,
                        'declaration' => "background-color:" . $button_bg_color . "!important;",
                    ));
                }
            }
        }

        /**
         * Supported taxonomies for the archive builder
         * @since 2.2
         */
        public static function supported_archive_builder_taxonomies()
        {
            $supported_taxonomies = array(
                'product_cat',
                'product_tag',
            );

            $supported_taxonomies = apply_filters('wcbd_supported_archive_taxonomies', $supported_taxonomies);

            return $supported_taxonomies;
        }

        /**
         * Check if the visual builder is active
         * @since 2.2
         */
        public static function is_vb()
        {
            if (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled()) {
                return true;
            } else {
                return false;
            }
        }

        /*
     * Fix The Shop module's missing pagination on archive layouts
     * @since 2.1.13
     */
        public static function fix_shop_module_pagination()
        {
            if (is_post_type_archive('product')) {
                add_filter('pre_get_posts', function ($query) {
                    $query->query_vars['no_found_rows'] = false;
                });
            }
        }

        /**
         * Fix missing content of the slider and the summary modules if the description builder is used
         * in cojugation with Divi v 3.29 ( the WooCommerce Builder update ).
         * 
         * Divi removes everything from the product page and creates a module for each part of the page
         * but this leads to removing all the components of the summary and the slider
         * 
         * @since 2.1.14
         */
        public static function fix_divi_woo_update_on_desc_builder()
        {

            /**
             * Under the following conditions, Divi removes everything hooked to
             * woocommerce_before_single_product_summary
             * and woocommerce_single_product_summary which causes conflicts with some 3rd party plugins on my layouts.
             * Re-adding them under the same conditions fixes the conflicts
             * 
             * @see /Divi/includes/builder/feature/woocommerce-modules.php
             */
            if (function_exists('et_builder_wc_get_product_layout') && function_exists('et_core_is_fb_enabled')) {
                $product_page_layout = et_builder_wc_get_product_layout(get_the_ID());

                if (
                    !$product_page_layout && !et_core_is_fb_enabled()
                    || ($product_page_layout && 'et_build_from_scratch' !== $product_page_layout)
                ) {
                    return;
                }
            }
            self::wc_slider_hooks();
            self::wc_summary_hooks();
        }

        /**
         * add the slider hooks back
         * @since 2.2.2
         */
        public static function wc_slider_hooks()
        {
            add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
            add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
        }

        /**
         * add the summary hooks back
         * @since 2.2.2
         */
        public static function wc_summary_hooks()
        {
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
        }

        /**
         * check if this product page uses our layout
         * @since 2.2.2
         */
        public static function is_wcbd_product_layout()
        {

            if (
                (WCBD_INIT::$product_layout_id && WCBD_INIT::$product_builder_used == 'divi_library') ||
                (WCBD_INIT::$product_builder_used == 'description')
            ) {
                return true;
            }

            return false;
        }

        /**
         * Fix caching issue on archive layouts
         * Divi loads its "static" css files on singular pages only.
         * On archive pages, it loads the global css files not the layout's cached files.
         * 
         * @since 2.1.14
         */
        public static function set_archive_as_single($is_singular)
        {

            if (WCBD_INIT::$page_layout && isset(WCBD_INIT::$page_layout['layout_type']) && WCBD_INIT::$page_layout['layout_type'] == 'archive') {
                $is_singular = true;
            }
            return $is_singular;
        }

        /**
         * get the product's first gallery image
         * 
         * @since 3.0.0
         */
        public static function get_first_gallery_image()
        {
            global $product;

            if (version_compare(WC()->version, '3.0.0', '>=')) {
                $ids = $product->get_gallery_image_ids();
            } else {
                $ids = $product->get_gallery_attachment_ids();
            }

            if (is_array($ids) && !empty($ids)) {

                // thumb
                $img = wp_get_attachment_image_url($ids[0], 'woocommerce_thumbnail');

                if ($img) {
                    echo "<span class='et_shop_image flip_image'><img src='" . esc_url($img) . "' alt><span class='et_overlay'></span></span>";
                }
            }
        }

        /**
         * Render module's items padding and margin
         * @since 3.2.0
         *
         * @param string $render_slug the module slug
         * @param string $type what to render, it could be padding or margin
         * @param string $selector the item CSS selector
         * @param string $values the actual padding/margin values
         * @return void
         */
        public static function render_padding_margin($type, $render_slug, $selector, $values){

            $positions = array(
                'top'       => $type == 'padding' ? 'padding-top' : 'margin-top',
                'right'     => $type == 'padding' ? 'padding-right' : 'margin-right',
                'bottom'    => $type == 'padding' ? 'padding-bottom' : 'margin-bottom',
                'left'      => $type == 'padding' ? 'padding-left' : 'margin-left',
            );

            if( !empty( $render_slug ) && !empty( $values ) && !empty( $selector ) ){
                $values = explode( '|', $values );
            
                $index = 0;

                foreach($positions as $position){

                    if( isset( $values[$index] ) && $values[$index] != '' ){
                        ET_Builder_Element::set_style( $render_slug, array(
                            'selector'    => $selector,
                            'declaration' => $position . ":" . esc_attr( $values[$index] ) . "!important;",
                        ) );
                    }

                    $index++;
                }
            }        
        }          
    }
endif;

$wcbd_helpers = new WCBD_HELPERS();

add_filter('et_core_page_resource_is_singular', array($wcbd_helpers, 'set_archive_as_single'));
