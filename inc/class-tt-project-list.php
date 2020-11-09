<?php
/**
 * Class Project_List
 *
 * Get projects from db and create table to display on front end
 * 
 * @since 1.0
 * 
 */


defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Project_List' ) ) {


    /**
     * Class
     * 
     */
    class Project_List
    {


        /**
         * Class Variables
         * 
         */
        private $status_order = ["New", "Ongoing", "In Process", "Waiting Client", "Complete", "Canceled"];



        /**
         * Constructor
         * 
         */
        public function __construct() {
            $this->get_projects_from_db();
        }


        /**
         * Get details from db
         * 
         */
        private function get_projects_from_db() {
            //Connect to Time Tracker Database
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $sql_string = "SELECT tt_project.*, tt_client.Company, NewTable.Minutes as LoggedMinutes, NewTable.Hours as LoggedHours, NewTable.LastWorked as LastEntry
                FROM tt_project 
                LEFT JOIN tt_client
                    ON tt_project.ClientID = tt_client.ClientID
                LEFT JOIN (SELECT tt_task.ProjectID, SUM(Minute(TIMEDIFF(EndTime, StartTime))) as Minutes, SUM(Hour(TIMEDIFF(EndTime, StartTime))) as Hours, MAX(StartTime) as LastWorked FROM tt_time LEFT JOIN tt_task ON tt_time.TaskID = tt_task.TaskID GROUP BY ProjectID) NewTable
                    ON tt_project.ProjectID = NewTable.ProjectID
                ORDER BY tt_project.ProjectID DESC";
            
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            $this->all_projects = $sql_result;
        }


        /**
         * Create HTML front facing table
         * 
         */
        public function create_table() {
            
            $projects = $this->all_projects;

            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            
            $table .= "<table class=\"tt-table project-list-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>ID</th>";
            $table .= "<th>Project</th>";
            $table .= "<th>Client</th>";
            $table .= "<th>Category</th>";
            $table .= "<th>Status</th>";
            $table .= "<th>Date Added</th>";
            $table .= "<th>Last Worked</th>";                        
            $table .= "<th>Due Date</th>";
            $table .= "<th>Notes</th>";
            $table .= "<th>Time Logged vs Estimate</th>";
            $table .= "</tr></thead>";
            
            //loop through each status, in order defined by variable at top of class
            foreach ($this->status_order as $status) {

                //create header row to define status section
                $table .= "<tr><td colspan=\"10\" id=\"status-header-row\">Status: " . $status . "</td></tr>";
            
                //Create body
                foreach ($projects as $item) {
                    
                    if ($item->PStatus == $status) {
                        //$ticket = $item->TaskID . "-" . $item->TDescription;
                        
                        //evaluate due date and current status, apply class based on result
                        if ($item->PDueDate == "0000-00-00") {
                            $due_date_formatted = "";
                            $due_date_class = "no-date";
                        } else {
                            $due_date_formatted = date_format(DateTime::createFromFormat("Y-m-d", $item->PDueDate), "n/j/y");
                            if (DateTime::createFromFormat("Y-m-d", $item->PDueDate) <= new DateTime() AND $item->PStatus<>"Canceled" AND $item->PStatus<>"Complete") {
                                $due_date_class = "late-date";
                            } elseif (DateTime::createFromFormat("Y-m-d", $item->PDueDate) <= new DateTime(date("Y-m-d", strtotime("+7 days"))) AND $item->PStatus<>"Canceled" AND $item->PStatus<>"Complete") {
                                $due_date_class = "soon-date";
                            } elseif (DateTime::createFromFormat("Y-m-d", $item->PDueDate) > new DateTime(date("Y-m-d", strtotime("+90 days"))) AND $item->PStatus<>"Canceled" AND $item->PStatus<>"Complete") {
                                $due_date_class = "on-hold-date";
                            } else {
                                $due_date_class = "ok-date";
                            }
                        }

                        //evaluate time worked vs estimate, format data to display and apply css class based on result
                        $hours_logged = $item->LoggedHours + round($item->LoggedMinutes/60,2);
                        if (($item->PTimeEstimate == 0 ) or ($item->PTimeEstimate == null)) {
                            $percent_time_logged = "";
                            $time_estimate_details_for_table = "";
                        } else {
                            $time_estimate_parts = explode(":", $item->PTimeEstimate);
                            $time_estimate_formatted = round($time_estimate_parts[0] + ($time_estimate_parts[1]/60),2);
                            $percent_time_logged = "<br/>" . round($hours_logged / $time_estimate_formatted * 100) . "%";
                            $time_estimate_details_for_table = " / " . $time_estimate_formatted . $percent_time_logged;
                        }                


                        if ( ($percent_time_logged <> "") and (round($hours_logged / $time_estimate_formatted * 100) > 100) ) {
                            $time_worked_vs_estimate_class = "over-time-estimate";
                        } else {
                            $time_worked_vs_estimate_class = "";
                        }

                        if ($item->PDateStarted == "0000-00-00 00:00:00") {
                            $date_started_formatted = "";
                        } else {
                            $date_started_formatted = date_format(DateTimeImmutable::createFromFormat("Y-m-d G:i:s", $item->PDateStarted), "n/j/y");
                        }

                        if ($item->LastEntry == "0000-00-00 00:00:00") {
                            $last_worked_formatted = "";
                        } else {
                            $last_worked_formatted = date_format(DateTimeImmutable::createFromFormat("Y-m-d G:i:s", $item->LastEntry), "n/j/y");
                        }
                        
                        //create row
                        $table .= "<tr class=\"" . $due_date_class . " " . $time_worked_vs_estimate_class . "\">";
                        $table .= "<td id=\"task-id\" class=\"not-editable\">" . $item->ProjectID . "</td>";
                        $table .= "<td id=\"project-name\" class=\"not-editable\">" . nl2br(stripslashes($item->PName)) . "</td>";
                        $table .= "<td id=\"client\" class=\"not-editable\">" . nl2br(stripslashes($item->Company)) . "</td>";
                        $table .= "<td id=\"project-category\" class=\"not-editable\">" . nl2br(stripslashes($item->PCategory)) . "</td>";
                        $table .= "<td id=\"project-status\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PStatus'," . $item->ProjectID . ")\">" . $item->PStatus . "</td>";
                        $table .= "<td id=\"date-started\" class=\"not-editable\">" . $date_started_formatted . "</td>";
                        $table .= "<td id=\"last-worked\" class=\"not-editable\">" . $last_worked_formatted . "</td>";
                        $table .= "<td id=\"due-date\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PDueDate'," . $item->ProjectID . ")\">" . $due_date_formatted . "</td>";
                        $table .= "<td id=\"project-notes\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PDetails'," . $item->ProjectID . ")\">" . nl2br(stripslashes($item->PDetails)) . "</td>";
                        $table .= "<td id=\"time-worked\" class=\"not-editable\">" . $hours_logged . $time_estimate_details_for_table . "</td>";
                        //close out row
                        $table .="</tr>";

                    } //close out if this is the status we're looking for

                } //close out rotate through each status

            } // foreach loop

            //close out table
            $table .= "</table>";

            return $table;
        } //close function to create to do list table for display

    } //close class

} //close if class exists