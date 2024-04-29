<?php
/**
 * Time Tracker Class_Hours_Worked_Detail 
 *
 * Get work time history from database
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class does not already exist
 * 
 * @since 1.0.0
 */
if ( !class_exists( 'Class_Hours_Worked_Detail' ) ) {

 
    /**
     * Main class
     * 
     * @since 1.0.0
     */
    class Class_Hours_Worked_Detail
    {

        private $clientid = null;
        private $projectid = null;
        private $rectaskid = null;
        private $taskid = null;
        private $timeid = null;        
        private $notes = null;
        private $startdate = null;
        private $enddate = null;
        protected $hours_worked;


        /**
         * Constructor
         * 
         * @since 1.0.0
         */     
        public function __construct() {
            $this->hours_worked = $this->query_database();
        }


        /**
         * Get data from db
         * TODO: Move to external function
         * 
         * @since 1.0.0
         * 
         * @return array Result of sql query.
         */ 
        private function query_database() {
            return tt_query_db($this->get_sql_string(), "array");
        }


        /**
         * Create sql query to get data
         * 
         * @since 1.0.0
         * 
         * @return string Sql string used to get data.
         */ 
        private function get_sql_string() {
            $sql_start = "SELECT tt_time.StartTime as StartTime, tt_time.EndTime as EndTime,
                EXTRACT(week FROM tt_time.StartTime) as WorkWeek,
                EXTRACT(week FROM Now()) as ThisWeek,
                MONTH(tt_time.StartTime) as WorkMonth,
                YEAR(tt_time.StartTime) as WorkYear,
                Minute(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as MinutesWorked,
                Hour(TIMEDIFF(tt_time.EndTime, tt_time.StartTime)) as HoursWorked,
                tt_client.Company, tt_client.Billable, tt_client.BillTo, tt_client.BillingRate, tt_project.PName, tt_time.Invoiced, tt_time.InvoicedTime as BilledTime
            FROM tt_time 
            LEFT JOIN tt_client ON tt_time.ClientID = tt_client.ClientID 
            LEFT JOIN tt_task ON tt_time.TaskID = tt_task.TaskID 
            LEFT JOIN tt_project ON tt_task.ProjectID = tt_project.ProjectID";
            $orderby = "ORDER BY WorkYear ASC, WorkMonth ASC, BillTo ASC, Company ASC";
            return $sql_start . " " . $this->get_where_clauses() . " " . $orderby;
        }


        /**
         * Refine data by query parameters
         * 
         * @since 2.4.0
         */      
        private function get_query_params() {
            $this->timeid = (isset($_GET['time-id']) ? intval($_GET['time-id']) : null);
            if (isset($_GET['task-id'])) {
                $this->taskid = intval($_GET['task-id']);
            } elseif (isset($_GET['task-number']) && $_GET['task-number'] <> "") {
                $this->taskid = intval($_GET['task-number']);
            } elseif (isset($_GET['task'])) {
                $this->taskid = (! is_null($_GET['task']) and $_GET['task'] <> "") ? get_task_id_from_name(sanitize_text_field($_GET['task'])) : null;
            }
            $this->rectaskid = (isset($_GET['recurring-task-id']) and $_GET['recurring-taask-id'] <> "") ? intval($_GET['recurring-task-id']) : null;
            if (isset($_GET['project-name'])) {
                $this->projectid = (! is_null($_GET['project-name']) and $_GET['project-name'] <> "") ? get_project_id_from_name(sanitize_text_field($_GET['project-name'])) : null;
            } elseif (isset($_GET['project-id'])) {
                $this->projectid = (! is_null($_GET['project-id']) and $_GET['project-id'] <> "") ? intval($_GET['project-id']) : null;
            }
            if (isset($_GET['client-name'])) {
                $this->clientid = (! is_null($_GET['client-name']) and $_GET['client-name'] <> "") ? get_client_id_from_name(sanitize_text_field($_GET['client-name'])) : null;
            } elseif (isset($_GET['client-id'])) {
				$this->clientid = (! is_null($_GET['client-id']) and $_GET['client-id'] <> "") ? intval($_GET['client-id']) : null;
            }
            $this->notes = (isset($_GET['notes']) ? sanitize_text_field($_GET['notes']) : null);
            $this->startdate = (isset($_GET['first-date']) ? sanitize_text_field($_GET['first-date']) : null);
            $this->enddate = (isset($_GET['last-date']) ? sanitize_text_field($_GET['last-date']) : null);
        }


        /**
         * Get where clauses depending on input
         * 
         * @since 2.4.0
         * 
         * @return string Sql where clause to put at end of sql string.
         */
        private function get_where_clauses() {
            global $wpdb;
            $this->get_query_params();
            $where_clauses = array();
            $where_clause = "";
            if (! is_null($this->clientid) or $this->clientid === 0) {
                array_push($where_clauses, "tt_time.ClientID = " . $this->clientid);
            }
            if (! is_null($this->projectid) or $this->projectid === 0) {
                //no project id field in time table - so we have to get tasks and then time associated with those tasks
                array_push($where_clauses, "tt_task.ProjectID = " . $this->projectid);
            }
            if (! is_null($this->rectaskid) or $this->rectaskid === 0) {
                //no recurring task id field in time table - so we have to get tasks and then time associated with those tasks
                array_push($where_clauses, "tt_task.RecurringTaskID = " . $this->rectaskid);
            }
            if ( $this->taskid != null or $this->taskid === 0) {
                array_push($where_clauses, "tt_time.TaskID = " . $this->taskid);
            }
            if ( ($this->timeid <> "") and (! is_null($this->timeid)) and ($this->timeid <> "null") ) {
                array_push($where_clauses, "tt_time.TimeID = " . $this->timeid);
            }
            if ( ($this->startdate <> "") and (! is_null($this->startdate)) ) {
                array_push($where_clauses, "tt_time.StartTime >= '" . $this->startdate . " 00:00:01'");
            }
            if ( ($this->enddate <> "") and (! is_null($this->enddate)) ) {
                array_push($where_clauses, "tt_time.EndTime <= '" . $this->enddate . " 23:59:59'");
            }
            if ( ($this->notes <> "") and (! is_null($this->notes)) ) {
                //Ref: https://developer.wordpress.org/reference/classes/wpdb/esc_like/
                $wild = "%";
                $search_like = "'" . $wild . $wpdb->esc_like( $this->notes ) . $wild . "'";
                array_push($where_clauses, "tt_time.TNotes LIKE " . $search_like);
            }
            if ( (count($where_clauses) > 1) or ((count($where_clauses) == 1) and ($where_clauses[0] <> "")) ) {
                $where_clause = " WHERE ";
                $where_clause .= implode(" AND ", $where_clauses);
            }
            return $where_clause;
        }
        
    } //close class
} //if class exists