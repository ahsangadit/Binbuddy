<?php

namespace BookneticApp\Backend\Settings\Controller;

use BookneticApp\Providers\Controller;
use BookneticApp\Providers\Curl;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Main extends Controller
{

	public function index()
	{
		$this->view( 'index' );
	}

	public function woocommerce_gateway_settings()
	{
		$wc_payment_gateway_id = Helper::_get('wc_payment_gateway_id', '', 'string');

		set_current_screen('woocommerce_page_wc-settings');
		$_GET['tab'] = 'checkout';
		$_GET['section'] = 'ppec_paypal';

		$wc_payment_gateways   = \WC_Payment_Gateways::instance();
		$wc_payment_gateways = $wc_payment_gateways->payment_gateways();

		if( !isset( $wc_payment_gateways[ $wc_payment_gateway_id ] ) )
			return;

		add_action( 'admin_print_styles', function ()
		{
			wp_enqueue_style( 'booknetic-font',  '//fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap' );
			wp_enqueue_style( 'booknetic-settings',  Helper::assets('/css/payment_gateways_settings_wc.css', 'Settings') );
		});

		_wp_admin_html_begin();

		print '<meta name="viewport" content="width=device-width, initial-scale=1"></head>';
		print '<body class="woocommerce_fileds">';

		do_action( 'admin_enqueue_scripts' );
		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );

		$wc_payment_gateways[$wc_payment_gateway_id]->admin_options();

		do_action( 'admin_print_footer_scripts' );

		print '<script type="text/javascript">if(typeof wpOnload==\'function\')wpOnload();</script>';
		print '</body>';
		print '</html>';
	}

}
