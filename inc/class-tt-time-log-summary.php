<?php
/**
 * Class Time_Log_Summary Extends Time_Log
 *
 * CLASS TO DISPLAY TIME LOG SUMMARY TABLE
 * 
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Log_Summary' ) ) {


    /**
     * Class
     * 
     */  
    class Time_Log_Summary extends Time_Log
    {


        /**
         * Class Variables
         * 
         */ 
        private $time_summary_array;
        private $time_detail_array;
        private $bill_to_names;



        /**
         * Constructor
         * 
         */        
        public function __construct() {
            //$this->get_time_log_from_db();
        }


        /**
         * Get results
         * 
         */
        public function create_summary_table() {
            $this->time_detail_array = $this->get_time_log_array_from_db();
            $this->time_summary_array = $this->summarize_data();
            return $this->get_summary_html();
        }
        
        
        /**
         * Add week start and week end dates to data
         * 
         **/
        private function add_week_start_and_end_dates() {
            $sorted_data = $this->sort_summary_data();
            //update current array by adding week start date and week end date
            foreach ($sorted_data as $k=>$entry) {
                $week_start_and_end_detail = $this->get_week_detail($entry['StartTime']);
                $sorted_data[$k]['Week Starting'] = strval($week_start_and_end_detail[0]);
                $sorted_data[$k]['Week Ending'] = strval($week_start_and_end_detail[1]);
            }
            return $sorted_data;                        
        }


        /**
         * Group data by week and bill to, Also create bill to names array
         * 
         */
        private function group_data_by_week_and_bill_to() {
            $grouped_data = array();
            $this->bill_to_names = array();
            $sorted_data_with_week_data = $this->add_week_start_and_end_dates();

            //create new array grouped by week start and bill to
            foreach ($sorted_data_with_week_data as $entry) {
                $grouped_data[$entry['Week Starting']][$entry['BillTo']] = $entry;
                if (array_key_exists($entry['BillTo'], $this->bill_to_names) == false) {
                    array_push($this->bill_to_names, $entry['BillTo']);
                }
            }
            sort($this->bill_to_names);
            array_push($this->bill_to_names, 'Total');
            return $grouped_data;
        }


        /**
         * Summarize and total data by week, bill to and total time worked, billed, etc
         * 
         */
        private function summarize_data() {
            $summarized = array();
            $grouped_data = $this->group_data_by_week_and_bill_to();

            //create new summary array totaling time by week start and bill to, also create total for each week
            //var_dump($grouped_data);
            foreach ($grouped_data as $week_start => $bill_to_entries) {
                foreach ($bill_to_entries as $bill_to => $bill_to_entry) {
                    $time_worked = tt_convert_to_decimal_time($bill_to_entry['LoggedHours'], $bill_to_entry['LoggedMinutes']);
                    
                    $logged_time_worked = 0;
                    $logged_time_billed = 0;
                    if (array_key_exists($week_start, $summarized)) {
                        $logged_time_worked = array_key_exists($bill_to, $summarized[$week_start]) ? $summarized[$week_start][$bill_to]['Time Worked'] : 0;
                        $logged_time_billed = array_key_exists($bill_to, $summarized[$week_start]) ? $summarized[$week_start][$bill_to]['Time Billed'] : 0;
                    }
                    $summarized[$week_start][$bill_to]['Week Ending'] = $bill_to_entry['Week Ending'];
                    $summarized[$week_start][$bill_to]['Time Worked'] = $logged_time_worked + $time_worked;
                    $summarized[$week_start][$bill_to]['Time Billed'] = $logged_time_billed + $bill_to_entry['InvoicedTime'];
                    $summarized[$week_start][$bill_to]['Display'] = $this->prepare_summary_display_data($summarized[$week_start][$bill_to]['Time Worked'], $summarized[$week_start][$bill_to]['Time Billed']);

                    if (array_key_exists($week_start, $summarized)) {
                        $total_time_worked = array_key_exists('Total', $summarized[$week_start]) ? $summarized[$week_start]['Total']['Time Worked'] : 0;
                        $total_time_billed = array_key_exists('Total', $summarized[$week_start]) ? $summarized[$week_start]['Total']['Time Billed'] : 0;
                    }
                    $summarized[$week_start]['Total']['Week Ending'] = $bill_to_entry['Week Ending'];
                    $summarized[$week_start]['Total']['Time Worked'] = $total_time_worked + $time_worked;
                    $summarized[$week_start]['Total']['Time Billed'] = $total_time_billed + $bill_to_entry['InvoicedTime'];
                    $summarized[$week_start]['Total']['Display'] = $this->prepare_summary_display_data($summarized[$week_start]['Total']['Time Worked'], $summarized[$week_start]['Total']['Time Billed']);
                }
            }
            $this->time_summary_array = $summarized;
            return $summarized;
        }


        /**
         * Prepare display output for each bill to and week combination
         * 
         */
        private function prepare_summary_display_data($time_worked, $time_billed) {
            $output = round($time_worked, 1) . ' Worked<br/>';
            $output .= round($time_billed, 1) . ' Billed<br/>';
            $output .= round($time_billed / $time_worked * 100,0) . '% Billed';
            return $output;
        }


        /**
         * Get Week Start Date and End Date
         * 
         */
        private function get_week_detail($start_time) {
            $time_obj = new \DateTime($start_time);
            $day_of_week = \date_format($time_obj, 'N');  //	1 for Monday through 7 for Sunday
            if ($day_of_week != 1) {
                $days_to_subtract = $day_of_week - 1;
                $time_obj = \date_sub($time_obj, date_interval_create_from_date_string($days_to_subtract . ' days'));
            }
            $week_start = \date_format($time_obj, 'n/j/Y');
            $time_obj = \date_add($time_obj, date_interval_create_from_date_string('6 days'));
            $week_end = \date_format($time_obj, 'n/j/Y');
            $week_detail = array($week_start, $week_end);
            return $week_detail;
        }


        /**
         * Sort DataArray
         * 
         */
        private function sort_summary_data() {
            //$billto = array_column($data, 'BillTo');
            //$starttime = array_column($data, 'StartTime');
            $data = $this->time_detail_array;
            array_multisort(array_column($data, 'BillTo'), SORT_ASC,
                            array_column($data, 'StartTime'),
                            $data);
            return $data;            
        }


        /**
         * Get table column order and table fields
         * 
         */
        private function get_summary_table_fields() {
            $cols = [
                "Start Date" => [
                    "fieldname" => "week-starting",
                    "id" => "",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",   //saved as string above
                    "class" => ""
                ],
                "End Date" => [
                    "fieldname" => "week-ending",
                    "id" => "",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",   //saved as string above
                    "class" => ""
                ]
            ];
            foreach ($this->bill_to_names as $bill_to_name) {
                $cols[$bill_to_name] = array(
                        "fieldname" => $bill_to_name,
                        "id" => "",
                        "editable" => false,
                        "columnwidth" => "",
                        "type" => "long text",
                        "class" => "tt-align-right"
                    );
            }           
            return $cols;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         */
        private function get_summary_data_for_display() {
            $table_data_item = array();
            $table_data = array();
            //put back in one dimensional array for table
            foreach ($this->time_summary_array as $week_start_date => $week_data) {
                foreach ($week_data as $bill_to_name => $entry) {
                    $table_data_item['week-starting'] = $week_start_date;
                    $table_data_item['week-ending'] = $entry['Week Ending'];
                    foreach ($this->bill_to_names as $bill_to) {
                        if (array_key_exists($bill_to, $week_data)) {
                            $table_data_item[$bill_to] = $week_data[$bill_to]['Display'];
                        } else {
                            $table_data_item[$bill_to] = "";
                        }
                    }
                }
                array_push($table_data, $table_data_item);
                $table_data_item = array();
            }
            return $table_data;
        }


        /**
         * Create Table
         * 
         */
        public function get_summary_html() {            
            $fields = $this->get_summary_table_fields();
            $display_data = $this->get_summary_data_for_display();
            $args["class"] = ["tt-table", "time-log-table", "time-log-summary-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $display_data, $args, "Time Log Summary by Bill To and Week", "");
            return $table;
        }
        
    } //close class

} //close if class exists