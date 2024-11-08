<?php
/**
 * Class Task_Edit
 *
 * CLASS TO DISPLAY DETAILS OF TASK WITH CAPABILITY OF EDITING
 * 
 * @since 3.1.0
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 3.1.0
 */
if ( !class_exists( 'Task_Edit' ) ) {

    /**
     * Class
     * 
     * @since 3.1.0
     */  
    class Task_Edit
    {
        
        
        /**
         * Class variables
         * 
         * @since 3.1.0
         */ 
        private $taskid;


        /**
         * Constructor
         * 
         * @since 3.1.0
         */ 
        public function __construct() {
            if (isset($_GET['task-id'])) {
                $this->taskid = sanitize_text_field($_GET['task-id']);
            }
        }


        /**
         * Get results
         * 
         * @since 3.1.0
         * 
         * @return string Html output showing details of one task, including time worked.
         */ 
        public function generate_output_for_display() {
            return $this->get_html();
        }
        
        
        /**
         * Query db for task details
         * 
         * @since 3.1.0
         * 
         * @return object Results of querying database for task details and time worked for a specific task.
         */ 
        private function get_task_details_from_db() {
            global $wpdb;
            $sql_string_format = "SELECT tt_task.TaskID, tt_task.TDescription, tt_task.ClientID, tt_task.ProjectID,
                    tt_task.TStatus, tt_task.TTimeEstimate, tt_task.TDateAdded, tt_task.TDueDate,
                    tt_task.TNotes TaskNotes, tt_client.Company, tt_project.ProjectID, tt_project.PName
                FROM tt_task
                LEFT JOIN tt_client
                    ON tt_task.ClientID = tt_client.ClientID
                LEFT JOIN tt_project
                    ON tt_task.ProjectID = tt_project.ProjectID
                WHERE tt_task.TaskID = %s";

            $sql_string = $wpdb->prepare($sql_string_format, $this->taskid);
            //tt_query_db returns type object by default
            return tt_query_db($sql_string);
        }
        
        
        /**
         * Add Start Work Timer Button
         *
         * @since 3.1.0
         * 
         * @return string Html button to start work for specific task.
         */
        private function add_start_work_button($tsk_id, $tsk_desc, $company) {
            //$display .= "<button id='tt-start-work-on-task-" . $this->taskid . "' class='tt-button tt-start-work-timer' onclick='start_timer_for_task("Logically Tech", "0-Undefined");'>Start Working</button>";
            $btn = "<button ";
            $btn .= "id=\"tt-start-work-for-" . $tsk_id . "\" ";
            $btn .= "class=\"tt-button tt-midpage-button\" ";
            $btn .= "onclick=\"start_timer_for_task('" . esc_textarea($company) . "', '" . $tsk_id . "-" . $tsk_desc . "');\"";
            $btn .= ">Start Working</button>";
            return $btn;         
        }


        /**
         * Generate HTML for front end display
         * 
         * @since 3.1.0
         * 
         * @return string Html output for display of one task, including time worked details.
         */ 
        private function get_html() {
            $task = $this->get_task_details_from_db();
            if (!$task) {
                return;
            }

            $date_added_formatted = tt_format_date_for_display(sanitize_text_field($task[0]->TDateAdded), "date_and_time"); 
            $due_date_formatted = tt_format_date_for_display(sanitize_text_field($task[0]->TDueDate), "date_only");
            $flds = new Time_Tracker_Display_Fields();
            $output = new Time_Tracker_Display_Table();

            $taskid = "<h2>Task # " . esc_textarea(sanitize_text_field($this->taskid)) . "</h2>";
            
            $description = "<strong>Description:</strong><br/>";
            // . wp_kses_post(nl2br($task[0]->TDescription)) . "<br/>";
            $fld = $flds->task;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-task', 'TaskID');
            $description .= $this->style_editable_field($out) . "<br/><br/>";

            $client = "<strong>Client:</strong><br/>  ";
            $fld = $flds->client_select;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-task', 'TaskID');
            $client .= $this->style_editable_field($out)  . "<br/><br/>";

            $project = "<strong>Project:</strong><br/>  ";
            $fld = $flds->project_select;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-project', 'ProjectID');
            $project .= $this->style_editable_field($out) . "<br/><br/>";

            $status = "<strong>Status:</strong><br/>  ";
            $fld = $flds->status;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-task', 'TaskID');
            $status .= $this->style_editable_field($out) . "<br/><br/>";
            
            $date_added = "<strong>Date Added::</strong><br/>  " . $date_added_formatted . "<br/><br/>";

            $due_date = "<strong>Due Date:</strong><br/>  " . $due_date_formatted . "<br/><br/>";

            $notes = "<strong>Notes:</strong><br/>  ";
            $fld = $flds->notes;
            $fld["fieldname"] = "TaskNotes";       //changed to disambiguate with Time Notes
            $out = $output->create_html_output($fld, $task[0], [], 'tt-task', 'TaskID');
            $notes .= $this->style_editable_field($out) . "<br/><br/>";

            $start_work_button = $this->add_start_work_button(intval($this->taskid), esc_textarea($task[0]->TDescription), sanitize_text_field($task[0]->Company)) . "<br/>";

            $display = $taskid . $description . $client . $project . $status . $date_added . $due_date . $notes . $start_work_button;

            return $display;
        }

        /**
         * Style editable fields.
         * 
         * @since 3.1.0
         * 
         * @param string Html element(s) to style.
         * 
         * @return string Html element wrapped in styled span.
         */
        private function style_editable_field($strHtml) {
            $strHtml = "<span class='tt-editable-field'>" . $strHtml . "</span>";
            return $strHtml;
        }


    }
}
