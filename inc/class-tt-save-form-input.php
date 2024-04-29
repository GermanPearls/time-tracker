<?php
/**
 * Class Save_Form_Input
 *
 * Save form input into db
 * 
 * @since 1.0.0
 * @since 1.0.0 - 8/14/20 update - CF7(ver5.2.1) now returning select fields in an array
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */
if ( !class_exists( 'Save_Form_Input' ) ) {
    

    /**
     * Class
     * 
     * @since 1.0.0
     */ 
    class Save_Form_Input
    {
        
        
        /**
         * Class Variables
         * 
         * @since 1.0.0
         */ 
        //private $data;
        private $form_post_id;
        private $original_submission;
        private $result;
        private $client_id;
        private $project_id;
        private $task_id;
                
        
        /**
         * Constructor
         * 
         * @since 1.0.0
         * 
         * @param array $cleaned_data Sanitized form input, in array as key-value pairs.
         * @param int $id Form post ID in Wordpress database, used to identify which form was submitted.
         */ 
        public function __construct($cleaned_data, $id) {
            //removed $form added insertid
            $this->form_post_id = $id;
            //$data = $this->clean_data($raw_data);
            $data = $cleaned_data;

            $this->original_submission = $this->serialize_data($data);
            $this->client_id = $this->get_client_id($data);
            $this->project_id = $this->get_project_id($data);
            $this->task_id = $this->get_task_id($data);		
			
			/*** Add new task ***/
            if ( $this->form_post_id == tt_get_form_id('Add New Task') ) {
                $this->save_new_task($data, $this->client_id, $this->project_id, $this->task_id, $this->original_submission);
            
            /*** Add new project ***/
            } elseif ( $this->form_post_id == tt_get_form_id('Add New Project') ) {
                $this->save_new_project($data, $this->client_id, $this->original_submission);           

            /*** Add new client ***/
            } elseif ( $this->form_post_id == tt_get_form_id('Add New Client') ) {
                $this->save_new_client($data, $this->original_submission);

            /*** Add new recurring task ***/
            } elseif ( $this->form_post_id == tt_get_form_id('Add New Recurring Task') ) {
                $this->save_new_recurring_task($data, $this->client_id, $this->project_id, $this->original_submission);

            /*** Add new time entry ***/
            } elseif ( $this->form_post_id == tt_get_form_id('Add Time Entry') ) {
                $this->save_new_time_entry($data);
                
                //if the user entered a new task status update it in the task table
                if ( ($data['new-task-status'] <> null) and ($data['new-task-status'] <> '') and ($data['new-task-status'] <> '---')) {
                    $this->update_task_status($data);            
                }

                //if the user entered notes for follow up, create a new task
                if ( ($data['follow-up'] <> null) and ($data['follow-up'] <> '') ) {
                    $this->create_follow_up_task($data);            
                }
            } 
        }
        
        
        /**
         * Serialize data to store in db with record so we have record of original entry
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function serialize_data($data) {
            $this->original_submission = serialize($data);
            return $this->original_submission;
        }


        /**
         * Save new task into db
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function save_new_task($data) {
            global $wpdb;
            $table_name = 'tt_task';

            //Add New Record to Database
            //wpdb class prepares this so it doesn't need to be SQL escaped
            foreach ($data as $key => $val) {
                //supports task-category and category
                if (strpos(strtolower($key), "category") !== false) {
                    $cat = $val;
                }
            }

            $wpdb->insert( $table_name, array(
                'TDescription' => $data['task-description'],
                'ClientID'   => $this->client_id,
                'ProjectID'    => $this->project_id,
                'TCategory' => $cat,
                'TStatus' => "New",
                'TTimeEstimate' => $this->get_time_estimate($data),
                'TDateAdded' => date('Y-m-d H:i:s'),
                'TDueDate' => $this->reformat_date($data, "due-date"),
                'TNotes' => $data['notes'],
                'TSubmission' => $this->original_submission
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }


        /**
         * Save new recurring task into db
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function save_new_recurring_task($data) {
            global $wpdb;
            $table_name = 'tt_recurring_task';

            //Add New Record to Database
            //wpdb class prepares this so it doesn't need to be SQL escaped

            //CF7 vs WPF - default to Monthly
            $freq = "Monthly";
            if ( array_key_exists("recur-freq", $data) ) {
                $freq = $data["recur-freq"];
            } elseif ( array_key_exists("frequency", $data)) {
                $freq = $data["frequency"];
            }

            $desc = "";
            if ( array_key_exists("task-desc", $data) ) {
                $desc = $data["task-desc"];
            } elseif ( array_key_exists("task-notes", $data)) {
                $desc = $data["task-notes"];
            }            

            $wpdb->insert( $table_name, array(
                'RTName' => $data['task-name'],
                'ClientID'   => $this->client_id,
                'ProjectID'    => $this->project_id,
                'RTTimeEstimate' => $this->get_time_estimate($data),
                'RTDescription' => $desc,
                'RTCategory' => $data['category'],
                'Frequency' => $freq,
                'EndRepeat' => $this->reformat_date($data, "end-repeat"),
                'RTSubmission' => $this->original_submission
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }


        /**
         * Save new project into db
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function save_new_project($data) {
            global $wpdb;
            $table_name = 'tt_project';

            //Add New Record to Database
            $wpdb->insert( $table_name, array(
                'PName' => $data['project-name'],
                'ClientID'   => $this->client_id,
                'PCategory'    => $data['project-category'],
                'PStatus' => "New",
                'PTimeEstimate' => $this->get_time_estimate($data),
                'PDateStarted' => date('Y-m-d H:i:s'),
                'PDueDate' => $this->reformat_date($data, "due-date"),
                'PDetails' => $data['project-details'],
                'PSubmission' => $this->original_submission
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }


        /**
         * Save new client into db
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function save_new_client($data) {
            global $wpdb;
            $table_name = 'tt_client';

            //Add New Record to Database
            $wpdb->insert( $table_name, array(
                'Company'   => $data['company'],
                'Contact'    => $data['contact-name'],
                'Email' => $data['contact-email'],
                'Phone' => $data['contact-telephone'],
                'BillTo' => $data['bill-to'],
                'Source' => $data['client-source'],
                'SourceDetails' => $data['client-source-details'],
                'CComments' => $data['comments'],
                'CSubmission' => $this->original_submission,
                'BillingRate' => $data['billing-rate']
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }

        
        /**
         * Save new time entry into db
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function save_new_time_entry($data) {
            global $wpdb;
            $table_name = 'tt_time';

            //Convert Start and End Times to Date Formats (from text)
            $start = \DateTime::createFromFormat('n/j/y g:i A', $data['start-time'])->format('Y-m-d H:i:ss');
            $end = \DateTime::createFromFormat('n/j/y g:i A', $data['end-time'])->format('Y-m-d H:i:ss');

            //find follow up field
            $follow_up = "";
            foreach ($data as $key => $val) {
                if (strpos(strtolower($key), "follow-up") !== false) {
                    if ($val != null && $val != "") {
                        $follow_up = $val;
                    }
                }
            }

            //Add New Record to Database
            $wpdb->insert( $table_name, array(
                'StartTime' => $start,
                'EndTime'   => $end,
                'TNotes'    => $data['time-notes'],
                'ClientID' => $this->client_id,
                'TaskID' => $this->task_id,
                'Invoiced' => $data['invoiced'],
                'InvoiceNumber' => $data['invoice-number'],
                'InvoicedTime' => $data['invoiced-time'] == "" ? Null : $data['invoiced-time'],
                'InvoiceComments' => $data['invoice-notes'],
                'FollowUp' => $follow_up,
                'NewTaskStatus' => $data['new-task-status'],
                'TimeSubmission' => $this->original_submission
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }


        /**
         * Update task status in db
         * 
         * @since 1.0.0
         * @since 3.0.13 clean up code
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function update_task_status($data) {
            $new_task_status = $data['new-task-status'];
            $update_task_status_string = "UPDATE tt_task SET TStatus ='" . $new_task_status . "' WHERE TaskID='" . $this->task_id . "'";
            $update_task_status_result = tt_query_db($update_task_status_string);
        }


        /**
         * Create follow up task in database
         * 
         * @since 1.0.0
         * @since 3.0.13 Correct function description
         * 
         * @param array $data Form data saved as key-value pairs.
         */
        private function create_follow_up_task($data) {
            global $wpdb;
            $start = date('Y-m-d H:i', strtotime($data['start-time']));
            $end = date('Y-m-d H:i', strtotime($data['end-time']));
            $follow_up_task_notes = "Created as a follow up to task id " . $this->task_id . " work completed between " . $start . " and " . $end;
            $table_name = 'tt_task';

            //Add New Record to Database
            //wpdb class prepares this so it doesn't need to be SQL escaped
            $wpdb->insert( $table_name, array(
                'TDescription' => $data['follow-up'],
                'ClientID'   => $this->client_id,
                'TStatus' => "New",
                'TNotes' => $follow_up_task_notes,
                'TDueDate' => date('Y-m-d'),
                'TTimeEstimate' => 0,
                'TSubmission' => $this->original_submission
            ) );
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
        }
        
        
        /**
         * Get client id from name
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         * 
         * @return string|int Client ID.
         */
        private function get_client_id($data) {
            if (array_key_exists("client-name", $data) && $data["client-name"] !="" && $data["client-name"] != null ) {
                return get_client_id_from_name($data['client-name']);
            } elseif (array_key_exists("client", $data) && $data["client"] !="" && $data["client"] != null ) {
                return get_client_id_from_name($data['client']);
            } else {
                //use default client if one exists, if not just enter null
                return  tt_get_user_options("time_tracker_categories", "default_client");
            }
        }


        /**
         * Get project id from name
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         * 
         * @return string|int Project ID.
         */        
        private function get_project_id($data) {
            //Project field in table requires a valid Project ID or null value, won't except empty string
            if (array_key_exists('project-name', $data) && $data['project-name'] != '' && $data['project-name'] != null) {
                return get_project_id_from_name(($data["project-name"]));
            } elseif (array_key_exists('project', $data) && $data['project'] != '' && $data['project'] != null) {
                return get_project_id_from_name(($data["project"]));
            }
            return null;
        }


        /**
         * Get task id from name
         * 
         * @since 1.0.0
         * 
         * @param array $data Form data saved as key-value pairs.
         * 
         * @return string|int Task ID.
         */
        private function get_task_id($data) {
            //Task field in table requires a valid Task ID or null value, won't except empty string
            if (array_key_exists('task-name', $data) and $data['task-name'] != '' and $data['task-name'] != null) {
                $task = $data['task-name'];
                $task_number_from_string = substr($task, 0, strpos($task,'-'));
                return $task_number_from_string;
            } elseif (array_key_exists('ticket', $data) and $data['ticket'] != '' and $data['ticket'] != null) {
                $task = $data['ticket'];
                $task_number_from_string = substr($task, 0, strpos($task,'-'));
                return $task_number_from_string;
            }
            return  tt_get_user_options("time_tracker_categories", "default_task");
        }


        /**
         * Get time estimate
         * 
         * @since 2.4.7
         * 
         * @param array $data Form data saved as key-value pairs.
         * 
         * @return double Time estimate, in x.xx hours format.
         */
        private function get_time_estimate($data) {
            $time_est = 0;
            foreach ($data as $key => $val) {
                if (strpos(strtolower($key), "time-estimate") !== false) {
                    if ($val != null && $val != "") {
                        $time_est = tt_convert_fraction_to_time($val);
                    }
                }
            }
            return $time_est;
        }


        /**
         * Get date from field and reformat for db
         * 
         * @since 2.4.7
         * 
         * @param array $data Form data saved as key-value pairs.
         * @param string $key Name of date field we are searching for.
         * 
         * @return string Date, formatted as yyyy-mm-dd.
         */
        private function reformat_date($data, $key) {
            if (array_key_exists($key, $data)) {
                //return \DateTime::createFromFormat('m/d/Y', $data[$key])->format('Y-m-d');
                //if it is already in correct format pass it back
                if (\DateTime::createFromFormat('Y-m-d', $data[$key])) {
                    return $data[$key];
                }
                $obj = \DateTime::createFromFormat('m/d/Y', $data[$key]);
                if ($obj) {
                    return $obj->format('Y-m-d');
                }
                return $data[$key];
            }
        }


        /**
         * Get result
         * 
         * @since 1.0.0
         * 
         * @return xxx Unknown. 
         */
        public function get_result() {
            return $this->result;
        }

    } //close class
} //close if not exists
