<?php
/**
 * Class Time_Tracker_Open_Task_List_Table
 *
 * SHORTCODE TO DISPLAY OPEN TASK LIST
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
if ( ! class_exists('Time_Tracker_Open_Task_List_Table') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */ 
    class Time_Tracker_Open_Task_List_Table {


        /**
         * Plugin Variables
         * 
         * @since 1.0.0
         */  
        public $shortcode = 'tt_open_task_list_table';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'open_task_list_table_shortcode' ) );
        }


        /**
         * Shortcode callback
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode output - html table summarizing open tasks.
         */
        public function open_task_list_table_shortcode() {
            $list = new Task_List;
            return $list->create_table("open_tasks");
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

$Time_Tracker_Open_Task_List_Table = new Time_Tracker_Open_Task_List_Table();