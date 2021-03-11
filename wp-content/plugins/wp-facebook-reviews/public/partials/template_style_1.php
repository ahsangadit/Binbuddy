<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Review_Pro
 * @subpackage WP_Review_Pro/public/partials
 */
 //html code for the template style
$plugin_dir = WP_PLUGIN_DIR;
$imgs_url = esc_url( plugins_url( 'imgs/', __FILE__ ) );

//loop if more than one row
for ($x = 0; $x < count($rowarray); $x++) {
	if(	$currentform[0]->template_type=="widget"){
		?>
		<div class="wprevpro_t1_outer_div_widget w3_wprs-row-padding-small wprevprodiv">
		<?php
		} else {
		?>
		<div class="wprevpro_t1_outer_div w3_wprs-row-padding wprevprodiv">
		<?php
	}
	//loop 
	foreach ( $rowarray[$x] as $review ) 
	{
			if($review->userpiclocal!=""){
				$userpic = $review->userpiclocal;
			} else {
				$userpic = $review->userpic;
			}

		
		//star number
		if($review->type=="Yelp"){
			//find business url
			$options = get_option('wprevpro_yelp_settings');
			$burl = $options['yelp_business_url'];
			if($burl==""){
				$burl="https://www.yelp.com";
			}
			$starfile = "yelp_stars_".$review->rating.".png";
			$yelp_logo = '<a href="'.$burl.'" target="_blank" rel="nofollow"><img src="'.$imgs_url.'yelp_outline.png" alt="" class="wprevpro_t1_yelp_logo"></a>';
		} else {
			$starfile = "stars_".$review->rating."_yellow.png";
			
			//facebook logo
			$burl = "https://www.facebook.com/pg/".$review->pageid."/reviews/";
			$yelp_logo = '<a href="'.$burl.'" target="_blank" rel="nofollow"><img src="'.$imgs_url.'facebook_small_icon.png" alt="" class="wprevpro_t1_fb_logo"></a>';
		}
		
		
		
		//star alt tag
		$altimgtag = $review->rating.' star review';
		//star html
		$starhtml = '';
		if($review->type=="Facebook"){
		if($review->rating>0){
			$starhtml = '<img src="'.$imgs_url.$starfile.'" alt="'.$altimgtag.'" class="wprevpro_t1_star_img_file">&nbsp;&nbsp;';
		} else if($review->recommendation_type=='positive'){
			$starfile = 'positive-min.png';
			$altimgtag = 'positive review';
			$starhtml = '<img src="'.$imgs_url.$starfile.'" alt="'.$altimgtag.'" class="wprevpro_t1_rec_img_file">&nbsp;&nbsp;';
		} else if($review->recommendation_type=='negative'){
			$starfile = 'negative-min.png';
			$altimgtag = 'negative review';
			$starhtml = '<img src="'.$imgs_url.$starfile.'" alt="'.$altimgtag.'" class="wprevpro_t1_rec_img_file">&nbsp;&nbsp;';
		}
		}
		
		$reviewtext = "";
		if($review->review_text !=""){
			$reviewtext = $review->review_text;
		}

		//per a row
		if($currentform[0]->display_num>0){
			$perrow = 12/$currentform[0]->display_num;
		} else {
			$perrow = 4;
		}
		
		//if this is twitter then add hashtag and @ links, also add div to showcase likes, retweets and replies
		if($review->type=="Twitter"){
			//Convert urls to <a> links
			$reviewtext = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a rel=\"nofollow\" target=\"_blank\" href=\"$1\">$1</a>", $reviewtext);

			//Convert hashtags to twitter searches in <a> links
			$reviewtext = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a rel=\"nofollow\" target=\"_new\" target=\"_blank\" href=\"https://twitter.com/search?q=$1\">#$1</a>", $reviewtext);

			//Convert attags to twitter profiles in &lt;a&gt; links
			$reviewtext = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a rel=\"nofollow\" target=\"_blank\" href=\"https://twitter.com/$1\">@$1</a>", $reviewtext);

		}
		
		$date_format = get_option( 'date_format' );
		if(isset($date_format) && $date_format!=''){
			$datehtml = date($date_format,$review->created_time_stamp);
		} else {
			$datehtml = date("n/d/Y",$review->created_time_stamp);
		}
		
		
	?>
		<div class="wprevpro_t1_DIV_1<?php if(	$currentform[0]->template_type=="widget"){echo ' marginb10';}?> w3_wprs-col l<?php echo $perrow; ?>">
			<div class="wprevpro_t1_DIV_2 wprev_preview_bg1_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?> wprev_preview_bradius_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>">
				<p class="wprevpro_t1_P_3 wprev_preview_tcolor1_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>">
					<span class="wprevpro_star_imgs_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><?php echo $starhtml; ?></span><?php echo stripslashes($reviewtext); ?>
				</p>
				<?php echo $yelp_logo; ?>
			</div><span class="wprevpro_t1_A_8"><img wprevid="<?php echo $review->id;?>" src="<?php echo $userpic; ?>" alt="<?php echo stripslashes($review->reviewer_name); ?> Avatar" class="wprevpro_t1_IMG_4" /></span> <span class="wprevpro_t1_SPAN_5 wprev_preview_tcolor2_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><?php echo stripslashes($review->reviewer_name); ?><br/><span class="wprev_showdate_T<?php echo $currentform[0]->style; ?><?php if($iswidget){echo "_widget";} ?>"><?php echo $datehtml; ?></span> </span>
		</div>
	<?php
	}
	//end loop
	?>
	</div>
<?php
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
