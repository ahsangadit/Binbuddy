<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();


if( count( $parameters['staff'] ) == 0 )
{
	print '<div class="booknetic_empty_box"><img src="' . Helper::assets('images/empty-staff.svg', 'front-end') . '"><span>' . bkntc__('Staff not found. Please go back and select a different option.') . '</div>';
	return;
}
$footer_text_option = Helper::getOption('footer_text_staff', '1');

if( Helper::getOption('any_staff', 'off') == 'on' ) :
?>
	<div class="booknetic_card booknetic_fade" data-id="-1">
		<div class="booknetic_card_image">
			<img src="<?php print Helper::icon('any_staff.svg', 'front-end')?>">
		</div>
		<div class="booknetic_card_title">
			<div><?php print bkntc__('Any staff')?></div>
			<?php if( $footer_text_option != '4' ) :?>
				<div class="booknetic_card_description"><?php print bkntc__('Select an available staff')?></div>
			<?php endif; ?>
		</div>
	</div>
<?php
endif;

foreach ( $parameters['staff'] AS $eq => $staffInf ) :
	?>
	<div class="booknetic_card booknetic_fade" data-id="<?php print $staffInf['id']?>">
		<div class="booknetic_card_image">
			<img src="<?php print Helper::profileImage($staffInf['profile_image'], 'Staff')?>">
		</div>
		<div class="booknetic_card_title">
			<div><?php print esc_html($staffInf['name'])?></div>
			<div class="booknetic_card_description">
				<?php if( $footer_text_option == '1' || $footer_text_option == '2' ) : ?>
					<div><?php print esc_html($staffInf['email'])?></div>
				<?php endif; ?>

				<?php if( $footer_text_option == '1' || $footer_text_option == '3' ) : ?>
					<div><?php print esc_html($staffInf['phone_number'])?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
endforeach;