<?php 
/**
 * Function Time_Tracker_Admin_Notice
 *
 * Admin dashboard notices
 * 
 * @since 2.4
 * 
 */

namespace Logically_Tech\Time_Tracker\Admin;

function tt_feedback_request() {
	$msg = "<div id='tt-admin-notice-review' class='notice notice-info is-dismissable tt-admin-notice'>";
    $msg .= "<p><h3>Thank you for trying the Time Tracker plugin.</h3>";
	$msg .= "We'd love to hear your feedback. ";
    $msg .= "Feel free to reach out directly with issues or recommendations at ";
    $msg .= "<a href='mailto:info@logicallytech.com'>info@logicallytech.com</a>. ";
    $msg .= "If you're enjoying the plugin and could take a few moments to leave a review, ";
    $msg .= "it would be greatly appreciated.<br/>";
    $msg .= "<button onclick=\"window.location.href='https://wordpress.org/support/plugin/time-tracker/reviews/#new-post'\" ";
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

function tt_dismiss_admin_notice_function($name, $months_out) {
    if ( ($_SERVER['REQUEST_METHOD'] = 'POST') && isset($_POST['type']) ){
		$type = sanitize_text_field($_POST['type']);
    	if ($type == 'confirmed') {
			if (check_ajax_referer('tt_dismiss_admin_notice_nonce', 'security')) {
                if ($months_out > 0) {
                    tt_update_admin_notice_timer($name, new \DateTime(date_format(get_option('time_tracker_install_time'), 'Y-m-d H:i:s') . " + " . $months_out + " months"));
                    $return = array(
						'success' => true,
						'msg' => 'Admin notice delayed for ' + $months_out + ' months.'
					);
					wp_send_json_success($return, 200);
                } else {
                    tt_update_admin_notice_timer($name, new \DateTime(date_format(get_option('time_tracker_install_time'), 'Y-m-d H:i:s') . " + 2 months"));
                    $return = array(
						'success' => false,
						'msg' => 'Error delaying admin notice, no delay time frame specified.'
					);
					wp_send_json_error($return, 500);
                }
            } else {
                $return = array(
                    'success' => false,
                    'msg' => 'Error delaying admin notice, security check failed.'
                );
                wp_send_json_error($return, 500);
            }
        }
    }
}

function tt_update_admin_notice_timer($name, $nexttime) {
    if (! get_option('time_tracker_admin_notices')) {
        add_option('time_tracker_admin_notices', array($name=>$nexttime));
    } else {
        $notices = get_option('time_tracker_admin_notices');
        $notices[$name] = $nexttime;
        update_option('time_tracker_admin_notices', $notices);
    }
}

function tt_add_admin_notice_timer($name, $nexttime) {
    if ( (! get_option('time_tracker_admin_notices')) or (! array_key_exists($name, get_option('time_tracker_admin_notices'))) ) {
        tt_update_admin_notice_timer($name, $nexttime);
    }
}

tt_add_admin_notice_timer('tt_feedback_request', new \DateTime(date_format(get_option('time_tracker_install_time'), 'Y-m-d H:i:s') . " + 1 month"));
add_action( 'admin_notices', 'tt_dashboard_notice' );
