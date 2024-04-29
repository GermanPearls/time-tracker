<?php
/**
 * Class Time_Tracker_Shortcode_Recurring_Task_List_Table
 *
 * SHORTCODE TO DISPLAY RECURRING TASK LIST
 * 
 * @since 1.1.1 
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 1.1.1
 */
if ( ! class_exists('Time_Tracker_Shortcode_Recurring_Task_List_Table') ) {

    /**
     * Class
     * 
     * @since 1.1.1
     */  
    class Time_Tracker_Shortcode_Recurring_Task_List_Table {


        /**
         * Class variables
         * 
         * @since 1.1.1
         */
        public $shortcode = 'tt_recurring_task_list_table';


        /**
         * Constructor
         * 
         * @since 1.1.1
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'recurring_task_list_table_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 1.1.1
         * 
         * @return string Shortcode output - html table showing recurring tasks.
         */
        public function recurring_task_list_table_shortcode() {
            $list = new Recurring_Task_List;
            return $list->create_table();
        }
    

        /**
         * Return
         * 
         * @since 1.1.1
         * 
         * @return string Shortcode.
         */
        public function get_shortcode() {
            return $this->shortcode;
        }

    }
}

$Time_Tracker_Shortcode_Recurring_Task_List_Table = new Time_Tracker_Shortcode_Recurring_Task_List_Table();