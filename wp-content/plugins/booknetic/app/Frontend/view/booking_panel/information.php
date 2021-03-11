<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<div>
	<?php
	if(
		Helper::getOption('facebook_login_enable', 'off', false) == 'on'
		&& !empty( Helper::getOption('facebook_app_id', '', false) )
		&& !empty( Helper::getOption('facebook_app_secret', '', false) )
	)
	{
		?>
		<button type="button" class="booknetic_social_login_facebook" data-href="<?php print site_url() . '/?booknetic_action=facebook_login'?>"><?php print bkntc__('CONTINUE WITH FACEBOOK')?></button>
		<?php
	}

	if(
		Helper::getOption('google_login_enable', 'off', false) == 'on'
		&& !empty( Helper::getOption('google_login_app_id', '', false) )
		&& !empty( Helper::getOption('google_login_app_secret', '', false) )
	)
	{
		?>
		<button type="button" class="booknetic_social_login_google" data-href="<?php print site_url() . '/?booknetic_action=google_login'?>"><?php print bkntc__('CONTINUE WITH GOOGLE')?></button>
		<?php
	}
	?>
</div>

<div class="form-row">
	<div class="form-group col-md-<?php print $parameters['show_only_name'] ? '12' : '6'?>">
		<label for="bkntc_input_name" data-required="true"><?php print bkntc__('Name')?></label>
		<input type="text" id="bkntc_input_name" class="form-control" name="first_name" value="<?php print esc_html( $parameters['name'] . ( $parameters['show_only_name'] ? ' ' . $parameters['surname'] : '' ) )?>">
	</div>
	<div class="form-group col-md-6<?php print $parameters['show_only_name'] ? ' booknetic_hidden' : ''?>">
		<label for="bkntc_input_surname"<?php print $parameters['show_only_name'] ? '' : ' data-required="true"'?>><?php print bkntc__('Surname')?></label>
		<input type="text" id="bkntc_input_surname" class="form-control" name="last_name" value="<?php print esc_html( $parameters['show_only_name'] ? '' : $parameters['surname'] )?>">
	</div>
</div>
<div class="form-row">
	<div class="form-group col-md-6">
		<label for="bkntc_input_email" <?php print $parameters['email_is_required']=='on'?' data-required="true"':''?>><?php print bkntc__('Email')?></label>
		<input type="text" id="bkntc_input_email" class="form-control" name="email" value="<?php print esc_html( $parameters['email'] )?>">
	</div>
	<div class="form-group col-md-6">
		<label for="bkntc_input_phone" <?php print $parameters['phone_is_required']=='on'?' data-required="true"':''?>><?php print bkntc__('Phone')?></label>
		<input type="text" id="bkntc_input_phone" class="form-control" name="phone" value="<?php print esc_html( $parameters['phone'] )?>" data-country-code="<?php print Helper::getOption('default_phone_country_code', '')?>">
	</div>
</div>

<div id="booknetic_custom_form">
	<?php
	foreach ( $parameters['custom_inputs'] AS $custom_data )
	{
		?>

		<div class="form-row">
			<?php
			print \BookneticApp\Backend\Customforms\Helpers\FormElements::formElement( 1, $custom_data['type'], $custom_data['label'], $custom_data['is_required'], $custom_data['help_text'], '', $custom_data['id'], $custom_data['options'] );
			?>
		</div>

		<?php
	}
	?>
</div>

