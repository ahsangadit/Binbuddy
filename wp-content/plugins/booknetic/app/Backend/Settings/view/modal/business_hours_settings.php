<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

function breakTpl( $start = '', $end = '', $display = false )
{
	?>
	<div class="form-row break_line<?php print $display?'':' hidden'?>">
		<div class="form-group col-md-9">
			<label for="input_duration" class="breaks-label"><?php print bkntc__('Breaks')?></label>
			<div class="input-group">
				<div class="col-md-6 p-0 m-0"><select class="form-control break_start" placeholder="<?php print bkntc__('Break start')?>"><option selected><?php print $start?></option></select></div>
				<div class="col-md-6 p-0 m-0"><select class="form-control break_end" placeholder="<?php print bkntc__('Break end')?>"><option selected><?php print $end?></option></select></div>
			</div>
		</div>

		<div class="form-group col-md-3">
			<img src="<?php print Helper::assets('icons/unsuccess.svg')?>" class="delete-break-btn">
		</div>
	</div>
	<?php
}

?>

<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/business_hours.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/business_hours.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Business hours')?>
		</div>
		<div class="ms-content">

			<div class="p-3">
				<?php
				$weekDays = [ bkntc__('Monday'), bkntc__('Tuesday'), bkntc__('Wednesday'), bkntc__('Thursday'), bkntc__('Friday'), bkntc__('Saturday'), bkntc__('Sunday') ];
				$ts_editInfo = $parameters['timesheet'];

				foreach ( $weekDays AS $dayNum => $weekDay )
				{
					$editInfo = isset($ts_editInfo[ $dayNum ]) ? $ts_editInfo[ $dayNum ] : false;

					?>
					<div class="form-row col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8 p-0">
						<div class="form-group col-lg-9 col-md-12">
							<label for="input_duration" class="timesheet-label"><?php print ($dayNum+1) . '. ' . $weekDay . ( $dayNum == 0 ? '<span class="copy_time_to_all" data-toggle="tooltip" data-placement="top" title="Copy to all"><i class="far fa-copy"></i></span>' : '' ) ?></label>
							<div class="input-group">
								<div class="col-md-6 p-0 m-0">
									<select id="input_timesheet_<?php print ($dayNum+1)?>_start" class="form-control" placeholder="<?php print bkntc__('Start time')?>"><option selected><?php print htmlspecialchars($editInfo['start'])?></option></select>
								</div>
								<div class="col-md-6 p-0 m-0">
									<select id="input_timesheet_<?php print ($dayNum+1)?>_end" class="form-control" placeholder="<?php print bkntc__('End time')?>"><option selected><?php print htmlspecialchars($editInfo['end'])?></option></select>
								</div>
							</div>
						</div>

						<div class="form-group col-lg-3 col-md-12">
							<div class="day_off_checkbox">
								<input type="checkbox" class="dayy_off_checkbox" id="dayy_off_checkbox_<?php print ($dayNum+1)?>"<?php print ($editInfo['day_off']? ' checked' : '')?>>
								<label for="dayy_off_checkbox_<?php print ($dayNum+1)?>"><?php print bkntc__('Add day off')?></label>
							</div>
						</div>
					</div>

					<div class="breaks_area col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8 p-0" data-day="<?php print ($dayNum+1)?>">
						<?php
						if( is_array( $editInfo['breaks'] ) )
						{
							foreach ( $editInfo['breaks'] AS $breakInf )
							{
								breakTpl( $breakInf[0], $breakInf[1], true );
							}
						}
						?>
					</div>

					<div class="add-break-btn"><i class="fas fa-plus-circle"></i> <?php print bkntc__('Add break')?></div>

					<?php
					if( $dayNum < 6 )
					{
						?>
						<div class="days_divider"></div>
						<div class="mt-5"></div>
						<?php
					}
					?>

					<?php
				}
				?>
			</div>

		</div>
	</div>

	<?php print breakTpl()?>
</div>