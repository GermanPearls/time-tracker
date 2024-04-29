<?php
/**
 * Class Time_Tracker_Shortcode_Time_Log_Table
 *
 * SHORTCODE TO DISPLAY TIME LOG. 
 * Accepts type of log (detail vs summary) as an argument and outputs respective table as html.
 *
 *  
 * @since 1.0.0
 * @since 2.2.0 Added ability to display summary table.  
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_Shortcode_Time_Log_Table') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */  
    class Time_Tracker_Shortcode_Time_Log_Table {


        /**
         * Variables
         * 
         * @since 1.0.0
         */ 
        public $shortcode = 'tt_time_log_table';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'time_log_table_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 1.0.0
         * @since 2.2.0 Added ability to display summary table.  
         * 
         * @return string Shortcode output - html table showing time log, detail or summary, as defined by arguments passed.
         */
        public function time_log_table_shortcode($atts) {
            // normalize attribute keys, lowercase
            $atts = array_change_key_case( (array) $atts, CASE_LOWER );

            //this sets defaults, and combines with user submitted atts
            $timelog_atts = shortcode_atts(
                array(
                    'type' => 'detail',
                ), $atts, 'timelog'
            );

            $time_detail = new Time_Log;
            if ($timelog_atts['type'] == 'detail') {
                return $time_detail->create_table();
            } elseif ($timelog_atts['type'] == 'summary') {
                $time_summary = new Time_Log_Summary;
                return $time_summary->create_summary_table();                
            }
        }
    

        /**
         * Return results
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode.
         */
        public function get_shortcode() {
            return $this->shortcode;
        }
    
    }
} 

$Time_Tracker_Shortcode_Time_Log_Table = new Time_Tracker_Shortcode_Time_Log_Table();