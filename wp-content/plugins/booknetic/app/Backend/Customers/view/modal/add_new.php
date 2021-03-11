<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/intlTelInput.min.css', 'front-end')?>">

<script type="application/javascript" src="<?php print Helper::assets('js/intlTelInput.min.js', 'front-end')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/utilsIntlTelInput.js', 'front-end')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/add_new.js', 'Customers')?>" id="add_new_JS" data-customer-id="<?php print (int)$parameters['customer']['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
	<div class="title-text"><?php print $parameters['customer'] ? bkntc__('Customer') : bkntc__('Add Customer')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addCustomerForm">

			<div class="form-row">
				<div class="form-group col-md-<?php print $parameters['show_only_name'] ? '12' : '6'?>">
					<label for="input_first_name"><?php print bkntc__('First Name')?> <span class="required-star">*</span></label>
					<input type="text" class="form-control" id="input_first_name" value="<?php print htmlspecialchars($parameters['customer']['first_name'])?>">
				</div>
				<div class="form-group col-md-6<?php print $parameters['show_only_name'] ? ' hidden' : ''?>">
					<label for="input_last_name"><?php print bkntc__('Last Name')?> <span class="required-star">*</span></label>
					<input type="text" class="form-control" id="input_last_name" value="<?php print htmlspecialchars($parameters['customer']['last_name'])?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_email"><?php print bkntc__('Email')?> <?php print $parameters['email_is_required']=='on'?'<span class="required-star">*</span>':''?></label>
					<input type="text" class="form-control" id="input_email" placeholder="example@gmail.com" value="<?php print htmlspecialchars($parameters['customer']['email'])?>">
				</div>
				<div class="form-group col-md-6">
					<label for="input_phone"><?php print bkntc__('Phone')?> <?php print $parameters['phone_is_required']=='on'?'<span class="required-star">*</span>':''?></label>
					<input type="text" class="form-control" id="input_phone" value="<?php print htmlspecialchars($parameters['customer']['phone_number'])?>" data-country-code="<?php print Helper::getOption('default_phone_country_code', '')?>">
				</div>
			</div>

			<?php if( Permission::isAdministrator() ) : ?>
				<div class="form-row">
					<div class="form-group col-md-6">
						<?php if( Helper::isSaaSVersion() ) : ?>
							<label>&nbsp;</label>
						<?php endif; ?>
						<div class="form-control-checkbox">
							<label for="input_allow_customer_to_login"><?php print bkntc__('Allow to log in')?></label>
							<div class="fs_onoffswitch">
								<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_allow_customer_to_login" <?php print ($parameters['customer']['user_id'] > 0 ? ' checked' : '')?>>
								<label class="fs_onoffswitch-label" for="input_allow_customer_to_login"></label>
							</div>
						</div>
					</div>
					<?php if( !Helper::isSaaSVersion() ): ?>
						<div class="form-group col-md-6" data-hide="allow_customer_to_login">
							<select class="form-control" id="input_wp_user_use_existing">
								<option value="yes" <?php print ($parameters['customer']['user_id'] > 0 ? ' selected' : '')?>><?php print bkntc__('Use existing WordPress user')?></option>
								<option value="no"><?php print bkntc__('Create new WordPress user')?></option>
							</select>
						</div>
					<?php else: ?>
						<input type="hidden" id="input_wp_user_use_existing" value="no">
					<?php endif; ?>
					<?php if( !Helper::isSaaSVersion() ): ?>
						<div class="form-group col-md-6" data-hide="existing_user">
							<label for="input_wp_user"><?php print bkntc__('WordPress user')?></label>
							<select class="form-control" id="input_wp_user">
								<?php
								foreach ( $parameters['users'] AS $user )
								{
									?>
									<option value="<?php print (int)$user['ID']?>" <?php print ($user['ID'] == $parameters['customer']['user_id'] ? ' selected' : '')?> data-email="<?php print htmlspecialchars( $user['user_email'] )?>"><?php print htmlspecialchars( $user['display_name'] )?></option>
									<?php
								}
								?>
							</select>
						</div>
					<?php endif; ?>
					<div class="form-group col-md-6" data-hide="create_password">
						<label for="input_wp_user_password"><?php print bkntc__('User password')?></label>
						<input type="text" class="form-control" id="input_wp_user_password" placeholder="*****">
					</div>
				</div>
			<?php endif; ?>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_image"><?php print bkntc__('Image')?></label>
					<input type="file" class="form-control" id="input_image">
					<div class="form-control" data-label="<?php print bkntc__('BROWSE')?>"><?php print bkntc__('PNG, JPG, max 800x800 to 5mb)')?></div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="input_gender"><?php print bkntc__('Gender')?></label>
					<select id="input_gender" class="form-control">
						<option value="male"<?php print ($parameters['customer']['gender'] == 'male' ? ' selected' : '')?>><?php print bkntc__('Male')?></option>
						<option value="female"<?php print ($parameters['customer']['gender'] == 'female' ? ' selected' : '')?>><?php print bkntc__('Female')?></option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label for="input_birthday"><?php print bkntc__('Date of birth')?></label>
					<div class="inner-addon left-addon">
						<i><img src="<?php print Helper::icon('calendar.svg')?>"/></i>
						<input type="text" class="form-control" id="input_birthday" value="<?php print ( empty($parameters['customer']['birthdate']) ? '' : Date::dateSQL( $parameters['customer']['birthdate'] ) )?>">
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_note"><?php print bkntc__('Note')?></label>
					<textarea id="input_note" class="form-control"><?php print htmlspecialchars($parameters['customer']['notes'])?></textarea>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addCustomerSave"><?php print $parameters['customer'] ? bkntc__('SAVE') : bkntc__('ADD CUSTOMER')?></button>
</div>
