<?php
namespace BookneticApp\Backend\Base\view;

use BookneticApp\Providers\Helper;

defined( 'ABSPATH' ) or die();
?>

<link rel="stylesheet" href="<?php print Helper::assets( 'css/permission_denied.css' )?>">

<div class="permission_denied_screen">
	<div class="permission_denied_title"><?php print bkntc__( 'Upgrade needed!' )?></div>
	<div class="permission_denied_subtitle"><?php print $parameters['text']?></div>
	<div class="permission_denied_image"><img src="<?php print Helper::assets('images/permission_denied.svg')?>"></div>
	<div class="permission_denied_footer">
		<?php if( !isset( $parameters['no_close_btn'] ) ):?>
			<button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"><?php print bkntc__('CLOSE')?></button>
		<?php endif;?>
		<a href="?page=booknetic&module=billing&upgrade=1" class="btn btn-primary btn-lg"><?php print bkntc__('UPGRADE NOW')?></a>
	</div>
</div>