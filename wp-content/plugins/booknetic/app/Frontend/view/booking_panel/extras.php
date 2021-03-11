<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

if( empty( $parameters['extras'] ) )
{
	print '<div class="booknetic_empty_box"><img src="' . Helper::assets('images/empty-extras.svg', 'front-end') . '"><span>' . bkntc__('Extras not found in this service. You can select other service or click the <span class="booknetic_text_primary">"Next step"</span> button.' , [] , false) . '</span></div>';
}
else
{
	print '<div class="booknetic_service_extra_title booknetic_fade">' . $parameters['service_name'] . '</div>';

	foreach ( $parameters['extras'] AS $eq => $extraInf )
	{
		?>
		<div class="booknetic_service_extra_card booknetic_fade<?php print $extraInf['max_quantity'] == 1 ? ' booknetic_extra_on_off_mode' : ''?>" data-id="<?php print (int)$extraInf['id']?>">
			<div class="booknetic_service_extra_card_image">
				<img src="<?php print Helper::profileImage($extraInf['image'], 'Services')?>">
			</div>
			<div class="booknetic_service_extra_card_title">
				<span><?php print esc_html($extraInf['name'])?></span>
				<span><?php print $extraInf['duration'] && $extraInf['hide_duration'] != 1 ? Helper::secFormat($extraInf['duration']*60) : ''?></span>
			</div>
			<div class="booknetic_service_extra_card_price">
				<?php print $extraInf['hide_price'] != 1 ? Helper::price( $extraInf['price'] ) : ''?>
			</div>
			<div class="booknetic_service_extra_quantity<?php print $extraInf['max_quantity'] == 1 ? ' booknetic_hidden' : ''?>">
				<div class="booknetic_service_extra_quantity_dec">-</div>
				<input type="text" class="booknetic_service_extra_quantity_input" value="0" data-max-quantity="<?php print (int)$extraInf['max_quantity']?>">
				<div class="booknetic_service_extra_quantity_inc">+</div>
			</div>
		</div>
		<?php
	}
}
