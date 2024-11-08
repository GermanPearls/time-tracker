<?php
/**
 * Class Time_Details_Edit
 *
 * CLASS TO DISPLAY DETAILS OF INDIVIDUAL TIME ENTRY FOR USERS TO EDIT
 * 
 * @since 3.1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Details_Edit' ) ) {

    /**
     * Class
     * 
     * @since 3.1.0
     */  
    class Time_Details_Edit
    {
        
        
        /**
         * Class variables
         * 
         * @since 3.1.0
         */ 
        private $timeid;


        /**
         * Constructor
         * 
         * @since 3.1.0
         */ 
        public function __construct() {
            if (isset($_GET['time-id'])) {
                $this->timeid = sanitize_text_field($_GET['time-id']);
            }
        }


        /**
         * Get results
         * 
         * @since 3.1.0
         * 
         * @return string Html output.
         */ 
        public function generate_output_for_display() {
            return $this->get_html();
        }
        
        
        /**
         * Query db for time details
         * 
         * @since 3.1.0
         * 
         * @return object Results from querying database for details of time entry.
         */ 
        private function get_time_details_from_db() {
            global $wpdb;

            $sql_string_format = "SELECT tt_time.TimeID, tt_task.TaskID, tt_task.ClientID, tt_task.ProjectID,
                    tt_task.TStatus, tt_task.TTimeEstimate, tt_task.TDateAdded, tt_task.TDueDate,
                    tt_task.TNotes TaskNotes, tt_client.Company, tt_project.ProjectID, tt_project.PName,
                    tt_time.TimeID, tt_time.StartTime, tt_time.EndTime, tt_time.TNotes TimeNotes, tt_time.FollowUp,
                    tt_time.Invoiced, tt_time.InvoiceNumber, tt_time.InvoicedTime, tt_time.InvoiceComments
                FROM tt_time
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID
                LEFT JOIN tt_project
                    ON tt_task.ProjectID = tt_project.ProjectID
                WHERE tt_time.TimeID = %s";

            $sql_string = $wpdb->prepare($sql_string_format, $this->timeid);
            return tt_query_db($sql_string);
        }
        
        
        /**
         * Generate HTML for front end display
         * 
         * @since 3.1.0
         * 
         * @return string Html output for display.
         */ 
        private function get_html() {
            $task = $this->get_time_details_from_db();

            if ( !$task ) {   
                $total_time_display = "";           
            }

            $flds = new Time_Tracker_Display_Fields();
            $output = new Time_Tracker_Display_Table();

            $display = "<h3>Time Entry ID: " . esc_textarea(sanitize_text_field($task[0]->TimeID)) . "</h3>";

            $client = "<strong>Client:</strong><br/>  ";
            $fld = $flds->client_select;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $client .= $this->style_editable_field($out)  . "<br/><br/>";

            $taskdescription = "<strong>Task:</strong><br/>  ";
            $fld = $flds->task;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $taskdescription .= $this->style_editable_field($out) . "<br/><br/>";

            $starttime = "<strong>Start Time:</strong><br/>  ";
            $fld = $flds->start_time;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $starttime .= $this->style_editable_field($out) . "<br/><br/>";

            $endtime = "<strong>End Time:</strong><br/>  ";
            $fld = $flds->end_time;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $endtime .= $this->style_editable_field($out) . "<br/><br/>";

            $timenotes = "<strong>Time Notes:</strong><br/>  ";
            $fld = $flds->time_notes;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $timenotes .= $this->style_editable_field($out) . "<br/><br/>";

            $followup = "<strong>Follow Up:</strong><br/>  ";
            $fld = $flds->time_follow_up;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $followup .= $this->style_editable_field($out) . "<br/><br/>";

            $invoiced = "<strong>Invoiced:</strong><br/>  ";
            $fld = $flds->time_invoiced;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $invoiced .= $this->style_editable_field($out) . "<br/><br/>";

            $invoicenumber = "<strong>Invoice Number:</strong><br/>  ";
            $fld = $flds->time_invoice_number;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $invoicenumber .= $this->style_editable_field($out) . "<br/><br/>";

            $invoicedtime = "<strong>Invoiced Time:</strong><br/>  ";
            $fld = $flds->time_invoice_amount;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $invoicedtime .= $this->style_editable_field($out) . "<br/><br/>";

            $invoicedcomments = "<strong>Invoice Comments:</strong><br/>  ";
            $fld = $flds->time_invoice_notes;
            $out = $output->create_html_output($fld, $task[0], [], 'tt-time', 'TimeID');
            $invoicedcomments .= $this->style_editable_field($out) . "<br/><br/>";

            $display .= $client . $taskdescription . $starttime . $endtime . $timenotes . $followup . $invoiced . $invoicenumber . $invoicedtime . $invoicedcomments;
            $display .= "</div>";

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