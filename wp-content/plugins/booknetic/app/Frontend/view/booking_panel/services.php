<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

if( count( $parameters['services'] ) == 0 )
{
	print '<div class="booknetic_empty_box"><img src="' . Helper::assets('images/empty-service.svg', 'front-end') . '"><span>' . bkntc__('Service not found. Please go back and select a different option.') . '</div>';
}

$lastCategoryPrinted = null;
foreach ( $parameters['services'] AS $eq => $serviceInf )
{
	if( $lastCategoryPrinted != $serviceInf['category_id'] )
	{
		print '<div class="booknetic_service_category booknetic_fade">' . esc_html( $serviceInf['category_name'] ) . '</div>';
		$lastCategoryPrinted = $serviceInf['category_id'];
	}
	?>

	<div class="booknetic_service_card booknetic_fade" data-id="<?php print $serviceInf['id']?>" data-is-recurring="<?php print (int)$serviceInf['is_recurring']?>" data-has-extras="<?php print $serviceInf['extras_count']>0?'true':'false'?>">
		<div class="booknetic_service_card_image">
			<img src="<?php print Helper::profileImage($serviceInf['image'], 'Services')?>">
		</div>
		<div class="booknetic_service_card_title">
			<span><?php print esc_html($serviceInf['name'])?></span>
			<span<?php print $serviceInf['hide_duration']==1 ? ' class="booknetic_hidden"' : ''?>><?php print Helper::secFormat($serviceInf['duration']*60)?></span>
		</div>
		<div class="booknetic_service_card_description">
			<?php print esc_html(Helper::cutText( $serviceInf['notes'], 65 ))?>
		</div>
		<div class="booknetic_service_card_price<?php print $serviceInf['hide_price']==1 ? ' booknetic_hidden' : ''?>">
			<?php print Helper::price( $serviceInf['real_price'] == -1 ? $serviceInf['price'] : $serviceInf['real_price'] )?>
		</div>
	</div>

	<?php
}
