<?php
/**
 * Time Tracker Display Pending Time (ie: hasn't been billed yet)
 *
 * Sort pending time by bill-to and display in tables
 * 
 * @since 1.0.0
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
     * @since 1.0.0
     */
    class Pending_Time
    {

        
        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
        }


        /**
         * Public function for child class
         * 
         * @since 2.4.0
         * 
         * @return array Data grouped by bill to name.
         */
        public function get_data_for_export() {
            $data = $this->get_time_grouped_by_billto();
            return $data;
        }


        /**
         * Create front end display of time not yet billed
         * 
         * @since 1.0.0
         * 
         * @return string Html to display as output.
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

                //DETAILS - TABLE WITHIN EACH SECTION
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
         * @since 1.0.0
         * @since 3.0.13 Included billing rate in query
         * 
         * @return array Array of time entries as received from sql query.
         */
        private function get_pending_time_from_db() {
            $sql_string = "SELECT tt_time.*, tt_client.Company, tt_client.BillTo, tt_client.BillingRate, tt_task.TaskID, tt_task.TDescription, tt_task.TTimeEstimate, tt_task.TStatus,
                    Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes,
                    Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours
                FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID
                WHERE (tt_time.Invoiced = \"\" OR tt_time.Invoiced IS NULL) AND tt_client.Billable = true
                ORDER BY tt_client.BillTo ASC, tt_time.ClientID ASC, tt_time.TaskID ASC, tt_time.StartTime ASC";
            return tt_query_db($sql_string, "array");
        }

                        
            
            
        /**
         * Regroup data by Bill To
         * 
         * @since 1.0.0
         * 
         * @return array Array of time, grouped by bill to name.
         */
        private function get_time_grouped_by_billto() {
            $pending_time = $this->get_pending_time_from_db();
            $grouped_time = array();
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
            }
            return $grouped_time;
        }


        /**
         * Get table column order and table fields
         * 
         * @since 2.4.0
         * @since 3.0.13 Updated to convert Invoice columns to widget to conserve space. Updated to use new field definition class.
         * 
         * @return array Multi-dimensional array, list of columns, with details for each column as key-value pairs.
         */
        private function get_table_fields() {
            $flds = new Time_Tracker_Display_Fields();
            $cols = [
                "Client" => $flds->client_select,
                "Task" => $flds->taskid,
                "Task Description" => $flds->task,
                "Time ID" => $flds->timeid,
                "Start Time" => $flds->start_time,
                "End Time" => $flds->end_time,
                "Time Logged v Estimate" => $flds->time_logged_v_estimate,
                "Invoiced Details" => $flds->invoice_details,
                "Task Status" => $flds->status,
                "Notes" => $flds->notes
            ];
            return $cols;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         * @since 2.4.0
         * 
         * @param array $time_entries Time entry details in array.
         * 
         * @return string Html output for display.
         */
        private function get_all_data_for_display($time_entries) {
            if (empty($time_entries)) {
                return "<strong>All caught up!</strong>";
            }
            $lastclient = "";
			//need the ampersand to pass by reference so item gets updated since we converted time_entries from object to array
            foreach ($time_entries as $i => &$item) {
                //style based on time logged vs estimate
                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item["TTimeEstimate"]));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item["LoggedHours"]), sanitize_text_field($item["LoggedMinutes"]));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
				$item["TimeLoggedVsEstimate"] = [
					"value" => $hours_logged . $percent_time_logged,
					"class" => $time_worked_vs_estimate_class
				];
                
                //add view task detail button
                $view_task_detail_button = "<button onclick='location.href=\"" . TT_HOME . "task-detail/?task-id=" . esc_attr($item["TaskID"]) . "\"' id=\"view-task-detail-" . esc_attr($item["TaskID"]) . "'\" class=\"open-task-detail-page tt-button tt-table-button\">View</button>";
                $item["TaskID"] = [
                    "value" => $item["TaskID"],
                    "button" => [
                        $view_task_detail_button
                    ]
                ];

                //add separation between companies
                if ($i != 0) {
                    if ($item["Company"] != $lastclient) {
                        foreach ($item as &$cell) {
                            $cell = $this->add_class_to_cell($cell, "tt-row-top-divider");
                        }
                    }
                }
                $lastclient = is_array($item["Company"]) ? $item["Company"]["value"] : $item["Company"];
            }
            return $time_entries;
        }


        /**
         * Add class to cell
         * 
         * @since 2.4.0
         * 
         * @param array|string|int $cel Value for current cell, or array defining value and details to be used to create cell.
         * @param string $cls Class name to be added to cell.
         */
        private function add_class_to_cell($cel, $cls) {
            if (is_array($cel)) {
                if (array_key_exists("class", $cel)) {
                    $cel["class"] = $cel["class"] . " " . $cls;
                } else {
                    $cel["class"] = $cls;
                }
            } else {
                $cel = [
                    "value" => $cel,
                    "class" => $cls
                ];
            }
            return $cel;
        }

        
        /**
         * Create Table
         * 
         * @since 1.0.0
         * 
         * @param array $time_entries Array of time entries.
         * 
         * @return string Html table to display.
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
