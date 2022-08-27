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
    $msg = "<div class=\"notice notice-info is-dismissable tt-admin-notice\">";
    $msg .= "<p>Thank you for trying the Time Tracker plugin. We\'d love to hear your feedback.";
    $msg .= "Feel free to reach out directly with issues or recommendations at ";
    $msg .= "<a href=\"mailto:info@logicallytech.com\">info@logicallytech.com</a>.";
    $msg .= "If you\'re enjoying the plugin and could take a few moments to leave a review, ";
    $msg .= "it would be greatly appreciated.";
    $mgs .= "<button href=\"https:\/\/wordpress.org\/support\/plugin\/time-tracker\/reviews\/#new-post\">";
    $msg .= "Leave a Review<\/button>";
    $msg .= "<\/p><\/div>";
    return $msg;    
}


function tt_admin_notice() {
    $notice = tt_feedback_request();
    echo $notice;
}

add_action( 'admin_notices', 'tt_admin_notice' );
