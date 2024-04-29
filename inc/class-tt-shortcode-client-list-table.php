<?php
/**
 * Class Time_Tracker_Shortcode_Client_List_Table
 *
 * SHORTCODE TO DISPLAY CLIENT LIST TABLE
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
if ( ! class_exists('Time_Tracker_Shortcode_Client_List_Table') ) {

    /**
     * Class
     * 
     */  
    class Time_Tracker_Shortcode_Client_List_Table {

        
        /**
         * Class Variables
         * 
         * @since 1.0.0
         */         
        public $shortcode = 'tt_client_list_table';

        
        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'client_list_table_shortcode' ) );
        }


        /**
         * Shortcode callback
         * 
         * @since 1.0.0
         * 
         * @return string Output to display.
         */
        public function client_list_table_shortcode() {
            $list = new Client_List;
            return $list->create_table();
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
    } //class
} //if class exists

$Time_Tracker_Shortcode_Client_List_Table = new Time_Tracker_Shortcode_Client_List_Table();