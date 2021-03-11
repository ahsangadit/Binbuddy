<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<script type="application/javascript" src="<?php print Helper::assets('js/help_to_find_custom_field_id.js', 'Smsnotifications')?>"></script>

<div class="modal-header">
	<h5 class="modal-title"><?php print bkntc__('Find custom field ID')?></h5>
	<span data-dismiss="modal" class="p-1 cursor-pointer"><i class="fa fa-times"></i></span>
</div>

<div class="modal-body">
	<div class="form-group mt-3">
		<label><?php print bkntc__('Custom field:')?></label>
		<select class="form-control" id="custom_field_select">
			<option></option>
			<?php
			foreach ( $parameters['fields'] AS $field )
			{
				print '<option value="' . (int)$field['id'] . '" data-type="' . htmlspecialchars($field['type']) . '">' . esc_html( $field['form_name'] . ' > ' . $field['label'] . ' (ID: ' . $field['id'] . ')' ) . '</option>';
			}
			?>
		</select>
	</div>
	<div class="form-group mt-3">
		<label><?php print bkntc__('Keyword to use:')?></label>
		<div class="form-control-plaintext" id="custom_field_key_area"><i><?php print bkntc__('Fistly select the custom field')?></i></div>
	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
</div>
