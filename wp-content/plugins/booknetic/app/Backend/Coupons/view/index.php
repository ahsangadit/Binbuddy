<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>
<script type="text/javascript" src="<?php print Helper::assets('js/coupons.js', 'Coupons')?>"></script>