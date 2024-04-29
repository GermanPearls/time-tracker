<?php
/**
 * Class Time_Tracker_Shortcode_Project_List_Table
 *
 * SHORTCODE TO DISPLAY PROJECT LIST
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
if ( ! class_exists('Time_Tracker_Shortcode_Project_List_Table') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */  
    class Time_Tracker_Shortcode_Project_List_Table {

        
        /**
         * Class Variables
         * 
         * @since 1.0.0
         */  
        public $shortcode = 'tt_project_list_table';


        /**
         * Constructor
         * 
         * @since 1.0.0
         */ 
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'project_list_table_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 1.0.0
         * 
         * @return string Shortcode output - html showing project details, a different table for each status.
         */ 
        public function project_list_table_shortcode() {
            $list = new Project_List;
            return $list->get_page_html_with_each_status_in_different_table();
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

$Time_Tracker_Shortcode_Project_List_Table = new Time_Tracker_Shortcode_Project_List_Table();