<?php
/**
 * Class Task_List
 *
 * Get and display entire task list
 * 
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Task_List' ) ) {


    /**
     * Class
     * 
     */
    class Task_List
    {


        /**
         * Constructor
         * 
         */
        public function __construct() {

        }


        /**
         * Get result
         * 
         */
        public function create_table($type) {
            return $this->get_html($type);
        }


        /**
         * Get table column order and table fields
         * 
         */
        private function get_table_fields() {
            $cols = [
                "ID" => [
                    "fieldname" => "TaskID",
                    "id" => "task-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "client",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Project ID" => [
                    "fieldname" => "ProjectID",
                    "id" => "project-id",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Project" => [
                    "fieldname" => "PName",
                    "id" => "project-name",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Type" => [
                    "fieldname" => "TCategory",
                    "id" => "task-type",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Task" => [
                    "fieldname" =>"TDescription",
                    "id" => "task-description",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Due Date" => [
                    "fieldname" => "TDueDate",
                    "id" => "due-date",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date",
                    "class" => "tt-align-right"
                ],
                "Status" => [
                    "fieldname" => "TStatus",
                    "id" => "task-status",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Date Added" => [
                    "fieldname" => "TDateAdded",
                    "id" => "date-added",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "date",
                    "class" => "tt-align-right"
                ],
                "Time Logged v Estimate" => [
                    "fieldname" => "TimeLoggedVsEstimate",
                    "id" => "time-worked",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => "tt-align-right"
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
         * Query db for OPEN tasks
         * 
         */
        private function get_open_tasks_from_db() {
            //Connect to Time Tracker Database
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;

            $sql_string = "SELECT tt_task.*, tt_client.Company, tt_project.ProjectID, tt_project.PName,
                    NewTable.Minutes as LoggedMinutes, NewTable.Hours as LoggedHours
                FROM tt_task 
                LEFT JOIN tt_client
                    ON tt_task.ClientID = tt_client.ClientID
                LEFT JOIN tt_project
                    ON tt_task.ProjectID = tt_project.ProjectID
                LEFT JOIN (SELECT TaskID, SUM(Minute(TIMEDIFF(EndTime, StartTime))) as Minutes, SUM(Hour(TIMEDIFF(EndTime, StartTime))) as Hours FROM tt_time GROUP BY TaskID) NewTable
                    ON tt_task.TaskID = NewTable.TaskID
                WHERE tt_task.TStatus <> \"Closed\" AND tt_task.TStatus <> \"Canceled\" AND tt_task.TStatus <> \"Complete\"
                ORDER BY tt_task.TDueDate ASC, tt_task.TDateAdded ASC";			
            
			$sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);            
            return $sql_result;
        }


        /**
         * Query db for ALL tasks
         * 
         */
        private function get_all_tasks_from_db() {
            //Connect to Time Tracker Database
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;

            $sql_string = "SELECT tt_task.*, tt_client.Company, tt_project.ProjectID, tt_project.PName,
                    NewTable.Minutes as LoggedMinutes, NewTable.Hours as LoggedHours
                FROM tt_task 
                LEFT JOIN tt_client
                    ON tt_task.ClientID = tt_client.ClientID
                LEFT JOIN tt_project
                    ON tt_task.ProjectID = tt_project.ProjectID
                LEFT JOIN (SELECT TaskID, SUM(Minute(TIMEDIFF(EndTime, StartTime))) as Minutes, SUM(Hour(TIMEDIFF(EndTime, StartTime))) as Hours FROM tt_time GROUP BY TaskID) NewTable
                    ON tt_task.TaskID = NewTable.TaskID
                ORDER BY tt_task.TaskID DESC";    
			
			$record_numbers = get_record_numbers_for_pagination_sql_query();	
			$subset_for_pagination = "LIMIT " . $record_numbers['limit'] . " OFFSET " . $record_numbers['offset'];
			$sql_string .= " " . $subset_for_pagination;
			
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $sql_result;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         */
        private function get_all_data_for_display($type) {
            if ($type == "open_tasks") {
                $tasks = $this->get_open_tasks_from_db();
            } else {
                $tasks = $this->get_all_tasks_from_db();
            }

            foreach ($tasks as $item) {
                $duedate = sanitize_text_field($item->TDueDate);
                $taskstatus = sanitize_text_field($item->TStatus);
                $taskid = sanitize_text_field($item->TaskID);

                $start_work_button = "<button onclick='start_timer_for_task(\"" . esc_attr(sanitize_text_field($item->Company)) . "\", \"" . esc_attr($taskid . "-" . sanitize_text_field($item->TDescription)) . "\")' id=\"task-" . esc_attr($taskid)  . "\" class=\"start-work-timer\">Start</button>";
                $task_details_button = "<button onclick='open_detail_for_task(\"" . esc_attr($taskid) . "\")' id=\"task-" . esc_attr($taskid)  . "\" class=\"open-task-detail\">View</button>";
                $item->TaskID = [
                    "value" => $taskid,
                    "button" => [
                        $start_work_button,
                        $task_details_button
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
            }
            return $tasks;
        }


        /**
         * Create Table
         * 
         */
        private function get_html($type) {            
            $fields = $this->get_table_fields();
            $tasks = $this->get_all_data_for_display($type);
            $args["class"] = ["tt-table", "task-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $tasks, $args, "tt_task", "TaskID");
            return $table;
        }
        
    } //close class

} //close if class exists