<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>

<link rel="stylesheet" type="text/css" href="<?php print Helper::assets('css/appointments.css', 'Appointments')?>" />
<script src='<?php print Helper::assets('js/appointment.js', 'Appointments')?>'></script>

<div class="fs-popover" id="customers-list-popover">
	<div class="fs-popover-title">
		<span><?php print bkntc__('Customer name')?></span>
		<img src="<?php print Helper::icon('cross.svg')?>" class="close-popover-btn">
	</div>
	<div class="fs-popover-content">

	</div>
</div>
