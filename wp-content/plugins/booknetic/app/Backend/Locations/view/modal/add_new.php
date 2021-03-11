<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/add_new.css', 'Locations')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/add_new.js', 'Locations')?>" id="add_new_JS" data-latitude="<?php print htmlspecialchars($parameters['location']['latitude'])?>" data-longitude="<?php print htmlspecialchars($parameters['location']['longitude'])?>" data-zoom="<?php print empty($parameters['location']['latitude']) ? 1 : 8?>" data-location-id="<?php print (int)$parameters['location']['id']?>"></script>

<div class="fs-modal-title">
	<div class="title-icon badge-lg badge-purple"><i class="fa fa-plus"></i></div>
	<div class="title-text"><?php print bkntc__('Add Location')?></div>
	<div class="close-btn" data-dismiss="modal"><i class="fa fa-times"></i></div>
</div>

<div class="fs-modal-body">
	<div class="fs-modal-body-inner">
		<form id="addLocationForm">

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_location_name"><?php print bkntc__('Location Name')?> <span class="required-star">*</span></label>
					<input type="text" class="form-control" id="input_location_name" value="<?php print htmlspecialchars($parameters['location']['name'])?>">
				</div>
			</div>

			<div class="form-group">
				<label for="input_image"><?php print bkntc__('Image')?></label>
				<input type="file" class="form-control" id="input_image">
				<div class="form-control" data-label="<?php print bkntc__('BROWSE')?>"><?php print bkntc__('(PNG, JPG, max 800x800 to 5mb)')?></div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_address"><?php print bkntc__('Address')?></label>
					<input type="text" class="form-control" id="input_address" value="<?php print htmlspecialchars($parameters['location']['address'])?>">
					<div id="divmap"></div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_phone"><?php print bkntc__('Phone')?></label>
					<input type="text" class="form-control" id="input_phone" value="<?php print htmlspecialchars($parameters['location']['phone_number'])?>">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="input_note"><?php print bkntc__('Description')?></label>
					<textarea id="input_note" class="form-control"><?php print htmlspecialchars($parameters['location']['notes'])?></textarea>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="fs-modal-footer">
	<?php
	if( $parameters['location']['id'] > 0 )
	{
		?>
		<button type="button" class="btn btn-lg btn-outline-secondary" id="hideLocationBtn"><?php print $parameters['location']['is_active'] != 1 ? bkntc__('UNHIDE LOCATION') : bkntc__('HIDE LOCATION')?></button>
		<?php
	}
	?>
	<button type="button" class="btn btn-lg btn-outline-secondary" data-dismiss="modal"><?php print bkntc__('CANCEL')?></button>
	<button type="button" class="btn btn-lg btn-primary" id="addLocationSave"><?php print bkntc__('ADD LOCATION')?></button>
</div>
