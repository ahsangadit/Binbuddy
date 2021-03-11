<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/bootstrap-year-calendar.min.css')?>">
<script type="application/javascript" src="<?php print Helper::assets('js/bootstrap-year-calendar.min.js')?>"></script>
<script type="application/javascript" src="<?php print Helper::assets('js/staff.js', 'Staff')?>" id="staff-js12394610" data-edit="<?php print $parameters['edit']?>"></script>
