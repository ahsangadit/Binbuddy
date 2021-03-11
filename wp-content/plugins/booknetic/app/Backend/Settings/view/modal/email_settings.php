<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/email_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/email_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Email settings')?>
		</div>
		<div class="ms-content">

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_mail_gateway"><?php print bkntc__('Mail Gateway')?>:</label>
					<select class="form-control" id="input_mail_gateway">
						<option value="wp_mail"<?php print ( Helper::getOption('mail_gateway', 'wp_mail') == 'wp_mail' ? ' selected' : '' )?>><?php print bkntc__('WordPress Mail')?></option>
						<option value="smtp"<?php print ( Helper::getOption('mail_gateway', 'wp_mail') == 'smtp' ? ' selected' : '' )?>><?php print bkntc__('SMTP')?></option>
					</select>
				</div>
			</div>

			<div class="smtp_details dashed-border">
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_smtp_hostname"><?php print bkntc__('SMTP Hostname')?>:</label>
						<input class="form-control" id="input_smtp_hostname" value="<?php print htmlspecialchars( Helper::getOption('smtp_hostname', '') )?>">
					</div>
					<div class="form-group col-md-3">
						<label for="input_smtp_port"><?php print bkntc__('SMTP Port')?>:</label>
						<input class="form-control" id="input_smtp_port" value="<?php print htmlspecialchars( Helper::getOption('smtp_port', '') )?>">
					</div>
					<div class="form-group col-md-3">
						<label for="input_smtp_secure"><?php print bkntc__('SMTP Secure')?>:</label>
						<select class="form-control" id="input_smtp_secure">
							<option value="tls"<?php print ( Helper::getOption('smtp_secure', 'tls') == 'tls' ? ' selected' : '' )?>><?php print bkntc__('TLS')?></option>
							<option value="ssl"<?php print ( Helper::getOption('smtp_secure', 'tls') == 'ssl' ? ' selected' : '' )?>><?php print bkntc__('SSL')?></option>
							<option value="no"<?php print ( Helper::getOption('smtp_secure', 'tls') == 'no' ? ' selected' : '' )?>><?php print bkntc__('Disabled ( not recommend )')?></option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="input_smtp_username"><?php print bkntc__('Username')?>:</label>
						<input class="form-control" id="input_smtp_username" value="<?php print htmlspecialchars( Helper::getOption('smtp_username', '') )?>">
					</div>
					<div class="form-group col-md-6">
						<label for="input_smtp_password"><?php print bkntc__('Password')?>:</label>
						<input class="form-control" id="input_smtp_password" value="<?php print htmlspecialchars( Helper::getOption('smtp_password', '') )?>">
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_sender_email"><?php print bkntc__('Sender E-mail')?>:</label>
					<input class="form-control" id="input_sender_email" value="<?php print htmlspecialchars( Helper::getOption('sender_email', '') )?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_sender_name"><?php print bkntc__('Sender Name')?>:</label>
					<input class="form-control" id="input_sender_name" value="<?php print htmlspecialchars( Helper::getOption('sender_name', '') )?>">
				</div>
			</div>

		</div>
	</div>
</div>