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
         * Get table column order and table fields
         * 
         */
        private function get_table_fields() {
            $cols = [
                "ID" => [
                    "fieldname" => "RecurringTaskID",
                    "id" => "recurring-task-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "company-name",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Project ID" => [
                    "fieldname" => "ProjectID",
                    "id" => "project-id",
                    "editable" => false,
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
                    "fieldname" => "RTCategory",
                    "id" => "task-category",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Task" => [
                    "fieldname" =>"RTName",
                    "id" => "recurring-task-name",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Frequency" => [
                    "fieldname" => "Frequency",
                    "id" => "frequency",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Last Created" => [
                    "fieldname" => "LastCreated",
                    "id" => "last-created",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "date",
                    "class" => "tt-align-right"
                ],
                "End Repeat" => [
                    "fieldname" => "EndRepeat",
                    "id" => "end-repeat",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date",
                    "class" => "tt-align-right"
                ],
                "Notes" => [
                    "fieldname" => "RTDescription",
                    "id" => "recurring-task-description",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
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
         * Create HTML table for front end display
         * 
         */
        public function get_html() {
            $fields = $this->get_table_fields();
            $tasks = $this->recurring_tasks;
            $args["class"] = ["tt-table", "task-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $tasks, $args, "tt_recurring_task", "RecurringTaskID");
            return $table;
        }
               






           
        /**
         * Create table
         * 
         */
        private function old_get_html() {
            $tasks = $this->recurring_tasks;
            $args = [];

            //note above header row
            $table = "<div style='font-weight:bold; text-align:center;'>Note: Gray shaded cells can't be changed.</div>";
            
            //start table
            $args['class'] = ["tt-table", "task-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table .= $tbl->start_table($args);

            //header row
            $table .= $tbl->start_row();
            $columns = $this->get_column_order();
            foreach ($columns as $name=>$details) {
                $table .= $tbl->start_header_data() . $name . $tbl->close_header_data();                
            }
            $table .= $tbl->close_row();

            //data
            foreach ($tasks as $item) {
                $end_repeat_args = [];
                $end_repeat_class = "";
                $ticket = sanitize_text_field($item->RecurringTaskID) . "-" . sanitize_text_field($item->RTName);
                
                /**
                *evaluate due date and current status, apply class based on result
                *$last_created = tt_format_date_for_display(sanitize_text_field($item->LastCreated), "date_only");
                *$end_repeat = tt_format_date_for_display(sanitize_text_field($item->EndRepeat), "date_only");
                *$today = new \DateTime();
                *if ( ($end_repeat <> "") and ($end_repeat < $today) ) {
                *    $end_repeat_class = "tt-recurring-task-complete";
                *}
                **/

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
                    if ( strlen($details["columnwidth"]) > 0 ) {
                        array_push($args["class"], ".tt-col-width-" . $details["columnwidth"] . "-pct");
                    }

                    $cell = $tbl->start_data($args);

                    //sanitize and escape based on field type
                    if ($details["type"] == "text") {
                        $cell .= esc_html(sanitize_text_field($item->$sql_fieldname));
                    } elseif ($details["type"] == "long text") {
                        $cell .= stripslashes(wp_kses_post(nl2br($item->$sql_fieldname)));
                    } elseif ($details["type"] == "date") {
                        $formatted_date = tt_format_date_for_display(sanitize_text_field($item->$sql_fieldname), "date_only");
                        $cell .= esc_html($formatted_date);
                    } elseif ($details["type"] == "date and time") {
                        $formatted_date = tt_format_date_for_display(sanitize_text_field($item->$sql_fieldname), "date_and_time");
                        $cell .= esc_html($formatted_date);
                    }  elseif ($details["type"] == "email") {
                        $cell .= esc_html(sanitize_email($item->$sql_fieldname));
                    } else {
                        $cell .= esc_html(sanitize_text_field($item->$sql_fieldname));
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