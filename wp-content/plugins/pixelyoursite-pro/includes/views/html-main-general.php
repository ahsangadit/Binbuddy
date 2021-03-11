<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>

<!-- Pixel IDs -->
<div class="card card-static">
    <div class="card-header">
        Pixel IDs
    </div>
    <div class="card-body">

        <?php if ( Facebook()->enabled() ) : ?>

            <div class="row align-items-center mb-3">
                <div class="col-3">
                    <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/facebook-small-square.png">
                </div>
                <div class="col-7">
                    <h4 class="label">Facebook Pixel ID:</h4>
                    <?php Facebook()->render_pixel_id( 'pixel_id', 'Facebook Pixel ID' ); ?>
                    <small class="form-text">
                        <a href="https://www.pixelyoursite.com/pixelyoursite-free-version/add-your-facebook-pixel"
                           target="_blank">How to get it?</a>
                    </small>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-3">

                </div>
                <div class="col-7">
                    <h4 class="label">Conversion API:</h4>
                    <?php Facebook()->render_checkbox_input(
                                "use_server_api",
                                "Send events directly from your web server to Facebook through the Conversion API. This can help you capture more events. An access token is required to use the server-side API. <a href='https://www.pixelyoursite.com/documentation/configure-server-side-events' target='_blank'>Generate Access Token</a>"
                            ); ?>
                    <?php Facebook()->render_checkbox_input("server_event_use_ajax","Use Ajax when conversion API is enabled. Keep this option active if you use a cache");?>

                    <?php Facebook()->render_text_area_array_item("server_access_api_token","Api token") ?>
                    <small class="form-text">
                        This is an experimental feature.
                    </small>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-3"></div>
                <div class="col-7">
                    <h4 class="label">test_event_code :</h4>
                    <?php Facebook()->render_text_input_array_item("test_api_event_code","Code"); ?>
                    <small class="form-text">
                        Use this if you need to test the server-side event. <strong>Remove it after testing</strong>
                    </small>
                </div>
            </div>



            <?php if ( isSuperPackActive() ) : ?>
                <?php SuperPack\renderFacebookPixelIDs(); ?>
            <?php endif; ?>

            <hr>
        <?php endif; ?>


        <?php if ( GA()->enabled() ) : ?>

            <div class="row align-items-center mb-3">
                <div class="col-3">
                    <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/analytics-square-small.png">
                </div>
                <div class="col-7">
                    <?php GA()->render_switcher_input("use_4_version");?>
                    <h4 class="switcher-label">Enable Google Analytics 4</h4>
                    <div class="mt-1">
                        <a href="https://www.pixelyoursite.com/documentation/enable-google-analytics-4" target="_blank">Watch this help video</a>
                    </div>
                    <h4 class="label mb-3 mt-3">Google Analytics tracking ID:</h4>
                    <?php GA()->render_pixel_id( 'tracking_id', 'Google Analytics tracking ID' ); ?>
                    <small class="form-text mb-2">
                        <a href="https://www.pixelyoursite.com/documentation/add-your-google-analytics-code"
                           target="_blank">How to get it?</a>
                    </small>
                    <input type="checkbox" class="custom-control-input" name="pys[ga][is_enable_debug_mode][-1]" value="0" checked />
                    <?php GA()->render_checkbox_input_array("is_enable_debug_mode","Enable Analytics Debug mode for this property");?>

                </div>
            </div>

            <?php if ( isSuperPackActive() ) : ?>
                <?php SuperPack\renderGoogleAnalyticsPixelIDs(); ?>
            <?php endif; ?>

            <hr>

        <?php endif; ?>

	    <?php if ( Ads()->enabled() ) : ?>

            <div class="row align-items-center mb-3">
                <div class="col-3">
                    <img class="tag-logo" src="<?php echo PYS_URL; ?>/dist/images/google-ads-square-small.png">
                </div>
                <div class="col-7">
                    <h4 class="label">Google Ads Tag:</h4>
                    <?php Ads()->render_pixel_id( 'ads_ids', 'AW-123456789' ); ?>
                    <small class="form-text">
                        <a href="https://www.pixelyoursite.com/documentation/google-ads-tag" target="_blank">How to get
                            it?</a>
                    </small>
                </div>
            </div>

            <?php if ( isSuperPackActive() ) : ?>
                <?php SuperPack\renderGoogleAdsIDs(); ?>
            <?php endif; ?>

            <hr>

	    <?php endif; ?>

        <?php do_action( 'pys_admin_pixel_ids' ); ?>

    </div>
</div>

<div class="card">
    <div class="card-header">
        Active Events:
    </div>
    <div class="card-body show" style="display: block;">
        <?php
            $customCount = EventsCustom()->getCount();
            //$customFdp = EventsFdp()->getCount();
            $signalEvents = EventsSignal()->getCount();
            $wooEvents = EventsWoo()->getCount();
            $eddEvents = EventsEdd()->getCount();

            if($signalEvents > 0) {
                $signalEvents = 1;
            }

            if(PYS()->getOption('search_event_enabled')) {
                $signalEvents++;
            }

        $total = $customCount + $signalEvents + $wooEvents + $eddEvents;
        ?>
        <p><strong>You have <?=$total?> active events in total.</strong></p>
        <p>You have <?=$signalEvents?> global active events. You can control them on this page.</p>
        <p>You have <?=$customCount?> manually added active events. You can control them on the <a href="<?=buildAdminUrl( 'pixelyoursite', 'events' )?>">Events page</a>.</p>
        <?php if(isWooCommerceActive()) : ?>
            <p>You have <?=$wooEvents?> WooCommerce active events. You can control them on the <a href="<?=buildAdminUrl( 'pixelyoursite', 'woo' )?>">WooCommerce page</a>.</p>
        <?php endif; ?>
        <?php if(isEddActive()) : ?>
            <p>You have <?=$eddEvents?> EDD active events. You can control them on the <a href="<?=buildAdminUrl( 'pixelyoursite', 'edd' )?>">EDD page</a>.</p>
        <?php endif; ?>
        <p class="mt-5 small">We count each manually added event, regardless of its name or targeted tag.</p>
        <p class="small">We don't count the Dynamic Ads for Blog events.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        About Parameters:
    </div>
    <div class="card-body show" style="display: block;">
        <p>The plugin tracks the following parameters by default for all the events and for all installed tags:
        <i>page_title, post_type, post_id, event_URL, user_role, plugin, event_time (pro), event_day (pro),
            event_month (pro), traffic_source (pro), UTMs (pro).</i></p>
        <p>Facebook, Pinterest, and Google Ads Page View event also tracks the following parameters: tags,
            category.</p>
        <p>You can add extra parameters to events configured on the Events tab.
        WooCommerce or Easy Digital Downloads events will have the e-commerce parameters specific to
            each tag.</p>
        <p>The Search event has the specific search parameter.</p>
        <p>The Signal event has various specific parameters, depending on the action that fires the event.</p>
    </div>
</div>
<!-- Signal Events -->
<div class="card">
    <div class="card-header has_switch">
        <?php PYS()->render_switcher_input('signal_events_enabled');?>Track key actions with the Signal event <?php cardCollapseBtn(); ?>
    </div>
    <div class="card-body show" style="display: block;">
        <?php if ( Facebook()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Facebook()->render_switcher_input( 'signal_events_enabled' ); ?>
                    <h4 class="switcher-label">Enable on Facebook</h4>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( GA()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php GA()->render_switcher_input( 'signal_events_enabled' ); ?>
                    <h4 class="switcher-label">Enable on Google Analytics</h4>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col col-offset-left">
                    <?php GA()->render_checkbox_input( 'signal_events_non_interactive',
                        'Non-interactive event' ); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( Ads()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Ads()->render_switcher_input( 'signal_events_enabled' ); ?>
                    <h4 class="switcher-label">Enable on Google Ads</h4>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( Pinterest()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Pinterest()->render_switcher_input( 'signal_events_enabled' ); ?>
                    <h4 class="switcher-label">Enable on Pinterest</h4>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( Bing()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Bing()->render_switcher_input( 'signal_events_enabled' ); ?>
                    <h4 class="switcher-label">Enable on Bing</h4>
                </div>
            </div>
        <?php endif; ?>

        <hr class="mb-2"/>

        <h4 class="label">Actions:</h4>
        <div class="row mt-4">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_click_enabled' ,"Internal Clicks/External Clicks"); ?>
                <div  class="col-offset-left">
                    <small> Specific parameters: <i>text, target_url</i></small>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_watch_video_enabled','Watch Video (YouTube and Vimeo embedded videos)' ); ?>
                <div  class="col-offset-left">
                    <small> Specific parameters: <i> video_type, video_title, video_id</i></small>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_tel_enabled',"Telephone links clicks" ); ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_email_enabled' ,"Email links clicks"); ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_form_enabled',"Forms" ); ?>
                <div  class="col-offset-left">
                    <small> Specific parameters: <i>text, from_class, form_id</i></small>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_user_signup_enabled',"User signups" ); ?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_download_enabled' ,"Downloads"); ?>
                <div  class="col-offset-left">
                    <h4 class="label">Extension of files to track as downloads:</h4>
                    <?php PYS()->render_tags_select_input( 'download_event_extensions' ); ?>
                    <small> Specific parameters: <i>download_type, download_name, download_url</i></small>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_comment_enabled',"Comments" ); ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <?php PYS()->render_checkbox_input( 'signal_adsense_enabled',"AdSense click" ); ?>
                <div  class="col-offset-left">
                    <small> Is not fired for Google, because Google has it's own support for AdSense</small>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">

                <div  class=" form-inline">
                    <?php PYS()->render_checkbox_input( 'signal_page_scroll_enabled',"trigger for scroll value:" ); ?>
                    <?php PYS()->render_number_input( 'signal_page_scroll_value',null,false,100 ); ?>
                    <div>% (add %)</div>
                </div>

            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <div  class="form-inline">
                    <?php PYS()->render_checkbox_input( 'signal_time_on_page_enabled',"trigger for time on page value:" ); ?>
                    <?php PYS()->render_number_input( 'signal_time_on_page_value' ); ?>
                    <div> seconds (add seconds)</div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Search -->
<div class="card">
    <div class="card-header has_switch">
        <?php PYS()->render_switcher_input('search_event_enabled');?> Track Searches <?php cardCollapseBtn(); ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-11">
                <p>This event will be fired when a search is performed on your website.</p>
            </div>
            <div class="col-1">
			    <?php renderPopoverButton( 'search_event' ); ?>
            </div>
        </div>

	    <?php if ( Facebook()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Facebook()->render_switcher_input( 'search_event_enabled' ); ?>
                    <h4 class="switcher-label">Enable the Search event on Facebook</h4>
                </div>
            </div>
        <?php endif; ?>

	    <?php if ( GA()->enabled() ) : ?>
            <div class="row mb-1">
                <div class="col">
                    <?php GA()->render_switcher_input( 'search_event_enabled' ); ?>
                    <h4 class="switcher-label">Enable the search event on Google Analytics</h4>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col col-offset-left">
				    <?php GA()->render_checkbox_input( 'search_event_non_interactive',
					    'Non-interactive event' ); ?>
                </div>
            </div>
	    <?php endif; ?>



	    <?php if ( Pinterest()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Pinterest()->render_switcher_input( 'search_event_enabled' ); ?>
                    <h4 class="switcher-label">Enable the Search event on Pinterest</h4>
	                <?php Pinterest()->renderAddonNotice(); ?>
                </div>
            </div>
	    <?php endif; ?>

        <?php if ( Bing()->enabled() ) : ?>
            <div class="row">
                <div class="col">
                    <?php Bing()->render_switcher_input( 'search_event_enabled' ); ?>
                    <h4 class="switcher-label">Enable the Search event on Bing</h4>
                    <?php Bing()->renderAddonNotice(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" >
    <div class="card-header has_switch" style="background-color:#cd6c46;color:white;">
        <?php PYS()->render_switcher_input('fdp_enabled');?> Dynamic Ads for Blog Setup <?php cardCollapseBtn(); ?>
    </div>
    <div class="card-body">
        <div class="row mt-3">
            <div class="col-11">
                This setup will help you to run Facebook Dynamic Product Ads for your blog content.
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <a href="https://www.pixelyoursite.com/facebook-dynamic-product-ads-for-wordpress" target="_blank">Click here to learn how to do it</a>
            </div>
        </div>
        <?php if ( Facebook()->enabled() ) : ?>
            <hr/>
            <div class="row mt-3">
                <div class="col">
                    <?php Facebook()->render_switcher_input( 'fdp_use_own_pixel_id' ); ?>
                    <h4 class="switcher-label">Fire this events just for this Pixel ID. Remember to connect this Pixel ID to your <a href="https://www.pixelyoursite.com/wordpress-feed-for-facebook-dynamic-ads" target="_blank">blog related Product Catalog</a></h4>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <label>Facebook Pixel ID:</label>
                    <?php Facebook()->render_text_input( 'fdp_pixel_id' ); ?>
                </div>
            </div>



            <hr/>

            <div class="row mt-5">
                <div class="col">
                    <label>Content type:</label><?php
                    $options = array(
                        'product'    => 'Product',
                        ''           => 'Empty'
                    );
                    Facebook()->render_select_input( 'fdp_content_type',$options ); ?>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <label>Currency:</label><?php
                    $options = array();
                    $cur = getPysCurrencySymbols();
                    foreach ($cur as  $key => $val) {
                        $options[$key]=$key;
                    }
                    Facebook()->render_select_input( 'fdp_currency',$options ); ?>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <?php Facebook()->render_switcher_input( 'fdp_view_content_enabled' ); ?>
                    <h4 class="switcher-label">Enable the ViewContent on every blog page</h4>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <?php Facebook()->render_switcher_input( 'fdp_view_category_enabled' ); ?>
                    <h4 class="switcher-label">Enable the ViewCategory on every blog categories page</h4>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-11">
                    <?php Facebook()->render_switcher_input( 'fdp_add_to_cart_enabled' ); ?>
                    <h4 class="switcher-label">Enable the AddToCart event on every blog page</h4>
                </div>

                <div class="col-11 form-inline col-offset-left">
                    <label>Value:</label>
                    <?php Facebook()->render_number_input( 'fdp_add_to_cart_value',"Value" ); ?>
                </div>

                <div class="col-11 form-inline col-offset-left">
                    <label>Fire the AddToCart event</label>

                    <?php
                    $options = array(
                        'scroll_pos'    => 'Page Scroll',
                        'comment'       => 'User commented',
                        'css_click'     => 'Click on CSS selector',
                        'ad_sense_click'=> 'AdSense Clicks',
                        'video_play'    => 'YouTube or Vimeo Play',
                        //Default event fires
                    );
                    Facebook()->render_select_input( 'fdp_add_to_cart_event_fire',$options ); ?>
                    <span id="fdp_add_to_cart_event_fire_scroll_block">
                        <?php Facebook()->render_number_input( 'fdp_add_to_cart_event_fire_scroll',50 ); ?> <span>%</span>
                    </span>

                    <?php Facebook()->render_text_input( 'fdp_add_to_cart_event_fire_css',"CSS selector"); ?>

                </div>
            </div>
            <div class="row mt-3">
                <div class="col-11">
                    <?php Facebook()->render_switcher_input( 'fdp_purchase_enabled' ); ?>
                    <h4 class="switcher-label">Enable the Purchase event on every blog page</h4>
                </div>
                <div class="col-11 form-inline col-offset-left">
                    <label>Value:</label>
                    <?php Facebook()->render_number_input( 'fdp_purchase_value',"Value" ); ?>
                </div>
                <div class="col-11 form-inline col-offset-left">
                    <label>Fire the Purchase event</label>

                    <?php
                    $options = array(
                        'scroll_pos'    => 'Page Scroll',
                        'comment'       => 'User commented',
                        'css_click'     => 'Click on CSS selector',
                        'ad_sense_click'=> 'AdSense Clicks',
                        'video_play'    => 'YouTube or Vimeo Play',
                        //Default event fires
                    );
                    Facebook()->render_select_input( 'fdp_purchase_event_fire',$options ); ?>
                    <span id="fdp_purchase_event_fire_scroll_block">
                        <?php Facebook()->render_number_input( 'fdp_purchase_event_fire_scroll',50 ); ?> <span>%</span>
                    </span>

                    <?php Facebook()->render_text_input( 'fdp_purchase_event_fire_css',"CSS selector"); ?>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col">
                    <strong>You need to upload your blog posts into a Facebook Product Catalog.</strong> You can do this with our dedicated plugin:
                    <a href="https://www.pixelyoursite.com/wordpress-feed-facebook-dpa" target="_blank">Click Here</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<h2 class="section-title mt-3">Global Settings</h2>

<div class="panel">
    <div class="row">
        <div class="col">
			<?php PYS()->render_switcher_input( 'debug_enabled' ); ?>
            <h4 class="switcher-label">Debugging Mode. You will be able to see details about the events inside
                your browser console (developer tools).</h4>
        </div>
    </div>
    <div class="row">
        <div class="col">
			<?php PYS()->render_switcher_input( 'track_utms' ); ?>
            <h4 class="switcher-label">Track UTMs</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
			<?php PYS()->render_switcher_input( 'track_traffic_source' ); ?>
            <h4 class="switcher-label">Track traffic source</h4>
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <h4 class="label">Ignore these user roles from tracking:</h4>
			<?php PYS()->render_multi_select_input( 'do_not_track_user_roles', getAvailableUserRoles() ); ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h4 class="label">Permissions:</h4>
			<?php PYS()->render_multi_select_input( 'admin_permissions', getAvailableUserRoles() ); ?>
        </div>
    </div>
</div>

<hr>
<div class="row justify-content-center">
    <div class="col-4">
        <button class="btn btn-block btn-sm btn-save">Save Settings</button>
    </div>
</div>
