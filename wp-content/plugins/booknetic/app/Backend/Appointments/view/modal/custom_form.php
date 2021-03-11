<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$customFieldsAreaId = 'custom_fields_area_' . rand(100,9999);
?>

<div id="<?php print $customFieldsAreaId?>">

	<?php

	foreach ($parameters['custom_data'] AS $customerId => $customers)
	{
		?>
		<div class="customer-fields-area dashed-border" data-customer="<?php print (int)$customerId?>">

			<?php
			print Helper::profileCard( $customers['customer_info']['name'] , $customers['customer_info']['profile_image'], $customers['customer_info']['email'], 'Customers' );

			foreach ( $customers['custom_fields'] AS $custom_data )
			{
			?>

				<div class="form-row">
					<?php
					print \BookneticApp\Backend\Customforms\Helpers\FormElements::formElement( (int)$customerId, $custom_data['type'], $custom_data['label'], $custom_data['is_required'], $custom_data['help_text'], $custom_data['input_value'], $custom_data['form_input_id'], $custom_data['options'], $custom_data['input_file_name'] );
					?>
				</div>

			<?php
			}
			?>

		</div>

		<?php
	}
	if( !count( $parameters['custom_data'] ) )
	{
		?>
		<div class="text-secondary font-size-14 text-center"><?php print bkntc__('No custom data found!')?></div>
		<?php
	}

	?>

</div>

<script>

	$("#<?php print $customFieldsAreaId?> input[type='date']").attr('type', 'text').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd'
	});

	booknetic.select2Ajax( $("#<?php print $customFieldsAreaId?> .custom-input-select2"), 'Customforms.get_custom_field_choices', function(input )
	{
		var inputId = input.data('input-id');

		return {
			input_id: inputId
		}
	});

	$("#<?php print $customFieldsAreaId?>").on('click', '.remove_custom_file_btn', function()
	{
		var placeholder = $(this).data('placeholder');

		$(this).parent().text( placeholder );
	});

</script>