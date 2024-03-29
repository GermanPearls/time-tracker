<?php
/**
 * Class Time_Tracker_Shortcode_Task_List_Table
 *
 * SHORTCODE TO DISPLAY ENTIRE TASK LIST
 * 
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_Shortcode_Task_List_Table') ) {

    /**
     * Class
     * 
     */  
    class Time_Tracker_Shortcode_Task_List_Table {


        /**
         * Class variables
         * 
         */
        public $shortcode = 'tt_task_list_table';


        /**
         * Constructor
         * 
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'task_list_table_shortcode' ) );
        }


        /**
         * Callback
         * 
         */
        public function task_list_table_shortcode() {
            $list = new Task_List;
            return $list->create_table("all_tasks");
        }
    

        /**
         * 
         * Return
         * 
         */
        public function get_shortcode() {
            return $this->shortcode;
        }

    } //class
} //if class exists

$Time_Tracker_Shortcode_Task_List_Table = new Time_Tracker_Shortcode_Task_List_Table();