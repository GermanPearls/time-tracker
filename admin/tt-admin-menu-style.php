<?php
/**
 * Time Tracker Menu - Styling Page
 *
 * Layout style / css page for Time Tracker Admin Menus
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Admin;


/**
 * Display a custom sub-menu page
 * 
 */
function tt_admin_menu_style() { 
   ?>
   <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <button onclick="javascript:location.href='<?php echo TT_HOME ?>'" class="tt-admin-to-front button-primary">Time Tracker Home</button>   
      <div class="tt-indent">
         <h2>Style Options</h2>
         <p>
            Use the options below if you'd like to over-ride your theme settings and set your own styling for the Time Tracker pages.
         </p>
         <hr/>
         <h3>Buttons</h3>
         <p>
            To set your own button colors, check the "over-ride theme style" checkbox below, and enter your own colors.<br/>
            Colors should be entered in hex, rgb, or rgba format.
         </p>
         <form action="options.php" method="post" id="tt-options">
         <?php
         do_settings_sections( 'time-tracker-style' );
         settings_fields( 'time_tracker_style' );
         submit_button( 'Save Settings' );
         ?>
      </form>      
         <hr/>
      </div>
   <?php   
}