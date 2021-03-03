<?php
/**
 * Class Recurring_Task_List
 *
 * Get and display entire task list
 * 
 * @since 1.1.1
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Recurring_Task_List' ) ) {


    /**
     * Class
     * 
     */
    class Recurring_Task_List
    {


        /**
         * Constructor
         * 
         */
        public function __construct() {
            $this->get_recurring_tasks_from_db();
        }


        /**
         * Get html result
         * 
         */
        public function create_table() {
            return $this->get_html();
        }


        /**
         * Get table column order
         * 
         */
        private function get_column_order() {
            $cols = [
                "ID" => [
                    "fieldname" => "RecurringTaskID",
                    "id" => "recurring-task-id",
                    "editable" => false
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "company-name",
                    "editable" => false
                ],
                "Project ID" => [
                    "fieldname" => "ProjectID",
                    "id" => "project-id",
                    "editable" => false
                ],
                "Project" => [
                    "fieldname" => "PName",
                    "id" => "project-name",
                    "editable" => false
                ],
                "Type" => [
                    "fieldname" => "RTCategory",
                    "id" => "task-category",
                    "editable" => false
                ],
                "Task" => [
                    "fieldname" =>"RTName",
                    "id" => "recurring-task-name",
                    "editable" => true
                ],
                "Frequency" => [
                    "fieldname" => "Frequency",
                    "id" => "frequency",
                    "editable" => false
                ],
                "Last Created" => [
                    "fieldname" => "LastCreated",
                    "id" => "last-created",
                    "editable" => false
                ],
                "End Repeat" => [
                    "fieldname" => "EndRepeat",
                    "id" => "end-repeat",
                    "editable" => true
                ],
                "Notes" => [
                    "fieldname" => "RTDescription",
                    "id" => "recurring-task-description",
                    "editable" => true
                ]
            ];
            return $cols;
        }
        
        
        /**
         * Query db for recurring tasks
         * 
         */
        private function get_recurring_tasks_from_db() {
            global $wpdb;

            $sql_string = "SELECT tt_recurring_task.*, tt_client.Company, tt_project.ProjectID, tt_project.PName
                FROM tt_recurring_task 
                LEFT JOIN tt_client
                    ON tt_recurring_task.ClientID = tt_client.ClientID
                LEFT JOIN tt_project
                    ON tt_recurring_task.ProjectID = tt_project.ProjectID
                ORDER BY tt_recurring_task.RecurringTaskID ASC";
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);            
            $this->recurring_tasks = $sql_result;
        }


        /**
         * Create table
         * 
         */
        private function get_html() {
            $tasks = $this->recurring_tasks;
            $args = [];

            //Begin creating table and header row
            $table = "<div style='font-weight:bold; text-align:center;'>Note: Gray shaded cells can't be changed.</div>";
            
            $args['class'] = ["tt-table", "task-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table .= $tbl->start_table($args);

            $table .= $tbl->start_row();

            $columns = $this->get_column_order();
            foreach ($columns as $name=>$details) {
                $table .= $tbl->start_header_data() . $name . $tbl->close_header_data();                
            }

            $table .= $tbl->close_row();


            //Add Data
            foreach ($tasks as $item) {
                $end_repeat_args = [];
                $end_repeat_class = "";
                $ticket = sanitize_text_field($item->RecurringTaskID) . "-" . sanitize_text_field($item->RTName);
                
                //evaluate due date and current status, apply class based on result
                $last_created = tt_format_date_for_display(sanitize_text_field($item->LastCreated), "date_only");
                $end_repeat = tt_format_date_for_display(sanitize_text_field($item->EndRepeat), "date_only");
                $today = new \DateTime();
                if ( ($end_repeat <> "") and ($end_repeat < $today) ) {
                    $end_repeat_class = "tt-recurring-task-complete";
                }

                $row = $tbl->start_row();
                
                foreach ($columns as $header=>$details) {
                    $sql_fieldname = $details["fieldname"];
                    $args = [];
                    $args["id"] = $details["id"];
                    if ($details["editable"]) {
                        $args["class"] = ["editable"];
                        $args["contenteditable"] = "true";
                        $args["onBlur"] = "updateDatabase(this, 'tt_recurring_task', 'RecurringTaskID', '" . $sql_fieldname . "', '" . $item->RecurringTaskID . "')";
                    } else {
                        $args["class"] = ["not-editable"];
                    }

                    $cell = $tbl->start_data($args);

                    if ($sql_fieldname == "LastCreated") {
                        $cell .= $last_created;
                    } elseif ($sql_fieldname == "EndRepeat") {
                        array_push($args["class"], $end_repeat_class);
                        $cell = $tbl->start_data($args);
                        $cell .= $end_repeat;
                    } else {
                        $cell .=  esc_textarea(sanitize_text_field($item->$sql_fieldname));    
                    }

                    $cell .= $tbl->close_data();
                    $row .= $cell;
                }

                $row .= $tbl->close_row();
                $table .= $row;
                
            } // foreach row loop

            $table .= $tbl->close_table();

            return $table;
        } 

        
    } //close class

} //close if class exists