<?php
/**
 * Class Time_Tracker_Display_Table
 *
 * Class to create display table on front end
 * 
 * @since 1.1.1
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Display_Table') ) {
    
    /**
     * Class
     * 
     */
    class Time_Tracker_Display_Table {

        public function __construct() {
        }


        /**
         * Add Arguments to Table Tag
         * 
         */
        private function add_arguments($arguments) {
            $args = "";
            foreach ($arguments as $type=>$value) {
                $args .= $type . "='";
                
                if (is_array($value)) {
                    foreach ($value as $ind_arg) {
                        $args .= esc_attr($ind_arg) . " ";
                    }
                } else {
                    $args .= esc_attr($value) . " ";
                }
                
                $args = trim($args) . "' ";
            }
            return " " . trim($args);
        }


        /**
         * Start Table Tag
         * 
         */
        public function start_table($head_arguments = null) {
            $tbl = "<table";
            if ($head_arguments) {
                $tbl .= $this->add_arguments($head_arguments);
            }
            $tbl .=">";
            return $tbl;
        }
        
        
        /**
         * Close Table Tag
         * 
         */
        public function close_table() {
            return "</table>";
        }


        /**
         * Start Row
         * 
         */
        public function start_row($row_arguments = null) {
            $row = "<tr";
            if ($row_arguments) {
                $row .= $this->add_arguments($row_arguments);
            }
            $row .=">";
            return $row;
        }


        /**
         * Close Row
         * 
         */
        public function close_row() {
            return "</tr>";
        }


        /**
         * Start Header Data
         * 
         */
        public function start_header_data($header_data_arguments = null) {
            $th = "<th";
            if ($header_data_arguments) {
                $th .= $this->add_arguments($header_data_arguments);
            }
            $th .= ">";
            return $th;
        }


        /**
         * Close Header Data
         * 
         */
        public function close_header_data() {
            return "</th>";
        }


        /**
         * Start Data
         * 
         */
        public function start_data($data_arguments = null) {
            $td = "<td";
            if ($data_arguments) {
                $td .= $this->add_arguments($data_arguments);
            }
            $td .= ">";
            return $td;
        }


        /**
         * Close Data
         * 
         */
        public function close_data() {
            return "</td>";
        }
		

    }  //close class
 }  //close if class exists
