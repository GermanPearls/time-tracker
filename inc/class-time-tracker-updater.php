<?php
/**
 * Class Time_Tracker_Updater
 *
 * Handle updates to Time Tracker Plugin
 * 
 * @since 2.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Tracker_Updater' ) ) {


    /**
     * Class
     * 
     */
    class Time_Tracker_Updater
    {


        /**
         * Constructor
         * 
         */
        public function __construct() {

        }


        /**
         * Update Version
         * 
         */
        public function tt_update_from($current_ver) {
            //if option wasn't set in db or it was version 1.x.x update to 2.0.0
            if ( (!$current_ver) or (substr($current_ver, 0, 1) == "1") ) {
                $this->tt_update_to_two();
                $this->tt_update_to_two_four();
            } else {
                $ver = explode(".", $current_ver);
                if ( (intval($ver[0]) == 2) and (intval($ver[1]) < 4) ) {
                    $this->tt_update_to_two_four();
                }
                $this->tt_update_plugin();
            }
        }


        /**
         * Update from 1.x.x to 2.0.0
         * 
         */
        private function tt_update_to_two() {
            $this->tt_update_pages();
            $this->tt_update_forms('true');
            $this->tt_update_version_in_db(TIME_TRACKER_VERSION);
        }
        
        
        /**
        * Update to 2.4.0
        *
        **/
        private function tt_update_to_two_four() {
            delete_option('time-tracker-sql-result');
            delete_option('time_tracker-sql-result');
            add_option('time_tracker_categories', array(
                'bill_to_names' => get_option('time-tracker')['bill-to-names'],
                'work_categories' => get_option('time-tracker')['work-categories'],
                'client_categories' => get_option('time-tracker')['client-categories'],
                'client_sub_categories' => get_option('time-tracker')['client-sub-categories'],
                'default_client' => null,
                'default_task' => null
                )
            );
            delete_option('time-tracker');    
        }
        


        /**
         * General Plugin Update
         * 
         */
        private function tt_update_plugin() {
            $this->tt_update_pages();
            $this->tt_update_forms();
            $this->tt_update_version_in_db(TIME_TRACKER_VERSION);
        }


        /**
         * Update version stored in database
         * 
         */
        private function tt_update_version_in_db($ver) {
            if (!get_option('time_tracker_version')) {
                add_option('time_tracker_version', $ver);
            } else {
                update_option('time_tracker_version', $ver);
            }
        }


        /**
         * Update pages so they match the current version
         * 
         */
        private function tt_update_pages() {
            require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-pages.php');
            $tt_pages = Time_Tracker_Activator_Pages::check_pages_match_current_version();
        }


        /**
         * Update forms so they match the current version
         * 
         */
        private function tt_update_forms($force_update = false) {
            require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-forms.php');
            if ($force_update) {
                $tt_forms = Time_Tracker_Activator_Forms::force_form_updates();
            } else {
                $tt_forms = Time_Tracker_Activator_Forms::check_forms_for_updates();
            }            
        }

    } //close class

}
