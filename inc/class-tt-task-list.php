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
            $this->get_open_tasks_from_db();
            $this->get_all_tasks_from_db();
        }


        /**
         * Get result
         * 
         */
        public function create_table($type) {
            return $this->get_html($type);
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
            $this->open_tasks = $sql_result;
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
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            $this->all_tasks = $sql_result;
        }



        /**
         * Get Due Date Class
         * 
         */
        private function get_due_date_class($duedate, $status) {
            if ( ($duedate == "0000-00-00") || ($duedate == null) ) {
                $due_date_formatted = "";
                $due_date_class = "no-date";
            } else {
                $due_date_formatted = date_format(\DateTime::createFromFormat("Y-m-d", $duedate), "n/j/y");
                if (\DateTime::createFromFormat("Y-m-d", $duedate) <= new \DateTime() AND $status<>"Canceled" AND $status<>"Complete") {
                    $due_date_class = "late-date";
                } elseif (\DateTime::createFromFormat("Y-m-d", $duedate) <= new \DateTime(date("Y-m-d", strtotime("+7 days"))) AND $status<>"Canceled" AND $status<>"Complete") {
                    $due_date_class = "soon-date";
                } elseif (\DateTime::createFromFormat("Y-m-d", $duedate) > new \DateTime(date("Y-m-d", strtotime("+90 days"))) AND $status<>"Canceled" AND $status<>"Complete") {
                    $due_date_class = "on-hold-date";
                } else {
                    $due_date_class = "ok-date";
                }
            }
            return $due_date_class;
        }


        /**
         * Create table
         * 
         */
        private function get_html($type) {
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
                $table .= "<td id=\"project-id\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'ProjectID'," . esc_textarea($taskid) . ")\">" . esc_textarea(sanitize_text_field($item->ProjectID)) . "</td>";
                $table .= "<td id=\"project-name\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->PName)) . "</td>";
                $table .= "<td id=\"task-type\" class=\"not-editable\">" . $task_icon . "</td>";
                $table .= "<td id=\"task-description\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TDescription'," . esc_attr($taskid)  . ")\">" . wp_kses_post(nl2br($item->TDescription)) . "</td>";
                $table .= "<td id=\"due-date\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TDueDate'," . esc_attr($taskid)  . ")\">" . esc_textarea($due_date_formatted) . "</td>";
                $table .= "<td id=\"task-status\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TStatus'," . esc_attr($taskid)  . ")\">" . esc_textarea($status) . "</td>";
                $table .= "<td id=\"date-added\" class=\"not-editable\">" . esc_textarea($dateadded) . "</td>";
                $table .= "<td id=\"time-worked\" class=\"not-editable\">" . esc_textarea($time_worked_display) . wp_kses_post($percent_time_logged) . "</td>";
                $table .= "<td id=\"task-notes\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TNotes'," . esc_attr($taskid)  . ")\">" . wp_kses_post(nl2br($item->TNotes)) . "</td>";
                //close out row
                $table .="</tr>";
            } // foreach loop

            $table .= "</table>";

            return $table;
        } 

        
    } //close class

} //close if class exists