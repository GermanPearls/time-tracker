<?php
/**
 * Class Time_Tracker_Save_Form_Data_WPF
 *
 * Hook into WPF after data saved to save form data into Time Tracker tables in database
 * Specific to WPF installations
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\WPF;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
 
/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_Save_Form_Data_WPF') ) {

    
    /**
     * Class
     * 
     */
    class Time_Tracker_Save_Form_Data_WPF {


        /**
         * Constructor
         * 
         */        
        public function __construct() {

        }


        /**
         * Save data to the db
         * 
         */ 
        public function saveTTData($fields, $entry, $form_id, $form_data="") {
            //$fields is array of fields with name, value, id, type
            //$entry is higher array with fields as sub arry and other items such as nonce, post id, etc
            //$form_data is similar to post_content of form from database, ie: form setup details

            $data = $form_data == "" ? "" : $this->clean_data($fields);
            new \Logically_Tech\Time_Tracker\Inc\Save_Form_Input($data, $form_id); 
        }


        /**
         * Sanitize data
         * 
         */
        private function clean_data($raw_data) {
            $clean_data = array();
            foreach ($raw_data as $id => $data) {            
                $name = str_replace(" ", "-", strtolower($data["name"]));
                $clean_data[$name] = filter_var($data['value'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            }
            return $clean_data;
        }


        /**
         * Get field name from form id and field id
         * 
         */
        private function get_field_name($frmid, $fldid) {
            $post = get_post($frmid, ARRAY_A);
            if ($post) {
                foreach ($post["post_content"]["fields"] as $fld) {
                    if (intval($fld["id"]) == intval($fldid)) {
                        return $fld["label"];
                    }
                }
            }
        }


    }  //close class
} //if class exists

$saveddata = new Time_Tracker_Save_Form_Data_WPF();
add_action( 'wpforms_process_entry_save', array($saveddata, 'saveTTData'), 10, 4 );