<?php
/**
 * Class Time_Tracker_Shortcode_Year_Summary
 *
 * SHORTCODE TO DISPLAY TOTAL HOURS FOR YEAR
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
if ( ! class_exists('Time_Tracker_Shortcode_Year_Summary') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */  
    class Time_Tracker_Shortcode_Year_Summary {


        /**
         * Class variables
         * 
         * @since 1.0.0
         */  
        public $shortcode = 'tt_year_summary';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */  
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'year_summary_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode output - html showing hours worked, summarized for a year.
         */
        public function year_summary_shortcode() {
            $year_summary = new Class_Hours_Worked_Year_Summary;
            return $year_summary->getHTML();
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

$Time_Tracker_Shortcode_Year_Summary = new Time_Tracker_Shortcode_Year_Summary();