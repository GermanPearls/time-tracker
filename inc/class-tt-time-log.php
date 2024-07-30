<?php
/**
 * Class Time_Log
 *
 * CLASS TO DISPLAY TIME LOG TABLE
 * 
 * @since 1.0.0
 * @since 3.0.13 correct typo in column heading
 * @since 3.0.13 using new class to define table fields
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

if ( !class_exists( 'Time_Log' ) ) {


    class Time_Log
    {

        private $clientid = null;
        private $projectid = null;
        private $rectaskid = null;
        private $taskid = null;
        private $timeid = null;        
        private $notes = null;
        private $startdate = null;
        private $enddate = null;
        private $record_limit = true;


        /**
         * Constructor
         * 
         * @since 1.0.0
         */        
        public function __construct() {
            $this->timeid = (isset($_GET['time-id']) ? intval($_GET['time-id']) : null);
            if (isset($_GET['task-id'])) {
                $this->taskid = intval($_GET['task-id']);
            } elseif (isset($_GET['task-number']) && $_GET['task-number'] <> "") {
                $this->taskid = intval($_GET['task-number']);
            } elseif (isset($_GET['task'])) {
                $this->taskid = (! is_null($_GET['task']) and $_GET['task'] <> "") ? get_task_id_from_name(sanitize_text_field($_GET['task'])) : null;
            }
            $this->rectaskid = (isset($_GET['recurring-task-id']) and $_GET['recurring-taask-id'] <> "") ? intval($_GET['recurring-task-id']) : null;
            if (isset($_GET['project-name'])) {
                $this->projectid = (! is_null($_GET['project-name']) and $_GET['project-name'] <> "") ? get_project_id_from_name(sanitize_text_field($_GET['project-name'])) : null;
            } elseif (isset($_GET['project-id'])) {
                $this->projectid = (! is_null($_GET['project-id']) and $_GET['project-id'] <> "") ? intval($_GET['project-id']) : null;
            }
            if (isset($_GET['client-name'])) {
                $this->clientid = (! is_null($_GET['client-name']) and $_GET['client-name'] <> "") ? get_client_id_from_name(sanitize_text_field($_GET['client-name'])) : null;
            } elseif (isset($_GET['client-id'])) {
				$this->clientid = (! is_null($_GET['client-id']) and $_GET['client-id'] <> "") ? intval($_GET['client-id']) : null;
            }
            $this->notes = (isset($_GET['notes']) ? sanitize_text_field($_GET['notes']) : null);
            $this->startdate = (isset($_GET['first-date']) ? sanitize_text_field($_GET['first-date']) : null);
            $this->enddate = (isset($_GET['last-date']) ? sanitize_text_field($_GET['last-date']) : null);
        }


        /**
         * Get results
         * 
         * @since 1.0.0
         * 
         * @return string html output
         */
        public function create_table() {
            return $this->get_html();
        }
        
        
        /**
         * Get record count
         * 
         * @since 3.0.5
         * 
         * @return int Count of records to be displayed
         */
        public function get_record_count() {
            return $this->get_time_log_record_count();
        }        
        
          
        /**
         * Get count of results
         * 
         * @since 3.0.5
         * 
         * @return int Count of records to be displayed
         * 
         */
        private function get_time_log_record_count() {
            $result = tt_query_db($this->create_sql_string("count"), "array");
            if ($result) {
                return intval($result[0]["TimeCount"]);
            }
        }
        

        /**
         * Get data from db - returns object
         * 
         * @since 1.0.0
         * 
         * @return object Time entries from database
         */
        private function get_time_log_from_db() {
            return tt_query_db($this->create_sql_string(), "object");
        }


        /**
         * Get data from db - return array
         * 
         * @since 2.2.0
         * 
         * @return array Time entries from database
         */
        protected function get_time_log_array_from_db() {
            return tt_query_db($this->create_sql_string(), "array");
        }


        /**
         * Prepare sql string
         * 
         * @since 1.0.0
         * 
         * @return string Complete sql string used to query database for time entries
         */
        private function create_sql_string($type="select") {   
            if ($type=="select") {
                $sql_string = $this->get_select_clause() . $this->get_from_clause() . $this->get_where_clauses() . $this->get_order_by_clause() . $this->get_limit_parameter();
            } elseif ($type=="count") {
                $sql_string = $this->get_count_clause() . $this->get_from_clause() . $this->get_where_clauses();
            }
            return $sql_string;
        }


        /**
         * Prepare sql string - SELECT portion
         * 
         * @since 3.0.5
         * 
         * @return string First part of sql string, indicating which fields to return 
         */
        private function get_select_clause() {
            $selectpart = "SELECT tt_time.*, tt_client.Company, tt_client.BillTo, tt_task.ProjectID, tt_task.TCategory, tt_task.RecurringTaskID, tt_task.TDescription, tt_task.TStatus, tt_task.TTimeEstimate,
                Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes,
                Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours";
            return $selectpart;
        }
        
        
        /**
         * Prepare sql string - SELECT portion for COUNT only
         * 
         * @since 3.0.5
         * 
         * @return string First part of sql string, indicating which fields to return, when only looking for count of records returned
         */
        private function get_count_clause() {
            $selectpart = "SELECT COUNT(tt_time.TimeID) as TimeCount";
            return $selectpart;
        }


        /**
         * Prepare sql string - FROM portion
         * 
         * @since 3.0.5
         * 
         * @return string From part of sql string, indicating from which table(s) we are getting data
         */
        private function get_from_clause() {
            $sql_from = " FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID";
            return $sql_from;
        }

        
        /**
         * Prepare sql string - ORDER portion
         * 
         * @since 3.0.5
         * 
         * @return string Part of sql string which indicates how records should be ordered
         */
        private function get_order_by_clause() {
            $orderby = " ORDER BY tt_time.StartTime DESC";
            return $orderby;
        }

        
        /**
         * Prepare sql string - LIMIT parameter
         * 
         * @since 2.3.0
         * 
         * @return string Part of sql string which limits the number of records to be returned. Used for pagination
         */
        private function get_limit_parameter() {
            if ($this->record_limit == false) {
                return "";
            } else {
                $record_numbers = get_record_numbers_for_pagination_sql_query();	
                $subset_for_pagination = " LIMIT " . $record_numbers['limit'] . " OFFSET " . $record_numbers['offset'];
                return $subset_for_pagination;                
            }
        }


        /**
         * Get where clauses depending on input
         * 
         * @since 2.2.0
         * 
         * @return string Part of sql string which defines criteria for records to be returned
         */
        private function get_where_clauses() {
            global $wpdb;
            $where_clauses = array();
            $where_clause = "";
            if (! is_null($this->clientid) or $this->clientid === 0) {
                array_push($where_clauses, "tt_time.ClientID = " . $this->clientid);
            }
            if (! is_null($this->projectid) or $this->projectid === 0) {
                //no project id field in time table - so we have to get tasks and then time associated with those tasks
                array_push($where_clauses, "tt_task.ProjectID = " . $this->projectid);
            }
            if (! is_null($this->rectaskid) or $this->rectaskid === 0) {
                //no recurring task id field in time table - so we have to get tasks and then time associated with those tasks
                array_push($where_clauses, "tt_task.RecurringTaskID = " . $this->rectaskid);
            }
            if ( $this->taskid != null or $this->taskid === 0) {
                array_push($where_clauses, "tt_time.TaskID = " . $this->taskid);
            }
            if ( ($this->timeid <> "") and (! is_null($this->timeid)) and ($this->timeid <> "null") ) {
                array_push($where_clauses, "tt_time.TimeID = " . $this->timeid);
            }
            if ( ($this->startdate <> "") and (! is_null($this->startdate)) ) {
                array_push($where_clauses, "tt_time.StartTime >= '" . $this->startdate . " 00:00:01'");
            }
            if ( ($this->enddate <> "") and (! is_null($this->enddate)) ) {
                array_push($where_clauses, "tt_time.EndTime <= '" . $this->enddate . " 23:59:59'");
            }
            if ( ($this->notes <> "") and (! is_null($this->notes)) ) {
                //Ref: developer.wordpress.org/reference/classes/wpdb/esc_like/
                $wild = "%";
                $search_like = "'" . $wild . $wpdb->esc_like( $this->notes ) . $wild . "'";
                array_push($where_clauses, "tt_time.TNotes LIKE " . $search_like);
            }
            if ( (count($where_clauses) > 1) or ((count($where_clauses) == 1) and ($where_clauses[0] <> "")) ) {
                $where_clause = " WHERE ";
                $where_clause .= implode(" AND ", $where_clauses);
            }
            return $where_clause;
        }


        /**
         * Set pagination property
         * 
         * @since 3.0.5
         * 
         */
        protected function remove_record_limit() {
            $this->record_limit = false;
        }
        
        
        /**
         * Get table column order and table fields
         * 
         * @since 1.4.0
         * @since 3.0.13 Clarify column header. Change client to dropdown. Change project to dropdown. Use new field definition class.
         * 
         * @return array Multi-dimensional array of columns to display with details for each.
         */
        private function get_table_fields() {
            $flds = new Time_Tracker_Display_Fields();
            $cols = [
                "Time ID" => $flds->timeid,
                "Client" => $flds->client_select,
                "Task" => $flds->task,
                "Type" => $flds->work_type,
                "Start Time" => $flds->start_time,
                "End Time" => $flds->end_time,
                "Time Logged v Estimate" => $flds->time_logged_v_estimate,                
                "Status" => $flds->status,
                "Invoice Details" => $flds->invoice_details,
                "Notes" => $flds->notes,
                "Follow Up" => $flds->follow_up
            ];
            return $cols;
        } 


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         * @since 1.4.0
         * 
         * @return object Time entries to be displayed, with additional items to display in table (ie: buttons, icons)
         */
        private function get_all_data_for_display() {
            $time_entries = $this->get_time_log_from_db();
            //$time_entries = $this->time_details;

            foreach ($time_entries as $item) {
                if ( (! is_null(sanitize_text_field($item->RecurringTaskID))) and (sanitize_text_field($item->RecurringTaskID) != "") ) {
                    $icon = tt_add_recurring_task_icon();
                    $task_category = $item->TCategory;
                    $item->TCategory = [
                        "value" => $task_category,
                        "icon" => $icon
                    ];                    
                }

                $delete_time_button = "<button onclick='location.href = \"" . TT_HOME . "delete-item/?time-id=" . esc_attr($item->TimeID) . "\"' id=\"delete-time-" . esc_attr($item->TimeID)  . "'\" class=\"open-delete-page tt-button tt-table-button\">Delete</button>";
                $item->TimeID = [
                    "value" => $item->TimeID,
                    "button" => [
                        $delete_time_button
                    ]
                ];

                $view_task_detail_button = "<button onclick='location.href=\"" . TT_HOME . "task-detail/?task-id=" . esc_attr($item->TaskID) . "\"' id=\"view-task-detail-" . esc_attr($item->TaskID) . "'\" class=\"open-task-detail-page tt-button tt-table-button\">View</button>";
                $item->TaskID = [
                    "value" => $item->TaskID,
                    "button" => [
                        $view_task_detail_button
                    ]
                ];

                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item->TTimeEstimate));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item->LoggedHours), sanitize_text_field($item->LoggedMinutes));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
                $item->TimeLoggedVsEstimate = [
                    "value" => $hours_logged . $percent_time_logged,
                    "class" => $time_worked_vs_estimate_class
                ];
            }
            return $time_entries;
        }


        /**
         * Create Table
         * 
         * @since 1.0.0
         * 
         * @return string Html table for display
         */
        public function get_html() {            
            $fields = $this->get_table_fields();
            $time_entries = $this->get_all_data_for_display();
            $args["class"] = ["tt-table", "time-log-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $time_entries, $args, "tt_time", "TimeID");
            return $table;
        }
        
    }
}