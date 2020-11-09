<?php
/**
 * Time Tracker Utility Functions
 *
 * Misc functions used throughout plugin
 * 
 * 
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Log' ) ) {


    /**
     * Class
     * 
     */  
    class Time_Log
    {


        /**
         * Class Variables
         * 
         */ 
        private $open_items;
        //private $tt_db;



        /**
         * Constructor
         * 
         */        
        public function __construct() {
            $this->get_time_log_from_db();
        }


        /**
         * Get results
         * 
         */
        public function create_table() {
            return $this->get_html();
        }


        /**
         * Get data from db
         * 
         */
        private function get_time_log_from_db() {
            //Connect to Time Tracker Database
            //$this->tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $sql_string = $this->create_sql_string();
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            $this->open_items = $sql_result;
        }


        /**
         * Prepare sql string
         * 
         */
        private function create_sql_string() {   
            global $wpdb;                      
            $selectfrom = "SELECT tt_time.*, tt_client.Company, tt_task.TDescription, tt_task.TStatus, tt_task.TTimeEstimate, Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes, Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours
                FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID";
            
            //combine-prepare selection criteria passed in _GET parameters
            //note the isset checks if the variable is set and is not null
            //https://stackoverflow.com/a/29497620
            $wherecriteria = "";
            if ( (isset($_GET['client'])) and (urldecode($_GET['client']) <>"") and (urldecode($_GET['client']) <> null) ) {
                $wherecriteria = $wpdb->prepare('WHERE tt_client.Company LIKE %s', urldecode($_GET['client']));
            }
            if ( (isset($_GET['notes'])) and (urldecode($_GET['notes']) <>"") and (urldecode($_GET['notes']) <> null) ) {
                //Ref: https://developer.wordpress.org/reference/classes/wpdb/esc_like/
                $wild = "%";
                $search_string = urldecode($_GET['notes']);
                $search_like = $wild . $wpdb->esc_like( $search_string ) . $wild;
                if ($wherecriteria == "") {
                    $wherecriteria = "WHERE ";
                } else {
                    $wherecriteria .= " AND "; 
                }
                $wherecriteria .= $wpdb->prepare('tt_time.TNotes LIKE %s', $search_like);
            }
            //sometimes null is getting passed as a string
            if ( (isset($_GET['task'])) and (urldecode($_GET['task']) <>"") and (urldecode($_GET['task']) <> null) and (urldecode($_GET['task']) <> "null") ) {
                if ($wherecriteria == "") {
                    $wherecriteria = "WHERE ";
                } else {
                    $wherecriteria .= " AND ";                    
                }
                $wherecriteria .= $wpdb->prepare('tt_task.TaskID = %s', urldecode($_GET['task']));
            }
            
            if ( isset($_GET['start']) and (urldecode($_GET['start']) <>"") ) {
                if ($wherecriteria == "") {
                    $wherecriteria = "WHERE ";
                } else {
                    $wherecriteria .= " AND ";
                }
                $wherecriteria .= $wpdb->prepare('tt_time.StartTime >= %s', $_GET['start']);
            }

            if ( isset($_GET['end'])  and (urldecode($_GET['end']) <>"") ) {
                if ($wherecriteria == "") {
                    $wherecriteria = "WHERE ";
                } else {
                    $wherecriteria .= " AND ";
                }
                $wherecriteria .= $wpdb->prepare('tt_time.StartTime <= %s', $_GET['end']);
            }

            $orderby = "ORDER BY tt_time.StartTime DESC";

            if ($wherecriteria == "") {
                $sql_string = $selectfrom . " " . $orderby;                
            } else {
                $sql_string = $selectfrom . " " . $wherecriteria . " " . $orderby;
            }
            return $sql_string;
        }


        /**
         * Create output
         * 
         */
        private function get_html() {
            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            $table .= "<table class=\"tt-table time-log-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>ID</th>";
            $table .= "<th>Client ID</th>";
            $table .= "<th>Client</th>";
            $table .= "<th>Task ID</th>";
            $table .= "<th>Task</th>";
            $table .= "<th>Start Time</th>";
            $table .= "<th>End Time</th>";
            $table .= "<th>Time Logged vs Estimate</th>";
            $table .= "<th>Status</th>";
            $table .= "<th>Invoiced?</th>";
            $table .= "<th>Invoice Number</th>";
            $table .= "<th>Invoiced Time</th>";
            $table .= "<th>Invoice Comments</th>";
            $table .= "<th>Notes</th>";
            $table .= "<th>Follow Up</th>";             
            $table .= "</tr></thead>";

            //Create body
            foreach ($this->open_items as $item) {
                //create row
                $table .= "<tr>";
                $table .= "<td class=\"not-editable\">" . $item->TimeID . "</td>";
                $table .= "<td class=\"not-editable\">" . $item->ClientID . "</td>";
                $table .= "<td class=\"not-editable\">" . nl2br(stripslashes($item->Company)) . "</td>";
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'TaskID'," . $item->TimeID . ")\">" . $item->TaskID . "</td>";
                $table .= "<td class=\"not-editable\">" . nl2br(stripslashes($item->TDescription)) . "</td>";
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'StartTime'," . $item->TimeID . ")\">" . tt_format_date_for_display($item->StartTime, 'date_and_time') . "</td>";
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'EndTime'," . $item->TimeID . ")\">" . tt_format_date_for_display($item->EndTime, 'date_and_time') . "</td>";
                $hours_logged = $item->LoggedHours + round($item->LoggedMinutes/60,2);
                if ( ($item->TTimeEstimate !=null) and (substr($item->TTimeEstimate,0,-3) != "00:00") ) {
                    $time_estimate_parts = explode(":", $item->TTimeEstimate);
                    $time_estimate_fraction = round((float)$time_estimate_parts[0] + ((float)$time_estimate_parts[1]/60),2);
                    $percent_time_logged = " / " . $time_estimate_fraction . "<br/>" . round($hours_logged / $time_estimate_fraction * 100) . "%";
                } else {
                    $time_estimate_fraction = "";
                    $percent_time_logged = "";
                }
                $table .= "<td class=\"not-editable\">" . $hours_logged . $percent_time_logged . "</td>";
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TStatus'," . $item->TaskID . ")\">" . $item->TStatus . "</td>";
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'Invoiced'," . $item->TimeID . ")\">" . $item->Invoiced . "</td>"; 
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceNumber'," . $item->TimeID . ")\">" . $item->InvoiceNumber . "</td>"; 
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoicedTime'," . $item->TimeID . ")\">" . $item->InvoicedTime . "</td>"; 
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceComments'," . $item->TimeID . ")\">" . nl2br(stripslashes($item->InvoiceComments)) . "</td>"; 
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'TNotes'," . $item->TimeID . ")\">" . nl2br(stripslashes($item->TNotes)) . "</td>";                
                $table .= "<td contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'FollowUp'," . $item->TimeID . ")\">" . nl2br(stripslashes($item->FollowUp)) . "</td>"; 
                $table .="</tr>";
            } // foreach loop

            $table .= "</table>";

            return $table;
        }

        
    } //close class

} //close if class exists