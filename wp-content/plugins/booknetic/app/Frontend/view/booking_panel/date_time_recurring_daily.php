<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div class="booknetic_recurring_div">

	<div class="booknetic_recurring_div_title"><?php print bkntc__('Daily')?></div>
	<div class="booknetic_dashed_border booknetic_recurring_div_c booknetic_recurring_div_padding">
		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="booknetic_daily_recurring_frequency"><?php print bkntc__('Every')?></label>
				<div class="booknetic_inner_addon booknetic_left_addon booknetic_right_addon">
					<img src="<?php print Helper::icon('calendar.svg')?>"/>
					<input type="text" class="form-control" id="booknetic_daily_recurring_frequency" value="1">
					<i class="booknetic_days_txt"><?php print bkntc__('DAYS')?></i>
				</div>
			</div>
			<div class="form-group col-md-6<?php print $parameters['date_based'] ? ' booknetic_hidden' : '' ?>">
				<label for="booknetic_daily_time"><?php print bkntc__('Time')?></label>
				<div class="booknetic_inner_addon booknetic_left_addon">
					<img src="<?php print Helper::icon('time.svg')?>"/>
					<select class="form-control" id="booknetic_daily_time">
						<?php
						if( $parameters['date_based'] )
						{
							print '<option selected>00:00</option>';
						}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="booknetic_recurring_start"><?php print bkntc__('Start date')?></label>
			<div class="booknetic_inner_addon booknetic_left_addon">
				<img src="<?php print Helper::icon('calendar.svg')?>"/>
				<input type="text" class="form-control" id="booknetic_recurring_start" value="<?php print Date::datee()?>">
			</div>
		</div>
		<div class="form-group col-md-4">
			<label for="booknetic_recurring_end"><?php print bkntc__('End date')?></label>
			<div class="booknetic_inner_addon booknetic_left_addon">
				<img src="<?php print Helper::icon('calendar.svg')?>"/>
				<input type="text" class="form-control" id="booknetic_recurring_end">
			</div>
		</div>
		<div class="form-group col-md-4">
			<label for="booknetic_recurring_times"><?php print bkntc__('Times')?></label>
			<input type="text" class="form-control" id="booknetic_recurring_times">
		</div>
	</div>

</div>


