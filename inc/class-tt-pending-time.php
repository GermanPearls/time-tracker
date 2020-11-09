<?php
/**
 * Time Tracker Display Pending Time (ie: hasn't been billed yet)
 *
 * Sort pending time by bill-to and display in tables
 * 
 * @since 1.0
 * 
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Pending_Time' ) ) {


    /**
     * Class
     * 
     */
    class Pending_Time
    {

        
        /**
         * Constructor
         * 
         */
        public function __construct() {
        }


        /**
         * Query db for time not yet billed
         * 
         */
        private function get_pending_time_from_db() {
            //Connect to Time Tracker Database
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $sql_string = "SELECT tt_time.*, tt_client.Company, tt_client.BillTo, tt_task.TDescription, tt_task.TTimeEstimate, Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes, Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours
                FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID
                WHERE (tt_time.Invoiced = \"\" OR tt_time.Invoiced IS NULL) AND tt_client.Billable = true
                ORDER BY tt_client.BillTo ASC, tt_time.ClientID ASC, tt_time.TaskID ASC, tt_time.StartTime ASC";
            $sql_result = $wpdb->get_results($query = $sql_string, $output = ARRAY_A);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $sql_result;
        } //close function get to do list from db


        /**
         * Regroup data by Bill To
         * 
         */
        private function get_time_grouped_by_billto() {
            $pending_time = $this->get_pending_time_from_db();
            $lastbillto = "not started";
            foreach ($pending_time as $item) {
                if ($item['BillTo'] == "") {
                    $billto = "Unknown";
                } else {
                    $billto = $item['BillTo'];
                }
                
                //create new key if first time seeing this bill to
                if ($lastbillto != $billto) {
                    $grouped_time[$billto][0] = $item;
                } else {
                    //or just add to the array under this key
                    array_push($grouped_time[$billto], $item);
                }

                if ($item['BillTo'] == "") {
                    $lastbillto = "Unknown";
                } else {
                    $lastbillto = $item['BillTo'];
                }
            }
            return $grouped_time;
        }


        /**
         * Create front end display of time not yet billed
         * 
         */
        public function display_pending_time() {
            $grouped_time = $this->get_time_grouped_by_billto();

            //TABLE OF CONTENTS - WITH LINKS
            $html = "<strong>Click a Link to Jump to That Section</strong>";
            $html .= "<ul>";
            foreach ($grouped_time as $billtoname => $time_details) {
                if ($billtoname != null) {
                    $html .= "<li><a href=\"#" . $billtoname . "\">Pending Time to Bill To: " . $billtoname . "</a></li>";
                } else {
                    $html .= "<li><a href=\"#None\">No Bill To Specified</a></li>";
                }
            }
            $html.= "</ul>";
            $html .= "<br/>";

            //DETAILS
            foreach ($grouped_time as $billtoname => $time_details) {
                if ($billtoname != null) {    
                    $html .= "<h2 id=\"" . $billtoname . "\">Pending Time, Bill To: " . $billtoname . "</h2>";
                } else {
                    $html .= "<h2 id=\"None\">Pending Time, No Bill To Specified</h2>";
                }
                $html .= $this->create_table($time_details);
            }
            return $html;
        }
        
        
        /**
         * Create individual tables for each bill to
         * 
         */ 
        private function create_table($time_entries) {    
            if (empty($time_entries)) {
                $table = "<strong>All caught up!</strong>";
                return $table;
            }

            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            $table .= "<table class=\"tt-table pending-time-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>Client</th>";
            $table .= "<th>Task</th>";
            $table .= "<th>Task Description</th>";
            $table .= "<th>Start</th>";
            $table .= "<th>End</th>";
            $table .= "<th>Time Logged vs Estimate</th>";
            $table .= "<th>Invoiced</th>";
            $table .= "<th>Invoice #</th>";
            $table .= "<th>Invoiced Time</th>";
            $table .= "<th>Invoice Notes</th>";
            $table .= "<th>Status</th>";            
            $table .= "<th>Notes</th>";
            $table .= "</tr></thead>";

            $previous_client = $time_entries[0]['Company'];

            //Create body
            foreach ($time_entries as $item) {
                if ($previous_client !== $item['Company']) {
                    $table .= "<tr><td class=\"divider-row\" colspan=\"12\"></td></tr>";
                }

                $ticket = $item['TaskID'] . "-" . $item['TDescription'];
        
                $time_fraction_logged = (float)$item['LoggedHours'] + round((float)$item['LoggedMinutes']/60,2);
                $time_logged = tt_convert_to_string_time((float)$item['LoggedHours'], (float)$item['LoggedMinutes']);
                if (($item['TTimeEstimate'] != null) && ($item['TTimeEstimate'] != 0)) {
                    $time_estimate_parts = explode(":", $item['TTimeEstimate']);
                    $time_estimate_as_number = tt_convert_to_decimal_time($time_estimate_parts[0], $time_estimate_parts[1]);
                    $percent_time_logged = " / " . $time_estimate_as_number . "<br/>" . round($time_fraction_logged / $time_estimate_as_number * 100) . "%";
                } else {
                    $percent_time_logged = "";
                }

                //create row
                $table .= "<tr>";           
                $table .= "<td id=\"client\" class=\"not-editable\">" . nl2br(stripslashes($item['Company'])) . "</td>";
                $table .= "<td id=\"task-id\" class=\"not-editable\">" . $item['TaskID'] . "</td>";
                $table .= "<td id=\"task-description\" class=\"not-editable\">"  . nl2br(stripslashes($item['TDescription'])) . "</td>";
                $table .= "<td id=\"start-time\" class=\"not-editable\">" . tt_format_date_for_display($item['StartTime'], "date_and_time") . "</td>";
                $table .= "<td id=\"end-time\" class=\"not-editable\">" . tt_format_date_for_display($item['EndTime'], "date_and_time") . "</td>";
                $table .= "<td id=\"time-logged\" class=\"not-editable\">" . $time_logged . "<br/>" . $time_fraction_logged . " hrs" . $percent_time_logged . "</td>";
                $table .= "<td id=\"invoiced\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'Invoiced'," . $item['TimeID'] . ")\">" . $item['Invoiced'] . "</td>";
                $table .= "<td id=\"invoice-number\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceNumber'," . $item['TimeID'] . ")\">" . $item['InvoiceNumber'] . "</td>";
                $table .= "<td id=\"invoiced-time\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoicedTime'," . $item['TimeID'] . ")\">" . $item['InvoicedTime'] . "</td>";
                $table .= "<td id=\"invoice-notes\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceComments'," . $item['TimeID'] . ")\">" . nl2br(stripslashes($item['InvoiceComments'])) . "</td>";
                $table .= "<td id=\"status\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TStatus'," . $item['TaskID'] . "), updateDatabase(this, 'time', 'TimeID', 'NewTaskStatus'," . $item['TimeID'] . ")\">" . $item['NewTaskStatus'] . "</td>";
                $table .= "<td id=\"task-notes\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'TNotes'," . $item['TimeID'] . ")\">" . nl2br(stripslashes($item['TNotes'])) . "</td>";
                //close out row
                $table .="</tr>";

                $previous_client = $item['Company'];

            } // foreach loop

            //close out table
            $table .= "</table>";

            return $table;
        } //close function to create to do list table for display

    } //close class

} //close if class exists