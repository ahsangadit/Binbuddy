<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<div class="form-row">
	<div class="form-group col-md-12">
		<table class="booknetic_table_gray booknetic_dashed_border booknetic_recurring_table">
			<thead>
			<tr>
				<th><?php print bkntc__('#')?></th>
				<th><?php print bkntc__('DATE')?></th>
				<th<?php print $parameters['date_based'] ? ' class="booknetic_hidden"' : ''?>><?php print bkntc__('TIME')?></th>
			</tr>
			</thead>
			<tbody id="booknetic_recurring_dates">
			<?php
			$index = 1;
			foreach ( $parameters['dates'] AS $date )
			{
				$hasError = $date[2] ? '' : '<span class="booknetic_data_has_error" title="' . bkntc__('DATE') . '"><img src="' . Helper::icon('warning_red.svg', 'front-end') . '"></span>';
				?>
				<tr>
					<td><?php print $index++?></td>
					<td data-date="<?php print esc_html($date[0])?>"><?php print Date::datee($date[0])?></td>
					<td data-time="<?php print esc_html($date[1])?>"<?php print $parameters['date_based'] ? ' class="booknetic_hidden"' : ''?>>
						<span class="booknetic_time_span"><?php print Date::time($date[1])?></span>
						<?php print $hasError?>
						<button type="button" class="booknetic_btn_secondary booknetic_date_edit_btn"><?php print bkntc__('EDIT')?></button>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
</div>

