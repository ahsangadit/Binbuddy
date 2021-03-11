<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

foreach ( $parameters['appearances'] AS $appearance )
{
	$cssFile = \BookneticApp\Backend\Appearance\Helpers\Theme::getThemeCss( $appearance['id'] );
	print '<link rel="stylesheet" href="' . str_replace(['http://', 'https://'], '//', $cssFile) . '?_r='.rand(0,10000).'" type="text/css">' . "\n";
}
?>

<link rel="stylesheet" href="<?php print Helper::assets('css/index.css', 'Appearance')?>" type='text/css'>
<link rel="stylesheet" href="<?php print Helper::assets('css/booknetic.light.css', 'Appearance')?>" type='text/css'>
<script type="application/javascript" src="<?php print Helper::assets('js/index.js', 'Appearance')?>"></script>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print bkntc__('Appearance')?> <span class="badge badge-warning row_count"><?php print count($parameters['appearances'])?></span></div>
	<div class="m_head_actions float-right">
		
	</div>
</div>

<div class="appearance_area">
	<div class="row">

		<?php
		foreach ( $parameters['appearances'] AS $appearance )
		{
			?>
			<div class="col-md-4">
				<div class="appearance_box<?php print $appearance['is_default']?' appearance_box_active':''?>" data-id="<?php print (int)$appearance['id']?>">
					<a href="?page=booknetic&module=appearance&action=edit&id=<?php print $appearance['id']?>" class="appearance_box_preview">

						<div class="booknetic_appointment" id="booknetic_theme_<?php print $appearance['id']?>">
							<div class="booknetic_appointment_steps">
								<div class="booknetic_appointment_steps_body">
									<div class="booknetic_appointment_step_element booknetic_selected_step"><span class="booknetic_badge">1</span> <span class="booknetic_step_title"> <?php print bkntc__('Location')?></span></div>
									<div class="booknetic_appointment_step_element booknetic_selected_step"><span class="booknetic_badge">2</span> <span class="booknetic_step_title"> <?php print bkntc__('Staff')?></span></div>
									<div class="booknetic_appointment_step_element booknetic_active_step"><span class="booknetic_badge">3</span> <span class="booknetic_step_title"> <?php print bkntc__('Service')?></span></div>
									<div class="booknetic_appointment_step_element"><span class="booknetic_badge">4</span> <span class="booknetic_step_title"> <?php print bkntc__('Service Extras')?></span></div>
									<div class="booknetic_appointment_step_element"><span class="booknetic_badge">5</span> <span class="booknetic_step_title"> <?php print bkntc__('Date & Time')?></span></div>
									<div class="booknetic_appointment_step_element"><span class="booknetic_badge">6</span> <span class="booknetic_step_title"> <?php print bkntc__('Information')?></span></div>
									<div class="booknetic_appointment_step_element"><span class="booknetic_badge">7</span> <span class="booknetic_step_title"> <?php print bkntc__('Confirmation')?></span></div>
								</div>
								<div class="booknetic_appointment_steps_footer">
									<div class="booknetic_appointment_steps_footer_txt1"><?php print Helper::getOption('company_phone', '') == '' ? '' : bkntc__('Have any questions?')?></div>
									<div class="booknetic_appointment_steps_footer_txt2"><?php print Helper::getOption('company_phone', '')?></div>
								</div>
							</div>
							<div class="booknetic_appointment_container">

								<div class="booknetic_appointment_container_header"><?php print bkntc__('Select service')?></div>
								<div class="booknetic_appointment_container_body">

									<div data-step-id="service">

										<div class="booknetic_service_category"><?php print bkntc__('Category 1')?></div>

										<div class="booknetic_service_card">
											<div class="booknetic_service_card_image">
												<img src="<?php print Helper::profileImage('', 'Services')?>">
											</div>
											<div class="booknetic_service_card_title">
												<span><?php print bkntc__('Service 1')?></span>
												<span>1h</span>
											</div>
											<div class="booknetic_service_card_description"><?php print bkntc__('Lorem ipsum dolor sit amet, consectetur adipiscing elit...')?></div>
											<div class="booknetic_service_card_price">$150.0</div>
										</div>


										<div class="booknetic_service_card booknetic_service_card_selected">
											<div class="booknetic_service_card_image">
												<img src="<?php print Helper::profileImage('', 'Services')?>">
											</div>
											<div class="booknetic_service_card_title">
												<span><?php print bkntc__('Service 2')?></span>
												<span>1h</span>
											</div>
											<div class="booknetic_service_card_description"><?php print bkntc__('Lorem ipsum dolor sit amet, consectetur adipiscing elit...')?></div>
											<div class="booknetic_service_card_price">$50.0</div>
										</div>

										<div class="booknetic_service_category"><?php print bkntc__('Category 2')?></div>
										<div class="booknetic_service_card">
											<div class="booknetic_service_card_image">
												<img src="<?php print Helper::profileImage('', 'Services')?>">
											</div>
											<div class="booknetic_service_card_title">
												<span><?php print bkntc__('Service 3')?></span>
												<span>1h</span>
											</div>
											<div class="booknetic_service_card_description"><?php print bkntc__('Lorem ipsum dolor sit amet, consectetur adipiscing elit...')?></div>
											<div class="booknetic_service_card_price">$40.0</div>
										</div>

									</div>

								</div>
								<div class="booknetic_appointment_container_footer">
									<button type="button" class="booknetic_btn_secondary booknetic_prev_step"><?php print bkntc__('BACK')?></button>
									<button type="button" class="booknetic_btn_primary booknetic_next_step"><span><?php print bkntc__('NEXT STEP')?></span></button>
								</div>
							</div>
						</div>

					</a>
					<div class="appearance_box_footer">
						<a href="?page=booknetic&module=appearance&action=edit&id=<?php print $appearance['id']?>" class="appearance_box_name"><?php print esc_html( $appearance['name'] ) ?></a>
						<?php
						if( $appearance['is_default'] )
						{
						?>
						<button class="btn btn-primary appearance_box_choose_btn" data-label-true="<?php print bkntc__('SELECTED')?>" data-label-false="<?php print bkntc__('SELECT')?>"><?php print bkntc__('SELECTED')?></button>
						<?php
						}
						else
						{
						?>
						<button class="btn btn-outline-secondary appearance_box_choose_btn" data-label-true="<?php print bkntc__('SELECTED')?>" data-label-false="<?php print bkntc__('SELECT')?>"><?php print bkntc__('SELECT')?></button>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>

		<div class="col-md-4">
			<a href="?page=booknetic&module=appearance&action=edit&id=0" class="appearance_add_new">
				<div class="dashed-border appearance_add_new_contetn">
					<img src="<?php print Helper::icon('add-employee.svg')?>">
					<div><?php print bkntc__('Create new style')?></div>
				</div>
			</a>
		</div>

	</div>
</div>
