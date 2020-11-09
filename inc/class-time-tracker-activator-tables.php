<?php
/**
 * Class Time_Tracker_Activator_Tables
 *
 * Initial activation of Time Tracker Plugin - CREATE SQL DB AND TABLES
 * 
 * @since 1.0
 * 
 */

/**
 * If class doesn't exist
 * 
 */
if ( ! class_exists('Time_Tracker_Activator_Tables') ) {

    /**
     * Class
     * 
     */
    class Time_Tracker_Activator_Tables {

        public static $charset_collate;


        /**
         * Constructor
         * 
         */
        public function __construct() {
            //self::setup_tables(self::get_table_list());
        }


        /**
         * Setup
         * 
         */
        public static function setup() {
            self::setup_tables(self::get_table_list());
        }


        /**
         * Define table array
         * 
         */
        public static function get_table_list() {
            $table_list = array(
                'tt_client',
                'tt_project',
                'tt_recurring_task',
                'tt_task',
                'tt_time'
            );
            return $table_list;
        }
        
        
        /**
         * Create tables in separate database
         * Tie into WP's dbDelta to aid in updates
         */
        private static function setup_tables($table_list) {
            //dbDelta function only works with wordpress database, trick it to work with TT database temporarily, then switch it back
            //Ref: https://codex.wordpress.org/Creating_Tables_with_Plugins
            global $wpdb;
            //$original_wpdb = $wpdb;
            //$wpdb = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            
            self::$charset_collate = $wpdb->get_charset_collate();
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
            foreach($table_list as $table) {
                dbDelta(self::sql_create_table($table) ); 
            }

            //switch wpdb to real wordpress database
            //$wpdb = $original_wpdb;
        }


        /**
         * Table Details
         * 
         */
        private static function sql_create_table($table_name) {
            if ($table_name == 'tt_client') {
                $sql = "CREATE TABLE tt_client (
                    ClientID int(11) NOT NULL auto_increment,
                    Company varchar(100) NOT NULL,
                    Contact varchar(100) DEFAULT NULL,
                    Email varchar(100) DEFAULT NULL,
                    Phone varchar(100) DEFAULT NULL,
                    Billable tinyint(1) NOT NULL DEFAULT '1',
                    BillTo varchar(100) DEFAULT NULL,
                    Source varchar(100) NOT NULL,
                    SourceDetails varchar(500) DEFAULT NULL,
                    CComments text DEFAULT NULL COMMENT 'client comments',
                    DateAdded timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    CSubmission text DEFAULT NULL,
                    PRIMARY KEY  (ClientID)
                ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_project') {
                $sql = "CREATE TABLE tt_project (
                    ProjectID int(11) NOT NULL auto_increment,
                    PName varchar(100) NOT NULL,
                    ClientID int(11) NOT NULL,
                    PCategory varchar(100) NULL,
                    PStatus varchar(100) DEFAULT NULL,
                    PTimeEstimate time DEFAULT NULL,
                    PDateStarted datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PDueDate date NOT NULL,
                    PDetails varchar(500) DEFAULT NULL,
                    Link varchar(100) DEFAULT NULL,
                    PSubmission text DEFAULT NULL,
                    PRIMARY KEY  (ProjectID),
                    FOREIGN KEY FK_ProjectTableToClientTable (ClientID) REFERENCES tt_client(ClientID)
                  ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_task') {
                $sql = "CREATE TABLE tt_task (
                    TaskID int(11) NOT NULL auto_increment,
                    TDescription text NOT NULL,
                    ClientID int(11) NOT NULL,
                    ProjectID int(11) DEFAULT NULL,
                    TCategory varchar(100) NULL,
                    TStatus varchar(50) DEFAULT NULL,
                    TTimeEstimate time DEFAULT '00:00:00',
                    TDateAdded datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    TDueDate date DEFAULT NULL,
                    TNotes text,
                    TSubmission text DEFAULT NULL,
                    PRIMARY KEY  (TaskID),
                    FOREIGN KEY FK_TaskTableToClientTable (ClientID) REFERENCES tt_client(ClientID),
                    FOREIGN KEY FK_TaskTableToProjectTable (ProjectID) REFERENCES tt_project(ProjectID)                
                    ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_recurring_task') {
                $sql = "CREATE TABLE tt_recurring_task (
                    RecurringTaskID int(11) NOT NULL auto_increment,
                    RTName varchar(1500) NOT NULL,
                    ClientID int(11) NOT NULL,
                    ProjectID int(11) DEFAULT NULL,
                    RTTimeEstimate time NOT NULL DEFAULT '00:00:00',
                    RTDescription text DEFAULT NULL,
                    Frequency varchar(250) NOT NULL,
                    EndRepeat date DEFAULT NULL,
                    RTSubmission text DEFAULT NULL,
                    PRIMARY KEY  (RecurringTaskID),
                    FOREIGN KEY FK_RecurringTaskTableToClientTable (ClientID) REFERENCES tt_client(ClientID),
                    FOREIGN KEY FK_RecurringTaskTableToProjectTable (ProjectID) REFERENCES tt_project(ProjectID)
                    ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_time') {
                $sql = "CREATE TABLE tt_time (
                    TimeID int(11) NOT NULL auto_increment,
                    StartTime datetime NOT NULL,
                    EndTime datetime NOT NULL,
                    TNotes text NOT NULL,
                    ClientID int(11) DEFAULT NULL,
                    TaskID int(11) DEFAULT NULL,
                    FollowUp text DEFAULT NULL,
                    Invoiced varchar(100) DEFAULT NULL,
                    InvoiceNumber varchar(100) DEFAULT NULL,
                    InvoicedTime decimal(10,2) DEFAULT NULL,
                    InvoiceComments varchar(100) DEFAULT NULL,
                    NewTaskStatus varchar(50) DEFAULT NULL,
                    TStamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    TimeSubmission text DEFAULT NULL,
                    PRIMARY KEY  (TimeID),
                    FOREIGN KEY FK_TimeTableToClientTable (ClientID) REFERENCES tt_client(ClientID),
                    FOREIGN KEY FK_TimeTableToTaskTable (TaskID) REFERENCES tt_task(TaskID)                      
                    ) " . self::$charset_collate . ";";
            } else {
                $sql = "";
            }        
            return $sql;
        }

    } //close class

}  //close if class exists