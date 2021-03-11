<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<script type="application/javascript" src="<?php print Helper::assets('js/send_test_message.js', 'Whatsappnotifications')?>" id="add_new_JS" data-mn="<?php print $_mn?>" data-notification-id="<?php print $parameters['id']?>"></script>

<div class="modal-header">
	<h5 class="modal-title"><?php print bkntc__('Send test message')?></h5>
	<span data-dismiss="modal" class="p-1 cursor-pointer"><i class="fa fa-times"></i></span>
</div>

<div class="modal-body">
	<div class="form-group mt-3">
		<label><?php print bkntc__('Phone number')?></label>
		<input type="text" class="form-control" placeholder="<?php print bkntc__('Phone number')?>" id="mdl_phone_number_input">
	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="sendMessageBtn"><?php print bkntc__('SEND')?></button>
</div>
