<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();


if( count( $parameters['locations'] ) == 0 )
{
	print '<div class="booknetic_empty_box"><img src="' . Helper::assets('images/empty-service.svg', 'front-end') . '"><span>' . bkntc__('There is no any Location for select.') . '</div>';
}

foreach ( $parameters['locations'] AS $eq => $location )
{
	?>
	<div class="booknetic_card booknetic_fade" data-id="<?php print $location['id']?>">
		<div class="booknetic_card_image">
			<img src="<?php print Helper::profileImage($location['image'], 'Locations')?>">
		</div>
		<div class="booknetic_card_title">
			<div><?php print esc_html($location['name'])?></div>
			<div class="booknetic_card_description<?php print Helper::getOption('hide_address_of_location', 'off') == 'on' ? ' booknetic_hidden' : ''?>"><?php print esc_html($location['address'])?></div>
		</div>
	</div>
	<?php
}