<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// JUST IN CASE
$layout = array(
    'id' => 0,
    'layout_content' => '',
    'page_type' => '',
);

$layout = WCBD_INIT::$page_layout;

get_header( 'shop' ); ?>

<div id="main-content" class="wcbd_main_content">
	<?php

		 /* This action does not eixts in cart page */
		if( $layout['page_type'] != 'cart' ){
			/**
			 * woocommerce_before_main_content hook.
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 * @hooked WC_Structured_Data::generate_website_data() - 30
			 */
			do_action( 'woocommerce_before_main_content' );
		}
		
	?>

	<?php 
		if( isset( $layout['layout_content'] ) ){
			echo WCBD_INIT::render_body_layout_content( $layout['layout_content'] );
		}		
	?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */

		 /* add the correct cart page filter */
		if( $layout['page_type'] == 'cart' ){
			do_action( 'woocommerce_after_cart' );
		}else{
			do_action( 'woocommerce_after_main_content' );
		}
	?>
</div> <!-- #main-content -->

<?php get_footer( 'shop' ); ?>
