<?php
/**
 * Time Tracker Export Pending Time (ie: hasn't been billed yet)
 *
 * Extends Class Pending_Time which pulls data from db and organizes it
 * 
 * @since 2.2.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Pending_Time_Export' ) ) {


    /**
     * Class
     * 
     * @since 2.2.0
     */
    class Pending_Time_Export extends Pending_Time
    {

        protected $original_data;
        public $saved_files;

        
        /**
         * Constructor
         * 
         * @since 2.2.0
         */
        public function __construct() {
            $this->original_data = parent::get_data_for_export();
            $this->saved_files = array();
        }


        /**
         * Export to CSV File(s)
         *
         * @since 2.2.0
         * 
         * @return array List of saved files.
         */
        public function export_each_billto() {
            $this->save_each_billto_as_a_file();         
            return $this->saved_files;
        }


        /**
         * Process Each Bill To Separately
         *
         * @since 2.2.0
         * 
         * @return null
         */
        private function save_each_billto_as_a_file() {
            foreach ($this->original_data as $billtoname => $time_details) {
                if (!empty($time_details)) {   
                    $export_and_dl = New Time_Tracker_Export_To_File_And_Download($this->get_save_path(),$this->get_filename($billtoname),".csv",$time_details);
                    $fl = $export_and_dl->save_to_file();
                    array_push($this->saved_files, $fl);
                }
            }
            return;         
        }        


        /**
         * Get File Save Path
         *
         * @since 2.2.0
         * 
         * @return string Path where files will be saved.
         */
        private function get_save_path() {
            $path = ABSPATH . "../tt_logs/exports";
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            return $path;            
        }


        /**
         * Get File Save Name
         *
         * @since 2.2.0
         * @since 3.0.13 Updated to allow more flexibility in name.
         * 
         * @param string $descriptor Description of file being saved.
         * 
         * @return string Name of file to save.
         */
        private function get_filename($descriptor, $nm="pending_time_export") {
            if (is_null($descriptor)) {
                $filename = $nm . "_" .  date('d-M-Y');
            } else {
                $filename = $nm . "_" . date('Y_M_d') . '_' . $descriptor;
            }
            if (!file_exists($filename)) {
                return $filename;
            }

            $i = 0;
            do {
                $i = $i + 1;
                $filename = $filename . '_' . $i;
            } while (file_exists($filename));
            return $filename;      
        }

    } //class
} //if exists