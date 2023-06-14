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
 */
if ( ! class_exists('Time_Tracker_Shortcode_Show_Time_Details') ) {

    /**
     * Class
     * 
     */  
    class Time_Tracker_Shortcode_Show_Time_Details {

        
        
        /**
         * Class variables
         * 
         */
        public $shortcode = 'tt_show_time_details';


        /**
         * Constructor
         * 
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'show_time_details_shortcode' ) );
        }


        /**
         * Callback
         * 
         */
        public function show_time_details_shortcode() {
            $time = new Time_Details;
            $display = $time->generate_output_for_display();
            return $display;
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

$tt_Shortcode_Show_Time_Details = new Time_Tracker_Shortcode_Show_Time_Details();