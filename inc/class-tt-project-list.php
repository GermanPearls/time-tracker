<?php
/**
 * Class Project_List
 *
 * Get projects from db and create table to display on front end
 * 
 * @since 1.0.0
 * @since 3.0.13 Added 'other' category for projects with statuses not in predefined list.
 * @since 3.0.13 Clarified column heading.
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
     * @since 1.0.0
     */
    class Project_List
    {
        private $clientid;
        private $notes;
        private $projectid;
        private $startdate;
        private $enddate;


        /**
         * Class Variables
         * 
         * @since 2.2.0
         * @since 3.0.13 added 'other' category for projects with statuses not in predefined list
         */
        private $status_order = ["New", "Ongoing", "In Process", "Waiting Client", "Complete", "Canceled", "Other"];


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            if (isset($_GET['project'])) {
                if ($_GET['project'] <> null) {
                    $this->projectid = get_project_id_from_name(sanitize_text_field($_GET['project']));
                }
            } elseif (isset($_GET['project-id'])) {
                $this->projectid = intval($_GET['project-id']);
            } else {
                $this->projectid = null;
            }
            if (isset($_GET['client'])) {
                if ($_GET['client'] <> null) {
                    $this->clientid = get_client_id_from_name(sanitize_text_field($_GET['client']));
                }
            } elseif (isset($_GET['client-id'])) {
                $this->clientid = intval($_GET['client-id']);
            } else {
                $this->clientid  = null;
            };
            $this->notes = (isset($_GET['notes']) ? sanitize_text_field($_GET['notes']) : null);
            $this->startdate = (isset($_GET['start']) ? sanitize_text_field($_GET['start']) : null);
            $this->enddate = (isset($_GET['end']) ? sanitize_text_field($_GET['end']) : null);
        }


        /**
         * Create HTML table for front end display - with all statuses combined
         * 
         * @since 2.2.0
         * 
         * @return string Html output including multiple tables for different statuses.
         */
        public function get_table_of_all_projects() {
            return $this->get_complete_table_in_html();
        }
        
        
        /**
         * Create HTML table for front end display
         * 
         * @since 1.0.0
         * 
         * @param string $pstatus One project status to create table for.
         * 
         * @return string Html table showing details of one project status.
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
         * @since 2.2.0
         * 
         * @return string Html output to display with heading and table for each project status.
         */
        public function get_page_html_with_each_status_in_different_table() {
            $html = "";
            foreach ($this->status_order as $pstatus) {
                $html .= "<h3>" . $pstatus . " Projects</h3>";
                $html .= $this->create_table($pstatus);
            }
            return $html;   
        }


        /**
         * Create HTML table with ALL STATUSES COMBINED
         * 
         * @since 2.2.0
         * 
         * @return string Html table of all statuses combined.
         */
        public function get_complete_table_in_html() {
            $fields = $this->get_table_fields();
            $projects = $this->get_all_data_for_display();
            $args["class"] = ["tt-table", "project-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $projects, $args, "tt_project", "ProjectID");
            return $table;
        }


        /**
         * Get details from db
         * 
         * @since 1.0.0
         * 
         * @param string $pstatus One project status to query projects for. Default null
         * 
         * @return array|object Details of projects from database query.
         */
        private function get_projects_from_db($pstatus=null) {
            $sql_string = "SELECT tt_project.*, tt_client.Company, 
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
                    ON tt_project.ProjectID = NewTable.ProjectID";
            $sql_string .= $this->get_where_clauses($pstatus);
            $sql_string .= " ORDER BY tt_project.ProjectID DESC";
            return tt_query_db($sql_string);
        }


        /**
         * Get where clauses depending on input
         * 
         * @since 2.2.0
         * @since 3.0.13 updated to find statuses not in predefined list when 'Other' passed as pstatus
         * 
         * @param string $pstatus One project status to query projects for. Default null
         * 
         * @return string Where clause to be added to end of sql query.
         */
        private function get_where_clauses($pstatus = null) {
            global $wpdb;
            $where_clauses = array();
            $where_clause = "";
            if ($this->clientid <> null) {
                array_push($where_clauses, "tt_project.ClientID = " . $this->clientid);
            }
            if ($this->projectid <> null) {
                array_push($where_clauses, "tt_project.ProjectID = " . $this->projectid);
            }
            if ($pstatus <> null) {
                if ($pstatus == "Other") {
                    $clause = "(";
                    foreach ($this->status_order as $status) {
                        if ($status <> "Other") {
                            if (strlen($clause) > 1) {
                                $clause .= " AND ";
                            }
                            $clause .= "tt_project.PStatus <> '" . $status . "'";
                        }
                    }
                    $clause .= ")";
                    array_push($where_clauses, $clause);
                } else {
                    array_push($where_clauses, "tt_project.PStatus = '" . $pstatus . "'");
                }
            }            
            if ( ($this->startdate <> "") and ($this->startdate <> null) ) {
                array_push($where_clauses, "tt_project.PDateStarted >= '" . $this->startdate . "'");
            }
            if ( ($this->enddate <> "") and ($this->enddate <> null) ) {
                array_push($where_clauses, "tt_project.PDueDate <= '" . $this->enddate . "'");
            }
            if ( ($this->notes <> "") and ($this->notes <> null) ) {
                //Ref: https://developer.wordpress.org/reference/classes/wpdb/esc_like/
                $wild = "%";
                $search_like = "'" . $wild . $wpdb->esc_like( $this->notes ) . $wild . "'";
                array_push($where_clauses, "tt_project.PDetails LIKE " . $search_like);
            }
            if ( (count($where_clauses) > 1) or ((count($where_clauses) == 1) and ($where_clauses[0] <> "")) ) {
                $where_clause = " WHERE ";
                $where_clause .= implode(" AND ", $where_clauses);
            }
            return $where_clause;
        }


        /**
         * Get table column order and table fields
         * 
         * @since 1.4.0
         * @since 3.0.13 Clarified column heading. Updated to use new field definition class.
         * 
         * @return array Multi-dimensional array of columns, each one having column details in key-value pairs.
         */
        private function get_table_fields() {
            $flds = new Time_Tracker_Display_Fields();
            $cols = [
                "Project ID" => $flds->projectid,
                "Project" => $flds->project_name,
                "Client" => $flds->client_select,
                "Category" => $flds->project_category,
                "Status" => $flds->project_status,
                "Date Added" => $flds->project_date_started,
                "Last Worked" => $flds->project_last_worked,
                "Due Date" => $flds->project_due_date,
                "Notes" => $flds->project_details,
                "Time Logged vs Estimate" => $flds->time_logged_v_estimate
            ];
            return $cols;
        }


        /**
         * Get Due Date Class
         * 
         * @since 1.0.0
         * 
         * @param date|string Due date of project.
         * @param string $projstatus Status of project.
         * 
         * @return string Class name to apply to project.
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
         * Iterate through data and add additional information for table
         * 
         * @since 2.2.0
         * 
         * @param string $projstatus Status of project. Default null
         * 
         * @return array Multi-dimensional array of projects, each project having details for display.
        **/
        private function get_all_data_for_display($pstatus=null) {
            $projects = $this->get_projects_from_db($pstatus);
            //add database data with time evaluations, classes, buttons, etc to forward on to table
            foreach ($projects as $item) {
                $duedate = sanitize_text_field($item->PDueDate);
                $projstatus = sanitize_text_field($item->PStatus);

                $project_details_button = "<button onclick='open_time_entries_for_project(\"" . esc_attr(sanitize_textarea_field($item->PName)) . "\")' id=\"project-" . esc_attr(sanitize_text_field($item->ProjectID))  . "\" class=\"open-project-detail tt-table-button\">View Time</button>";
                $delete_project_button = "<button onclick='location.href = \"" . TT_HOME . "delete-item/?project-id=" . esc_attr(sanitize_text_field($item->ProjectID)) . "\"' id=\"delete-project-" . esc_attr(sanitize_text_field($item->ProjectID))  . "'\" class=\"open-delete-page tt-button tt-table-button\">Delete</button>";
                $item->ProjectID = [
                    "value" => $item->ProjectID,
                    "button" => [
                        $project_details_button,
                        $delete_project_button
                    ]
                ];

                $due_date_class = $this->get_due_date_class($duedate, $projstatus);
                $item->PDueDate = [
                    "value" => $item->PDueDate,
                    "class" => $due_date_class
                ];

                $time_estimate_formatted = get_time_estimate_formatted(sanitize_text_field($item->PTimeEstimate));
                $hours_logged = tt_convert_to_decimal_time(sanitize_text_field($item->LoggedHours), sanitize_text_field($item->LoggedMinutes));
                $percent_time_logged = get_percent_time_logged($time_estimate_formatted, $hours_logged);
                $time_worked_vs_estimate_class = get_time_estimate_class($percent_time_logged);
                $item->TimeLoggedVsEstimate = [
                    "value" => $hours_logged . " / " . $time_estimate_formatted . "<br/>" . $percent_time_logged . "%",
                    "class" => $time_worked_vs_estimate_class
                ];
            }
            return $projects;
        }

    } //close class

} //close if class exists