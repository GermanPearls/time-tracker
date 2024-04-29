<?php
/**
 * Class Time_Tracker_Activator_Tables
 *
 * Initial activation of Time Tracker Plugin. Creates tables in Wordpress database.
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

/**
 * If class doesn't exist
 * 
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_Activator_Tables') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */
    class Time_Tracker_Activator_Tables {

        public static $charset_collate;


        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            //self::setup_tables(self::get_table_list());
        }


        /**
         * Setup
         * 
         * @since 1.0.0
         */
        public static function setup() {
            self::setup_tables(self::get_table_list());
        }


        /**
         * Define table array
         * 
         * @since 1.0.0
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
         * Database Table Updates
         * 
         * @since 2.5.0
         * 
         * @param string $old_ver Old version of Time Tracker plugin, in form x.x.x.
         */
        public static function check_tables_for_updates($old_ver) {
            $ver = explode(".", $old_ver);
            if ( (intval($ver[0]) ==2) and (intval($ver[1]) < 5)) {
                self::tt_update_tables_to_two_five();
            }
        }

        /**
         * Update to version 2.5.0
         * 
         * @since 2.5.0
         */
        private static function tt_update_tables_to_two_five() {
            //add rate to client table
            global $wpdb;
            $sqlcheck = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME='tt_client' AND COLUMN_NAME='BillingRate'";
            $col = $wpdb->query($sqlcheck);
            if (empty($col)) {
                $sqladd = "ALTER TABLE tt_client ADD BillingRate int(11) NULL DEFAULT NULL";
                $wpdb->query($sqladd);
            }
        }
        
        
        /**
         * Create tables
         * 
         * @since 1.0.0
         * 
         * @param array $table_list {
         *      @type string Name of table.
         * }
         */
        private static function setup_tables($table_list) {
            global $wpdb;
            self::$charset_collate = $wpdb->get_charset_collate();
            
            //need this for dbdelta to work
            //require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
            foreach($table_list as $table) {
                //dbDelta spits out error on foreign keys
                //dbDelta(self::sql_create_table($table) ); 
                $wpdb->query(self::sql_create_table($table));
            }
        }


        /**
         * Table Details
         * 
         * @since 1.0.0
         * 
         * @param string $table_name Name of table.
         * 
         * @return string SQL command to create table.
         */
        private static function sql_create_table($table_name) {
            if ($table_name == 'tt_client') {
                $sql = "CREATE TABLE IF NOT EXISTS tt_client (
                    ClientID int(11) NOT NULL auto_increment,
                    Company varchar(100) NULL DEFAULT '',
                    Contact varchar(100) DEFAULT NULL,
                    Email varchar(100) DEFAULT NULL,
                    Phone varchar(100) DEFAULT NULL,
                    Billable tinyint(1) NOT NULL DEFAULT '1',
                    BillTo varchar(100) DEFAULT NULL,
                    BillingRate int(11) NULL DEFAULT NULL,
                    Source varchar(100) NULL DEFAULT '',
                    SourceDetails varchar(500) DEFAULT NULL,
                    CComments text DEFAULT NULL COMMENT 'client comments',
                    DateAdded timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    CSubmission text DEFAULT NULL,
                    PRIMARY KEY  (ClientID)
                ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_project') {
                $sql = "CREATE TABLE IF NOT EXISTS tt_project (
                    ProjectID int(11) NOT NULL auto_increment,
                    PName varchar(100) NULL DEFAULT '',
                    ClientID int(11) NOT NULL,
                    PCategory varchar(100) NULL,
                    PStatus varchar(100) DEFAULT NULL,
                    PTimeEstimate time DEFAULT NULL,
                    PDateStarted datetime NULL,
                    PDueDate date NOT NULL,
                    PDetails varchar(500) DEFAULT NULL,
                    Link varchar(100) DEFAULT NULL,
                    PSubmission text DEFAULT NULL,
                    PRIMARY KEY  (ProjectID),
                    FOREIGN KEY FK_ProjectTableToClientTable (ClientID) REFERENCES tt_client(ClientID)
                  ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_task') {
                $sql = "CREATE TABLE IF NOT EXISTS tt_task (
                    TaskID int(11) NOT NULL auto_increment,
                    TDescription text DEFAULT NULL,
                    ClientID int(11) NOT NULL,
                    ProjectID int(11) DEFAULT NULL,
                    TCategory varchar(100) NULL,
                    RecurringTaskID int(11) DEFAULT NULL,
                    TStatus varchar(50) DEFAULT NULL,
                    TTimeEstimate time DEFAULT '00:00:00',
                    TDateAdded datetime NULL,
                    TDueDate date NOT NULL,
                    TNotes text DEFAULT NULL,
                    TSubmission text DEFAULT NULL,
                    PRIMARY KEY  (TaskID),
                    FOREIGN KEY FK_TaskTableToClientTable (ClientID) REFERENCES tt_client(ClientID),
                    FOREIGN KEY FK_TaskTableToProjectTable (ProjectID) REFERENCES tt_project(ProjectID),
                    FOREIGN KEY FK_TaskTableToRecurringTaskTable (RecurringTaskID) REFERENCES tt_recurring_task(RecurringTaskID)                
                    ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_recurring_task') {
                $sql = "CREATE TABLE IF NOT EXISTS tt_recurring_task (
                    RecurringTaskID int(11) NOT NULL auto_increment,
                    RTName varchar(1500) NULL DEFAULT '',
                    ClientID int(11) NOT NULL,
                    ProjectID int(11) DEFAULT NULL,
                    RTTimeEstimate time NOT NULL DEFAULT '00:00:00',
                    RTDescription text DEFAULT NULL,
                    RTCategory varchar(100) NULL,
                    Frequency varchar(250) NULL DEFAULT '',
                    LastCreated date NOT NULL,
                    EndRepeat date NOT NULL,
                    RTSubmission text DEFAULT NULL,
                    PRIMARY KEY  (RecurringTaskID),
                    FOREIGN KEY FK_RecurringTaskTableToClientTable (ClientID) REFERENCES tt_client(ClientID),
                    FOREIGN KEY FK_RecurringTaskTableToProjectTable (ProjectID) REFERENCES tt_project(ProjectID)
                    ) " . self::$charset_collate . ";";
            } elseif ($table_name == 'tt_time') {
                $sql = "CREATE TABLE IF NOT EXISTS tt_time (
                    TimeID int(11) NOT NULL auto_increment,
                    StartTime datetime NULL,
                    EndTime datetime NULL,
                    TNotes text DEFAULT NULL,
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
}