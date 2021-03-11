<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

$formInputTpls = [
	'label'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formLabel('%label%', '%helptext%'),
	'text'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formText('%label%', false, '%helptext%', '%placeholder%' ),
	'textarea'	=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formTextarea('%label%', false, '%helptext%', '%placeholder%' ),
	'number'	=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formNumber('%label%', false, '%helptext%', '%placeholder%' ),
	'date'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formDate('%label%', false, '%helptext%', '%placeholder%' ),
	'time'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formTime('%label%', false, '%helptext%', '%placeholder%' ),
	'select'	=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formSelect('%label%', false, '%helptext%', '%placeholder%' ),
	'checkbox'	=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formCheckbox(0, '%label%', false, '%helptext%', -1 ),
	'radio'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formRadio(0, '%label%', false, '%helptext%', -1 ),
	'file'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formFile('%label%', false, '%helptext%', '%placeholder%' ),
	'link'		=>	\BookneticApp\Backend\Customforms\Helpers\FormElements::formLink('%label%', '%helptext%' )
];
?>

<script src="<?php print Helper::assets('js/edit.js', 'Customforms')?>" id="notifications-script"></script>
<link rel='stylesheet' href='<?php print Helper::assets('css/edit.css', 'Customforms')?>' type='text/css'>

<div class="m_header">
	<div class="row">
		<div class="m_head_title col-md-3"><?php print bkntc__('Customize')?></div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-sm-6 pl-3 pr-3 pt-2 pr-sm-0 p-md-0">
					<input type="text" class="form-control" value="<?php print htmlspecialchars( $parameters['form']['name'] )?>" placeholder="<?php print bkntc__('Form name')?>" id="input_form_name">
				</div>
				<div class="col-sm-6 pl-3 pr-3 pt-2 pl-sm-0 p-md-0">
					<select class="form-control" multiple id="input_form_services">
						<?php
						foreach( $parameters['services'] AS $service )
						{
							print '<option value="' . (int)$service['id'] . '"' . ( in_array( (string)$service['id'], explode(',', $parameters['form']['service_ids'] ) ) ? ' selected' : '' ) . '>' . htmlspecialchars( $service['name'] ) . '</option>';
						}
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="m_head_actions col-md-3 float-right">
			<button type="button" class="btn btn-lg btn-success float-right ml-1" id="save-form-btn"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE FORM')?></button>
		</div>
	</div>
</div>

<div class="fs_separator"></div>

<div class="row m-4">

	<div class="col-xl-3 col-md-6 col-lg-5 p-3 pr-md-1">
		<div class="fs_portlet">
			<div class="fs_portlet_title"><?php print bkntc__('SAVE FORM')?>Elements</div>
			<div class="fs_portlet_content p-0">

				<div class="row m-0 p-0">

					<div class="col-md-6 p-0 formbuilder_element" data-type="label">
						<img src="<?php print Helper::icon('label.svg', 'Customforms')?>">
						<span><?php print bkntc__('Label')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="text">
						<img src="<?php print Helper::icon('text.svg', 'Customforms')?>">
						<span><?php print bkntc__('Text input')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="textarea">
						<img src="<?php print Helper::icon('textarea.svg', 'Customforms')?>">
						<span><?php print bkntc__('Textarea')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="number">
						<img src="<?php print Helper::icon('number.svg', 'Customforms')?>">
						<span><?php print bkntc__('Number input')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="date">
						<img src="<?php print Helper::icon('datepicker.svg', 'Customforms')?>">
						<span><?php print bkntc__('Date input')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="time">
						<img src="<?php print Helper::icon('timepicker.svg', 'Customforms')?>">
						<span><?php print bkntc__('Time input')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="select">
						<img src="<?php print Helper::icon('select.svg', 'Customforms')?>">
						<span><?php print bkntc__('Select')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="checkbox">
						<img src="<?php print Helper::icon('checkbox.svg', 'Customforms')?>">
						<span><?php print bkntc__('Check-box')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="radio">
						<img src="<?php print Helper::icon('radio.svg', 'Customforms')?>">
						<span><?php print bkntc__('Radio buttons')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="file">
						<img src="<?php print Helper::icon('file.svg', 'Customforms')?>">
						<span><?php print bkntc__('File')?></span>
					</div>

					<div class="col-md-6 p-0 formbuilder_element" data-type="link">
						<img src="<?php print Helper::icon('link.svg', 'Customforms')?>">
						<span><?php print bkntc__('Link')?></span>
					</div>

				</div>

			</div>
		</div>
	</div>

	<div class="col-xl-6 col-md-6 col-lg-7 p-3 pr-md-3 pr-xl-1 pl-md-1">
		<div class="fs_portlet">
			<div class="fs_portlet_title"><?php print bkntc__('Form')?></div>
			<div class="fs_portlet_content" id="formbuilder_area">
				<?php
				foreach ($parameters['inputs'] AS $input)
				{
					$type = $input['type'];

					if( !isset( $formInputTpls[ $type ] ) )
						continue;

					$tpl = $formInputTpls[ $type ];

					$options = json_decode( $input['options'], true );
					$options = is_array( $options ) ? $options : [];
					$options['label'] = $input['label'];
					$options['help_text'] = $input['help_text'];
					$options['is_required'] = $input['is_required'];

					if( isset( $input['choices'] ) )
					{
						$options['choices'] = $input['choices'];
					}

					$tpl = str_replace( [
						'%label%',
						'%helptext%',
						'%placeholder%',
						'data-required="false"',
					], [
						htmlspecialchars($input['label']),
						htmlspecialchars($input['help_text']),
						isset( $options['placeholder'] ) ? htmlspecialchars( $options['placeholder'] ) : '',
						'data-required="' . ($input['is_required'] ? 'true' : 'false') . '"',
					], $tpl );

					print '<div class="form_element" data-type="' . htmlspecialchars($type) . '" data-id="' . (int)$input['id'] . '" data-options="' . htmlspecialchars( json_encode( $options ) ) . '">' . $tpl . '<img class="remove-element-btn" src="' . Helper::icon('remove.svg', 'Customforms') . '"></div>';

				}
				?>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 col-lg-5 p-3 pr-md-1 pr-xl-3 pl-xl-1">
		<div class="fs_portlet">
			<div class="fs_portlet_title"><?php print bkntc__('Options')?></div>
			<div id="formbuilder_options" class="fs_portlet_content">

				<div class="form-row hidden" data-for="label,text,textarea,number,date,time,select,checkbox,radio,file,link">
					<div class="form-group col-md-12">
						<label for="formbuilder_options_label"><?php print bkntc__('Label')?></label>
						<input type="text" class="form-control" id="formbuilder_options_label" maxlength="255" placeholder="<?php print bkntc__('Max: 255 symbol')?>">
					</div>
				</div>

				<div class="form-row hidden" data-for="text,textarea,number,date,time,file,select">
					<div class="form-group col-md-12">
						<label for="formbuilder_options_placeholder"><?php print bkntc__('Placeholder')?></label>
						<input type="text" class="form-control" id="formbuilder_options_placeholder" maxlength="200" placeholder="<?php print bkntc__('Max: 200 symbol')?>">
					</div>
				</div>

				<div class="form-row hidden" data-for="text,textarea,number">
					<div class="form-group col-md-6">
						<label for="formbuilder_options_min_length"><?php print bkntc__('Min length')?></label>
						<input type="text" class="form-control" id="formbuilder_options_min_length">
					</div>
					<div class="form-group col-md-6">
						<label for="formbuilder_options_max_length"><?php print bkntc__('Max length')?></label>
						<input type="text" class="form-control" id="formbuilder_options_max_length">
					</div>
				</div>

				<div class="form-row hidden" data-for="label,text,textarea,number,date,time,select,checkbox,radio,file,link">
					<div class="form-group col-md-12">
						<label for="formbuilder_options_help_text"><?php print bkntc__('Help text')?></label>
						<input type="text" class="form-control" id="formbuilder_options_help_text" maxlength="500" placeholder="<?php print bkntc__('Max: 500 symbol')?>">
					</div>
				</div>

				<div class="form-row hidden" data-for="file">
					<div class="form-group col-md-12">
						<label for="formbuilder_options_allowed_file_formats"><?php print bkntc__('Allowed file formats')?></label>
						<input type="text" class="form-control" id="formbuilder_options_allowed_file_formats" maxlength="500" placeholder="doc,docx,xls,xlsx,jpg,jpeg,png,gif,mp4,zip,rar,csv">
					</div>
				</div>

				<div class="form-row hidden" data-for="text,textarea,number,date,time,select,checkbox,radio,file">
					<div class="form-group col-md-12">
						<div class="form-control-plaintext">
							<input id="formbuilder_options_is_required" type="checkbox">
							<label for="formbuilder_options_is_required"><?php print bkntc__('Is required')?></label>
						</div>
					</div>
				</div>

				<div class="form-row hidden" data-for="link">
					<div class="form-group col-md-12">
						<label for="formbuilder_options_url"><?php print bkntc__('URL')?></label>
						<input type="text" class="form-control" id="formbuilder_options_url" maxlength="200" placeholder="<?php print bkntc__('Max: 200 symbol')?>">
					</div>
				</div>

				<div class="form-row hidden" data-for="select,checkbox,radio" data-choices="true">
					<div class="form-group col-md-12">
						<div class="form-control-plaintext">
							<label><?php print bkntc__('Choices')?></label>
							<div id="choices_area"></div>
							<div id="formbuilder_options_add_new_choice"><i class="fa fa-plus-circle"></i> <?php print bkntc__('Add new')?></div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

</div>

<script>

	var formInputTpls			= <?php print json_encode($formInputTpls)?>;
	var currentModuleAssetsURL	= "<?php print Helper::assets('', 'Customforms')?>";
	var currentFormID			= <?php print $parameters['id']?>;

</script>