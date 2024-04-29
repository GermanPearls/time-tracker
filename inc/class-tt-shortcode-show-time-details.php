<?php
/**
 * Class Time_Tracker_Shortcode_Show_Time_Details
 *
 * SHORTCODE TO DISPLAY DETAILS OF INDIVIDUAL TIME ENTRY
 * 
 * @since 3.1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 3.1.0
 */
if ( ! class_exists('Time_Tracker_Shortcode_Show_Time_Details') ) {

    /**
     * Class
     * 
     * @since 3.1.0
     */  
    class Time_Tracker_Shortcode_Show_Time_Details {

        
        
        /**
         * Class variables
         * 
         * @since 3.1.0
         */
        public $shortcode = 'tt_show_time_details';


        /**
         * Constructor
         * 
         * @since 3.1.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'show_time_details_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 3.1.0
         * 
         * @return string Shortcode output - html showing details of an individual time entry.
         */
        public function show_time_details_shortcode() {
            $time = new Time_Details;
            return $time->generate_output_for_display();
        }
    

        /**
         * Return results
         * 
         * @since 3.1.0
         * 
         * @return string Shortcode.
         */
        public function get_shortcode() {
            return $this->shortcode;
        }

    }
} 

$tt_Shortcode_Show_Time_Details = new Time_Tracker_Shortcode_Show_Time_Details();