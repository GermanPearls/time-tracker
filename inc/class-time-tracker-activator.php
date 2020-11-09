<?php
/**
 * Class Time_Tracker_ACtivator
 *
 * Initial activation of Time Tracker Plugin
 * 
 * @since 1.0
 * 
 */


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Activator') ) {

    //global $TT_DB_NAME;
    
    /**
     * Class
     * 
     */
    class Time_Tracker_Activator {

        private static $cf7_active = false;


        public static function activate() {
            self::define_plugin_variables();
            self::cf7_plugin_activated();
            if (self::$cf7_active) {
                include_once(TT_PLUGIN_DIR_INC . 'function-tt-cron-recurring-tasks.php');
                require_once(plugin_dir_path( __FILE__ ) . '/class-time-tracker-activator-tables.php');
                require_once(plugin_dir_path( __FILE__ ) . '/class-time-tracker-activator-forms.php');
                require_once(plugin_dir_path( __FILE__ ) . '/class-time-tracker-activator-pages.php');
                Time_Tracker_Activator_Tables::setup();
                Time_Tracker_Activator_Forms::setup();
                Time_Tracker_Activator_Pages::setup();
            } else {
                ?>
                <script type="text/javascript">
                window.alert('Time Tracker requires Contact Form 7 plugin to work properly. Please install the Contact Form 7 plugin before activating Time Tracker.');
                </script>
                <?php
                die('Please install the Contact Form 7 plugin before activating Time Tracker.');
            }
        }


        /**
         * Definitions
         * 
         */
        private static function define_plugin_variables() {
            //Time Tracker Database Name
            //if (! defined('TT_DB_NAME')) {
                //define('TT_DB_NAME', DB_NAME . "_tt"); //time tracker database name
            //}
        }


        private static function cf7_plugin_activated() {
            if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
                //plugin is activated
                self::$cf7_active = true;
            }
        }

    }  //close class
 }  //close if class exists