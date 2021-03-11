<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Backend\Appointments\Model\Timesheet;
use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Backend\Services\Model\Service;
use BookneticApp\Backend\Settings\Helpers\LocalizationService;
use BookneticApp\Backend\Staff\Model\Staff;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Menu;
use BookneticApp\Providers\Session;

defined( 'ABSPATH' ) or die();

$localization = [
	// Appearance
	'are_you_sure'					=> bkntc__('Are you sure?'),

	// Appointments
	'select'						=> bkntc__('Select...'),
	'firstly_select_service'		=> bkntc__('Please firstly choose a service!'),
	'fill_all_required'				=> bkntc__('Please fill in all required fields correctly!'),
	'timeslot_is_not_available'		=> bkntc__('This time slot is not available!'),

	// Base
	'are_you_sure_want_to_delete'	=> bkntc__('Are you sure you want to delete?'),
	'rows_deleted'					=> bkntc__('Rows deleted!'),
	'delete'                        => bkntc__('DELETE'),
	'cancel'                        => bkntc__('CANCEL'),
	'dear_user'                     => bkntc__('Dear user'),

	// calendar
	'group_appointment'				=> bkntc__('Group appointment'),

	// Customforms
	'select_services'				=> bkntc__('Select services...'),
	'changes_saved'					=> bkntc__('Changes has been saved!'),

	// Dashboard
	'loading'					    => bkntc__('Loading...'),

	// Notifications
	'fill_form_correctly'			=> bkntc__('Fill the form correctly!'),
	'saved_successfully'			=> bkntc__('Saved succesfully!'),
	'type_email'   					=> bkntc__('Please type email!'),
	'type_phone_number'   			=> bkntc__('Please type phone number!'),

	// Services
	'delete_service_extra'			=> bkntc__('Are you sure that you want to delete this service extra?'),
	'no_more_staff_exist'			=> bkntc__('No more Staff exists for select!'),
	'delete_special_day'			=> bkntc__('Are you sure to delete this special day?'),
	'times_per_month'				=> bkntc__('time(s) per month'),
	'times_per_week'				=> bkntc__('time(s) per week'),
	'every_n_day'					=> bkntc__('Every n day(s)'),
	'delete_service'				=> bkntc__('Are you sure you want to delete this service?'),
	'delete_category'				=> bkntc__('Are you sure you want to delete this category?'),
	'category_name'					=> bkntc__('Category name'),

	// months
	'January'               		=> bkntc__('January'),
	'February'              		=> bkntc__('February'),
	'March'                 		=> bkntc__('March'),
	'April'                 		=> bkntc__('April'),
	'May'                   		=> bkntc__('May'),
	'June'                  		=> bkntc__('June'),
	'July'                  		=> bkntc__('July'),
	'August'                		=> bkntc__('August'),
	'September'             		=> bkntc__('September'),
	'October'               		=> bkntc__('October'),
	'November'              		=> bkntc__('November'),
	'December'              		=> bkntc__('December'),

	//days of week
	'Mon'                   		=> bkntc__('Mon'),
	'Tue'                   		=> bkntc__('Tue'),
	'Wed'                   		=> bkntc__('Wed'),
	'Thu'                   		=> bkntc__('Thu'),
	'Fri'                   		=> bkntc__('Fri'),
	'Sat'                   		=> bkntc__('Sat'),
	'Sun'                   		=> bkntc__('Sun'),
];

$servicesIsOk       = Service::count() > 0;
$businessHoursIsOk  = Timesheet::where('service_id', 'is', null)->where('staff_id', 'is', null)->count() > 0;
$companyDetailsIsOk = Helper::getOption('company_name', '') != '';

?>
<html>
<head>
	<title><?php print esc_html( Helper::getOption('backend_title', 'Booknetic', false) )?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="//fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
	<link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.13/css/all.css?ver=5.0.2' type='text/css'>

	<link rel="stylesheet" href="<?php print Helper::assets('css/bootstrap.min.css')?>" type='text/css'>

	<link rel='stylesheet' href='<?php print Helper::assets('css/main.css')?>' type='text/css'>
	<link rel='stylesheet' href='<?php print Helper::assets('css/animate.css')?>' type='text/css'>
	<link rel='stylesheet' href='<?php print Helper::assets('css/select2.min.css')?>' type='text/css'>
	<link rel='stylesheet' href='<?php print Helper::assets('css/select2-bootstrap.css')?>' type='text/css'>
	<link rel="stylesheet" href="<?php print Helper::assets('css/bootstrap-datepicker.css')?>" type='text/css'>

	<script type="application/javascript" src="<?php print Helper::assets('js/jquery-3.3.1.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/popper.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/bootstrap.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/select2.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/jquery-ui.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/jquery.ui.touch-punch.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/bootstrap-datepicker.min.js')?>"></script>
	<script type="application/javascript" src="<?php print Helper::assets('js/jquery.nicescroll.min.js')?>"></script>

	<link rel="shortcut icon" href="<?php print Helper::profileImage( Helper::getOption('whitelabel_logo_sm', 'logo-sm', false), 'Base')?>">

	<script src="<?php print Helper::assets('js/booknetic.js')?>"></script>

	<script>
		var ajaxurl			    =	'?page=<?php print \BookneticApp\Providers\Backend::MENU_SLUG?>&ajax=1',
			currentModule	    =	"<?php print htmlspecialchars(\BookneticApp\Providers\Backend::$currentModule)?>",
			assetsUrl		    =	"<?php print Helper::assets('')?>",
			frontendAssetsUrl	=	"<?php print Helper::assets('', 'front-end')?>",
			weekStartsOn	    =	"<?php print Helper::getOption('week_starts_on', 'sunday') == 'monday' ? 'monday' : 'sunday'?>",
			dateFormat  	    =	"<?php print esc_html(Helper::getOption('date_format', 'Y-m-d'))?>",
			timeFormat  	    =	"<?php print esc_html(Helper::getOption('time_format', 'H:i'))?>",
			localization	    =   <?php print json_encode($localization)?>;
	</script>

</head>
<body class="minimized_left_menu-">

	<div id="booknetic_progress" class="booknetic_progress_waiting booknetic_progress_done"><dt></dt><dd></dd></div>

	<div class="left_side_menu">

		<div class="l_m_head">
			<img src="<?php print Helper::profileImage( Helper::getOption('whitelabel_logo', 'logo', false), 'Base')?>" class="head_logo_xl">
			<img src="<?php print Helper::profileImage( Helper::getOption('whitelabel_logo_sm', 'logo-sm', false), 'Base')?>" class="head_logo_sm">
		</div>

		<ul class="l_m_nav">
			<?php foreach ( \BookneticApp\Providers\Backend::getMenu( Menu::MENU_TYPE_LEFT ) AS $menu ) { ?>
				<?php if ( $menu->hasParent() ) { ?>
					<li class="l_m_nav_item is_parent" data-id="<?php print $menu->getParent( 'id' )?>">
						<a href="#" class="l_m_nav_item_link">
							<i class="l_m_nav_item_icon <?php print $menu->getParent( 'icon' )?>"></i>
							<span class="l_m_nav_item_text"><?php print $menu->getParent( 'name' )?></span>
							<i class="l_m_nav_item_icon is_collapse_icon fa fa-chevron-down"></i>
						</a>
					</li>
				<?php } ?>
				<li class="l_m_nav_item <?php print $menu->isActive()?'active_menu':''?><?php print ( $menu->hasParentID() ? ' is_sub' : '' ); ?>" <?php print ( $menu->hasParentID() ? 'data-parent-id="' . $menu->getParentID() . '"' : '' ); ?>>
					<a href="<?php print $menu->getLink()?>" class="l_m_nav_item_link">
						<?php if ( $menu->hasParentID() ) { ?>
							<span class="l_m_nav_item_icon_dot"></span>
						<?php } else { ?>
							<i class="l_m_nav_item_icon <?php print $menu->getIcon()?>"></i>
						<?php } ?>
						<span class="l_m_nav_item_text"><?php print $menu->getName()?></span>
					</a>
				</li>
			<?php } ?>

			<li class="l_m_nav_item d-md-none">
				<?php
				if( !Helper::isSaaSVersion() )
				{
					?>
					<a href="index.php" class="l_m_nav_item_link">
						<i class="l_m_nav_item_icon fab fa-wordpress"></i>
						<span class="l_m_nav_item_text"><?php print bkntc__('Back to WordPress')?></span>
					</a>
					<?php
				}
				?>
			</li>

		</ul>

	</div>

	<div class="top_side_menu">
		<div class="t_m_left">
			<?php
			if( !Helper::isSaaSVersion() )
			{
				?>
				<button class="btn btn-default btn-lg d-md-block d-none" type="button" id="back_to_wordpress_btn"><img src="<?php print Helper::icon('back.svg')?>" class="pr-2"> <span><?php print bkntc__('WORDPRESS')?></span></button>
				<?php
			}
			?>
			<button class="btn btn-default btn-lg d-md-none" type="button" id="open_menu_bar"><i class="fa fa-bars"></i></button>
		</div>
		<div class="t_m_right">

			<div class="user_visit_card">
				<div class="circle_image">
					<img src="<?php print get_avatar_url(get_current_user_id())?>">
				</div>
				<div class="user_visit_details" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
					<span><?php print bkntc__('Hello %s', [ esc_html(wp_get_current_user()->display_name) ]) ?> <i class="fa fa-angle-down"></i></span>
				</div>
				<div class="dropdown-menu dropdown-menu-right row-actions-area">
					<?php foreach ( \BookneticApp\Providers\Backend::getMenu( Menu::MENU_TYPE_TOP ) AS $menu ) { ?>
						<a href="<?php print $menu->getLink()?>" class="dropdown-item info_action_btn"><i class="<?php print $menu->getIcon()?>"></i> <?php print $menu->getName()?></a>
					<?php } ?>

					<?php if( Helper::isSaaSVersion() ): ?>
					<a href="#" class="dropdown-item share_your_page_btn"><i class="fa fa-share"></i> <?php print bkntc__('Share you page')?></a>
					<?php endif; ?>

					<hr class="mt-2 mb-2"/>
					<a href="<?php echo wp_logout_url( home_url() ); ?>" class="dropdown-item edit_action_btn"><i class="fa fa-sign-out-alt"></i> <?php print bkntc__('Log out')?></a>
				</div>
			</div>
		</div>
		<?php if(
			Helper::isSaaSVersion() &&
			Helper::getOption('enable_language_switcher', 'off', false) == 'on' &&
			count( Helper::getOption('active_languages', [], false) ) > 1
		):?>
			<div class="language-chooser-bar">
				<div class="language-chooser" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
					<span><?php print esc_html( LocalizationService::getLanguageName( Session::get('active_language', get_locale()) ) )?></span>
					<i class="fa fa-angle-down"></i>
				</div>
				<div class="dropdown-menu dropdown-menu-right row-actions-area" id="language-switcher-select">
					<?php foreach ( Helper::getOption('active_languages', [], false) AS $active_language ):?>
						<div data-language-key="<?php print esc_html( $active_language )?>" class="dropdown-item info_action_btn"><?php print esc_html( LocalizationService::getLanguageName( $active_language ) )?></div>
					<?php endforeach;?>
				</div>
			</div>
		<?php endif;?>
	</div>

	<div class="main_wrapper">
		<?php
		if( isset($childViewFile) && file_exists( $childViewFile ) )
			require_once $childViewFile;
		?>
	</div>

	<div class="starting_guide_icon" data-actions="0">
		<img src="<?php print Helper::icon('starting_guide.svg')?>">
	</div>

	<div class="starting_guide_panel">
		<div class="starting_guide_head">
			<div class="starting_guide_title"><i class="fa fa-rocket"></i> <?php print bkntc__('Starting guide')?></div>
			<div class="starting_guide_progress_bar">
				<div class="starting_guide_progress_bar_stick"><div class="starting_guide_progress_bar_stick_color"></div></div>
				<div class="starting_guide_progress_bar_text"><span>01</span><span> / 03</span></div>
			</div>
		</div>
		<div class="starting_guide_body">
			<a href="?page=booknetic&module=settings&setting=company" class="starting_guide_steps<?php print ($companyDetailsIsOk ? ' starting_guide_steps_completed' : '')?>"><?php print bkntc__('Company details')?></a>
			<a href="?page=booknetic&module=settings&setting=business_hours" class="starting_guide_steps<?php print ($businessHoursIsOk ? ' starting_guide_steps_completed' : '')?>"><?php print bkntc__('Business hours')?></a>
			<?php
			if( !Helper::isSaaSVersion() )
			{
				$locationsIsOk   = Location::count() > 0;
				$staffIsOk       = Staff::count() > 0;
				?>
				<a href="?page=booknetic&module=locations" class="starting_guide_steps<?php print ($locationsIsOk ? ' starting_guide_steps_completed' : '')?>"><?php print bkntc__('Create location')?></a>
				<a href="?page=booknetic&module=staff" class="starting_guide_steps<?php print ($staffIsOk ? ' starting_guide_steps_completed' : '')?>"><?php print bkntc__('Create staff')?></a>
				<?php
			}
			?>
			<a href="?page=booknetic&module=services" class="starting_guide_steps<?php print ($servicesIsOk ? ' starting_guide_steps_completed' : '')?>"><?php print bkntc__('Create service')?></a>
		</div>
	</div>

</body>
</html>