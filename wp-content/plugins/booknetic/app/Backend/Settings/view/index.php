<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$openSetting = Helper::_get('setting', '', 'string');
$success = Helper::_get('success', '', 'string');
$msg = Helper::_get('msg', '', 'string');
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/settings.css', 'Settings')?>">
<link rel="stylesheet" href="<?php print Helper::assets('css/bootstrap-year-calendar.min.css')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/bootstrap-year-calendar.min.js')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/settings.js', 'Settings')?>" id="settingsJS" <?php if( !empty($openSetting) ) print 'data-goto="'.esc_html($openSetting).'"'; ?>></script>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Settings')?> <span class="badge badge-warning row_count">9</span></div>
</div>

<div class="row mr-0">
	<button class="btn btn-primary btn-lg settings-floating-button is-left hidden-important"></button>

	<div class="col-md-12 col-xl-3 col-lg-12 d-none d-xl-block settings-left-menu hidden-important">

		<div>

			<div class="settings-left-menu-title"><?php print bkntc__('Settings')?> <span class="badge badge-warning row_count">9</span></div>

			<div class="service_categories_list">

				<div class="settings_menu" data-view="general">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('general-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('General settings')?></div>
						<div class="sc-description"><?php print bkntc__('You can customize general settings about booking from here')?></div>
					</div>
				</div>

				<div class="settings_menu" data-view="booking_panel_steps">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('booking-steps-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('Front-end panels')?></div>
						<div class="sc-description"><?php print bkntc__('You can customize booking and customer panel and change labels from here')?></div>
					</div>

					<div class="clearfix"></div>

					<div class="settings_submenus">
						<div data-view="booking_panel_steps"><?php print bkntc__('Booking Steps')?></div>
						<?php if( !Helper::isSaaSVersion() ):?>
							<div data-view="customer_panel"><?php print bkntc__('Customer Panel')?></div>
						<?php endif;?>
						<div data-view="booking_panel_labels"><?php print bkntc__('Labels')?></div>
					</div>
				</div>

				<div class="settings_menu" data-view="payments">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('payments-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('Payment settings')?></div>
						<div class="sc-description"><?php print bkntc__('Currency, price format , general settings about payment , paypal, stripe and so on')?></div>
					</div>

					<div class="clearfix"></div>

					<div class="settings_submenus">
						<div data-view="payments"><?php print bkntc__('General')?></div>
						<div data-view="payment_gateways"><?php print bkntc__('Payment methods')?></div>
					</div>
				</div>

				<div class="settings_menu" data-view="company">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('company-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('Company details')?></div>
						<div class="sc-description"><?php print bkntc__('Enter your company name, logo, address, phone number, website from here')?></div>
					</div>
				</div>

				<div class="settings_menu" data-view="business_hours">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('business-hours-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('Business Hours')?></div>
						<div class="sc-description"><?php print bkntc__('You will be able to co-ordinate your company\'s overall work schedule')?></div>
					</div>
				</div>

				<div class="settings_menu" data-view="holidays">
					<div class="sc-bars-cls"><img src="<?php print Helper::icon('holidays-settings.svg', 'Settings')?>"></div>
					<div class="sc-title">
						<div class="sc-title-div"><?php print bkntc__('Holidays')?></div>
						<div class="sc-description"><?php print bkntc__('You can easily set with selecting special days, holidays over the calendar')?></div>
					</div>
				</div>

				<?php if( ! Helper::isSaaSVersion() ): ?>
					<div class="settings_menu" data-view="email">
						<div class="sc-bars-cls"><img src="<?php print Helper::icon('email-settings.svg', 'Settings')?>"></div>
						<div class="sc-title">
							<div class="sc-title-div"><?php print bkntc__('Email settings')?></div>
							<div class="sc-description"><?php print bkntc__('You must set this settings for email notifications')?></div>
						</div>
					</div>

					<div class="settings_menu" data-view="integrations_zoom">
						<div class="sc-bars-cls"><img src="<?php print Helper::icon('integrations-settings.svg', 'Settings')?>"></div>
						<div class="sc-title">
							<div class="sc-title-div"><?php print bkntc__('Integrations settings')?></div>
							<div class="sc-description"><?php print bkntc__('You can change settings for integrated services from here.')?></div>
						</div>
						<div class="clearfix"></div>
						<div class="settings_submenus">
							<div data-view="google_calendar"><?php print bkntc__('Google calendar')?></div>
							<div data-view="sms"><?php print bkntc__('SMS / WhatsApp')?></div>
							<div data-view="integrations_zoom"><?php print bkntc__('Zoom')?></div>
							<div data-view="integrations_facebook_api"><?php print bkntc__('Continue with Facebook')?></div>
							<div data-view="integrations_google_login"><?php print bkntc__('Continue with Google')?></div>
						</div>
					</div>

					<div class="settings_menu" data-view="backup">
						<div class="sc-bars-cls"><img src="<?php print Helper::icon('backup-settings.svg', 'Settings')?>"></div>
						<div class="sc-title">
							<div class="sc-title-div"><?php print bkntc__('Export & Import data')?></div>
							<div class="sc-description"><?php print bkntc__('You can export all Booknetic data and import from here.')?></div>
						</div>
					</div>
				<?php elseif( Helper::getOption('zoom_enable', 'off', false) === 'on' ): ?>
					<div class="settings_menu" data-view="connect_zoom">
						<div class="sc-bars-cls"><img src="<?php print Helper::icon('integrations-settings.svg', 'Settings')?>"></div>
						<div class="sc-title">
							<div class="sc-title-div"><?php print bkntc__('Integration with Zoom')?></div>
							<div class="sc-description"><?php print bkntc__('You can change settings for zoom integration from here.')?></div>
						</div>
					</div>
				<?php endif; ?>

			</div>

		</div>

	</div>
	<div class="col-md-12 col-xl-9 col-lg-12 settings-main-container hidden"></div>
	<div class="col-md-12 settings-main-menu">
		<div>
			<div class="row">
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="general">
						<div class="settings-icon"><img src="<?php print Helper::icon('general-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('General settings')?></div>
						<div class="settings-description"><?php print bkntc__('You can customize general settings about booking from here')?></div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="booking_panel_steps">
						<div class="settings-icon"><img src="<?php print Helper::icon('booking-steps-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('Front-end panels')?></div>
						<div class="settings-description"><?php print bkntc__('You can customize booking and customer panel and change labels from here')?></div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="payments">
						<div class="settings-icon"><img src="<?php print Helper::icon('payments-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('Payment settings')?></div>
						<div class="settings-description"><?php print bkntc__('Currency, price format , general settings about payment , paypal, stripe and so on')?></div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="company">
						<div class="settings-icon"><img src="<?php print Helper::icon('company-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('Company details')?></div>
						<div class="settings-description"><?php print bkntc__('Enter your company name, logo, address, phone number, website from here')?></div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="business_hours">
						<div class="settings-icon"><img src="<?php print Helper::icon('business-hours-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('Business Hours')?></div>
						<div class="settings-description"><?php print bkntc__('You will be able to co-ordinate your company\'s overall work schedule')?></div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
					<div class="settings-chart" data-view="holidays">
						<div class="settings-icon"><img src="<?php print Helper::icon('holidays-settings.svg', 'Settings')?>"></div>
						<div class="settings-title"><?php print bkntc__('Holidays')?></div>
						<div class="settings-description"><?php print bkntc__('You can easily set with selecting special days, holidays over the calendar')?></div>
					</div>
				</div>

				<?php if( ! Helper::isSaaSVersion() ): ?>
					<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
						<div class="settings-chart" data-view="email">
							<div class="settings-icon"><img src="<?php print Helper::icon('email-settings.svg', 'Settings')?>"></div>
							<div class="settings-title"><?php print bkntc__('Email settings')?></div>
							<div class="settings-description"><?php print bkntc__('You must set this settings for email notifications')?></div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
						<div class="settings-chart" data-view="integrations_zoom">
							<div class="settings-icon"><img src="<?php print Helper::icon('integrations-settings.svg', 'Settings')?>"></div>
							<div class="settings-title"><?php print bkntc__('Integrations settings')?></div>
							<div class="settings-description"><?php print bkntc__('You can change settings for integrated services from here.')?></div>
						</div>
					</div>
					<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
						<div class="settings-chart" data-view="backup">
							<div class="settings-icon"><img src="<?php print Helper::icon('backup-settings.svg', 'Settings')?>"></div>
							<div class="settings-title"><?php print bkntc__('Export & Import data')?></div>
							<div class="settings-description"><?php print bkntc__('You can export all Booknetic data and import from here.')?></div>
						</div>
					</div>
				<?php elseif( Helper::getOption('zoom_enable', 'off', false) === 'on' ):?>
					<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
						<div class="settings-chart" data-view="connect_zoom">
							<div class="settings-icon"><img src="<?php print Helper::icon('integrations-settings.svg', 'Settings')?>"></div>
							<div class="settings-title"><?php print bkntc__('Integration with Zoom')?></div>
							<div class="settings-description"><?php print bkntc__('You can change settings for zoom integration from here.')?></div>
						</div>
					</div>
				<?php endif;?>

			</div>
		</div>
	</div>
</div>

<?php if( $success === 'false' && !empty( $msg ) ): ?>
	<script>
		booknetic.toast("<?php print esc_html( $msg )?>", 'unsuccess');
	</script>
<?php endif;?>
