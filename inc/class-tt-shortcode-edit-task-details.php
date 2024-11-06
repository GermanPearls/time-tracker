<?php
/**
 * Class Time_Tracker_Shortcode_Edit_Task_Details
 *
 * SHORTCODE TO EDIT  TASK
 * 
 * @since 3.1.0
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 3.1.0
 */
if ( ! class_exists('Time_Tracker_Shortcode_Edit_Task_Details') ) {

    /**
     * Class
     * 
     * @since 3.1.0
     */  
    class Time_Tracker_Shortcode_Edit_Task_Details {

        
        
        /**
         * Class variables
         * 
         * @since 3.1.0
         */
        public $shortcode = 'tt_edit_task_details';


        /**
         * Constructor
         * 
         * @since 3.1.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'edit_task_details_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 3.1.0
         * 
         * @return string Shortcode output - task details on editable form.
         */
        public function edit_task_details_shortcode() {
            $task = new Task_Edit;
            return $task->generate_output_for_display();
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

$tt_Shortcode_Edit_Task_Details = new Time_Tracker_Shortcode_Edit_Task_Details();