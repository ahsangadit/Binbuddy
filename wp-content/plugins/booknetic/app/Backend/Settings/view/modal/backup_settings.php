<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/backup_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/backup_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right" id="export_data_btn"><i class="fa fa-cloud-download-alt pr-2"></i> <?php print bkntc__('EXPORT DATA')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title"><?php print bkntc__('Export & Import data')?></div>
		<div class="ms-content">

			<div class="row mb-4 mt-3">
				<div class="col-md-4">
					<input type="file" class="form-control" accept=".Booknetic" id="file_to_restore">
					<div class="form-control" data-label="<?php print bkntc__('BROWSE')?>"><?php print bkntc__('Select file to restore')?></div>
				</div>
				<div class="col-md-3">
					<button type="button" class="btn btn-lg btn-primary" id="import_data_btn"><?php print bkntc__('IMPORT DATA')?></button>
				</div>
			</div>

		</div>
	</div>

</div>