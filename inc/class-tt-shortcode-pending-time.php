<?php
/**
 * Class Time_Tracker_Shortcode_Pending_Time
 *
  * The [pending-time-table] shortcode.  Accepts a parameter and will display a table of time not yet invoiced (ie: pending).
 *
 * @since 1.0.0
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_Shortcode_Pending_Time') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */  
    class Time_Tracker_Shortcode_Pending_Time {


        /**
         * Class Variables
         * 
         * @since 1.0.0
         */  
        public $shortcode = 'tt_pending_time_table';
        private $atts = [];
        private $billto = '';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */  
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'pending_time_table_shortcode' ) );
        }


        /**
         * Get attributes of shortcode
         * 
         * @since 1.0.0
         * 
         * @return string Get bill to name, passed as an attribute in the shortcode.
         */  
        private function get_attributes($atts) {
            // normalize attribute keys, lowercase
            $atts = array_change_key_case( (array) $atts, CASE_LOWER );
            // get bill to attribute, or set to 'all' for default
            if ( array_key_exists( 'billto', $atts ) ) {
                return $atts['billto'];
            } else {
                return '';  //default if none specified
            }
        }


        /**
         * Callback
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode output - pending time, ie: time that has not yet been invoiced.
         */  
        public function pending_time_table_shortcode($atts) {
            $this->billto = $this->get_attributes($atts);
            $list = new Pending_Time;
            return $list->display_pending_time();
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

$Time_Tracker_Shortcode_Pending_Time = new Time_Tracker_Shortcode_Pending_Time();