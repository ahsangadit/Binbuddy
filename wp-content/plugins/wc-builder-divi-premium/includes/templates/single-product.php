<?php

/**
 * @since 2.0.0
 */
get_header('shop');

$builder_used     = WCBD_INIT::$product_builder_used;

?>

<?php do_action('woocommerce_before_main_content'); ?>

<?php while (have_posts()) : the_post(); ?>
    <?php
    do_action('woocommerce_before_single_product');

    if (post_password_required()) {
        echo get_the_password_form();
        return;
    }
    ?>
    <div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <?php
            /**
             * Hook: wcbd_before_single_product_content
             * 
             * @hooked WC_Structured_Data::generate_product_data() - 10
             */
            do_action('wcbd_before_single_product_content');
            ?>
            <?php

            if ($builder_used == 'divi_library') {
                // get the layout content 
                $layout_id     = WCBD_INIT::$product_layout_id;
                if ($layout = get_post($layout_id)) {
                    echo WCBD_INIT::render_body_layout_content($layout->post_content);
                }
            } else {
                the_content();
            }

            ?>
        </div><!-- /.entry-content -->
    </div> <!-- #product- -->
    <?php do_action('woocommerce_after_single_product'); ?>

<?php endwhile; ?>

<?php do_action('woocommerce_after_main_content'); ?>

<?php get_footer('shop'); ?>