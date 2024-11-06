<?php
/**
 * Class Time_Tracker_Shortcode_Edit_Time_Entry
 *
 * SHORTCODE TO EDIT TIME ENTRY
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
if ( ! class_exists('Time_Tracker_Shortcode_Edit_Time_Entry') ) {

    /**
     * Class
     * 
     * @since 3.1.0
     */  
    class Time_Tracker_Shortcode_Edit_Time_Entry {

        
        
        /**
         * Class variables
         * 
         * @since 3.1.0
         */
        public $shortcode = 'tt_edit_time_entry';


        /**
         * Constructor
         * 
         * @since 3.1.0
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'edit_time_entry_shortcode' ) );
        }


        /**
         * Callback
         * 
         * @since 3.1.0
         * 
         * @return string Shortcode output - time entry details on editable form.
         */
        public function edit_time_entry_shortcode() {
            $time = new Time_Details_Edit;
            return $time->generate_output_for_display();
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

$tt_Shortcode_Edit_Time_Entry = new Time_Tracker_Shortcode_Edit_Time_Entry();