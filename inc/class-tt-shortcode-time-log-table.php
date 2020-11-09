<?php
/**
 * Class Time_Tracker_Shortcode_Time_Log_Table
 *
 * SHORTCODE TO DISPLAY TIME LOG
 * 
 * 
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_Shortcode_Time_Log_Table') ) {

    /**
     * Class
     * 
     */  
    class Time_Tracker_Shortcode_Time_Log_Table {


        /**
         * Class
         * 
         */ 
        public $shortcode = 'tt_time_log_table';


        /**
         * Constructor
         * 
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'time_log_table_shortcode' ) );
        }


        /**
         * Callback
         * 
         */
        public function time_log_table_shortcode() {
            $list = new Time_Log;
            $table = $list->create_table();
            return $table;
        }
    

        /**
         * Return results
         * 
         */
        public function get_shortcode() {
            return $this->shortcode;
        }
    
    } //class
} //if class exists

$Time_Tracker_Shortcode_Time_Log_Table = new Time_Tracker_Shortcode_Time_Log_Table();