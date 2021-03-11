<ul class="dk_admin_settings_menu">
    <li><a href="#products_layouts" class="active"><?php esc_html_e( 'Products', 'wc-builder-divi' ); ?></a></li>
    <li><a href="#archive_layouts"><?php esc_html_e( 'Shop & Archive', 'wc-builder-divi' ); ?></a></li>
    <li><a href="#cart_layouts"><?php esc_html_e( 'Cart', 'wc-builder-divi' ); ?></a></li>
    <li><a href="#checkout_layouts"><?php esc_html_e( 'Checkout & Thank You', 'wc-builder-divi' ); ?></a></li>
    <li><a href="#account_layouts"><?php esc_html_e( 'My Account', 'wc-builder-divi' ); ?></a></li>
</ul>

<form action="options.php" method="post" class="wcbd_settings_form" id="wcbd_settings_form">
    <?php
        $layouts            = WCBD_INIT::get_divi_library_layouts();
        $archive_layouts    = WCBD_INIT::get_archive_layouts();
        $saved_settings     = WCBD_INIT::plugin_settings();

        settings_fields( 'divi_woo_settings' );
        settings_errors();
    ?>
    <div class="admin_settings_group" id="products_layouts">
        <div class="admin_settings_header">
            <h2 class="title"><?php esc_html_e( 'Product Layout', 'wc-builder-divi' ); ?></h2>
        </div><!-- admin_settings_header -->

        <div class="admin_settings_content">
            <table class="form-table wcbd_admin_table">
                <tbody>

                    <tr>
                        <th scope="row">
                            <label for="default_product_layout"><?php esc_html_e( 'General Default Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[default_product_layout]', $saved_settings['default_product_layout']); 
                            ?>
                            <p class="description">
                                <?php esc_html_e( '- This layout will be used for all products at once.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">    
                            <label for="products_under_cat_layout"><?php esc_html_e( 'Based On The Product Category', 'wc-builder-divi' ); ?></label>
                            <p class="description" style="font-weight:normal;">
                                <?php esc_html_e( '- The layouts here will override the General Default Layout you selected above.', 'wc-builder-divi' ); ?>
                            </p>
                        </th>
                        <td>
                            <?php 
                                wcbd_get_product_archives_and_divi_layouts('product_cat');
                            ?>
                            
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2">
                            <hr>
                            <h2 class="title"><?php esc_html_e( 'Display', 'wc-builder-divi' ); ?></h2>
                        </th>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="fullwidth_row_fix">
                                <?php esc_html_e( 'Make Fullwidth Rows 100% width', 'wc-builder-divi' ); ?>
                            </label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="divi_woo_settings[fullwidth_row_fix]" id="fullwidth_row_fix" value="1" <?php checked( 1, $saved_settings['fullwidth_row_fix'], true ) ?> /><?php esc_html_e('Enable', 'wc-builder-divi'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( '- By default, when you make a row full-width in Divi Builder, it\'ll be 80% width only. This option affects product pages only.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table><!-- /#products_layouts -->         
        </div><!-- admin_settings_content -->
    </div><!-- /.admin_settings_group -->


    <div class="admin_settings_group" id="archive_layouts">
        <div class="admin_settings_header">
            <h2 class="title"><?php esc_html_e( 'Shop & Archive Layouts', 'wc-builder-divi' ); ?></h2>
        </div><!-- admin_settings_header -->
        
        <div class="admin_settings_content">
            <table class="form-table wcbd_admin_table">
                <tbody>
                    <!-- Default Categories Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="default_categories_layout"><?php esc_html_e( 'Default Categories Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo wcbd_select_saved_divi_and_archive_layouts('divi_woo_settings[default_categories_layout]', $saved_settings['default_categories_layout']); 
                            ?>
                            
                            <p class="description">
                                <?php esc_html_e( '- This layout will be used for all the product CATEGORIES at once.', 'wc-builder-divi' ); ?>
                            </p>
                            <p class="description">
                                <?php esc_html_e( '- You can override this layout for individual categories in the archive builder.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>
                    <!-- Default Categories Layout End -->
                    
                    <!-- Default Tags Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="default_tags_layout"><?php esc_html_e( 'Default Tags Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo wcbd_select_saved_divi_and_archive_layouts('divi_woo_settings[default_tags_layout]', $saved_settings['default_tags_layout']); 
                            ?>
                            <p class="description">
                                <?php esc_html_e( '- This layout will be used for all the product TAGS at once.', 'wc-builder-divi' ); ?>
                            </p>
                            <p class="description">
                                <?php esc_html_e( '- You can override this layout for individual tags in the archive builder.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>
                    <!-- Default Tags Layout End -->

                    <!-- Shop Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="shop_layout"><?php esc_html_e( 'Shop Page Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo wcbd_select_saved_divi_and_archive_layouts('divi_woo_settings[shop_layout]', $saved_settings['shop_layout']); 
                            ?>
                            <p class="description">
                                <?php esc_html_e( '- The layout for the shop page ( under Pages -> Shop ).', 'wc-builder-divi' ); ?>
                            </p>
                            <p class="description">
                                <?php esc_html_e( '- For this layout to work, the shop page must use WordPress\' default editor, NOT Divi Builder AND it must be EMPTY.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>
                    <!-- Shop Layout End -->

                    <!-- Search Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="products_search_layout"><?php esc_html_e( 'Search Page Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo wcbd_select_saved_divi_and_archive_layouts('divi_woo_settings[products_search_layout]', $saved_settings['products_search_layout']); 
                            ?>
                            <p class="description">
                                <?php esc_html_e( '- This layout works only on Products search page, not WordPress search page.', 'wc-builder-divi' ); ?>
                            </p>
                            <p class="description">
                                <?php esc_html_e( '- For this layout to work, the shop page must use WordPress\' default editor, NOT Divi Builder AND it must be EMPTY.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>
                    <!-- Search Layout End -->

                    <tr>
                        <th colspan="2">
                            <hr>
                            <h2 class="title"><?php esc_html_e( 'Display', 'wc-builder-divi' ); ?></h2>
                        </th>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="fullwidth_row_fix_archive">
                                <?php esc_html_e( 'Make Fullwidth Rows 100% width', 'wc-builder-divi' ); ?>
                            </label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="divi_woo_settings[fullwidth_row_fix_archive]" id="fullwidth_row_fix_archive" value="1" <?php checked( 1, $saved_settings['fullwidth_row_fix_archive'], true ) ?> /><?php esc_html_e('Enable', 'wc-builder-divi'); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( '- By default, when you make a row full-width in Divi Builder, it\'ll be 80% width only. This option affects product archive pages only.', 'wc-builder-divi' ); ?>
                            </p>
                        </td>
                    </tr>  
                </tbody>
            </table><!-- /#archive_layouts -->
        </div><!-- admin_settings_content -->
    </div><!-- /.admin_settings_group -->

    <div class="admin_settings_group" id="cart_layouts">
        <div class="admin_settings_header">
            <h2 class="title"><?php esc_html_e( 'Cart Page', 'wc-builder-divi' ); ?></h2>
        </div><!-- admin_settings_header -->
        <div class="admin_settings_content">
            <table class="form-table wcbd_admin_table">
                <tbody>
                    <!-- Default Cart Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="cart_layout"><?php esc_html_e( 'Default Cart Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[cart_layout]', $saved_settings['cart_layout']); 
                            ?>
                        </td>
                    </tr>
                    <!-- Default Cart Layout End -->   
                    
                    <!-- Empty Cart Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="empty_cart_layout"><?php esc_html_e( 'Empty Cart Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[empty_cart_layout]', $saved_settings['empty_cart_layout']); 
                            ?>

                            <p class="description">
                                <?php esc_html_e( "- If the client didn't add anything to the cart, this layout will be displayed.", "wc-builder-divi" ); ?>
                            </p>
                        </td>
                    </tr>
                    <!-- Empty Cart Layout End -->  
                    
                </tbody>
            </table>    
        </div>                        
    </div><!-- /.admin_settings_group -->

    <div class="admin_settings_group" id="checkout_layouts">
        <div class="admin_settings_header">
            <h2 class="title"><?php esc_html_e( 'Checkout & Thank You Pages', 'wc-builder-divi' ); ?></h2>
        </div><!-- admin_settings_header -->
        <div class="admin_settings_content">
            <table class="form-table wcbd_admin_table">
                <tbody>
                    <!-- Default Chcekout Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="checkout_layout"><?php esc_html_e( 'Checkout Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[checkout_layout]', $saved_settings['checkout_layout']); 
                            ?>
                            <p class="description"><?php esc_html_e( '- The layout for the checkout and pay pages.', 'wc-builder-divi' ); ?></p>
                        </td>
                    </tr>
                    <!-- Default Chcekout Layout End -->   
                    
                    <!-- Thank You Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="thankyou_layout"><?php esc_html_e( 'Thank You Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[thankyou_layout]', $saved_settings['thankyou_layout']); 
                            ?>
                            <p class="description"><?php esc_html_e( '- The layout for the Thank You/Order Received Page.', 'wc-builder-divi' ); ?></p>
                        </td>
                    </tr>
                    <!-- Thank You Layout End -->  
                    
                </tbody>
            </table>    
        </div>                        
    </div><!-- /.admin_settings_group -->

    <div class="admin_settings_group" id="account_layouts">
        <div class="admin_settings_header">
            <h2 class="title"><?php esc_html_e( 'My Account Page', 'wc-builder-divi' ); ?></h2>
        </div><!-- admin_settings_header -->
        <div class="admin_settings_content">
            <table class="form-table wcbd_admin_table">
                <tbody>
                    <!-- Logged-in My Account Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="logged_in_account_layout"><?php esc_html_e( 'My Account Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[logged_in_account_layout]', $saved_settings['logged_in_account_layout']); 
                            ?>
                            <p class="description"><?php esc_html_e( '- This layout will be used for the My Account page if the user is logged-in.', 'wc-builder-divi' ); ?></p>
                        </td>
                    </tr>
                    <!-- Logged-in My Account Layout End -->   
                    
                    <!-- Logged-out My Account Layout Start -->
                    <tr>
                        <th scope="row">
                            <label for="logged_out_account_layout"><?php esc_html_e( 'Login/Register Layout', 'wc-builder-divi' ); ?></label>
                        </th>
                        <td>
                            <?php 
                                echo WCBD_INIT::html_select_divi_library_layouts('divi_woo_settings[logged_out_account_layout]', $saved_settings['logged_out_account_layout']); 
                            ?>
                            <p class="description"><?php esc_html_e( '- This layout will be used for the My Account page when the user is logged-out.', 'wc-builder-divi' ); ?></p>
                        </td>
                    </tr>
                    <!-- Logged-out My Account Layout End -->  
                    
                </tbody>
            </table>    
        </div>                        
    </div><!-- /.admin_settings_group -->
    <?php submit_button(); ?>
</form>