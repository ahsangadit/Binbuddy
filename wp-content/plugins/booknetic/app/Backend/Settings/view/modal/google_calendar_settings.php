<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

?>
<div id="booknetic_settings_area">
	<link rel="stylesheet" href="<?php print Helper::assets('css/google_calendar_settings.css', 'Settings')?>">
	<script type="application/javascript" src="<?php print Helper::assets('js/google_calendar_settings.js', 'Settings')?>"></script>

	<div class="actions_panel clearfix">
		<button type="button" class="btn btn-lg btn-success settings-save-btn float-right"><i class="fa fa-check pr-2"></i> <?php print bkntc__('SAVE CHANGES')?></button>
	</div>

	<div class="settings-light-portlet">
		<div class="ms-title">
			<?php print bkntc__('Google calendar')?>
		</div>
		<div class="ms-content">

			<form class="position-relative">

				<div class="form-row enable_disable_row">

					<div class="form-group col-md-2">
						<input id="input_google_calendar_enable" type="radio" name="input_google_calendar_enable" value="off"<?php print Helper::getOption('google_calendar_enable', 'off')=='off'?' checked':''?>>
						<label for="input_google_calendar_enable"><?php print bkntc__('Disabled')?></label>
					</div>
					<div class="form-group col-md-2">
						<input id="input_google_calendar_disable" type="radio" name="input_google_calendar_enable" value="on"<?php print Helper::getOption('google_calendar_enable', 'off')=='on'?' checked':''?>>
						<label for="input_google_calendar_disable"><?php print bkntc__('Enabled')?></label>
					</div>

				</div>

				<div id="google_calendar_settings_area">

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_google_calendar_redirect_uri"><?php print bkntc__('Redirect URI')?>:</label>
							<input class="form-control" id="input_google_calendar_redirect_uri" value="<?php print \BookneticApp\Integrations\GoogleCalendar\GoogleCalendarService::redirectURI() ?>" readonly>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_google_calendar_client_id"><?php print bkntc__('Client ID')?>:</label>
							<input class="form-control" id="input_google_calendar_client_id" value="<?php print htmlspecialchars( Helper::getOption('google_calendar_client_id', '') )?>">
						</div>
						<div class="form-group col-md-6">
							<label for="input_google_calendar_client_secret"><?php print bkntc__('Client Secret')?>:</label>
							<input class="form-control" id="input_google_calendar_client_secret" value="<?php print htmlspecialchars( Helper::getOption('google_calendar_client_secret', '') )?>">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_google_calendar_event_title"><?php print bkntc__('Event title')?>:</label>
							<input class="form-control" id="input_google_calendar_event_title" value="<?php print htmlspecialchars( Helper::getOption('google_calendar_event_title', '') )?>">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="input_google_calendar_event_description"><?php print bkntc__('Event description')?>:</label>
							<textarea class="form-control" id="input_google_calendar_event_description"><?php print htmlspecialchars( Helper::getOption('google_calendar_event_description', '') )?></textarea>
							<button type="button" class="btn btn-default btn-sm mt-2" data-load-modal="Settings.keywords_list" data-parameter-show_zoom_keywords="1"><?php print bkntc__('List of keywords')?> <i class="far fa-question-circle"></i></button>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="input_google_calendar_2way_sync"><?php print bkntc__('Sync method for busy slots from Google Calendar')?>: <i class="far fa-question-circle do_tooltip" data-content="<?php print bkntc__("1. Live sync;<br/>If you have a few staff, this method would be more convenient for you. When your customers are booking, the plugin will connect to the google calendar and sync busy slots in real-time.<br/>2. Background sync;<br/>For this method, first, you must configure the Cron jobs ( <a href='https://www.booknetic.com/documentation/cron-job' target='_blank'>How to?</a> ). The shorter you set the Cron jobs interval, the more accuracy you will get. This method is usually designed for businesses with a large number of employees and using the \"Any Staff\" option. Because in this case, when your customer selects Any staff option, it might take more than 30-60 seconds to sync all Staff busy slots with Google calendar. By choosing this method, the plugin Cron Jobs will connect to the Google Calendars in the background at the interval you set up and will store the busy slots of all your employees in your local databases. During booking, it will read the information directly from your database. Errors in this method are inevitable. For example, if you configure your cron jobs to run every 15 minutes, the busy slot you add to your Google calendar will be stored in the plugin's local database every 15 minutes. That is, within these 15 minutes, someone can book an appointment in that time slot. Therefore, the shorter you configure the Cron jobs, the less likely there will be errors.")?>"></i></label>
							<select class="form-control" id="input_google_calendar_2way_sync">
								<option value="on"<?php print Helper::getOption('google_calendar_2way_sync', 'off') == 'on' ? ' selected' : ''?>><?php print bkntc__('Live sync')?></option>
								<option value="on_background"<?php print Helper::getOption('google_calendar_2way_sync', 'off') == 'on_background' ? ' selected' : ''?>><?php print bkntc__('Background sync')?></option>
								<option value="off"<?php print Helper::getOption('google_calendar_2way_sync', 'off') == 'off' ? ' selected' : ''?>><?php print bkntc__('Don\'t sync busy slots')?></option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label for="input_google_calendar_sync_interval"><?php print bkntc__('Since what date do events in Google calendar sync?')?>:</label>
							<select class="form-control" id="input_google_calendar_sync_interval">
								<option value="1"<?php print Helper::getOption('google_calendar_sync_interval', '1') == '1' ? ' selected' : ''?>><?php print bkntc__('Events up to 1 month')?></option>
								<option value="2"<?php print Helper::getOption('google_calendar_sync_interval', '1') == '2' ? ' selected' : ''?>><?php print bkntc__('Events up to 2 month')?></option>
								<option value="3"<?php print Helper::getOption('google_calendar_sync_interval', '1') == '3' ? ' selected' : ''?>><?php print bkntc__('Events up to 3 month')?></option>
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_google_calendar_add_attendees"><?php print bkntc__('Add customers as attendees in your calendar events')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_google_calendar_add_attendees"<?php print Helper::getOption('google_calendar_add_attendees', 'off')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_google_calendar_add_attendees"></label>
								</div>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_google_calendar_send_notification"><?php print bkntc__('Send email invitations to attendees by Google')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_google_calendar_send_notification"<?php print Helper::getOption('google_calendar_send_notification', 'off')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_google_calendar_send_notification"></label>
								</div>
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<div class="form-control-checkbox">
								<label for="input_google_calendar_can_see_attendees"><?php print bkntc__('Customers can see other attendees')?>:</label>
								<div class="fs_onoffswitch">
									<input type="checkbox" class="fs_onoffswitch-checkbox" id="input_google_calendar_can_see_attendees"<?php print Helper::getOption('google_calendar_can_see_attendees', 'off')=='on'?' checked':''?>>
									<label class="fs_onoffswitch-label" for="input_google_calendar_can_see_attendees"></label>
								</div>
							</div>
						</div>
					</div>


				</div>

			</form>

		</div>
	</div>
</div>