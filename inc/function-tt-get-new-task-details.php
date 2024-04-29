<?php 
/**
 * Function Time_Tracker_Get_New_Task_Details
 *
 * Get details of most recent task added so we can pass it along to time entry form and start entering time
 * Called by ajax when user enters task and clicks 'save and start working' button
 * 
 * @since 2.4.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

/**
 * Get details of most recent task added.
 * 
 * @since 2.4.0
 * 
 * @return array Results of query including success, msg, and details of task, if applicable.
 */
function tt_get_new_task_details_function() {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

       		if ( check_ajax_referer('tt_start_timer_for_new_task_nonce', 'security')) {
				
				$sql_string = 'SELECT TaskID, TDescription, tt_client.Company as Client from tt_task LEFT JOIN tt_client ON tt_task.ClientID=tt_client.ClientID WHERE tt_task.TaskID=(SELECT MAX(TaskID) FROM tt_task)';
				$task_row = tt_query_db($sql_string);
				
                $return = array(
                    'success' => true,
                    'msg' => 'We found the new task, ID# ' . $task_row[0]->TaskID . ' and will start logging time for this task.',
                    'ticket' => $task_row[0]->TaskID . "-" . $task_row[0]->TDescription,
                    'client' => $task_row[0]->Client
                );
				wp_send_json_success($return, 200);
				die();
			
			} else {
				$return = array(
					'success' => false,
					'msg' => 'failed security check'
				);
				wp_send_json_failure($return, 200);
				die();
			}

	}  //if post and update set
}