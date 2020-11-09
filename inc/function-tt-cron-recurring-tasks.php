<?php
/**
 * 
 * Class TT_Cron_Recurring_Tasks
 * 
 * Hooks into WP cron to schedule recurring tasks
 *
 * 
 * @since 1.0
 * 
 */

 defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'TT_Cron_Recurring_Tasks' ) ) {


    /**
     * Class
     * 
     */
    class TT_Cron_Recurring_Tasks
    {

        
        public $created = 0;

        
        /**
         * Constructor
         * 
         */
        public function __construct() {
            require_once(ABSPATH . 'wp-includes/wp-db.php'); //use wordpress db class
            require_once(ABSPATH . 'wp-config.php');  //wordpress db details and error handling
            //if (! defined('TT_DB_NAME')) {
                //define('TT_DB_NAME', DB_NAME . "_tt"); //time tracker database name
            //}
        }


        /**
         * Create new tasks
         * 
         */
        public function create_new_tasks() {
            $today = new DateTime();
            $recurring_tasks = $this->get_recurring_tasks_from_db();
            if ($recurring_tasks == null) {
                return;
            }

            foreach ($recurring_tasks as $task) {        
                $last_created_obj = date_create_immutable_from_format('Y-m-d', $task->LastCreated);
                $last_created_plus_week = $last_created_obj->modify('next Sunday');
                $last_created_plus_month = $last_created_obj->modify('first day of next month');

                /*
                 * For weekly tasks, if it's been more than a week since the last task was created, create the next Sunday's task
                 * 
                 */
                if ( ($task->Frequency == "Weekly") && ($today >= $last_created_plus_week)) {                                      
                    $due_date = date_format($last_created_plus_week->modify('next Friday'), 'Y-m-d');
                    $this->create_new_task(
                        $task->RTName . " " . $last_created_plus_week->format("n/j/y"),
                        $task->ClientID,
                        $task->ProjectID,
                        $task->RTTimeEstimate,
                        $due_date,
                        $task->RTDescription,
                        $task->Frequency . " Recurring Task ID " . $task->RecurringTaskID
                    );
                    $this->created = $this->created + 1;
                    $this->update_last_created($task->RecurringTaskID, $last_created_plus_week->format("Y-m-d"));

                /*
                 * For monthly tasks, if it's past the next 1st of the month, create the next month's task
                 * 
                 */
                } elseif ( ($task->Frequency == "Monthly") && ($today >= $last_created_plus_month)) {
                    $due_date = date_format($last_created_plus_month->modify('last day of month'), 'Y-m-d');
                    $this->create_new_task(
                        $task->RTName . " " . $last_created_plus_month->format("F Y"),
                        $task->ClientID,
                        $task->ProjectID,
                        $task->RTTimeEstimate,
                        $due_date,
                        $task->RTDescription,
                        $task->Frequency . " Recurring Task ID " . $task->RecurringTaskID
                    );
                    $this->created = $this->created + 1;
                    $this->update_last_created($task->RecurringTaskID, $last_created_plus_month->format("Y-m-d"));
                } //monthly
            }  //for each
            log_cron('Recurring task cron completed, ' . $this->created . ' new task(s) were created.');
        }
        
                
        /**
         * Add new task to db
         * 
         */
        private function create_new_task($desc, $client, $proj, $time_est, $due, $notes, $details) {
            //if ( ($tt_db == false) or ($tt_db instanceof wpdb) != true) {
                //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            //}
            global $wpdb;
            $table_name = 'tt_task';
            $result = $wpdb->insert( $table_name, array(
                'TDescription' => $desc,
                'ClientID'   => $client,
                'ProjectID'    => $proj,
                'TStatus' => "New",
                'TTimeEstimate' => $time_est,
                'TDueDate' => $due,
                'TNotes' => $notes,
                'TSubmission' => $details
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }


        /**
         * Updated last created date for recurring task
         * 
         */
        private function update_last_created($task_id, $new_date) {
            //if ( ($tt_db == false) or ($tt_db instanceof wpdb) != true) {
              //  $tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            //}
            global $wpdb;
            $result = $wpdb->update('tt_recurring_task', array('LastCreated'=>$new_date), array('RecurringTaskID'=>$task_id));
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }
        
        
        /**
         * Query recurring tasks table for all active recurring tasks
         * 
         */
        private function get_recurring_tasks_from_db() {
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $today_object = new DateTime();
            $today_formatted_for_sql = date_format($today_object, 'Y-m-d');

            $sql_string = $wpdb->prepare('SELECT * FROM `tt_recurring_task` WHERE (EndRepeat = %s) OR (EndRepeat <= %s)', "0000-00-00", $today_formatted_for_sql);    
            $result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $result;
        }

    }  //class
} //close if class exists


/**
 * Define cron function
 * 
 */
$recurring_task_check = new TT_Cron_Recurring_Tasks();
add_action('tt_recurring_task_check', array($recurring_task_check, 'create_new_tasks'), 10, 2);


/**
 * schedule cron job daily, if it's not already scheduled
 * 
 */
if ( ! wp_next_scheduled('tt_recurring_task_check') ) {
    //$args = array(
    //    'sslverify' => apply_filters('https_local_ssl_verify', true)
    //);
    wp_schedule_event(time(), 'daily', 'tt_recurring_task_check');
}