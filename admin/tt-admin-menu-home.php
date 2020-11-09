<?php
/**
 * Time Tracker Menu - Main Page
 *
 * Layout homepage for Time Tracker Admin Menus
 * 
 * @since 1.0
 * 
 */



/**
 * Display a custom menu page
 * 
 */
function tt_admin_menu_home() { 
   ?>
   <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <?php 
         if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
            ?>
            <div class="tt-indent">
               <h2>IMPORTANT</h2>
               <p class="tt-important">The Contact Form 7 plugin must be installed and activated for Time Tracker to work properly. Please <a href="/wp-admin/plugin-install.php?s=contact+form+7&tab=search&type=term">install CF7</a> and activate it now.</p>
            </div>
            <?php
         }
         ?>
      <button onclick="location.href='/time-tracker';" class="tt-admin-to-front button-primary ">Time Tracker Home</button>
      <form action="options.php" method="post" id="tt-options">
         <?php
         do_settings_sections( 'time-tracker' );
         settings_fields( 'time-tracker' );
         submit_button( 'Save Settings' );
         ?>
      </form>
      <br/>
      <br/>
      Icons made by <a href="https://www.flaticon.com/free-icon/check-list_3203134" title="fjstudio">fjstudio</a> from <a href="https://www.flaticon.com/" title="Flaticon"> www.flaticon.com</a>
   <?php   
}