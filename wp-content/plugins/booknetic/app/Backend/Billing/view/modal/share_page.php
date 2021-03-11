<?php
namespace BookneticApp\Backend\Billing\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets( 'css/share_page.css', 'Billing' )?>">
<script type="text/javascript" src="<?php print Helper::assets('js/add_new.js', 'Billing')?>" id="share_page_JS"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-share"></i></div>
	<div class="title-text"><?php print bkntc__('Share your page')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">

		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="input_booking_page_url"><?php print bkntc__('Your page URL')?>:</label>
				<input type="text" id="input_booking_page_url" readonly class="form-control" value="<?php print site_url() . '/' . esc_html( Permission::tenantInf()->domain )?>">
				<a href="mailto:?subject=<?php print urlencode( bkntc__( 'Schedule time with me' ) )?>&body=<?php print urlencode( bkntc__( 'You can see my real-time availability and schedule time with me at %s', [ site_url() . '/' . esc_html( Permission::tenantInf()->domain ) ] ) )?>" class="btn btn-primary mt-2"><?php print bkntc__('Send link via Email')?></a>
			</div>
		</div>

		<?php if( Helper::getOption('customer_panel_enable', 'off', false) == 'on' ):?>
		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="input_customer_cabinet_url"><?php print bkntc__('Customer panel URL')?>:</label>
				<input type="text" id="input_customer_cabinet_url" readonly class="form-control" value="<?php print Helper::customerPanelURL()?>">
			</div>
		</div>
		<?php endif;?>

		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="input_booking_enbed"><?php print bkntc__('Add to your Website')?>:</label>
				<textarea id="input_booking_enbed" readonly class="form-control"><!-- <?php print Helper::getOption('powered_by', 'Booknetic', false)?> iframe --><iframe src="<?php print site_url() . '/' . esc_html( Permission::tenantInf()->domain )?>?iframe=1" style="width:980px; height:600px;"></iframe><!-- <?php print Helper::getOption('powered_by', 'Booknetic', false)?> iframe --></textarea>
			</div>
		</div>

	</div>
</div>

<div class="fs-modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>



