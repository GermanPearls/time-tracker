<?php
/**
 * Time Tracker Utility Functions
 *
 * Misc functions used throughout plugin
 * 
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

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
            //$this->get_time_log_from_db();
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
            global $wpdb;
            $sql_string = $this->create_sql_string();
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $sql_result;
        }


        /**
         * Prepare sql string
         * 
         */
        private function create_sql_string() {   
            global $wpdb;		
			$selectfrom = "SELECT tt_time.*, tt_client.Company, tt_task.ProjectID, tt_task.TCategory, tt_task.RecurringTaskID, tt_task.TDescription, tt_task.TStatus, tt_task.TTimeEstimate,
                    Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedMinutes,
                    Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as LoggedHours
                FROM tt_time 
                LEFT JOIN tt_client
                    ON tt_time.ClientID = tt_client.ClientID
                LEFT JOIN tt_task
                    ON tt_time.TaskID = tt_task.TaskID";
            
            //combine-prepare selection criteria passed in _GET parameters
            //note the isset checks if the variable is set and is not null
            //https://stackoverflow.com/a/29497620
            $wherecriteria = "";
            
            if (isset($_GET['client'])) {
                $client = sanitize_text_field($_GET['client']);
                if ( ($client <>"") and ($client <> null) ) {
                    $wherecriteria = $wpdb->prepare('WHERE tt_client.Company LIKE %s', $client);
                }
            }

            if (isset($_GET['notes'])) {
                $notes = sanitize_text_field($_GET['notes']);
                if ( ($notes <>"") and ($notes <> null) ) {
                    //Ref: https://developer.wordpress.org/reference/classes/wpdb/esc_like/
                    $wild = "%";
                    $search_string = $notes;
                    $search_like = $wild . $wpdb->esc_like( $search_string ) . $wild;
                    if ($wherecriteria == "") {
                        $wherecriteria = "WHERE ";
                    } else {
                        $wherecriteria .= " AND "; 
                    }
                    $wherecriteria .= $wpdb->prepare('tt_time.TNotes LIKE %s', $search_like);
                }
            }

            if (isset($_GET['task'])) {
                //sometimes null is getting passed as a string
                $task = sanitize_text_field($_GET['task']);
                if ( ($task <>"") and ($task <> null) and ($task <> "null") ) {
                    if ($wherecriteria == "") {
                        $wherecriteria = "WHERE ";
                    } else {
                        $wherecriteria .= " AND ";                    
                    }
                    $wherecriteria .= $wpdb->prepare('tt_task.TaskID = %s', $task);
                }
            }
            
            if (isset($_GET['project'])) {
                //sometimes null is getting passed as a string
                $project = sanitize_text_field($_GET['project']);
                if ( ($project <>"") and ($project <> null) and ($project <> "null") ) {
                    if ($wherecriteria == "") {
                        $wherecriteria = "WHERE ";
                    } else {
                        $wherecriteria .= " AND ";                    
                    }
                    $project_id = get_project_id_from_name($project);
                    $wherecriteria .= $wpdb->prepare('tt_task.ProjectID = %s', $project_id);
                }
            }
            
            if ( isset($_GET['start'])) {
                $start = sanitize_text_field($_GET['start']);
                if ( $start <>"" ) {
                    if ($wherecriteria == "") {
                        $wherecriteria = "WHERE ";
                    } else {
                        $wherecriteria .= " AND ";
                    }
                    $wherecriteria .= $wpdb->prepare('tt_time.StartTime >= %s', $start);
                }
            }

            if ( isset($_GET['end'])) {
                $end = sanitize_text_field($_GET['end']);
                if ( $end <>"" ) {
                    if ($wherecriteria == "") {
                        $wherecriteria = "WHERE ";
                    } else {
                        $wherecriteria .= " AND ";
                    }
                    $wherecriteria .= $wpdb->prepare('tt_time.StartTime <= %s', $end);
                }
            }

            $orderby = "ORDER BY tt_time.StartTime DESC";
			
			$record_numbers = get_record_numbers_for_pagination_sql_query();	
			$subset_for_pagination = "LIMIT " . $record_numbers['limit'] . " OFFSET " . $record_numbers['offset'];
            
			if ($wherecriteria == "") {
                $sql_string = $selectfrom . " " . $orderby . " " . $subset_for_pagination;                
            } else {
                $sql_string = $selectfrom . " " . $wherecriteria . " " . $orderby . " " . $subset_for_pagination;
            }
			//var_dump($sql_string);
            return $sql_string;
        }


        /**
         * Get table column order and table fields
         * 
         */
        private function get_table_fields() {
            $cols = [
                "ID" => [
                    "fieldname" => "TimeID",
                    "id" => "time-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Client ID" => [
                    "fieldname" => "ClientID",
                    "id" => "client-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "client-name",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Task ID" => [
                    "fieldname" => "TaskID",
                    "id" => "task-id",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Type" => [
                    "fieldname" => "TCategory",
                    "id" => "task-type",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Task" => [
                    "fieldname" =>"TDescription",
                    "id" => "task-description",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Start Time" => [
                    "fieldname" => "StartTime",
                    "id" => "start-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date and time",
                    "class" => "tt-align-right"
                ],
                "End Time" => [
                    "fieldname" => "EndTime",
                    "id" => "end-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "date and time",
                    "class" => ""
                ],
                "Time Logged Vs Estimate" => [
                    "fieldname" => "TimeLoggedVsEstimate",
                    "id" => "time-logged-vs-estimate",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => "tt-align-right"
                ],
                "Status" => [
                    "fieldname" => "TStatus",
                    "id" => "task-status",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => "tt-align-right"
                ],
                "Invoiced?" => [
                    "fieldname" => "Invoiced",
                    "id" => "invoiced",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoice Number" => [
                    "fieldname" => "InvoiceNumber",
                    "id" => "invoice-number",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoiced Time" => [
                    "fieldname" => "InvoicedTime",
                    "id" => "invoice-time",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Invoice Comments" => [
                    "fieldname" => "InvoiceComments",
                    "id" => "invoice-comments",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Notes" => [
                    "fieldname" => "TNotes",
                    "id" => "task-notes",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Follow Up" => [
                    "fieldname" => "FollowUp",
                    "id" => "follow-up",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ]
            ];
            return $cols;
        }


        /**
         * Get Data from Table and Append with Any Extra Info
         * 
         */
        private function get_all_data_for_display() {
            $time_entries = $this->get_time_log_from_db();

            foreach ($time_entries as $item) {
                if ( (sanitize_text_field($item->RecurringTaskID) != null) and (sanitize_text_field($item->RecurringTaskID) != "") ) {
                    $icon = tt_add_recurring_task_icon();
                    $task_category = $item->TCategory;
                    $item->TCategory = [
                        "value" => $task_category,
                        "icon" => $icon
                    ];                    
                }

                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item->TTimeEstimate));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item->LoggedHours), sanitize_text_field($item->LoggedMinutes));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
                $item->TimeLoggedVsEstimate = [
                    "value" => $hours_logged . $percent_time_logged,
                    "class" => $time_worked_vs_estimate_class
                ];
            }
            return $time_entries;
        }


        /**
         * Create Table
         * 
         */
        public function get_html() {            
            $fields = $this->get_table_fields();
            $time_entries = $this->get_all_data_for_display();
            $args["class"] = ["tt-table", "time-log-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $time_entries, $args, "tt_time", "TimeID");
            return $table;
        }











        /**
         * Create output
         * 
         */
        private function old_get_html() {
            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            $table .= "<table class=\"tt-table time-log-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>ID</th>";
            $table .= "<th>Client ID</th>";
            $table .= "<th>Client</th>";
            $table .= "<th>Task ID</th>";
            $table .= "<th>Type</th>";
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
                $timeid = sanitize_text_field($item->TimeID);
                $taskid = sanitize_text_field($item->TaskID);
                $starttime = tt_format_date_for_display(sanitize_text_field($item->StartTime), 'date_and_time');
                $endtime = tt_format_date_for_display(sanitize_text_field($item->EndTime), 'date_and_time');
                $task_icon = sanitize_text_field($item->RecurringTaskID) != null ? tt_add_recurring_task_icon() : "";

                //create row
                $table .= "<tr>";
                $table .= "<td class=\"not-editable\">" . esc_textarea($timeid) . "</td>";
                $table .= "<td class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->ClientID)) . "</td>";
                $table .= "<td class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->Company)) . "</td>";
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'TaskID'," . esc_attr($timeid) . ")\">" . esc_textarea($taskid) . "</td>";
                $table .= "<td class=\"not-editable\">" . $task_icon . "</td>";
                $table .= "<td class=\"not-editable\">" . wp_kses_post(nl2br($item->TDescription)) . "</td>";
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'StartTime'," . esc_attr($timeid) . ")\">" . esc_textarea($starttime) . "</td>";
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'EndTime'," . esc_attr($timeid) . ")\">" . esc_textarea($endtime) . "</td>";
                
                $loggedhrs = sanitize_text_field($item->LoggedHours);
                $loggedmin = sanitize_text_field($item->LoggedMinutes);
                $timeest = sanitize_text_field($item->TTimeEstimate);
                $hours_logged = $loggedhrs + round($loggedmin/60,2);
                if ( ($timeest !=null) and (substr($timeest,0,-3) != "00:00") ) {
                    $time_estimate_parts = explode(":", $timeest);
                    $time_estimate_fraction = round((float)$time_estimate_parts[0] + ((float)$time_estimate_parts[1]/60),2);
                    $percent_time_logged = " / " . $time_estimate_fraction . "<br/>" . round($hours_logged / $time_estimate_fraction * 100) . "%";
                } else {
                    $time_estimate_fraction = "";
                    $percent_time_logged = "";
                }

                $table .= "<td class=\"not-editable\">" . wp_kses_post($hours_logged . $percent_time_logged) . "</td>";
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_task', 'TaskID', 'TStatus'," . esc_attr($taskid) . ")\">" . esc_textarea(sanitize_text_field($item->TStatus)) . "</td>";
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'Invoiced'," . esc_attr($timeid) . ")\">" . esc_textarea(sanitize_text_field($item->Invoiced)) . "</td>"; 
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceNumber'," . esc_attr($timeid) . ")\">" . esc_textarea(sanitize_text_field($item->InvoiceNumber)) . "</td>"; 
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoicedTime'," . esc_attr($timeid) . ")\">" . esc_textarea(sanitize_text_field($item->InvoicedTime)) . "</td>"; 
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'InvoiceComments'," . esc_attr($timeid) . ")\">" . stripslashes(wp_kses_post(nl2br($item->InvoiceComments))) . "</td>"; 
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'TNotes'," . esc_attr($timeid) . ")\">" . stripslashes(wp_kses_post(nl2br($item->TNotes))) . "</td>";                
                $table .= "<td class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_time', 'TimeID', 'FollowUp'," . esc_attr($timeid) . ")\">" . stripslashes(wp_kses_post(nl2br($item->FollowUp))) . "</td>"; 
                $table .="</tr>";
            } // foreach loop

            $table .= "</table>";

            return $table;
        }

        
    } //close class

} //close if class exists