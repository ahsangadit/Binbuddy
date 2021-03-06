<?php
if( !defined( 'ABSPATH' ) ) exit; // exit if accessed directly

class WCBD_INIT{

	protected static $required_item = array();
	public static $product_layout_id = '0';
	public static $page_layout 	= false;
	public static $product_builder_used = 'divi_library';
	public $layout_type = false;
	public static $plugin_settings = false;
	public static $saved_divi_library_layouts = false;
	public static $admin_bar_post_id = false;
	public static $admin_bar_link_id = false;
	public static $admin_bar_link_title = false;

	public function __construct(){

		// on plugin activation
		register_activation_hook( WCBD_PLUGIN_PATH . 'wc-builder-divi.php', array( $this, 'on_plugin_activation' ) );

		// get plugin settings
		self::$plugin_settings = self::plugin_settings();

		// show notice on plugin activation
		if( get_transient( 'divi_woo_required_transient' ) ){
			add_action( 'admin_notices', array( $this, 'woo_divi_are_required_notice' ) );
			delete_transient( 'divi_woo_required_transient' );
		}

		// enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_load_scripts' ) );

		// load my templates
		if( self::is_woo_active() && self::is_divi_active() ){
			add_filter( 'template_include', array( $this, 'my_new_layout' ), 99 );
			add_action( 'edit_form_top', array( $this, 'show_shop_page_notice' ) );
		}

		// load vb layout
		add_filter( 'single_template', array( $this, 'vb_single_template' ) );

		add_filter( 'body_class', array( $this, 'fix_body_classes' ), 9999 );

		// clear modules cache
		add_action( 'et_builder_ready', function(){
			add_action( 'admin_head', array( $this, 'clear_modules_cache' ), 999 );
		} );

		// add product vb assets
		add_action( 'wp_footer', array( $this, 'load_product_vb_assets' ) );
		add_filter( 'et_pb_templates_loading_amount', function(){ return 57; } );
		
	}

	// set default settings & get saved settings from database
	public static function plugin_settings(){

		$default_settings = array(
			'default_product_layout' 			=> 0,
			'products_under_archive_layout' 	=> array(), // [tax_id][layout_id]
			'default_categories_layout'			=> 0,
			'default_tags_layout'				=> 0,
			'shop_layout'						=> 0,
			'products_search_layout'			=> 0,
			'cart_layout'						=> 0,
			'empty_cart_layout'					=> 0,
			'thankyou_layout'					=> 0,
			'checkout_layout'					=> 0,
			'logged_in_account_layout'			=> 0,
			'logged_out_account_layout'			=> 0,
			'fullwidth_row_fix'					=> 0,
			'fullwidth_row_fix_archive'			=> 0,
		); 
		$saved_settings = get_option( 'divi_woo_settings' );
		return wp_parse_args( $saved_settings, $default_settings );
	}

	// check for divi theme, Extra theme, or a child of any of them is used
	public static function is_divi_active(){
		if( DIVIKINGDOM_THEME_NAME == 'divi' ){
			return version_compare( DIVIKINGDOM_THEME_VERSION, '3.1', '<' ) ? false : true;
		}elseif( DIVIKINGDOM_THEME_NAME == 'extra' ){
			return version_compare( DIVIKINGDOM_THEME_VERSION, '2.1', '<' ) ? false : true;
		}else{
			return false;
		}
	}

	// check if WooCommerce is active
	public static function is_woo_active(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if( is_plugin_active( 'woocommerce/woocommerce.php' ) ){
			return true;
		}else{
			return false;
		}
	}

	// notice output to show after plugin activation and in plugin settings page
	// about Divi and WooCommerce are required
	public static function woo_divi_are_required_notice(){
		if( ! self::is_divi_active() ){
			self::$required_item[] = '<b><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=38335&tid1=pro_builder" target="_blank">Divi 3.1+ or Extra Theme 2.1+</a></b>';
		}

		if( ! self::is_woo_active() ){
			self::$required_item[] = '<b><a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a></b>';
		}

		if( count( self::$required_item ) ){
			$message = '<b>' . WCBD_PLUGIN_NAME . '</b> requires ' . implode( ' & ', self::$required_item ) . ' to be active to work!';
			?>
				<div class="notice notice-error">
					<p>
						<?php echo $message; ?>
					</p>
				</div><!-- /.notice -->
			<?php
		}
	}

	// what will happen on plugin activation
	public function on_plugin_activation(){

		// set transient for a notice about Divi and WooCommerce to be shown once on activation
		set_transient( 'divi_woo_required_transient', true, 1 );

		//WCBD_SETTINGS::register_my_post_types();
		//flush_rewrite_rules();
	}

	// clear modules cache
	public function clear_modules_cache(){
		
	?>
	
		<script>
			var ET_Prefix = 'et_pb_templates_et_pb_woopro_';
			// all my modules
			var WCBD_MODULES = [ 

				ET_Prefix + 'title',
				ET_Prefix + 'breadcrumb',
				ET_Prefix + 'image',
				ET_Prefix + 'gallery',
				ET_Prefix + 'rating',
				ET_Prefix + 'price',
				ET_Prefix + 'excerpt',
				ET_Prefix + 'add_to_cart',
				ET_Prefix + 'meta',
				ET_Prefix + 'tabs',
				ET_Prefix + 'upsells',
				ET_Prefix + 'related_products',
				ET_Prefix + 'description',
				ET_Prefix + 'additional_info',
				ET_Prefix + 'reviews',
				ET_Prefix + 'summary',
				ET_Prefix + 'images_slider',
				ET_Prefix + 'cover',
				ET_Prefix + 'notices',
				ET_Prefix + 'navigation',
				'et_pb_templates_et_pb_wcbd_page_title',
				'et_pb_templates_et_pb_wcbd_archive_desc',
				'et_pb_templates_et_pb_wcbd_archive_products',
				'et_pb_templates_et_pb_wcbd_cat_cover',
				'et_pb_templates_et_pb_wcbd_products_search',
				'et_pb_templates_et_pb_wcbd_cart_products',
				'et_pb_templates_et_pb_wcbd_cart_cross_sells',
				'et_pb_templates_et_pb_wcbd_cart_totals',
				'et_pb_templates_et_pb_wcbd_checkout_classic',
				'et_pb_templates_et_pb_wcbd_myaccount_classic',
				'et_pb_templates_et_pb_wcbd_myaccount_login',
				'et_pb_templates_et_pb_wcbd_thankyou',
				'et_pb_templates_et_pb_wcbd_order_tracking',
				'et_pb_templates_et_pb_wcbd_categories',

			]; // array of all modules created by this plugin

			for(var module in localStorage){
				if(WCBD_MODULES.indexOf(module) != -1){
					localStorage.removeItem(module);
				}
			}
		</script>

	<?php

	}

	// my new layout
	public function my_new_layout( $template ){

		/**
		 * is_woocommerce() can be used to check for products, taxonomies, shop, search
		 * cart and checkout are standard pages with shortcodes and thus are not included
		 * @see https://docs.woocommerce.com/wc-apidocs/function-is_woocommerce.html
		 */
		if( !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page() ){
			return $template;
		}
		
		// Get Product layout
		if( is_product() ){

			self::$product_builder_used 	= self::get_product_builder_used( get_the_ID() );
			
			// If saved layout is used
			if( self::$product_builder_used == 'divi_library' ){
				self::$product_layout_id = self::get_product_layout_id( get_the_ID() );

				// stop everthing if the product uses the WC default layout
				if( self::$product_layout_id == '0' ){
					return $template;
				}

				// check visual builder
				// Ps: if visual builder is enabled, load the WooCommerce default Layout
				if( isset( $_GET['et_fb'] ) && $_GET['et_fb'] == 1 ){
					return $template;
				}	
			}

			// add some classes to the body
			$this->layout_type = 'product';
			add_filter( 'body_class', array( $this, 'add_body_classes' ));

			// add the fullwidth row class
			$this->add_fullwidth_row_class( self::$plugin_settings['fullwidth_row_fix'] );

			// some cleaning
			wcbd_remove_wc_divi_html_wrappers();

			// add the page html wrapper
			add_action( 'woocommerce_before_main_content', 'wcbd_woocommerce_output_content_wrapper', 10 );
			add_action( 'woocommerce_after_main_content', 'wcbd_woocommerce_output_content_wrapper_end', 10 );

			if( self::$product_builder_used == 'divi_library' && self::$product_layout_id ){
				// add edit this layout link
				self::add_admin_bar_link( array(
					'post_id' => self::$product_layout_id,
					'link_id' => 'edit-wcbd-product-layout',
					'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
				) );

				/**
				 * Remove Enable the visual builder button because it activates the builder on the description area
				 * not the actual layout
				 * @since 2.1.15
				 */
				remove_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );
			}

			/**
			 * fix woocommerce update issues in the description builder
			 * @todo To be removed later
			 */
			if( class_exists( 'ET_Builder_Module_Woocommerce_Add_To_Cart' ) ){
				WCBD_HELPERS::fix_divi_woo_update_on_desc_builder();
			}
			
			// change the single product template to mine
			$template = WCBD_PLUGIN_PATH .'includes/templates/single-product.php';			
		}

		// Product archive
		if( is_product_taxonomy() ){

			$supported_taxonomies 	= WCBD_HELPERS::supported_archive_builder_taxonomies();
			$current_tax_obj 				= get_queried_object();

			$current_taxonomy_name 	= esc_html( $current_tax_obj->taxonomy );
			$current_taxonomy_id 		= absint( $current_tax_obj->term_id );

			if( !in_array( $current_taxonomy_name, (array)$supported_taxonomies ) ){
				return $template;
			}

			// see if this term has a specific layout
			$args = array(
				'posts_per_page' 	=> 1,
				'post_type' 		=> WCBD_ARCHIVES_POST_TYPE,
				'post_status'		=> 'publish',
				'tax_query' 		=> array(
					array(
						'taxonomy'         => $current_taxonomy_name,
						'field'            => 'id',
						'terms'            => $current_taxonomy_id,
						'operator'         => 'IN',
						'include_children' => false,
					),
				),
			);			

			// get the layout
			$layouts = new WP_Query( $args );

			if( count( $layouts->posts ) ){

				self::$page_layout = array(
					'layout_id' => $layouts->posts[0]->ID,
					'layout_content' => $layouts->posts[0]->post_content,
				);			

			}else{
				// get the default layout if no specific one found
				if( $current_taxonomy_name == 'product_cat' || $current_taxonomy_name == 'product_tag' ){
					$default_layout = $current_taxonomy_name == 'product_cat' ? absint(self::$plugin_settings['default_categories_layout']) : absint(self::$plugin_settings['default_tags_layout']);
					
					if( $default_layout != 0 && $layout = get_post( $default_layout ) ){
						if( $layout->post_status == 'publish' ){

							// see if it has a wpml translation
							$wpml_post = $this->get_wpml_translated_layout( $layout->ID, $layout->post_type );

							$layout_id = isset( $wpml_post['layout_id'] ) ? $wpml_post['layout_id'] : esc_html( $layout->ID );

							$layout_content = isset( $wpml_post['layout_content'] ) ? $wpml_post['layout_content'] : $layout->post_content;

							self::$page_layout = array(
								'layout_id' => $layout_id,
								'layout_content' => $layout_content,
							);
						}	
					}
				}
			}

			// load the new archive layout
			if( isset( self::$page_layout['layout_id'] ) && isset( self::$page_layout['layout_content'] ) ){
				
				// some html cleaning
				wcbd_remove_wc_divi_html_wrappers();

				// add body classes
				$this->layout_type = 'archive';
				add_filter( 'body_class', array( $this, 'add_body_classes' ));

				// add the fullwidth row class
				$this->add_fullwidth_row_class( self::$plugin_settings['fullwidth_row_fix_archive'] );

				// add edit layout link
				self::add_admin_bar_link( array(
					'post_id' => self::$page_layout['layout_id'],
					'link_id' => 'edit-wcbd-archive-layout',
					'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
				) );

				// add page type
				self::$page_layout['page_type'] = 'archive';
				self::$page_layout['layout_type'] = 'archive';
				$template = WCBD_PLUGIN_PATH . 'includes/templates/page.php';	
			}
		}
		
		// Shop & Search page layout
		if( is_shop() ){

			if( is_search() ){
				$layout = absint( self::$plugin_settings['products_search_layout'] );
			}else{
				$layout = absint( self::$plugin_settings['shop_layout'] );
			}
			
			// check if the layout exists and published
			if( $layout != 0 && $layout = get_post( $layout ) ){

				if( $layout->post_status == 'publish' ){

					// some cleaning
					wcbd_remove_wc_divi_html_wrappers();

					// add body classes
					$this->layout_type = 'archive';
					add_filter( 'body_class', array( $this, 'add_body_classes' ));

					// add the fullwidth row class
					$this->add_fullwidth_row_class( self::$plugin_settings['fullwidth_row_fix_archive'] );

					// see if it has a wpml translation
					$wpml_post = $this->get_wpml_translated_layout( $layout->ID, $layout->post_type );

					$layout_id = isset( $wpml_post['layout_id'] ) ? $wpml_post['layout_id'] : esc_html( $layout->ID );

					$layout_content = isset( $wpml_post['layout_content'] ) ? $wpml_post['layout_content'] : $layout->post_content;

					self::$page_layout = array(
						'layout_id' => $layout_id,
						'layout_content' => $layout_content,
					);

					// add edit layout link
					self::add_admin_bar_link( array(
						'post_id' => self::$page_layout['layout_id'],
						'link_id' => 'edit-wcbd-archive-layout',
						'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
					) );

					// add page type
					self::$page_layout['page_type'] = 'archive';
					self::$page_layout['layout_type'] = 'archive';
					$template = WCBD_PLUGIN_PATH . 'includes/templates/page.php';					
				}

			}
		}

		// Cart Layout
		if( is_cart() ){
			if( WCBD_HELPERS::is_vb() ){
				return $template;
			}
			if( WC()->cart->is_empty() ){
				if( !isset( $_GET['removed_item'] ) ){
					// this removed_item is added on ajax requests while removing products from the cart
					$layout = absint( self::$plugin_settings['empty_cart_layout'] );
					$this->layout_type = 'empty_cart';
				}else{
					$layout = 0;
				}
			}else{
				$layout = absint( self::$plugin_settings['cart_layout'] );
				$this->layout_type = 'cart';
			}
			
			// check if the layout exists and published
			if( $layout != 0 && $layout = get_post( $layout ) ){

				if( $layout->post_status == 'publish' ){

					$layout_id 		= $layout->ID;
					$layout_content = $layout->post_content;

					// some cleaning
					wcbd_remove_wc_divi_html_wrappers();

					// add body classes
					add_filter( 'body_class', array( $this, 'add_body_classes' ));

					// see if it has a wpml translation
					$wpml_post = $this->get_wpml_translated_layout( $layout->ID, $layout->post_type );

					$layout_id = isset( $wpml_post['layout_id'] ) ? $wpml_post['layout_id'] : esc_html( $layout->ID );

					$layout_content = isset( $wpml_post['layout_content'] ) ? $wpml_post['layout_content'] : $layout->post_content;

					self::$page_layout = array(
						'layout_id' => $layout_id,
						'layout_content' => $layout_content,
					);
					
					/**
					 * Remove "Enable Visual Builder" link to avoid confusion & conflicts () the page layout itself and 
					 * the saved layout in Divi Library
					 */
					remove_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );

					// add edit layout link
					self::add_admin_bar_link( array(
						'post_id' => self::$page_layout['layout_id'],
						'link_id' => 'edit-wcbd-page-layout',
						'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
					) );

					// add page type
					self::$page_layout['page_type'] = 'cart';
					$template = WCBD_PLUGIN_PATH . 'includes/templates/page.php';			
				}

			}
		}

		// checkout layout
		if( is_checkout() ){
			if( WCBD_HELPERS::is_vb() ){
				return $template;
			}
			if( is_order_received_page() ){
				$layout = absint( self::$plugin_settings['thankyou_layout'] );
				$this->layout_type = 'thankyou';
			}else{
				$layout = absint( self::$plugin_settings['checkout_layout'] );
				$this->layout_type = 'checkout';
			}

			// check if the layout exists and published
			if( $layout != 0 && $layout = get_post( $layout ) ){

				if( $layout->post_status == 'publish' ){

					$layout_id 		= $layout->ID;
					$layout_content = $layout->post_content;

					// some cleaning
					wcbd_remove_wc_divi_html_wrappers();

					// add body classes
					add_filter( 'body_class', array( $this, 'add_body_classes' ));

					// see if it has a wpml translation
					$wpml_post = $this->get_wpml_translated_layout( $layout->ID, $layout->post_type );

					$layout_id = isset( $wpml_post['layout_id'] ) ? $wpml_post['layout_id'] : esc_html( $layout->ID );

					$layout_content = isset( $wpml_post['layout_content'] ) ? $wpml_post['layout_content'] : $layout->post_content;

					self::$page_layout = array(
						'layout_id' => $layout_id,
						'layout_content' => $layout_content,
					);

					/**
					 * Remove "Enable Visual Builder" link to avoid confusion & conflicts () the page layout itself and 
					 * the saved layout in Divi Library
					 */
					remove_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );

					// add edit layout link
					self::add_admin_bar_link( array(
						'post_id' => self::$page_layout['layout_id'],
						'link_id' => 'edit-wcbd-page-layout',
						'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
					) );
					
					// add page type
					self::$page_layout['page_type'] = 'checkout';
					$template = WCBD_PLUGIN_PATH . 'includes/templates/page.php';					
				}

			}
		}

		// account layout
		if( is_account_page() ){
			if( WCBD_HELPERS::is_vb() ){
				return $template;
			}
			if( is_user_logged_in() ){
				$layout = absint( self::$plugin_settings['logged_in_account_layout'] );
				$this->layout_type = 'logged_in_account';
			}else{
				$layout = absint( self::$plugin_settings['logged_out_account_layout'] );
				$this->layout_type = 'logged_out_account';
			}

			// check if the layout exists and published
			if( $layout != 0 && $layout = get_post( $layout ) ){

				if( $layout->post_status == 'publish' ){

					$layout_id 		= $layout->ID;
					$layout_content = $layout->post_content;

					// some cleaning
					wcbd_remove_wc_divi_html_wrappers();

					// add body classes
					add_filter( 'body_class', array( $this, 'add_body_classes' ));

					// see if it has a wpml translation
					$wpml_post = $this->get_wpml_translated_layout( $layout->ID, $layout->post_type );

					$layout_id = isset( $wpml_post['layout_id'] ) ? $wpml_post['layout_id'] : esc_html( $layout->ID );

					$layout_content = isset( $wpml_post['layout_content'] ) ? $wpml_post['layout_content'] : $layout->post_content;

					self::$page_layout = array(
						'layout_id' => $layout_id,
						'layout_content' => $layout_content,
					);

					/**
					 * Remove "Enable Visual Builder" link to avoid confusion & conflicts () the page layout itself and 
					 * the saved layout in Divi Library
					 */
					remove_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );
					
					// add edit layout link
					self::add_admin_bar_link( array(
						'post_id' => self::$page_layout['layout_id'],
						'link_id' => 'edit-wcbd-page-layout',
						'link_title' => esc_html__( 'Edit This Layout', 'wc-builder-divi' ),
					) );
					
					// add page type
					self::$page_layout['page_type'] = 'account';
					$template = WCBD_PLUGIN_PATH . 'includes/templates/page.php';					
				}

			}
		}
		return $template;
	}

	// load vb single template
	public function vb_single_template( $template ){
		global $post;

		// vb archive single layout
		if( $post->post_type == WCBD_ARCHIVES_POST_TYPE ){
			// add body classes
			$this->layout_type = 'archive';
			add_filter( 'body_class', array( $this, 'add_body_classes' ));

			// add the fullwidth row class
			$this->add_fullwidth_row_class( self::$plugin_settings['fullwidth_row_fix_archive'] );

			//remove_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );

			if( current_user_can( 'edit_post', $post->ID ) ){
				add_action( 'wp_before_admin_bar_render', function(){
					global $wp_admin_bar;

					// remove the default one as it points to an actual post in the front end
					$wp_admin_bar->remove_menu( 'et-disable-visual-builder' );

					$page_url = get_edit_post_link( get_the_ID() );

					$wp_admin_bar->add_menu( array(
						'id'    => 'et-disable-visual-builder',
						'title' => esc_html__( 'Exit Visual Builder', 'et_builder' ),
						'href'  => esc_url( $page_url ),
					) );
				} );				
			}
			// load the template
			$template = WCBD_PLUGIN_PATH . 'includes/templates/single-vb.php';
		}
		return $template;
	}

	// load front end scripts
	public function load_scripts(){
		wp_enqueue_style( 'wcbd-css', WCBD_PLUGIN_URL . 'includes/assets/frontend/style.css' );
        wp_enqueue_script( 'wcbd-js', WCBD_PLUGIN_URL . 'includes/assets/frontend/main.js', array( 'jquery' ), false, true );
        
        // register slick slider
        wp_register_style('wcbd-carousel-lib-css', WCBD_PLUGIN_URL . 'includes/assets/slick/slick.css');
        wp_register_script('wcbd-carousel-lib-js', WCBD_PLUGIN_URL . 'includes/assets/slick/slick.min.js', array('jquery'), true);
        
        // init the carousel
        wp_register_style('wcbd-carousel-lib-default-css', WCBD_PLUGIN_URL . 'includes/assets/frontend/carousel.css');
        wp_register_script('wcbd-carousel-init-js', WCBD_PLUGIN_URL . 'includes/assets/frontend/carousel.js', array('jquery', 'wcbd-carousel-lib-js'), true);

        // load the slider assets in the visual builder
        if(!empty($_GET['et_fb']) && $_GET['et_fb'] == 1){
            wp_enqueue_style('wcbd-carousel-lib-css');
            wp_enqueue_style('wcbd-carousel-lib-default-css');
            wp_enqueue_script('wcbd-carousel-lib-js');
            wp_enqueue_script('wcbd-carousel-init-js');
        }

	}

	// load back end scripts
	public function admin_load_scripts(){
		wp_enqueue_script( 'woo-pro-divi-admin-js', WCBD_PLUGIN_URL . 'includes/assets/admin/main.admin.js', array( 'jquery' ), null, true );
		wp_enqueue_style( 'woo-pro-divi-admin-css', WCBD_PLUGIN_URL . 'includes/assets/admin/style.admin.css' );
	}

	// get array of all divi layouts
	public static function get_divi_library_layouts(){

		// get all divi layouts
		$query_args = array(
			'meta_query'      => array(
				array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_type'       => 'et_pb_layout',
			'posts_per_page'  => '-1'
		);

		$layouts_query = get_posts( $query_args );

		$layouts = array();

		if( $layouts_query ){

			foreach( $layouts_query as $layout ){
				$layouts[] = array(
					'id' 	=> esc_attr( $layout->ID ),
					'name' 	=> esc_attr( $layout->post_title )
				);
			}
			self::$saved_divi_library_layouts = $layouts;
		}
		return $layouts;		
	}
	
	/**
	 * get an HTML select element with all Divi Library Layouts
	 * @since 2.2.0
	 */
	public static function html_select_divi_library_layouts( $input_name = '', $selected_value = 0 ){
		
		if( !self::$saved_divi_library_layouts ){
			self::$saved_divi_library_layouts = self::get_divi_library_layouts();
		}
		
		ob_start();
		?>

			<select name="<?php echo esc_attr( $input_name ); ?>">
				<option value="0" <?php echo absint($selected_value) == 0 ? 'selected' : ''; ?>><?php esc_html_e( '-- Default WooCommerce Layout --', 'wc-builder-divi' ); ?></option>

				<!-- A list of all divi library layouts -->
				<?php if( count( self::$saved_divi_library_layouts ) ): ?>

					<?php foreach( self::$saved_divi_library_layouts as $layout ): ?>

						<option value="<?php echo esc_attr($layout['id']) ?>" <?php echo $layout['id'] == absint($selected_value) ? 'selected' : ''; ?>><?php echo esc_attr($layout['name']) ?></option>

					<?php endforeach; ?>
				<?php endif; ?>

			</select>

		<?php
		return ob_get_clean();
	}
	// get archive layouts
	public static function get_archive_layouts( $return_ids = false ){
		
		$query_args = array(
			'post_type'       	=> WCBD_ARCHIVES_POST_TYPE,
			'posts_per_page'  	=> '-1',
			'post_status'		=> 'publish',
		);

		$layouts_query = get_posts( $query_args );

		$layouts = array();

		if( $layouts_query ){

			foreach( $layouts_query as $layout ){
				$layouts[] = array(
					'id' 	=> esc_attr( $layout->ID ),
					'name' 	=> esc_attr( $layout->post_title )
				);
			}

		}

		if( $return_ids == true ){
				
			$layouts_ids = array();
			
			if( count( $layouts ) ){
				foreach( $layouts as $layout ){
					$layouts_ids[] = $layout['id'];
				}
			}

			return $layouts_ids;
		}else{
			return $layouts;
		}		
	}

	// get the builder used for product
	public static function get_product_builder_used( $product_id ){
		$builder = esc_attr( get_post_meta( $product_id, '_main_product_builder', true ) );
		
		if( $builder != 'description' && $builder != 'divi_library' ){
			$builder = 'divi_library';
		}

		return $builder;
	}

	// get product stored layout
	public static function get_product_layout_meta( $product_id ){
		return esc_attr( get_post_meta( $product_id, WCBD_PRODUCT_LAYOUT_KEY, true ) );
	}

	// get product layout ID
	public static function get_product_layout_id( $product_id ){
		$layout_id 						= '0';
		$layout_meta					= self::get_product_layout_meta( $product_id );
		$general_default_layout 		= esc_attr( self::$plugin_settings['default_product_layout'] );
		$products_under_archive_layout 	= (array)self::$plugin_settings['products_under_archive_layout'];

		// general default
		if( $layout_meta == 'general_default' ){
			$layout_id = $general_default_layout;
		}

		// category default
		if( $layout_meta == '' || $layout_meta == 'cat_default' ){

			$has_category_layout = false;

			if( count( $products_under_archive_layout ) ){

				// get the current product terms
				$terms = get_the_terms( $product_id, 'product_cat' );	
				
				if( is_array( $terms ) && count( $terms ) ){
					foreach( $terms as $term ){
						
						$term_id 	= esc_attr($term->term_id);

						if( isset( $products_under_archive_layout[$term_id] ) ){

							$layout_id 	= esc_attr( $products_under_archive_layout[$term_id] );

							if( $layout_id == 'general_default' ){
								$layout_id = $general_default_layout;
							}

							$has_category_layout = true;

							break;
						}
					}
				}
			}
			/**
			 * the category has no layout && is not stored in the plugin's settings
			 * means that the category is new, so by default, it will use the general default layout
			 */
			if( $has_category_layout == false ){
				$layout_id = $general_default_layout;
			}
		}

		// product specific layout
		if( is_numeric( $layout_meta ) && $layout_meta > 0 ){
			$layout_id = absint( $layout_meta );
		}

		// woocommerce default
		if( $layout_id == '0' ){
			return '0';
		}

		// check if the layout exists
		if( !self::layout_exists( $layout_id ) ){

			// reset to the general default
			$layout_id = $general_default_layout;
			
			// check if the general refault exists, if not, go back to default woocommerce layout
			if( !self::layout_exists( $layout_id ) ){
				$layout_id = '0';
			}
		}

		return $layout_id;
	}

	// check if the layout exists
	public static function layout_exists( $layout_id = 0 ){

		$layout = get_post( $layout_id );
		if( $layout && $layout->post_status == 'publish' ){
			return true;
		}else{
			return false;
		}
	}

	// add single product body classes
	public function add_body_classes( $classes ){

		// classes to remove
		$remove_classes = array( 
			'et_right_sidebar', 
			'et_left_sidebar', 
		);

		// general class
		$classes[] = 'wcbd_layout';

		// product classes
		if( $this->layout_type == 'product' ){
			$classes[] = 'woo_product_divi_layout';
			$classes[] = 'et_pb_pagebuilder_layout';

			/**
			 * Fix Divi 3.10 issue that made pages with transparent nav to have padding at the top of the page
			 */
			if( in_array( 'et_transparent_nav', $classes ) ){
				$classes[] = 'et_full_width_page';
			}			
		}

		// archive classes
		if( $this->layout_type == 'archive' ){
			$classes[] = 'wcbd_archive_layout';
			$classes[] = 'woocommerce';			// for single archive post type to make them work like noormal archive pages
			$classes[] = 'woocommerce-page';	// for single archive post type to make them work like noormal archive pages

			// remove full-width classe because it causes issues with products columns number
			$remove_classes[] = 'et_full_width_page';
		}

		// cart with products page
		if( $this->layout_type == 'cart' ){
			$classes[] = 'wcbd_cart_with_products_layout';
		}

		// empty cart account page
		if( $this->layout_type == 'empty_cart' ){
			$classes[] = 'wcbd_empty_cart_layout';
		}

		// thank you page
		if( $this->layout_type == 'thankyou' ){
			$classes[] = 'wcbd_thankyou_layout';
		}

		// checkout page
		if( $this->layout_type == 'checkout' ){
			$classes[] = 'wcbd_checkout_layout';
		}

		// logged-in account page
		if( $this->layout_type == 'logged_in_account' ){
			$classes[] = 'wcbd_account_logged_in_layout';
		}

		// logged-out account page
		if( $this->layout_type == 'logged_out_account' ){
			$classes[] = 'wcbd_account_logged_out_layout';
		}

		// remove some classes
		$classes = array_diff( $classes, $remove_classes );

		return $classes;
	}

	// add full-width row class
	public function add_fullwidth_row_class( $enable ){

		if( $enable == 1 ){
			add_filter( 'body_class', function($c){
				$c[] = 'wcbd_fullwidth_row';
				return $c;
			} );
		}
	}

	// get layout content
	public static function get_divi_layout_content( $layout_id ){
		if( ! $layout_id ){
			return;
		}
		
		if( $layout = get_post( $layout_id ) ){
			echo do_shortcode( et_pb_fix_shortcodes( wpautop( $layout->post_content ) ) );
		}
	}

	// buffering woocommerce functions
	public static function content_buffer( $func ){

		$output = '';
		if( function_exists( $func ) ){
			ob_start();
			$func();
			$output = ob_get_contents();
			ob_end_clean();		
		}
		return $output;

	}	

	// render ET font icons content css property
	public static function et_icon_css_content( $font_icon ){
		$icon = preg_replace( '/(&amp;#x)|;/', '', et_pb_process_font_icon( $font_icon ) );

		return '\\' . $icon;
	}	

	// fix body class
	public function fix_body_classes( $classes ){
		/**
		* et_pb_pagebuilder_layout class will be added to the product page if the builder is enabled for the decription
		* and the builder is not used but the page layout settings is selected to be fullwidth
		* which will make the page 100% width with no margings at all.

		* Remove this class in case there is no layout chosen from the right top box
		*/
		if( function_exists( 'is_product' ) && is_product() ){

			if( !in_array( 'woo_product_divi_layout', $classes ) ){
				$classes = array_diff( $classes, array( 'et_pb_pagebuilder_layout' ) );		
			}

		}

		return $classes;
	}	

	// load the visual builder assets
	public function load_product_vb_assets(){
		if( class_exists( 'woocommerce' ) && isset( $_GET['et_fb'] ) && (int)$_GET['et_fb'] == 1 ){
			global $product;

			if( is_product() ){

				// remove the default additional info heading
				add_filter( 'woocommerce_product_additional_information_heading', function(){
					return false;
				} );

				// product categories
				if( version_compare( WC()->version, '3.0.0', '>=' ) ){
					$categories = wc_get_product_category_list( get_the_ID(), ' / ', '<div class="product_categories"><span class="posted_in">', '</span></div>' );
				}else{
					$categories = $product->get_categories( ' / ', '<div class="product_categories"><span class="posted_in">', '</span></div>' );
				}			

				// product featured image
				if( has_post_thumbnail() ){
					$featured_image = get_the_post_thumbnail_url();
					$has_featured_image = '1';
				}else{
					$featured_image = esc_url( wc_placeholder_img_src() );
					$has_featured_image = '0';
				}

				// product gallery images
				if( version_compare( WC()->version, '3.0.0', '>=' ) ){
					$attachment_ids = $product->get_gallery_image_ids();
				}else{
					$attachment_ids = $product->get_gallery_attachment_ids();
				}

				$attachment_urls = array();

				if( count( $attachment_ids ) ){
					
					$attachment_urls[] = $featured_image;

					foreach( $attachment_ids as $id ){
						if( count( $image = wp_get_attachment_image_src( $id, 'full' ) ) ){
							$attachment_urls[] = esc_url( $image[0] );
						}
					}
				}

				// product reviews
				$product_reviews = comments_open( get_the_ID() ) ? self::content_buffer( 'comments_template' ) : '';

				$helpers = array(
					'is_product'			=> 1,
					'title' 				=> self::content_buffer( 'woocommerce_template_single_title' ),
					'additional_info' 		=> self::content_buffer( 'woocommerce_product_additional_information_tab' ),
					'product_excerpt'		=> self::content_buffer( 'woocommerce_template_single_excerpt' ),
					'product_meta'			=> self::content_buffer( 'woocommerce_template_single_meta' ),
					'product_price'			=> self::content_buffer( 'woocommerce_template_single_price' ),
					'product_rating'		=> self::content_buffer( 'woocommerce_template_single_rating' ),
					'product_cats'			=> $categories,
					'featured_image'		=> $featured_image,
					'has_featured_image'	=> $has_featured_image,
					'product_gallery_images' => $attachment_urls,
					'product_reviews'		=> $product_reviews,
				);				
				

			}else{
				$helpers = array(
					'is_product' 	=> 0,
					'error_message' => esc_html__( 'This module works only on product pages.', 'wc-builder-divi' ),
				);
			}
			$helpers['path_url'] = WCBD_PLUGIN_URL;

			// breadcrumb
			$helpers['breadcrumb'] = self::content_buffer( 'woocommerce_breadcrumb' );

			/* Translated text */
			$produtc_title 		=  esc_html__( 'Product Title!', 'wc-builder-divi' );
			$helpers['text'] 	=  array(
				'quantity' 			=> esc_html__( 'Quantity', 'woocommerce' ),
				'color' 			=> esc_html__( 'Color', 'wc-builder-divi' ),
				'size' 				=> esc_html__( 'Size', 'wc-builder-divi' ),
				'variation' 				=> esc_html__( 'Variation', 'wc-builder-divi' ),
				'choose_option' 	=> __( 'Choose an option', 'woocommerce' ),
				'variation_desc' 	=> esc_html__( 'Here will be the selected variation description if you set it in the product editing page. Next is the variation price and the stock availability status.', 'wc-builder-divi' ),
				'instock'			=> __( 'In stock', 'woocommerce' ),
				'add_to_cart' 		=> __( 'Add to cart', 'woocommerce' ),
				'button' 		=> esc_html__( 'Button', 'wc-builder-divi' ),
				'notice' 			=> esc_html__( 'Here will be the content of WooCommerce messages if any.', 'wc-builder-divi' ),
				'product_title'		=> $produtc_title,
				'product_cats'		=> '<a href="#">' . esc_html__( 'Category Name', 'wc-builder-divi' ) . '</a>' . ' / ' . '<a href="#">' . esc_html__( 'Another Category', 'wc-builder-divi' ) . '</a>',
				'description'		=> esc_html__( 'Description', 'woocommerce' ),
				'additional_info'	=> esc_html__( 'Additional information', 'woocommerce' ),
				'reviews'			=> esc_html__( 'Reviews', 'woocommerce' ),
				'sku'				=> esc_html__( 'SKU', 'woocommerce' ),
				'categories'		=> esc_html__( 'Categories', 'woocommerce' ),
				'tags'				=> esc_html__( 'Tags', 'woocommerce' ),
				'customer_reviews' 	=> sprintf( _n( '%s customer review', '%s customer reviews', 2, 'woocommerce' ), '<span class="count">2</span>' ),
				'comments_count' 	=> sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', 2, 'woocommerce' ) ), 2, '<span>' . $produtc_title . '</span>' ),
				'add_review'		=> __( 'Add a review', 'woocommerce' ),
				'your_rating'		=> esc_html__( 'Your rating', 'woocommerce' ),
				'order_received'	=> __( 'Thank you. Your order has been received.', 'woocommerce' ),
				'order_failed'		=> __( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ),
				'pay'				=> __( 'Pay', 'woocommerce' ),
				'myaccount'			=> __( 'My account', 'woocommerce' ),
				'_order_number'		=> __( 'Order number:', 'woocommerce' ),
				'_date'				=> __( 'Date:', 'woocommerce' ),
				'_email' 			=> __( 'Email:', 'woocommerce' ),
				'_total'			=> __( 'Total:', 'woocommerce' ),
				'total'				=> __( 'Total', 'woocommerce' ),
				'_payment_method'	=> __( 'Payment method:', 'woocommerce' ),
				'pay_on_delivery'	=> __( 'Pay with cash upon delivery.', 'woocommerce' ),
				'cash_on_delivery'	=> __( 'Cash on delivery', 'woocommerce' ),
				'cash_on_delivery_desc'	=> __( 'Pay with cash upon delivery.', 'woocommerce' ),
				'product'			=> __( 'Product', 'woocommerce' ),
				'dl_remaing'		=> __( 'Downloads remaining', 'woocommerce' ),
				'expires'			=> __( 'Expires', 'woocommerce' ),
				'download'			=> __( 'Download', 'woocommerce' ),
				'downloads'			=> __( 'Downloads', 'woocommerce' ),
				'file_name'			=> __( 'File Name', 'woocommerce' ),
				'order_details'		=> __( 'Order details', 'woocommerce' ),
				'subtotal'			=> __( 'Subtotal', 'woocommerce' ),
				'_subtotal'			=> __( 'Subtotal:', 'woocommerce' ),
				'_shipping'			=> __( 'Shipping:', 'woocommerce' ),
				'shipping'			=> __( 'Shipping', 'woocommerce' ),
				'shipping_to'			=> __( 'Shipping to', 'wc-builder-divi' ),
				'order_again'		=> __( 'Order again', 'woocommerce' ),
				'billing_address'	=> __( 'Billing address', 'woocommerce' ),
				'shipping_address'	=> __( 'Shipping address', 'woocommerce' ),
				'_note'				=> __( 'Note:', 'woocommerce' ),
				'extra_note'		=> __( 'Well, This is an extra note!', 'woocommerce' ),
				'login'					=> __( 'Login', 'woocommerce' ),
				'register'			=> __( 'Register', 'woocommerce' ),
				'returning_customer' => __( 'Returning customer?', 'woocommerce' ),
				'click_to_login'	=> __( 'Click here to login', 'woocommerce' ),
				'login_message'	=> __( 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
				'username' => esc_html__( 'Username', 'woocommerce' ),
				'username_email' => esc_html__( 'Username or email', 'woocommerce' ),
				'password' => esc_html__( 'Password', 'woocommerce' ),
				'remember_me' => esc_html__( 'Remember me', 'woocommerce' ),
				'lost_pass' => esc_html__( 'Lost your password?', 'woocommerce' ),
				'have_coupon' => __( 'Have a coupon?', 'woocommerce' ),
				'enter_coupon' => __( 'Click here to enter your code', 'woocommerce' ),
				'apply_coupon' => esc_html__( 'If you have a coupon code, please apply it below.', 'woocommerce' ),
				'coupon' => esc_attr__( 'Coupon code', 'woocommerce' ),
				'_coupon' => esc_attr__( 'Coupon:', 'woocommerce' ),
				'apply_button' => esc_attr__( 'Apply coupon', 'woocommerce' ),
				'billing' => esc_html__( 'Billing details', 'woocommerce' ),
				'first_name' => esc_html__( 'First name', 'woocommerce' ),
				'last_name' => esc_html__( 'Last name', 'woocommerce' ),
				'company_name' => esc_html__( 'Company name', 'woocommerce' ),
				'optional' => esc_html__( 'optional', 'woocommerce' ),
				'country' => esc_html__( 'Country', 'woocommerce' ),
				'select_country' => esc_html__( 'Select a country&hellip;', 'woocommerce' ),
				'street_address' => esc_html__( 'Street address', 'woocommerce' ),
				'address_placeholder' => esc_html__( 'House number and street name', 'woocommerce' ),
				'address_2' => esc_html__( 'Apartment, suite, unit etc.', 'woocommerce' ),
				'city' => esc_html__( 'Town / City', 'woocommerce' ),
				'state' => esc_html__( 'State / County', 'woocommerce' ),
				'zip' => esc_html__( 'Postcode / ZIP', 'woocommerce' ),
				'phone' => esc_html__( 'Phone', 'woocommerce' ),
				'email_address' => esc_html__( 'Email address', 'woocommerce' ),
				'create_account' => esc_html__( 'Create an account?', 'woocommerce' ),
				'ship_diff' => esc_html__( 'Ship to a different address?', 'woocommerce' ),
				'product_name' => esc_html__( 'Product name', 'wc-builder-divi' ),
				'flat_rate' => esc_html__( 'Flat rate', 'woocommerce' ),
				'proceed_paypal' => esc_html__( 'Proceed to PayPal', 'woocommerce' ),
				'proceed_checkout' => esc_html__( 'Proceed to checkout', 'woocommerce' ),
				'paypal' => esc_html__( 'PayPal', 'woocommerce' ),
				'paypal_desc' => esc_html__( "Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.", 'woocommerce' ),
				'privacy' => sprintf( __( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.', 'woocommerce' ), "<a href='#' class='woocommerce-privacy-policy-link'>" . __( 'Privacy policy', 'woocommerce' ) . "</a>" ),
				'terms' => sprintf( __( 'I have read and agree to the website %s', 'woocommerce' ), "<a href='#'>". __( 'Terms and conditions', 'woocommerce' ) ."</a>" ),
				'place_order' => esc_html__( 'Place order', 'woocommerce' ),
				'price' => esc_html__( 'Price', 'woocommerce' ),
				'update_cart' => esc_html__( 'Update cart', 'woocommerce' ),
				'update' => esc_html__( 'Update', 'woocommerce' ),
				'change_address' => esc_html__( 'Change address', 'woocommerce' ),
				'order_notes' => esc_html__( 'Order notes', 'woocommerce' ),
				'order_notes_placeholder' => esc_html__( 'Notes about your order, e.g. special notes for delivery.', 'woocommerce' ),
				'page_title' => esc_html__( 'Page Title!', 'wc-builder-divi' ),
			);

			wp_localize_script( 'wcbd-js', 'WCBD_Helpers', $helpers );
		}		
	}

	/** 
	 * Show shop page notice to prevent any issues with the shop and search layouts
	 */
	public function show_shop_page_notice( $post ){
		$shop_page_id = wc_get_page_id( 'shop' );

		if ( $post && absint( $post->ID ) === $shop_page_id ) {
			echo '<div class="notice notice-error">';
			/* translators: %s: URL to read more about the shop page. */
			echo '<p>' . sprintf( wp_kses_post( __( 'IMPORTANT: If you want to customize the shop page using the WooCommerce Builder, you MUST keep the content area below EMPTY! <a href="%s" target="_blank">Read more about this here</a>.', 'wc-builder-divi' ) ), 'https://docs.divikingdom.com/build-woocommerce-shop-layout/' ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * add a clearfix div
	 * @since 2.2.0
	 */
	static function add_clearfix(){
		echo '<div class="clearfix"></div>';
	}

	/**
	 * get WPML translated layout
	 * @since 2.1.7
	 */
	public function get_wpml_translated_layout( $layout_id, $post_type ){

		$output = false;
		if( class_exists( 'SitePress' ) || class_exists('Polylang') ){
			/***
			 * Check if the layout has a translation for the current language
			*/
			$layout_id = esc_html( $layout_id );
			$post_type = esc_html( $post_type );

			$wpml_layout_id = apply_filters( 'wpml_object_id', $layout_id, $post_type );

			if( $wpml_layout_id ){

				if( $wpml_post = get_post( $wpml_layout_id ) ){
					
					if( $wpml_post->post_status == 'publish' ){

						$output['layout_id'] 		= esc_html( $wpml_post->ID );
						$output['layout_content'] 	= $wpml_post->post_content;
					}
				}
				
			}
		}
		return $output;
	}

	/**
	 * Add link to the admin bar
	 * @since 2.1.15
	 */
	public static function add_admin_bar_link( $args = array() ){

		if( !count( $args ) ){
			return;
		}
		self::$admin_bar_post_id = isset( $args['post_id'] ) ? $args['post_id'] : false;
		self::$admin_bar_link_id = isset( $args['link_id'] ) ? $args['link_id'] : false;
		self::$admin_bar_link_title = isset( $args['link_title'] ) ? $args['link_title'] : false;

		// add edit layout link
		if( current_user_can( 'edit_post', self::$admin_bar_post_id ) ){
				add_action( 'admin_bar_menu', function(){
						global $wp_admin_bar;
						$wp_admin_bar->add_menu( array(
								'id' => self::$admin_bar_link_id,
								'title' => self::$admin_bar_link_title,
								'href' => get_edit_post_link( self::$admin_bar_post_id ),
						) );
				}, 999 );					
		}
	}

	/**
	 * render body layout content with wrappers
	 * @since 2.1.20
	 */
	public static function render_body_layout_content( $content ){

		$function_exists = false;

		if( $content ){
			if( function_exists( 'et_theme_builder_frontend_render_common_wrappers' ) ){
				$function_exists = true;

				et_theme_builder_frontend_render_common_wrappers( 'common', true );
				echo "<div class='et-l et-l--body'>";
			}
			
			echo do_shortcode( et_pb_fix_shortcodes( wpautop( $content ) ) );

			if( $function_exists ){
				echo "</div>"; 
				et_theme_builder_frontend_render_common_wrappers( 'common', false );
			}
		}
	}
}

new WCBD_INIT();