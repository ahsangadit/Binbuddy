<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>

<div id="booknetic_loading" class="hidden"><div><?php print bkntc__('Installing...')?></div><div><?php print bkntc__('( it can take some time, please wait... )')?></div></div>

<div id="booknetic_alert" class="hidden"></div>

<div class="booknetic_area">
	<div class="booknetic_install_panel">
		<?php
		if( isset( $hasError ) && !empty( $hasError ) )
		{
			print '<div class="booknetic_has_error">' . $hasError . '</div>';
		}
		?>

		<input type="hidden" name="_wpnonce" value="<?php print wp_create_nonce('booknetic_install')?>">
		<div class="booknetic_logo_d"><img src="<?php print Helper::assets('images/logo-black.svg')?>"></div>
		<div class="booknetic_input_d"><input type="text" placeholder="<?php print bkntc__('Purchase code')?>" name="purchase_code" id="booknetic_install_purchase_code"></div>
		<div class="booknetic_input_d">
			<select type="text" name="where_find" id="booknetic_install_where_find">
				<option disabled selected><?php print bkntc__('Where did You find us?')?></option>
				<?php
				foreach ( $select_options AS $value => $option )
				{
					print '<option value="' . esc_html( $value ) . '">' . esc_html( $option ) . '</option>';
				}
				?>
			</select>
		</div>
		<div class="booknetic_input_d"><input type="text" placeholder="<?php print bkntc__('Email | Subscribe newsletter')?>" name="email" id="booknetic_install_email"></div>
		<div class="booknetic_submit_d"><button type="button" id="booknetic_install_btn"><?php print bkntc__('INSTALL')?></button></div>
		<div class="booknetic_help_text"><?php print bkntc__('Install process can take 30-60 sec., please wait...')?></div>
	</div>
</div>