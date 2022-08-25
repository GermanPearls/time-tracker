<?php
/**
 * Time Tracker Display Pending Time (ie: hasn't been billed yet)
 *
 * Sort pending time by bill-to and display in tables
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Pending_Time' ) ) {


    /**
     * Class
     * 
     */
    class Pending_Time
    {

        
        /**
         * Constructor
         * 
         */
        public function __construct() {
        }


        /**
         * Public function for child class
         * 
         */
        public function get_data_for_export() {
            $data = $this->get_time_grouped_by_billto();
            return $data;
        }


        /**
         * Create front end display of time not yet billed
         * 
         */
        public function display_pending_time() {
            $grouped_time = $this->get_time_grouped_by_billto();
            if ($grouped_time) {

                //TABLE OF CONTENTS - WITH LINKS
                $html = "<strong>Click a Link to Jump to That Section</strong>";
                $html .= "<ul>";
                foreach ($grouped_time as $billtoname => $time_details) {
                    if ($billtoname != null) {
                        $html .= "<li><a href=\"#" . esc_attr($billtoname) . "\">Pending Time to Bill To: " . esc_textarea($billtoname) . "</a></li>";
                    } else {
                        $html .= "<li><a href=\"#None\">No Bill To Specified</a></li>";
                    }
                }
                $html.= "</ul>";
                $html .= "<br/>";

                //DETAILS
                foreach ($grouped_time as $billtoname => $time_details) {
                    if ($billtoname != null) {    
                        $html .= "<h2 id=\"" . esc_attr($billtoname) . "\">Pending Time, Bill To: " . esc_textarea($billtoname) . "</h2>";
                    } else {
                        $html .= "<h2 id=\"None\">Pending Time, No Bill To Specified</h2>";
                    }
                    $html .= $this->create_table($this->get_all_data_for_display($time_details));
                }
            } else {
                $html = "All caught up!";
            }
            return $html;
        }
        
        
        /**
         * Query db for time not yet billed
         * 
         */
        private function get_pending_time_from_db() {
            global $wpdb;
            $sql_string = "SELECT tt_time.*, tt_client.Company, tt_client.BillTo, tt_task.TDescription, tt_task.TTimeEstimate,
                    Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes,
                    Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours
                FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID
                WHERE (tt_time.Invoiced = \"\" OR tt_time.Invoiced IS NULL) AND tt_client.Billable = true
                ORDER BY tt_client.BillTo ASC, tt_time.ClientID ASC, tt_time.TaskID ASC, tt_time.StartTime ASC";
            $sql_result = $wpdb->get_results($query = $sql_string, $output = ARRAY_A);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $sql_result;
        } //close function get to do list from db

                        
            
            
        /**
         * Regroup data by Bill To
         * 
         */
        private function get_time_grouped_by_billto() {
            $pending_time = $this->get_pending_time_from_db();
            if ($pending_time) {
                $lastbillto = "not started";
                foreach ($pending_time as $item) {
                    $billto = sanitize_text_field($item['BillTo']) == "" ? "Unknown" : sanitize_text_field($item['BillTo']);

                    //create new key if first time seeing this bill to
                    if ($lastbillto != $billto) {
                        $grouped_time[$billto][0] = $item;
                    } else {
                        //or just add to the array under this key
                        array_push($grouped_time[$billto], $item);
                    }
                    $lastbillto = $billto;
                }
            } else {
                $grouped_time = array();
            }
            return $grouped_time;
        }


        /**
         * Get table column order and table fields
         * 
         */
        private function get_table_fields() {
            $cols = [
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "client",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Task" => [
                    "fieldname" =>"TaskID",
                    "id" => "task-id",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Task Description" => [
                    "fieldname" =>"TDescription",
                    "id" => "task-description",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Start Time" => [
                    "fieldname" => "StartTime",
                    "id" => "start-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date and time",
                    "class" => "tt-align-right"
                ],
                "End Time" => [
                    "fieldname" => "EndTime",
                    "id" => "end-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date and time",
                    "class" => ""
                ],
                "Time Logged v Estimate" => [
                    "fieldname" => "TimeLoggedVsEstimate",
                    "id" => "time-worked",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoiced?" => [
                    "fieldname" => "Invoiced",
                    "id" => "invoiced",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoice Number" => [
                    "fieldname" => "InvoiceNumber",
                    "id" => "invoice-number",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoiced Time" => [
                    "fieldname" => "InvoicedTime",
                    "id" => "invoice-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoice Comments" => [
                    "fieldname" => "InvoiceComments",
                    "id" => "invoice-comments",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Status" => [
                    "fieldname" => "TStatus",
                    "id" => "task-status",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Notes" => [
                    "fieldname" => "TNotes",
                    "id" => "task-notes",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ]
            ];
            return $cols;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         */
        private function get_all_data_for_display($time_entries) {
            if (empty($time_entries)) {
                return "<strong>All caught up!</strong>";
            }
			//need the ampersand to pass by reference so item gets updated since we converted time_entries from object to array
            foreach ($time_entries as &$item) {
                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item["TTimeEstimate"]));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item["LoggedHours"]), sanitize_text_field($item["LoggedMinutes"]));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
				$item["TimeLoggedVsEstimate"] = [
					"value" => $hours_logged . $percent_time_logged,
					"class" => $time_worked_vs_estimate_class
				];
            }
            return $time_entries;
        }

        
        /**
         * Create Table
         * 
         */
        private function create_table($time_entries) {            
            $fields = $this->get_table_fields();
            $args["class"] = ["tt-table", "pending-time-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $time_entries, $args, "tt_time", "TimeID");
            return $table;
        }

    } //close class

} //close if class exists