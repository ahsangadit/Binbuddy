<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<link href='<?php print Helper::assets('css/calendar.css', 'Calendar')?>' rel='stylesheet' />
<link href='<?php print Helper::assets('plugins/fullcalendar/packages/core/main.css', 'Calendar')?>' rel='stylesheet' />
<link href='<?php print Helper::assets('plugins/fullcalendar/packages/daygrid/main.css', 'Calendar')?>' rel='stylesheet' />
<link href='<?php print Helper::assets('plugins/fullcalendar/packages/timegrid/main.css', 'Calendar')?>' rel='stylesheet' />
<link href='<?php print Helper::assets('plugins/fullcalendar/packages/list/main.css', 'Calendar')?>' rel='stylesheet' />

<script src='<?php print Helper::assets('plugins/fullcalendar/packages/core/main.js', 'Calendar')?>'></script>
<script src='<?php print Helper::assets('plugins/fullcalendar/packages/interaction/main.js', 'Calendar')?>'></script>
<script src='<?php print Helper::assets('plugins/fullcalendar/packages/daygrid/main.js', 'Calendar')?>'></script>
<script src='<?php print Helper::assets('plugins/fullcalendar/packages/timegrid/main.js', 'Calendar')?>'></script>
<script src='<?php print Helper::assets('plugins/fullcalendar/packages/list/main.js', 'Calendar')?>'></script>
<script src='<?php print Helper::assets('js/calendar.js', 'Calendar')?>'></script>

<div class="m_header d-flex">
	<div class="m_head_title"><?php print bkntc__('Calendar')?></div>

	<div class="staff_filter_area clearfix">

		<div class="staff_arrows staff_arrow_left"><i class="fa fa-chevron-left"></i></div>

		<div class="staff-section">
			<?php
			foreach ( $parameters['staff'] AS $staff )
			{
				print '<div data-staff="' . (int)$staff['id'] . '">' . Helper::profileCard($staff['name'], $staff['profile_image'], '', 'Staff', true) . '</div>';
			}
			?>
		</div>

		<div class="staff_arrows staff_arrow_right"><i class="fa fa-chevron-right"></i></div>

	</div>

	<div class="m_head_actions">
		<div class="filters_panel">
			<div>
				<select class="form-control" data-placeholder="<?php print bkntc__('Location filter')?>" id="calendar_location_filter">
					<option></option>
					<?php
					foreach ( $parameters['locations'] AS $location )
					{
						print '<option value="' . (int)$location['id'] . '">' . esc_html($location['name']) . '</option>';
					}
					?>
				</select>
			</div>
			<div>
				<select class="form-control" data-placeholder="<?php print bkntc__('Service filter')?>" id="calendar_service_filter">
					<option></option>
					<?php
					foreach ( $parameters['services'] AS $service )
					{
						print '<option value="' . (int)$service['id'] . '">' . esc_html($service['name']) . '</option>';
					}
					?>
				</select>
			</div>
		</div>
	</div>

</div>

<div class="fs-calendar-container">
	<div id='fs-calendar'></div>
</div>

<div class="create_new_appointment_btn" data-toggle="tooltip" data-placement="top" title="<?php print bkntc__('New appointment')?>">
	<i class="fa fa-plus"></i>
</div>

<script type="application/javascript">
	localization['TODAY']   = "<?php print bkntc__('TODAY');?>";
	localization['month']   = "<?php print bkntc__('month');?>";
	localization['week']    = "<?php print bkntc__('week');?>";
	localization['day']     = "<?php print bkntc__('day');?>";
	localization['list']    = "<?php print bkntc__('list');?>";
	localization['all-day'] = "<?php print bkntc__('all-day');?>";
</script>