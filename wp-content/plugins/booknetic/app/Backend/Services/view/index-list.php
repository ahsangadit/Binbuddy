<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>
<link rel="stylesheet" type="text/css" href="<?php print Helper::assets('css/bootstrap-colorpicker.min.css', 'Services')?>" />
<script type="application/javascript" src="<?php print Helper::assets('js/bootstrap-colorpicker.min.js', 'Services')?>"></script>

<script type="text/javascript" src="<?php print Helper::assets('js/services-list.js', 'Services')?>"></script>
