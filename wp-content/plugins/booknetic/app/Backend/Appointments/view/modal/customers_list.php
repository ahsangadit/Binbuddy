<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<div class="clist-area hidden">
	<?php
	foreach( $parameters['customers'] AS $customer )
	{
		print '<div class="list_left_right_box">';
			print '<div class="list_left_box">';
			print Helper::profileCard( $customer['customer_name'], $customer['profile_image'], $customer['email'], 'Customers' );
			print '</div>';
			print '<div class="appointment-status-' . htmlspecialchars( $customer['status'] ) .'"></div>';
		print '</div>';
	}
	?>
</div>

<script>
	$(".clist-area").fadeIn(400);
</script>
