<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>

<script type="application/javascript" src="<?php print Helper::assets('js/payments.js', 'Payments')?>"></script>


