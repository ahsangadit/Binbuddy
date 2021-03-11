<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hztech.biz
 * @since      1.0.0
 *
 * @package    Billbuddy_Coupon
 * @subpackage Billbuddy_Coupon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Billbuddy_Coupon
 * @subpackage Billbuddy_Coupon/admin
 * @author     HZTECH <info@hztech.biz>
 */
class Billbuddy_Coupon_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_action( 'init', array( $this, 'create_posttype' ) );
		add_action( 'admin_head', array( $this, 'custom_post_css' ) );
		add_action( 'init', array( $this, 'init_remove_support' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_publish_metabox' ) );
		add_action( 'edit_form_after_title', array( $this, 'custom_fields_payment' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'filter_post_data' ), '99', 2 );
		add_action('admin_menu', array($this,'add_setting_submenu') );
		add_action( 'admin_init', array( $this,  'billbuddy_coupon_settings') );
		add_filter( 'manage_coupons_posts_columns', array( $this, 'smashing_coupons_columns' ) );
		add_action( 'manage_coupons_posts_custom_column', array( $this, 'custom_coupons_column'), 10, 2);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Payment_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Payment_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/billbuddy-coupon-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Payment_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Payment_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/billbuddy-coupon-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_style( 'font-awesome-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css' );
	}

	public function create_posttype()
	{

		register_post_type( 'Coupons',
		                    array(
			                    'labels'       => array(
				                    'name'          => __( 'Coupons' ),
				                    'singular_name' => __( 'Billbuddy_Coupon' ),
				                    'all_items'     => __( 'All Coupons' ),
				                    'add_new_item'  => __( 'Add New Coupon' )
			                    ),
			                    'public'       => true,
			                    'has_archive'  => true,
			                    'rewrite'      => array( 'slug' => 'coupons' ),
			                    'show_in_rest' => true,
			                    'menu_icon'    => '',

		                    )
		);
	}

	public function add_setting_submenu()
	{
		add_submenu_page(
			'edit.php?post_type=coupons', //$parent_slug
			'Settings',  //$page_title
			'Settings',        //$menu_title
			'manage_options',           //$capability
			'coupon-settings',//$menu_slug
			array($this,'billbuddy_coupon_page')//$function
		);

	}

	function billbuddy_coupon_settings()
	{
		$current_url = home_url() . '/wp-admin/admin.php?page=coupon-settings';
		add_option( 'coupon_url', $current_url );
		register_setting( 'coupon-settings', 'coupon' );

		add_settings_section( 'billbuddy-coupon-section', 'Settings', 'billbuddy_coupon_setting_section_callback', 'billbuddy-coupon' );

	}


//add_submenu_page callback function

	public function billbuddy_coupon_page()
	{
		$coupon = get_option( 'coupon' );
		ob_start(); ?>

		<?php

		if(isset($_POST['submit'])){
			$coupon = $_POST['coupon'];

			update_option('coupon',$coupon);
		}


		?>
        <h1>Settings</h1>
        <form action="" name="coupon_form" method="post" enctype="multipart/form-data">
			<?php
			settings_fields( 'coupon-settings' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                <tr class="stripe-id-wrap">
                    <th><label for="coupon">Coupon</label></th>
                    <td><input type="text" name="coupon" id="coupon"
                               value="<?= $coupon; ?>" class="regular-text"></td>
                </tr>
                </tbody>
            </table>
			<?php	//	do_settings_sections( 'payment-settings' );
			submit_button( 'Save Changes' );
			?>

        </form>
		<?php
		$content = ob_get_clean();
		echo $content;
	}


	public function menu_page_output()
	{
		//Menu Page output code
	}

	public function custom_post_css()
	{
		echo '<style type="text/css" media="screen">
        #adminmenu .menu-icon-coupons div.wp-menu-image:before{
            font-family: "Font Awesome 5 Free";
            content: "\f02c"; 
            display: inline-block;
            padding-right: 3px;
            vertical-align: middle;
            font-weight: 900;
        } 
     </style>';
	}

	function init_remove_support()
	{
		$post_type = 'coupons';
		remove_post_type_support( $post_type, 'editor' );
	}

	function remove_publish_metabox()
	{
		// if ( $_GET['action'] == 'edit' ) {
		// 	remove_meta_box( 'submitdiv', 'coupons', 'side' );
		// }
	}

	function custom_fields_payment( $post )
	{
		$id      = $_GET['post'];
		$is_edit = ( $_GET['action'] == null ) ? false : true;
		$scr = get_current_screen()->id;

		if ( $scr == 'coupons' ) {
			?>
            <table class="form-table" role="presentation">
                <tbody>
                <tr class="first-name-wrap">
                    <th><label for="first_name">First Name</label></th>
                    <td><input type="text" name="first_name" id="first_name"
                               value="<?= get_post_meta( $id, 'first_name', true ); ?>" <?= ( $is_edit ) ? 'disabled' : '' ?>
                               class="regular-text"></td>
                </tr>

                <tr class="last-name-wrap">
                    <th><label for="last_name">Last Name</label></th>
                    <td><input type="text" name="last_name" id="last_name"
                               value="<?= get_post_meta( $id, 'last_name', true ); ?>" <?= ( $is_edit ) ? 'disabled' : '' ?>
                               class="regular-text"></td>
                </tr>

                </tbody>
            </table>
			<?php
		}
	}

	function filter_post_data( $data, $postarr )
	{

		if ( $postarr['post_type'] == 'coupons' && $postarr['original_publish'] == 'Publish' ) {
			update_post_meta( $postarr['ID'], 'first_name', $postarr['first_name'] );
			update_post_meta( $postarr['ID'], 'last_name', $postarr['last_name'] );
		}

		return $data;
	}

	function unserializeForm($str) {
		$strArray = explode("&", $str);
		foreach($strArray as $key => $item) {
			$array = explode("=", $item);
			$returndata[] = $array;
		}
		return $returndata;
	}

	function smashing_coupons_columns( $columns ) {
		$columns = array(
			'cb' => $columns['cb'],
			'title' => __( 'Title'),
			'last_name' => __( 'Last Name', 'last_name' ),
			'first_name' => __( 'First Name', 'first_name' ),
			'date' => __( 'Date', 'date' ),
		);
		return $columns;
	}


	function custom_coupons_column( $column, $post_id ) {
		if ( 'first_name' === $column ) {
			echo get_post_meta( $post_id, 'first_name' , true );
		}
		if ( 'city' === $column ) {
			echo get_post_meta( $post_id, 'city' , true );
		}
		if ( 'zip' === $column ) {
			echo get_post_meta( $post_id, 'zip' , true );
		}
	}

}

