<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
$weekStartsOn = Helper::getOption('week_starts_on', 'sunday') == 'monday' ? 'monday' : 'sunday';
?>

<div class="booknetic_recurring_div">

	<div class="booknetic_recurring_div_title"><?php print bkntc__('Days of week')?></div>
	<div class="booknetic_dashed_border booknetic_recurring_div_c">
		<div class="booknetic_clearfix">
			<?php
			if( $weekStartsOn == 'sunday' )
			{
				?>
				<div class="booknetic_day_of_week_box">
					<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_7">
					<label for="booknetic_day_of_week_checkbox_7"><?php print bkntc__('Sun')?></label>
				</div>
				<?php
			}
			?>
			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_1"/>
				<label for="booknetic_day_of_week_checkbox_1"><?php print bkntc__('Mon')?></label>
			</div>

			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_2">
				<label for="booknetic_day_of_week_checkbox_2"><?php print bkntc__('Tue')?></label>
			</div>

			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_3">
				<label for="booknetic_day_of_week_checkbox_3"><?php print bkntc__('Wed')?></label>
			</div>

			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_4">
				<label for="booknetic_day_of_week_checkbox_4"><?php print bkntc__('Thu')?></label>
			</div>

			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_5">
				<label for="booknetic_day_of_week_checkbox_5"><?php print bkntc__('Fri')?></label>
			</div>

			<div class="booknetic_day_of_week_box">
				<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_6">
				<label for="booknetic_day_of_week_checkbox_6"><?php print bkntc__('Sat')?></label>
			</div>

			<?php
			if( $weekStartsOn == 'monday' )
			{
				?>
				<div class="booknetic_day_of_week_box">
					<input type="checkbox" class="booknetic_day_of_week_checkbox" id="booknetic_day_of_week_checkbox_7">
					<label for="booknetic_day_of_week_checkbox_7"><?php print bkntc__('Sun')?></label>
				</div>
				<?php
			}
			?>

		</div>
		<div class="booknetic_times_days_of_week_area">

			<div class="form-row booknetic_hidden" data-day="1">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Monday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_1"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="2">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Tuesday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_2"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="3">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Wednesday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_3"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="4">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Thursday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_4"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="5">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Friday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_5"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="6">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Saturday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_6"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
				</div>
			</div>

			<div class="form-row booknetic_hidden" data-day="7">
				<div class="form-group col-md-3">
					<div class="form-control-plaintext"><?php print bkntc__('Sunday')?></div>
				</div>
				<div class="form-group col-md-4">
					<div class="booknetic_inner_addon booknetic_left_addon">
						<img src="<?php print Helper::icon('time.svg')?>"/>
						<select class="form-control booknetic_wd_input_time" id="booknetic_time_wd_7"></select>
					</div>
				</div>
				<div class="col-md-2 booknetic_copy_time_to_all">
					<img src="<?php print Helper::icon('copy-to-all.svg', 'front-end')?>">
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
