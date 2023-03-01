<?php
/**
 * Class Time_Tracker_Save_Form_Data_CF7
 *
 * Hook into CF7 after data saved to save form data into Time Tracker tables in database
 * Specific to CF7 installations
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\CF7;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
 
/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_Save_Form_Data_CF7') ) {

    
    /**
     * Class
     * 
     */
    class Time_Tracker_Save_Form_Data_CF7 {


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
        public function saveTTData() {
            $form = \WPCF7_Submission::get_instance();
            $cleaned_data = $this->clean_data($form->get_posted_data());
            $id = $form->get_contact_form()->id();
            new \Logically_Tech\Time_Tracker\Inc\Save_Form_Input($cleaned_data, $id); 
        }
  
        
        /**
         * Sanitize data
         * 
         */
        private function clean_data($raw_data) {
            $clean_data = array();
            foreach ($raw_data as $key => $data) {
                if (is_array($data)) {
                    //$clean_data[$key] = filter_var(htmlspecialchars_decode($data[0], ENT_NOQUOTES), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                    //$raw = $data[0];
                    //$clean_data[$key] = htmlspecialchars_decode(filter_var($raw, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES), ENT_NO_QUOTES);
                    $clean_data[$key] = filter_var($data[0], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                } else {
                    //$clean_data[$key] = filter_var(htmlspecialchars_decode($data, ENT_NOQUOTES), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                    //$clean_data[$key] = htmlspecialchars_decode(filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES), ENT_NOQUOTES);
                    $clean_data[$key] = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                }
            }
            return $clean_data;
        }

    }  //close class
} //if class exists

$saveddata = new Time_Tracker_Save_Form_Data_CF7();

add_action( 'wpcf7_before_send_mail', array($saveddata, 'saveTTData') );