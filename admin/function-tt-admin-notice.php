<?php 
/**
 * Function Time_Tracker_Admin_Notice
 *
 * Admin dashboard notices
 * 
 * @since 2.4
 * 
 */

function tt_feedback_request() {
	$msg = "<div class='notice notice-info is-dismissable tt-admin-notice'>";
    $msg .= "<p><h3>Thank you for trying the Time Tracker plugin.</h3>";
	$msg .= "We'd love to hear your feedback. ";
    $msg .= "Feel free to reach out directly with issues or recommendations at ";
    $msg .= "<a href='mailto:info@logicallytech.com'>info@logicallytech.com</a>. ";
    $msg .= "If you're enjoying the plugin and could take a few moments to leave a review, ";
    $msg .= "it would be greatly appreciated.<br/>";
    $msg .= "<button href='https://wordpress.org/support/plugin/time-tracker/reviews/#new-post' ";
	$msg .= "style='padding: 5px 15px; margin-top:15px;'>";
    $msg .= "Leave a Review</button>";
	$msg .= tt_dismiss_notice_button("tt_feedback_request", 6);
    $msg .= "</p></div>";
    return $msg;    
}

function tt_dismiss_notice_button($notice, $mnths) {
	$btn = "<button onclick='tt_dismiss_admin_notice(" . $notice . ", " . $mnths . ")' ";
	$btn .= "style='padding: 5px 15px; margin-left:15px; margin-top:15px;'>";
	$btn .= "Dismiss for " . $mnths . " Months</button>";
	return $btn;
}


function tt_dashboard_notice() {
    $notices = array('tt_feedback_request');
    $timers = get_option('time_tracker_admin_notices');
    foreach ($notices as $notice) {
	    if (! array_key_exists($notice, $timers)) {
		 echo call_user_func($notice);
	    } elseif ( (array_key_exists($notice, $timers)) and (($timers[$notice] == null) or ($timers[$notice] < new \DateTime())) ) {
	    	echo call_user_func($notice);
	    }
    }
}

add_action( 'admin_notices', 'tt_dashboard_notice' );
