<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Integrations\LoginButtons\FacebookLogin;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/integrations_facebook_api_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/integrations_facebook_api_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Facebook API / Continue with Facebook button')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row enable_disable_row">

					<div class="form-group col-md-2">
						<input id="input_facebook_login_enable" type="radio" name="input_facebook_login_enable" value="off"<?php print Helper::getOption('facebook_login_enable', 'off')=='off'?' checked':''?>>
						<label for="input_facebook_login_enable"><?php print bkntc__('Disabled')?></label>
					</div>
					<div class="form-group col-md-2">
						<input id="input_facebook_login_disable" type="radio" name="input_facebook_login_enable" value="on"<?php print Helper::getOption('facebook_login_enable', 'off')=='on'?' checked':''?>>
						<label for="input_facebook_login_disable"><?php print bkntc__('Enabled')?></label>
					</div>

				</div>

				<div id="integrations_facebook_api_settings_area">

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_google_calendar_redirect_uri"><?php print bkntc__('Redirect URI')?>:</label>
							<input class="form-control" id="input_google_calendar_redirect_uri" value="<?php print FacebookLogin::callbackURL() ?>" readonly>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_facebook_app_id"><?php print bkntc__('App ID')?>: <span class="required-star">*</span></label>
							<input class="form-control" id="input_facebook_app_id" value="<?php print htmlspecialchars( Helper::getOption('facebook_app_id', '') )?>">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_facebook_app_secret"><?php print bkntc__('App Secret')?>: <span class="required-star">*</span></label>
							<input class="form-control" id="input_facebook_app_secret" value="<?php print htmlspecialchars( Helper::getOption('facebook_app_secret', '') )?>">
						</div>
					</div>



				</div>

			</form>

		</div>
	</div>
</div>