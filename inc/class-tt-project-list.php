<?php
/**
 * Class Project_List
 *
 * Get projects from db and create table to display on front end
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

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
            //$this->get_projects_from_db();
        }


        /**
         * Get details from db
         * 
         */
        private function get_projects_from_db($pstatus) {
            global $wpdb;
            $sql_string = $wpdb->prepare("SELECT tt_project.*, tt_client.Company, 
                    NewTable.Minutes as LoggedMinutes,
                    NewTable.Hours as LoggedHours,
                    NewTable.LastWorked as LastEntry
                FROM tt_project 
                LEFT JOIN tt_client
                    ON tt_project.ClientID = tt_client.ClientID
                LEFT JOIN (SELECT tt_task.ProjectID, SUM(Minute(TIMEDIFF(EndTime, StartTime))) as Minutes,
                        SUM(Hour(TIMEDIFF(EndTime, StartTime))) as Hours,
                        MAX(StartTime) as LastWorked 
                    FROM tt_time LEFT JOIN tt_task ON tt_time.TaskID = tt_task.TaskID GROUP BY ProjectID) NewTable
                    ON tt_project.ProjectID = NewTable.ProjectID
                WHERE tt_project.PStatus = %s
                ORDER BY tt_project.ProjectID DESC", $pstatus);
            
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $sql_result;
        }


        /**
         * Get table column order and table fields
         * 
         */
        private function get_table_fields() {
            $cols = [
                "ID" => [
                    "fieldname" => "ProjectID",
                    "id" => "recurring-task-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Project" => [
                    "fieldname" => "PName",
                    "id" => "company-name",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Client" => [
                    "fieldname" => "ClientID",
                    "id" => "project-id",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Category" => [
                    "fieldname" => "PCategory",
                    "id" => "project-name",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Status" => [
                    "fieldname" => "PStatus",
                    "id" => "task-category",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => ""
                ],
                "Date Added" => [
                    "fieldname" =>"PDateStarted",
                    "id" => "recurring-task-name",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => "tt-align-right"
                ],
                "Last Worked" => [
                    "fieldname" => "LastEntry",
                    "id" => "frequency",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "text",
                    "class" => "tt-align-right"
                ],
                "Due Date" => [
                    "fieldname" => "PDueDate",
                    "id" => "last-created",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "date",
                    "class" => "tt-align-right"
                ],
                "Notes" => [
                    "fieldname" => "PDetails",
                    "id" => "end-repeat",
                    "editable" => true,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ],
                "Time Logged vs Estimate" => [
                    "fieldname" => "TimeLoggedVsEstimate",
                    "id" => "recurring-task-description",
                    "editable" => false,
                    "columnwidth" => "",
                    "type" => "long text",
                    "class" => ""
                ]
            ];
            return $cols;
        }


        /**
         * Get Due Date Class
         * 
         */
        private function get_due_date_class($duedate, $projstatus) {
            //evaluate due date and current status, apply class based on result
            $due_date_formatted = tt_format_date_for_display($duedate, "date_only");
            $due_date_object = \DateTime::createFromFormat("Y-m-d", $duedate);
            
            if ($due_date_formatted = "") {
                $due_date_class = "no-date";
            } elseif ($due_date_object <= new \DateTime() AND $projstatus<>"Canceled" AND $projstatus<>"Complete") {
                $due_date_class = "late-date";
            } elseif ($due_date_object <= new \DateTime(date("Y-m-d", strtotime("+7 days"))) AND $projstatus<>"Canceled" AND $projstatus<>"Complete") {
                $due_date_class = "soon-date";
            } elseif ($due_date_object > new \DateTime(date("Y-m-d", strtotime("+90 days"))) AND $projstatus<>"Canceled" AND $projstatus<>"Complete") {
                $due_date_class = "on-hold-date";
            } else {
                $due_date_class = "ok-date";
            }
            return $due_date_class;
        }
     
        
        /**
         * Get Percentage of Time Logged vs Time Estimate
         * 
         */
        private function get_percent_time_logged($time_estimate_formatted, $hours_logged) {
            //evaluate time worked vs estimate, format data to display and apply css class based on result

            if (($time_estimate_formatted == 0 ) or ($time_estimate_formatted == null)) {
                $percent_time_logged = "";
                $time_estimate_details_for_table = "";
            } else {
                $percent_time_logged = round($hours_logged / $time_estimate_formatted * 100);
                //$percent_time_logged = "<br/>" . round($hours_logged / $time_estimate_formatted * 100) . "%";
                $time_estimate_details_for_table = " / " . $time_estimate_formatted . $percent_time_logged;
            }     
            return $percent_time_logged;
        }


        /**
         * Iterate through data and add additional information for table
         * 
        **/
        private function get_all_data_for_display($pstatus) {
            $projects = $this->get_projects_from_db($pstatus);
            foreach ($projects as $item) {
                $duedate = sanitize_text_field($item->PDueDate);
                $projstatus = sanitize_text_field($item->PStatus);

                $project_details_button = "<button onclick='open_time_entries_for_project(\"" . esc_attr(sanitize_textarea_field($item->PName)) . "\")' id=\"project-" . esc_attr(sanitize_text_field($item->ProjectID))  . "\" class=\"open-project-detail chart-button\">View Time</button>";
                $item->ProjectID = [
                    "value" => $item->ProjectID,
                    "button" => $project_details_button
                ];

                $due_date_class = $this->get_due_date_class($duedate, $projstatus);
                $item->PDueDate = [
                    "value" => $item->PDueDate,
                    "class" => $due_date_class
                ];

                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item->PTimeEstimate));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item->LoggedHours), sanitize_text_field($item->LoggedMinutes));
                $percent_time_logged = $this->get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
                $item->TimeLoggedVsEstimate = [
                    "value" => $hours_logged . " / " . $time_estimate_formatted . "<br/>" . $percent_time_logged . "%",
                    "class" => $time_worked_vs_estimate_class
                ];
            }
            return $projects;
        }


        /**
         * Create HTML table for front end display
         * 
         */
        public function create_table($pstatus) {
            $fields = $this->get_table_fields();
            $projects = $this->get_all_data_for_display($pstatus);
            $args["class"] = ["tt-table", "project-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $projects, $args, "tt_project", "ProjectID");
            return $table;
        }


        /**
         * Combine All Project Status Tables for One Page
         * 
         */
        public function get_page_html() {
            $html = "";
            foreach ($this->status_order as $pstatus) {
                $html .= "<h3>" . $pstatus . " Projects</h3>";
                $html .= $this->create_table($pstatus);
            }
            return $html;   
        }










        /**
         * Create HTML front facing table
         * 
         */
        public function old_create_table() {
            
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
                $table .= "<tr><td colspan=\"10\" id=\"status-header-row\">Status: " . esc_textarea($status) . "</td></tr>";
            
                //Create body
                foreach ($projects as $item) {
                    $projstatus = sanitize_text_field($item->PStatus);
                    
                    if ($projstatus == $status) {
                        //$ticket = $item->TaskID . "-" . $item->TDescription;
                        $duedate = sanitize_text_field($item->PDueDate);
                        $timeestimate = sanitize_text_field($item->PTimeEstimate);
                        $datestarted = sanitize_text_field($item->PDateStarted);
                        $lastentry = sanitize_text_field($item->LastEntry);
                        $projid = sanitize_text_field($item->ProjectID);
                        
                        $due_date_class = $this->get_due_date_class($duedate, $projstatus);

                        //evaluate time worked vs estimate, format data to display and apply css class based on result
                        $hours_logged = $item->LoggedHours + round($item->LoggedMinutes/60,2);
                        if (($timeestimate == 0 ) or ($timeestimate == null)) {
                            $percent_time_logged = "";
                            $time_estimate_details_for_table = "";
                        } else {
                            $time_estimate_parts = explode(":", $timeestimate);
                            $time_estimate_formatted = round($time_estimate_parts[0] + ($time_estimate_parts[1]/60),2);
                            $percent_time_logged = "<br/>" . round($hours_logged / $time_estimate_formatted * 100) . "%";
                            $time_estimate_details_for_table = " / " . $time_estimate_formatted . $percent_time_logged;
                        }                


                        if ( ($percent_time_logged <> "") and (round($hours_logged / $time_estimate_formatted * 100) > 100) ) {
                            $time_worked_vs_estimate_class = "over-time-estimate";
                        } else {
                            $time_worked_vs_estimate_class = "";
                        }

                        if ($datestarted = '0000-00-00') {
                            $date_started_formatted = "";
                        } else {
                            $date_started_formatted = tt_format_date_for_display($datestarted, 'date_only');
                        }
                        $last_worked_formatted = tt_format_date_for_display($lastentry, 'date_only');
                        $due_date_formatted = tt_format_date_for_display($duedate, 'date_only');
                        
                        //create row
                        $table .= "<tr class=\"" . esc_attr($due_date_class) . " " . esc_attr($time_worked_vs_estimate_class) . "\">";
                        
                        $table .= "<td id=\"task-id\" class=\"not-editable\">" . esc_textarea($projid);
                        $table .= "<button onclick='open_time_entries_for_project(\"" . esc_attr($item->PName) . "\")' id=\"" . esc_attr($projid)  . "\" class=\"open-time-entry-detail chart-button\">View Time</button>";
                        $table .= "</td>";
                        
                        $table .= "<td id=\"project-name\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->PName)) . "</td>";
                        $table .= "<td id=\"client\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->Company)) . "</td>";
                        $table .= "<td id=\"project-category\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($item->PCategory)) . "</td>";
                        $table .= "<td id=\"project-status\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PStatus'," . esc_attr($projid) . ")\">" . esc_textarea($projstatus) . "</td>";
                        $table .= "<td id=\"date-started\" class=\"not-editable\">" . esc_textarea($date_started_formatted) . "</td>";
                        $table .= "<td id=\"last-worked\" class=\"not-editable\">" . esc_textarea($last_worked_formatted) . "</td>";
                        $table .= "<td id=\"due-date\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PDueDate'," . esc_attr($projid) . ")\">" . esc_textarea($due_date_formatted) . "</td>";
                        $table .= "<td id=\"project-notes\" class=\"tt-editable\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_project', 'ProjectID', 'PDetails'," . esc_attr($projid) . ")\">" . wp_kses_post(nl2br($item->PDetails)) . "</td>";
                        $table .= "<td id=\"time-worked\" class=\"not-editable\">" . html_entity_decode(esc_html($hours_logged . $time_estimate_details_for_table)) . "</td>";
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