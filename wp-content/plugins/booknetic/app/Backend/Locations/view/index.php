<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php print Helper::getOption('google_maps_api_key', '', false)?>" async defer></script>
<script type="text/javascript" src="<?php print Helper::assets('js/locations.js', 'Locations')?>"></script>
