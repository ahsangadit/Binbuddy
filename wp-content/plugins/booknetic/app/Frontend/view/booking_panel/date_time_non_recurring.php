<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div class="booknetic_date_time_area<?php print $parameters['date_based'] ? ' booknetic_date_based_reservation' : '';?>">
	<div class="booknetic_calendar_div">
		<div class="booknetic_calendar_head">
			<div class="booknetic_prev_month"> < </div>
			<div class="booknetic_month_name"></div>
			<div class="booknetic_next_month"> > </div>
		</div>
		<div id="booknetic_calendar_area"></div>
	</div>
	<div class="booknetic_time_div">
		<div class="booknetic_times_head"><?php print bkntc__('Time')?></div>
		<div class="booknetic_times">
			<div class="booknetic_times_title"><?php print bkntc__('Select date')?></div>
			<div class="booknetic_times_list booknetic_clearfix"></div>
		</div>
	</div>
</div>
