<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<div class="booknetic_recurring_div">

	<div class="booknetic_recurring_div_title"><?php print bkntc__('Days of week')?></div>
	<div class="booknetic_dashed_border booknetic_recurring_div_c booknetic_recurring_div_padding">

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="booknetic_monthly_recurring_type"><?php print bkntc__('On')?></label>
				<select class="form-control" id="booknetic_monthly_recurring_type">
					<option value="specific_day"><?php print bkntc__('Specific day')?></option>
					<option value="1"><?php print bkntc__('First')?></option>
					<option value="2"><?php print bkntc__('Second')?></option>
					<option value="3"><?php print bkntc__('Third')?></option>
					<option value="4"><?php print bkntc__('Fourth')?></option>
					<option value="last"><?php print bkntc__('Last')?></option>
				</select>
			</div>
			<div class="form-group col-md-4">
				<label for="booknetic_monthly_recurring_day_of_week">&nbsp;</label>
				<select class="form-control" id="booknetic_monthly_recurring_day_of_week">
					<option value="1">1. <?php print bkntc__('Monday')?></option>
					<option value="2">2. <?php print bkntc__('Tuesday')?></option>
					<option value="3">3. <?php print bkntc__('Wednesday')?></option>
					<option value="4">4. <?php print bkntc__('Thursday')?></option>
					<option value="5">5. <?php print bkntc__('Friday')?></option>
					<option value="6">6. <?php print bkntc__('Saturday')?></option>
					<option value="7">7. <?php print bkntc__('Sunday')?></option>
				</select>
				<select class="form-control" id="booknetic_monthly_recurring_day_of_month" multiple>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select>
			</div>
			<div class="form-group col-md-4<?php print $parameters['date_based'] ? ' booknetic_hidden' : '' ?>">
				<label for="booknetic_monthly_time"><?php print bkntc__('Time')?></label>
				<div class="booknetic_inner_addon booknetic_left_addon">
					<img src="<?php print Helper::icon('time.svg')?>"/>
					<select class="form-control" id="booknetic_monthly_time">
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
