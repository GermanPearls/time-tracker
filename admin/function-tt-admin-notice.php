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

function tt_display_admin_notice() {
    global $pagenow;
    //if ( $pagenow == 'index.php' ) {
          echo '<div class="notice notice-info is-dismissible tt-admin-notice">
          <p>Thank you for trying the Time Tracker plugin. We\'d love to hear your feedback.
          Feel free to reach out directly with any issues or recommendations at 
          <a href="mailto:info@logicallytech.com">info@logicallytech.com</a>.
          If you\'re enjoying the plugin and could take a few moments to leave a review 
          it would be greatly appreciated.
          <button onclick=window.location.href="https://wordpress.org/support/plugin/time-tracker/reviews/#new-post">
          Leave a Review</button></p>
          </div>';
    //}
}

add_action( 'admin_notices', 'tt_display_admin_notice' );
