<?php
/**
 * Class Time_Tracker_Shortcode_Month_Summary
 *
 * SHORTCODE TO DISPLAY TOTAL HOURS BY COMPANY AND GRAND TOTAL
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
if ( ! class_exists('Time_Tracker_Shortcode_Month_Summary') ) {

  /**
   * Class
   * 
   * @since 1.0.0
   */  
  class Time_Tracker_Shortcode_Month_Summary {


    /**
     * Plugin Variables
     * 
     * @since 1.0.0
     */   
    public $shortcode = 'tt_month_summary';


    /**
     * Constructor
     * 
     * @since 1.0.0
     */    
    public function __construct() {
      add_shortcode( $this->shortcode, array( $this, 'month_summary_shortcode' ) );
    }


    /**
     * Shortcode callback
     * 
     * @since 1.0.0
     * 
     * @return string Shortcode output - html table showing monthly summary of hours worked.
     */
    public function month_summary_shortcode() {    
      $month_summary= new Class_Hours_Worked_Month_Summary;
      return $month_summary->createHTMLTable();
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

$Time_Tracker_Shortcode_Month_Summary = new Time_Tracker_Shortcode_Month_Summary();