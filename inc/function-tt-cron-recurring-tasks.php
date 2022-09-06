<?php
/**
 * 
 * Class TT_Cron_Recurring_Tasks
 * 
 * Hooks into WP cron to schedule recurring tasks
 *
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

use \DateTime;
use \DateTimeImmutable;
use \DateTimeZone;
use \DateTimeImmutable\modify as modify;
use \DateTimeImmutable\createFromMutable as createFromMutable;

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
	    public $recurring_tasks;

        
        /**
         * Constructor
         * 
         */
        public function __construct() {
        	$this->query_db_for_recurring_tasks();
	    }


        /**
         * Create new tasks
        * 
        */
        public function create_new_tasks() {
            if ($this->recurring_tasks != null) {
                return $this->create_missing_tasks();
            } else {
                return 0;
            }
        }
	    
	    
        /**
        * Create  recurring tasks
        *
        **/
        private function create_missing_tasks() {
            foreach ($this->recurring_tasks as $task) {      
                $last_created_obj = get_last_created_date($task);
                $last_created_plus_week = $last_created_obj->modify('next Sunday');
                $last_created_plus_month = $last_created_obj->modify('first day of next month');

                /** For weekly tasks, if it's been more than a week since the last task was created, create the next Sunday's task **/
                if ( (sanitize_text_field($task->Frequency) == "Weekly") && ($today >= $last_created_plus_week)) {                                      
                    $this->create_new_task(
                    sanitize_text_field($task->RTName) . " " . $last_created_plus_week->format("n/j/y"),
                    sanitize_text_field($task->ClientID),
                    (($task->ProjectID == null) OR ($task->ProjectID == '')) ? null : sanitize_text_field($task->ProjectID),
                    sanitize_text_field($task->RTTimeEstimate),
                    date_format($last_created_plus_week->modify('next Friday'), 'Y-m-d'),
                    sanitize_text_field($task->RTDescription),
                    sanitize_text_field($task->Frequency) . " Recurring Task ID " . sanitize_text_field($task->RecurringTaskID),
                    sanitize_text_field($task->RTCategory),
                    sanitize_text_field($task->RecurringTaskID)
                    );
                    $this->created = $this->created + 1;
                    $this->update_last_created(sanitize_text_field($task->RecurringTaskID), $last_created_plus_week->format("Y-m-d"));

                /** For monthly tasks, if it's past the next 1st of the month, create the next month's task **/
                } elseif ( (sanitize_text_field($task->Frequency) == "Monthly") && ($today >= $last_created_plus_month)) {
                    $this->create_new_task(
                    sanitize_text_field($task->RTName). " " . $last_created_plus_month->format("F Y"),
                    sanitize_text_field($task->ClientID),
                    (($task->ProjectID == null) OR ($task->ProjectID == '')) ? null : sanitize_text_field($task->ProjectID),
                    sanitize_text_field($task->RTTimeEstimate),
                    date_format($last_created_plus_month->modify('last day of this month'), 'Y-m-d'),
                    sanitize_text_field($task->RTDescription),
                    sanitize_text_field($task->Frequency) . " Recurring Task ID " . sanitize_text_field($task->RecurringTaskID),
                    sanitize_text_field($task->RTCategory),
                    sanitize_text_field($task->RecurringTaskID)
                    );
                    $this->created = $this->created + 1;
                    $this->update_last_created(sanitize_text_field($task->RecurringTaskID), $last_created_plus_month->format("Y-m-d"));
                } 
                }  
                log_cron('Recurring task cron completed, ' . $this->created . ' new task(s) created.');
                return $this->created;		
        }
	
	    
        /**
        * Check if any recurring tasks need to be created
        *
        **/
        private function query_db_for_recurring_tasks() {
            $today = new \DateTimeImmutable();
            $this->recurring_tasks = $this->get_recurring_tasks_from_db();
            if ($this->recurring_tasks == null) {
                log_cron('no recurring tasks returned from db query');
            } else {
                log_cron('recurring tasks is ' . print_r($this->recurring_tasks, true));
            }
        }
	
	    
        /**
        * Get Last Created Date
        *
        **/
        private function get_last_created_date($tsk) {
            if ($tsk->LastCreated == "0000-00-00") {
                $today = new \DateTimeImmutable();
                $last_created_obj = $today->modify('last day of last month');
            } else {
                $tz = (get_option('timezone_string')) ? new DateTimeZone(get_option('timezone_string')) : new DateTimeZone('UTC');  
                $last_created_obj = date_create_immutable_from_format('Y-m-d', trim($tsk->LastCreated), $tz);
            }
            return $last_created_obj;
        }
        
                
        /**
         * Add new task to db
         * 
         */
        private function create_new_task($desc, $client, $proj, $time_est, $due, $notes, $details, $category, $r_task_id) {
            global $wpdb;
            $table_name = 'tt_task';
            //wpdb->insert columns and values should be raw (not escaped) per https://developer.wordpress.org/reference/classes/wpdb/insert/#parameters
            $result = $wpdb->insert( $table_name, array(
                'TDescription' => $desc,
                'ClientID'   => $client,
                'ProjectID'    => $proj,
                'TCategory' => $category,
                'RecurringTaskID' => $r_task_id,
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
            global $wpdb;
            //wpdb->update columns and values should be raw (not escaped) per https://developer.wordpress.org/reference/classes/wpdb/update/#parameters
            $result = $wpdb->update('tt_recurring_task', array('LastCreated'=>$new_date), array('RecurringTaskID'=>$task_id));
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }
        
        
        /**
         * Query recurring tasks table for all active recurring tasks
         * 
         */
        private function get_recurring_tasks_from_db() {
            global $wpdb;
            $today_object = new \DateTime();
            $today_formatted_for_sql = date_format($today_object, 'Y-m-d');

            $sql_string = $wpdb->prepare('SELECT * FROM `tt_recurring_task` WHERE (EndRepeat = %s) OR (EndRepeat >= %s)', "0000-00-00", $today_formatted_for_sql);    
            $result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $result;
        }

    }  //class
} //close if class exists



/**
 * Define cron callback function
 * 
 */
function tt_create_recurring_tasks_function() {
    $recurring_task_check = new TT_Cron_Recurring_Tasks();
    return $recurring_task_check->create_new_tasks();
}


/**
 * Add action to cron
 * 
 */
add_action('tt_recurring_task_check', 'tt_create_recurring_tasks_function', 10, 2);


/**
 * schedule cron job daily, if it's not already scheduled
 * 
 */
if ( ! wp_next_scheduled('tt_recurring_task_check') ) {
    wp_schedule_event(time(), 'daily', 'tt_recurring_task_check');
}


/**
 * Manual request to run recurring task check - via ajax call
 * 
 */
function tt_run_recurring_task_cron() {
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if (check_ajax_referer('tt_run_recurring_task_cron_nonce', 'security')) {
            $tsks = tt_create_recurring_tasks_function();
            $return = array(
                'success' => true,
                'msg' => "System checked for missing tasks from recurring jobs, " . $tsks . " task(s) created."
            );
            wp_send_json_success($return, 200); 
        } else {
            $return = array(
                'success' => false,
                'msg' => 'Security check failed. Action aborted.'
            );
            wp_send_json_error($return, 500);
        }
    } else {
        $return = array(
            'success' => false,
            'msg' => 'Incorrect request. Action aborted.'
        );
        wp_send_json_error($return, 500);
    }
    die();
}
