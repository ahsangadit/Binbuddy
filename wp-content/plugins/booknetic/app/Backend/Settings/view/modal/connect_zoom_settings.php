<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Integrations\Zoom\ZoomService;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$zoomData = Helper::getOption('zoom_user_data', []);
?>
<div id="booknetic_settings_area">
	<script type="application/javascript" src="<?php print Helper::assets('js/connect_zoom_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">&nbsp;</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Integration with Zoom')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row">
					<div class="form-group col-md-12">

						<button type="button" id="connect_zoom" class="btn btn-primary btn-lg<?php print (!empty( $zoomData )? ' hidden' : '')?>"><?php print bkntc__('CLICK TO CONNECT ZOOM ACCOUNT')?></button>

						<?php if( !empty( $zoomData ) ):?>
							<div id="disconnect_zoom_area">
								<div class="alert alert-success"><?php print bkntc__('Zoom account ( %s ) has been connected successfully.', [ esc_html( $zoomData['user_email'] ) ])?></div>
								<button type="button" class="btn btn-danger btn-lg" id="disconnect_zoom"><?php print bkntc__('DISCONNECT')?></button>
							</div>
						<?php endif;?>

					</div>
				</div>

			</form>

		</div>
	</div>
</div>