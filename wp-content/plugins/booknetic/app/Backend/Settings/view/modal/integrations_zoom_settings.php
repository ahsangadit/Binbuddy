<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/integrations_zoom_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/integrations_zoom_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Integration with Zoom')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row enable_disable_row">

					<div class="form-group col-md-2">
						<input id="input_zoom_enable" type="radio" name="input_zoom_enable" value="off"<?php print Helper::getOption('zoom_enable', 'off')=='off'?' checked':''?>>
						<label for="input_zoom_enable"><?php print bkntc__('Disabled')?></label>
					</div>
					<div class="form-group col-md-2">
						<input id="input_zoom_disable" type="radio" name="input_zoom_enable" value="on"<?php print Helper::getOption('zoom_enable', 'off')=='on'?' checked':''?>>
						<label for="input_zoom_disable"><?php print bkntc__('Enabled')?></label>
					</div>

				</div>

				<div id="integrations_zoom_settings_area">

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_zoom_api_key"><?php print bkntc__('API Key')?>: <span class="required-star">*</span></label>
							<input class="form-control" id="input_zoom_api_key" value="<?php print htmlspecialchars( Helper::getOption('zoom_api_key', '') )?>">
						</div>

						<div class="form-group col-md-6">
							<label for="input_zoom_api_secret"><?php print bkntc__('API Secret')?>: <span class="required-star">*</span></label>
							<input class="form-control" id="input_zoom_api_secret" value="<?php print htmlspecialchars( Helper::getOption('zoom_api_secret', '') )?>">
						</div>

						<div class="form-group col-md-12">
							<label for="input_zoom_meeting_title"><?php print bkntc__('Meeting topic')?>: <span class="required-star">*</span></label>
							<input class="form-control" id="input_zoom_meeting_title" value="<?php print htmlspecialchars( Helper::getOption('zoom_meeting_title', '') )?>">
						</div>

						<div class="form-group col-md-12">
							<label for="input_zoom_meeting_agenda"><?php print bkntc__('Meeting description')?>: <span class="required-star">*</span></label>
							<textarea class="form-control" id="input_zoom_meeting_agenda"><?php print htmlspecialchars( Helper::getOption('zoom_meeting_agenda', '') )?></textarea>
							<button type="button" class="btn btn-default btn-sm mt-2" data-load-modal="Settings.keywords_list"><?php print bkntc__('List of keywords')?> <i class="far fa-question-circle"></i></button>
						</div>

						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_zoom_set_random_password"><?php print bkntc__('Set random password for meetings')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_zoom_set_random_password"<?php print Helper::getOption('zoom_set_random_password', 'on')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_zoom_set_random_password"></label>
								</div>
							</div>
						</div>

					</div>



				</div>

			</form>

		</div>
	</div>
</div>