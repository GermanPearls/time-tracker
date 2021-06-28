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









        /**
         * Create table
         * 
         */
        private function old_get_html($type) {
            if ($type == "open_tasks") {
                $tasks = $this->open_tasks;
            } else {
                $tasks = $this->all_tasks;
            }

            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            $table .= "<table class=\"tt-table task-list-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>ID</th>";
            $table .= "<th>Client</th>";
            $table .= "<th>Project ID</th>";
            $table .= "<th>Project</th>";
            $table .= "<th>Type</th>";            
            $table .= "<th>Task</th>";
            $table .= "<th>Due Date</th>";
            $table .= "<th>Status</th>";
            $table .= "<th>Date Added</th>";
            $table .= "<th>Time Logged vs Estimate</th>";
            $table .= "<th>Notes</th>";             
            $table .= "</tr></thead>";

            //Create body
            foreach ($tasks as $item) {
                $ticket = sanitize_text_field($item->TaskID) . "-" . sanitize_text_field($item->TDescription);
                
                //evaluate due date and current status, apply class based on result
                $duedate = sanitize_text_field($item->TDueDate);
                $status = sanitize_text_field($item->TStatus);
                $due_date_class = $this->get_due_date_class($duedate, $status);
                $due_date_formatted = date_format(\DateTime::createFromFormat("Y-m-d", $duedate), "n/j/y") ? date_format(\DateTime::createFromFormat("Y-m-d", $duedate), "n/j/y") : "";

                //evaluate time worked vs estimate, format data to display and apply css class based on result
                $hours_logged = intval(sanitize_text_field($item->LoggedHours)) + round(intval(sanitize_text_field($item->LoggedMinutes))/60,2);
                $time_estimate_parts = explode(":", sanitize_text_field($item->TTimeEstimate));
                $time_estimate_formatted = round((float)$time_estimate_parts[0] + ((float)$time_estimate_parts[1]/60),2);
                if ((sanitize_text_field($item->TTimeEstimate) == 0 ) or (sanitize_text_field($item->TTimeEstimate) == null)) {
                    $percent_time_logged = "";
                    $time_worked_display = $hours_logged;
                } else {
                    $percent_time_number = round($hours_logged / $time_estimate_formatted * 100);
                    $percent_time_logged = "<br/>" . $percent_time_number . "%<br/>";
                    if ($percent_time_number > 100) {
                        $percent_time_number = 100;
                    }
                    $percent_time_logged .= "<div style='display:inline-block;width:100px;height:20px;border:1px solid black;'>";
                    $percent_time_logged .= "<div style='background-color:green;height:20px;float:left;width:" . $percent_time_number . "px;'></div>";
                    $percent_time_logged .= "<div style='background-color:red;height:20px;'></div></div>"; 
                    $time_worked_display = $hours_logged . " / " . $time_estimate_formatted;
                }                
                //this doesn't work because time estimate can be over 24 hours so it can't be converted to a date/time object
                //$time_estimate_formatted = round( ( (DateTimeImmutable::createFromFormat('H:i:s', $item->TTimeEstimate))->format('G') + (DateTimeImmutable::createFromFormat('H:i:s', $item->TTimeEstimate))->format('i')/60),2);
                //number_format($item->TTimeEstimate,2)'
                if ( ($percent_time_logged <> "") and (round($hours_logged / $time_estimate_formatted * 100) > 100) ) {
                    $time_worked_vs_estimate_class = "over-time-estimate";
                } else {
                    $time_worked_vs_estimate_class = "";
                }

                $taskid = sanitize_text_field($item->TaskID);
                $dateadded = date_format(\DateTimeImmutable::createFromFormat("Y-m-d G:i:s", sanitize_text_field($item->TDateAdded)), 'n/j/y');
                $task_icon = $item->RecurringTaskID != null ? tt_add_recurring_task_icon() : "";
                
                //create row
                $table .= "<tr class=\"" . esc_attr($due_date_class) . " " . esc_attr($time_worked_vs_estimate_class) . "\">";

                $table .= "<td id=\"task-id\" class=\"not-editable\">" . esc_textarea($taskid) . "<br/>";
                $table .= "<button onclick='start_timer_for_task(\"" . esc_attr(sanitize_text_field($item->Company)) . "\", \"" . esc_attr($ticket) . "\")' id=\"" . esc_attr($taskid)  . "\" class=\"start-work-timer\">Start</button>";
                $table .= "<button onclick='open_detail_for_task(\"" . esc_attr($taskid) . "\")' id=\"" . esc_attr($taskid)  . "\" class=\"open-task-detail\">View</button>";
                $table .= "</td>";
                
                $table .= "<td id=\"client\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->Company)) . "</td>";
                $table .= "<td id=\"project-id\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'ProjectID'," . esc_textarea($taskid) . ")\">" . esc_textarea(sanitize_text_field($item->ProjectID)) . "</td>";
                $table .= "<td id=\"project-name\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->PName)) . "</td>";
                $table .= "<td id=\"task-type\" class=\"not-editable\">" . $task_icon . "</td>";
                $table .= "<td id=\"task-description\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TDescription'," . esc_attr($taskid)  . ")\">" . wp_kses_post(nl2br($item->TDescription)) . "</td>";
                $table .= "<td id=\"due-date\" class=\"tt-editable tt-align-center\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TDueDate'," . esc_attr($taskid)  . ")\">" . esc_textarea($due_date_formatted) . "</td>";
                $table .= "<td id=\"task-status\" class=\"tt-editable tt-align-center\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TStatus'," . esc_attr($taskid)  . ")\">" . esc_textarea($status) . "</td>";
                $table .= "<td id=\"date-added\" class=\"not-editable tt-align-center\">" . esc_textarea($dateadded) . "</td>";
                $table .= "<td id=\"time-worked\" class=\"not-editable tt-align-center\">" . esc_textarea($time_worked_display) . wp_kses_post($percent_time_logged) . "</td>";
                $table .= "<td id=\"task-notes\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TNotes'," . esc_attr($taskid)  . ")\">" . wp_kses_post(nl2br($item->TNotes)) . "</td>";
                //close out row
                $table .="</tr>";
            } // foreach loop

            $table .= "</table>";

            return $table;
        } 

        
    } //close class

} //close if class exists