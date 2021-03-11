<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/sms_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/sms_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('SMS/WhatsApp settings')?>
		</div>
		<div class="ms-content">

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_sms_account_sid"><?php print bkntc__('Account SID')?>:</label>
					<input class="form-control" id="input_sms_account_sid" value="<?php print htmlspecialchars( Helper::getOption('sms_account_sid', '') )?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_sms_auth_token"><?php print bkntc__('Auth Token')?>:</label>
					<input class="form-control" id="input_sms_auth_token" value="<?php print htmlspecialchars( Helper::getOption('sms_auth_token', '') )?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_sender_phone_number"><?php print bkntc__('Sender phone number for SMS')?>:</label>
					<input class="form-control" id="input_sender_phone_number" value="<?php print htmlspecialchars( Helper::getOption('sender_phone_number', '') )?>" placeholder="+15123456789">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_sender_phone_number_whatsapp"><?php print bkntc__('Sender phone number for WhatsApp')?>:</label>
					<input class="form-control" id="input_sender_phone_number_whatsapp" value="<?php print htmlspecialchars( Helper::getOption('sender_phone_number_whatsapp', '') )?>" placeholder="+15123456789">
				</div>
			</div>

		</div>
	</div>
</div>