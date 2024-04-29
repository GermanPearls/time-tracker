<?php
/**
 * Class Time_Tracker_Shortcode_Show_Task_Details
 *
 * SHORTCODE TO DISPLAY ENTIRE TASK LIST
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
if ( ! class_exists('Time_Tracker_Shortcode_Show_Task_Details') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */  
    class Time_Tracker_Shortcode_Show_Task_Details {

        
        
        /**
         * Class variables
         * 
         * @since 1.0.0
         */
        public $shortcode = 'tt_show_task_details';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'show_task_details_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode output - details of all tasks.
         */
        public function show_task_details_shortcode() {
            $task = new Task_Details;
            return $task->generate_output_for_display();
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

$tt_Shortcode_Show_Task_Details = new Time_Tracker_Shortcode_Show_Task_Details();