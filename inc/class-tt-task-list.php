<?php
/**
 * Class Task_List
 *
 * Get and display entire task list
 * 
 * @since 1.0.0
 * @since 3.0.13 clarify column header, use new class to define table fields
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */
if ( !class_exists( 'Task_List' ) ) {


    /**
     * Class
     * 
     * @since 1.0.0
     */
    class Task_List
    {

        private $clientid;
        private $rectaskid;
        private $taskid;
        private $timeid;
        private $notes;
        private $projectid;
        private $startdate;
        private $enddate;
        private $assoc_field;
        private $assoc_id;
        private $closed_status = ["COMPLETE", "CANCELED"];
        private $status_search;


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            //$this->timeid = (isset($_GET['time-id']) ? intval($_GET['time-id']) : null);
            if (isset($_GET['task-id'])) {
                $this->taskid = intval($_GET['task-id']);
            } elseif (isset($_GET['task-number'])) {
                $this->taskid = intval($_GET['task-number']);
            } elseif (isset($_GET['task'])) {
                if ($_GET['task'] <> null) {
                    $this->taskid = get_task_id_from_name(sanitize_text_field($_GET['task']));
                }
            } else {
                $this->taskid  = null;
            };
            $this->rectaskid = (isset($_GET['recurring-task-id']) ? intval($_GET['recurring-task-id']) : null);
            if (isset($_GET['project'])) {
                if ($_GET['project'] <> null) {
                    $this->projectid = get_project_id_from_name(sanitize_text_field($_GET['project']));
                }
            } elseif (isset($_GET['project-id'])) {
                $this->projectid = intval($_GET['project-id']);
            } else {
                $this->projectid = null;
            }
            if (isset($_GET['client'])) {
                if ($_GET['client'] <> null) {
                    $this->clientid = get_client_id_from_name(sanitize_text_field($_GET['client']));
                }
            } elseif (isset($_GET['client-id'])) {
                $this->clientid = intval($_GET['client-id']);
            } else {
                $this->clientid  = null;
            };
            $this->notes = (isset($_GET['notes']) ? sanitize_text_field($_GET['notes']) : null);
            $this->startdate = (isset($_GET['start']) ? sanitize_text_field($_GET['start']) : null);
            $this->enddate = (isset($_GET['end']) ? sanitize_text_field($_GET['end']) : null);
        }


        /**
         * Get task list for a parent item
         * 
         * @since 2.2.0
         * 
         * @param string $tbl_name Name of table used for filtering by parent item.
         * @param array $parent_record Array in key-value pair with filter by field name and filter by value.
         * 
         * @return object Results of querying database for tasks.
         */
        public function get_task_list_for_parent_item($tbl_name, $parent_record) {
            $this->assoc_field = $tbl_name . "." . sanitize_text_field(array_key_first($parent_record));
            $this->assoc_id = intval($parent_record[$this->assoc_field]);
            return $this->get_all_tasks_from_db();
        }


        /**
         * Get result
         * 
         * @since 1.0.0
         * 
         * @param string $type Optional. Which table to display, open_tasks returns only open tasks, anything else returns all tasks. Default "".
         * @param string $associated_field Optional. Field to filter results, if passed. Default "".
         * @param int $associated_id Optional. ID used to filter associated field. Default 0.
         * 
         * @return string Html output showing details of task(s) queried.
         */
        public function create_table($type = "", $associated_field = "", $associated_id=0) {
            if ($associated_field <> "") {
                $this->assoc_field = sanitize_text_field($associated_field);
                $this->assoc_id = intval($associated_id);
            }
            return $this->get_html($type);
        }


        /**
         * Get table column order and table fields
         * 
         * @since 1.4.0
         * @since 3.0.13 clarify column header, change client to dropdown, change project to dropdown
         * @since 3.0.13 use new class to insert field definitions
         * 
         * @return array Multi-dimensional array of columns to display with details for each.
         */
        private function get_table_fields() {
            $flds = new Time_Tracker_Display_Fields();
            $cols = [
                "Task ID" => $flds->taskid,
                "Task" => $flds->task,
                "Client" => $flds->client_select,
                "Project ID" => $flds->project_select,
                "Type" => $flds->work_type,
                "Due Date" => $flds->due_date,
                "Status" => $flds->status,
                "Date Added" => $flds->date_added,
                "Time Logged v Estimate" => $flds->time_logged_v_estimate,
                "Notes" => $flds->notes
            ];
            return $cols;
        }    
        
        
        /**
         * Query db for OPEN tasks
         * 
         * @since 1.0.0
         * 
         * @return object Results of querying database for only open tasks.
         */
        private function get_open_tasks_from_db() {
            $this->status_search = "OPEN";
            return $this->get_all_tasks_from_db();
        }


        /**
         * Query db for ALL tasks
         * 
         * @since 1.0.0
         * 
         * @return object Results of querying database for tasks.
         */
        private function get_all_tasks_from_db() {
            $sql_string = "SELECT tt_task.*, tt_client.Company, tt_project.ProjectID, tt_project.PName,
                    NewTable.Minutes as LoggedMinutes, NewTable.Hours as LoggedHours
                FROM tt_task 
                LEFT JOIN tt_client
                    ON tt_task.ClientID = tt_client.ClientID
                LEFT JOIN tt_project
                    ON tt_task.ProjectID = tt_project.ProjectID
                LEFT JOIN (SELECT TaskID, SUM(Minute(TIMEDIFF(EndTime, StartTime))) as Minutes, SUM(Hour(TIMEDIFF(EndTime, StartTime))) as Hours FROM tt_time GROUP BY TaskID) NewTable
                    ON tt_task.TaskID = NewTable.TaskID";
            $sql_string .= $this->get_where_clauses();
            $sql_string .= $this->get_order_by();  
			$record_numbers = get_record_numbers_for_pagination_sql_query();	
			$subset_for_pagination = "LIMIT " . $record_numbers['limit'] . " OFFSET " . $record_numbers['offset'];
			$sql_string .= " " . $subset_for_pagination;
			
            return tt_query_db($sql_string);
        }


        /**
         * Get order clauses depending on type of search
         * 
         * @since 2.2.0
         * 
         * @return string Order by clause to be added to end of sql statement.
         */
        private function get_order_by() {
            if ($this->status_search == "OPEN") {
                $order_by = " ORDER BY tt_task.TDueDate ASC, tt_task.TDateAdded ASC";
            } else {
                $order_by = " ORDER BY tt_task.TaskID DESC";
            }
            return $order_by;
        }


        /**
         * Get where clauses depending on input
         * 
         * @since 2.2.0
         * 
         * @return string Where clause to be added to end of sql statement.
         */
        private function get_where_clauses() {
            global $wpdb;
            $where_clauses = array();
            $where_clause = "";
            if ($this->status_search == "OPEN") {
                foreach ($this->closed_status as $status_name) {
                    array_push($where_clauses, "UCASE(tt_task.TStatus) NOT LIKE '%" . $status_name . "%'");
                }
            }
            if (($this->assoc_id > 0) and ($this->assoc_field <>"")) {
                array_push($where_clauses, $this->assoc_field . "=" . $this->assoc_id);
            }
            if ($this->clientid <> null) {
                array_push($where_clauses, "tt_task.ClientID=" . $this->clientid);
            }
            if ($this->projectid <> null) {
                array_push($where_clauses, "tt_task.ProjectID=" . $this->projectid);
            }
            if ($this->rectaskid <> null) {
                array_push($where_clauses, "tt_task.RecurringTaskID=" . $this->rectaskid);
            }
            if ($this->taskid <> null) {
                array_push($where_clauses, "tt_task.TaskID=" . $this->taskid);
            }
            if ( ($this->startdate <> "") and ($this->startdate <> null) ) {
                array_push($where_clauses, "tt_task.StartTime >= '" . $this->startdate . "'");
            }
            if ( ($this->enddate <> "") and ($this->enddate <> null) ) {
                array_push($where_clauses, "tt_task.StartTime <= '" . $this->enddate . "'");
            }
            if ( ($this->notes <> "") and ($this->notes <> null) ) {
                //Ref: developer.wordpress.org/reference/classes/wpdb/esc_like/
                $wild = "%";
                $search_like = "'" . $wild . $wpdb->esc_like( $this->notes ) . $wild . "'";
                array_push($where_clauses, "tt_task.TNotes LIKE " . $search_like);
            }
            if ( (count($where_clauses) > 1) or ((count($where_clauses) == 1) and ($where_clauses[0] <> "")) ) {
                $where_clause = " WHERE ";
                $where_clause .= implode(" AND ", $where_clauses);
            }
            return $where_clause;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         * @since 1.4.0
         * 
         * @param string $type Which table to display, open_tasks returns only open tasks, anything else returns all tasks.
         * 
         * @return array Multi-dimensional array of tasks, with details for each task.
         */
        private function get_all_data_for_display($type) {
            $future_dates_divider_added = false;
            if ($type == "open_tasks") {
                $tasks = $this->get_open_tasks_from_db();
            } else {
                $tasks = $this->get_all_tasks_from_db();
            }

            foreach ($tasks as $item) {
                $duedate = sanitize_text_field($item->TDueDate);
                $taskstatus = sanitize_text_field($item->TStatus);
                $taskid = sanitize_text_field($item->TaskID);

                $start_work_button = "<button onclick='start_timer_for_task(\"" . esc_attr(sanitize_text_field($item->Company)) . "\", \"" . esc_attr($taskid . "-" . sanitize_text_field($item->TDescription)) . "\")' id=\"start-task-" . esc_attr($taskid)  . "\" class=\"start-work-timer tt-table-button\">Start</button>";
                $task_details_button = "<button onclick='open_detail_for_task(\"" . esc_attr($taskid) . "\")' id=\"view-task-" . esc_attr($taskid)  . "\" class=\"open-task-detail tt-table-button\">View</button>";
                $delete_task_button = "<button onclick='location.href = \"" . TT_HOME . "delete-item/?task-id=" . esc_attr($taskid) . "\"' id=\"delete-task-" . esc_attr($taskid)  . "'\" class=\"open-delete-page tt-button tt-table-button\">Delete</button>";
                $item->TaskID = [
                    "value" => $taskid,
                    "button" => [
                        $start_work_button,
                        $task_details_button,
                        $delete_task_button
                    ]
                ];

                if ( (sanitize_text_field($item->RecurringTaskID) != null) and (sanitize_text_field($item->RecurringTaskID) != "") ) {
                    $icon = tt_add_recurring_task_icon();
                    $task_category = $item->TCategory;
                    $item->TCategory = [
                        "value" => $task_category,
                        "icon" => $icon
                    ];                    
                }

                $due_date_class = get_due_date_class($duedate, $taskstatus);
                $item->TDueDate = [
                    "value" => $duedate,
                    "class" => $due_date_class
                ];

                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item->TTimeEstimate));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item->LoggedHours), sanitize_text_field($item->LoggedMinutes));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
                $item->TimeLoggedVsEstimate = [
                    "value" => $hours_logged . $percent_time_logged,
                    "class" => $time_worked_vs_estimate_class
                ];

                //put border above row if date >12 months out
                if ( ($due_date_class == "far-future-date") && ($future_dates_divider_added == false) ) {
                    $future_dates_divider_added = true;
                    foreach ($item as &$cell) {
                        $cell = $this->add_class_to_cell($cell, "tt-border-top-divider");
                    }
                }
            }
            return $tasks;
        }


        /**
         * Add class to cell
         * 
         * @since 1.0.0
         * 
         * @param array|string $cel Details for a single cell, may be value only (string) or contain value and other cell parameters (array).
         * @param string $cls Classname to be added to cell details.
         * 
         * @return array Details for a single cell in the output table, including class.
         */
        private function add_class_to_cell($cel, $cls) {
            if (is_array($cel)) {
                $cel["class"] = $cel["class"] . " " . $cls;
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
         * @return string Html output, table of task details.
         */
        private function get_html($type) {            
            $fields = $this->get_table_fields();
            $tasks = $this->get_all_data_for_display($type);                
            $args["class"] = ["tt-table", "task-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            return $tbl->create_html_table($fields, $tasks, $args, "tt_task", "TaskID");
        }
        
    }
}