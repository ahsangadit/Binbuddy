<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

if( !count( $parameters['customers'] ) || !count( $parameters['extras'] ) )
{
	?>
	<div class="text-secondary font-size-14 text-center"><?php print bkntc__('Extras not found!')?></div>
	<?php
}
else
{
	foreach ( $parameters['customers'] AS $customerInf )
	{
		?>
		<div class="customer-fields-area dashed-border pb-3" data-customer-id="<?php print (int)$customerInf['id']?>">

			<?php
			print Helper::profileCard( $customerInf['first_name'].' '.$customerInf['last_name'] , $customerInf['profile_image'], $customerInf['email'], 'Customers' );

			foreach ( $parameters['extras'] AS $extraInf )
			{
				?>

				<div class="row mb-2" data-extra-id="<?php print (int)$extraInf['id']?>">
					<div class="col-md-4">
						<div class="form-control-plaintext"><?php print esc_html($extraInf['name'])?></div>
					</div>
					<div class="col-md-3">
						<input type="number" class="form-control extra_quantity" value="<?php print isset($parameters['appointment_extras'][ (int)$customerInf['id'].'_'.(int)$extraInf['id'] ])?$parameters['appointment_extras'][ (int)$customerInf['id'].'_'.(int)$extraInf['id'] ]:0?>">
					</div>
					<div class="col-md-5">
						<div class="form-control-plaintext help-text text-secondary">( max quantity: <?php print (int)$extraInf['max_quantity']?> )</div>
					</div>
				</div>

				<?php
			}
			?>

		</div>

		<?php
	}
}
