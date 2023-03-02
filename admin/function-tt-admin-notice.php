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
	$msg .= tt_dismiss_notice_button("tt_feedback_request", 3);
    $msg .= "</p></div>";
    return $msg;    
}

function tt_dismiss_notice_button($notice, $mnths) {
	$btn = "<button onclick=\"dismiss_admin_notice('" . $notice . "', " . $mnths . ")\" ";
	$btn .= "style='padding: 5px 15px; margin-left:15px; margin-top:15px;'>";
	$btn .= "Dismiss for " . $mnths . " Months</button>";
	return $btn;
}


function tt_dashboard_notice() {
    $notices = array('tt_feedback_request');
    foreach ($notices as $notice) {
        if (tt_time_to_display_notice($notice)) {
            echo call_user_func('Logically_Tech\Time_Tracker\Admin\\' . $notice);
        }
    }
}


function tt_time_to_display_notice($nm) {
    $timers = get_option('time_tracker_admin_notices');
    if (array_key_exists($nm, $timers)) {
        if ($timers[$nm] == null) {
            return true;
        }
        if (is_int($timers[$nm])) {
            if ($timers[$nm] < time()) {
                return true;
            }
        }
        if (is_object($timers[$nm])) {
            if (date_timestamp_get($timers[$nm]) < time()) {
                return true;
            }
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

tt_add_new_admin_notice_timer('tt_feedback_request', new \DateTime(date_format(get_option('time_tracker_install_time'), 'Y-m-d H:i:s') . " + 1 month"));
add_action( 'admin_notices', 'Logically_Tech\Time_Tracker\Admin\tt_dashboard_notice' );
