<?php 
/**
 * Function Time_Tracker_Admin_Notice
 *
 * Admin dashboard notices
 * 
 * @since 2.4
 * @since 3.0.12 fix fatal activation error from function looking for option before it was created
 * @since 3.0.13 turned off beta tester request notice
 * 
 */

namespace Logically_Tech\Time_Tracker\Admin;

function tt_feedback_request() {
	$msg = tt_admin_notice_div_start(str_replace(__NAMESPACE__ . "\\", "", __FUNCTION__));
    $msg .= "<p><h3>Thank you for trying the Time Tracker plugin.</h3>";
	$msg .= "We'd love to hear your feedback. ";
    $msg .= "Feel free to reach out directly with issues or recommendations at ";
    $msg .= tt_email_lt_link() . ". ";
    $msg .= "If you're enjoying the plugin and could take a few moments to leave a review, ";
    $msg .= "it would be greatly appreciated.<br/>";
    $msg .= "<button onclick=\"window.location.href='https://wordpress.org/support/plugin/time-tracker/reviews/#new-post'\" ";
	$msg .= "style='padding: 5px 15px; margin-top:15px;'>";
    $msg .= "Leave a Review</button>";
	$msg .= tt_dismiss_notice_button(str_replace(__NAMESPACE__ . "\\", "", __FUNCTION__), 3);
    $msg .= "</p></div>";
    return $msg;    
}

function tt_beta_tester_search() {
    $msg = tt_admin_notice_div_start(str_replace(__NAMESPACE__ . "\\", "", __FUNCTION__));
    $msg .= "<p><h3>Time Tracker Plugin is Looking for Beta Testers!</h3>";
    $msg .= "Interested in testing the next major update to the Time Tracker plugin? ";
    $msg .= "Based on user feedback, Time Tracker was updated to integrate with EITHER Contact Forms 7 <i>OR</i> WP Forms! ";
    $msg .= "We've been testing internally but would love for others to use the updated version and let us know of any bugs or thoughts. ";
    $msg .= "If interested, please email " . tt_email_lt_link() . ".</p>";
    $msg .= tt_email_lt_button() . tt_dismiss_notice_button(str_replace(__NAMESPACE__ . "\\", "", __FUNCTION__), 1);
    $msg .= "</div>";
    return $msg;
}

function tt_email_lt_button() {
	$btn = "<button onclick=\"location.href='mailto:info@logicallytech.com';\" ";
	$btn .= "style='padding: 5px 15px; margin: 5px 15px 10px 15px;'>";
	$btn .= "Send Email</button>";
	return $btn;
}

function tt_dismiss_notice_button($notice, $mnths) {
	$btn = "<button onclick=\"dismiss_admin_notice('" . $notice . "', " . $mnths . ")\" ";
	$btn .= "style='padding: 5px 15px; margin: 5px 15px 10px 15px;'>";
	$btn .= "Dismiss for " . $mnths . " Month(s)</button>";
	return $btn;
}

function tt_admin_notice_div_start($idtext) {
    return "<div id='" . $idtext . "' class='notice notice-info is-dismissable tt-admin-notice'>";
}

function tt_email_lt_link() {
    return "<a href='mailto:info@logicallytech.com'>info@logicallytech.com</a>";
}

function tt_dashboard_notice() {
    $notices = get_option('time_tracker_admin_notices');
    if ($notices) {
        foreach ($notices as $notice=>$tm) {
            if (tt_time_to_display_notice($tm)) {
                echo call_user_func('Logically_Tech\Time_Tracker\Admin\\' . $notice);
            }
        }
    }
}

function tt_time_to_display_notice($tm) {
    if ($tm == null) {
        return true;
    }
    if (is_int($tm)) {
        if ($tm < time()) {
            return true;
        }
    }
    if (is_object($tm)) {
        if (date_timestamp_get($tm) < time()) {
            return true;
        }
    }
    return false;
}


function tt_dismiss_admin_notice_function() {
    if ($_SERVER['REQUEST_METHOD'] = 'POST'){
        if (check_ajax_referer('tt_dismiss_admin_notice_nonce', 'security')) {
            $name = isset($_POST['nm']) ? $_POST['nm'] : '';
            $months_out = isset($_POST['mnths']) ? \intval($_POST['mnths']) : 0;
            if ( ($months_out > 0) and ($name != '') ) {
                tt_update_admin_notice_timer($name, strtotime("+" . $months_out . "months", time()));
                $return = array(
                    'success' => true,
                    'msg' => 'Admin notice delayed for ' . strval($months_out) . ' months.'
                );
                wp_send_json_success($return, 200);
            } else {
                tt_update_admin_notice_timer($name, strtotime("+1month", time()));
                $return = array(
                    'success' => false,
                    'msg' => 'Error delaying admin notice, no delay time frame specified. Delayed for default of 1 month'
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

function tt_get_install_timestamp() {
    $opt = get_option('time_tracker_install_time');
    if (is_int($opt)) {
        return $opt;
    }
    if (is_object($opt)) {
        return $opt->getTimestamp();
    }
    if (is_string($opt)) {
        return strtotime($opt);
    }
}

function tt_update_admin_notice_timer($name, $nexttime) {
    if (! get_option('time_tracker_admin_notices')) {
        $val = array();
        $val[$name] = $nexttime;
        add_option('time_tracker_admin_notices', $val);
    } else {
        $notices = get_option('time_tracker_admin_notices');
        $notices[$name] = $nexttime;
        update_option('time_tracker_admin_notices', $notices);
    }
}


function tt_add_new_admin_notice_timer($name, $nexttime) {
	if (! get_option('time_tracker_admin_notices')) {
        $val = array();
        $val[$name] = $nexttime;
        add_option('time_tracker_admin_notices', $val);
    } else {
        $notices = get_option('time_tracker_admin_notices');
        if (!array_key_exists($name, $notices)) {
			$notices[$name] = $nexttime;
			update_option('time_tracker_admin_notices', $notices);
		}
    }
}

if (! get_option('time_tracker_install_time')) {
    $dt_notice = new \DateTime(date_format(new \DateTime(), 'Y-m-d H:i:s') . " + 1 month");
} else {
    $dt_notice = new \DateTime(date_format(get_option('time_tracker_install_time'), 'Y-m-d H:i:s') . " + 1 month");
}

tt_add_new_admin_notice_timer('tt_feedback_request', $dt_notice);
//tt_add_new_admin_notice_timer('tt_beta_tester_search', new \DateTime());

add_action( 'admin_notices', 'Logically_Tech\Time_Tracker\Admin\tt_dashboard_notice' );
