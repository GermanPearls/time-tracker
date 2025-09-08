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
 * @since 3.2.0 Cleaned up code
 * 
 * @return array Results of query including success, msg, and details of task, if applicable.
 */
function tt_get_new_task_details_function() {
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        $return = array(
            'success' => 'false',
            'message' => 'Incorrect request, action aborted'
        );
		wp_send_json_error($return, 500);
	}

	if ( ! check_ajax_referer('tt_start_timer_for_new_task_nonce', 'security')) {
        $return = array(
            'success' => 'false',
            'message' => 'Failed security check, action aborted'
        );
		wp_send_json_error($return, 500);
	}
			
	$sql_string = 'SELECT TaskID, TDescription, tt_client.Company as Client from tt_task LEFT JOIN tt_client ON tt_task.ClientID=tt_client.ClientID WHERE tt_task.TaskID=(SELECT MAX(TaskID) FROM tt_task)';
	$task_row = tt_query_db($sql_string);
	
	if ( $task_row ) {		
		$return = array(
			'success' => true,
			'msg' => 'We found the new task, ID# ' . $task_row[0]->TaskID . ' and will start logging time for this task.',
			'ticket' => $task_row[0]->TaskID . "-" . $task_row[0]->TDescription,
			'client' => $task_row[0]->Client
		);
		wp_send_json_success($return, 200);
	} else {
		$return = array(
            'success' => 'false',
            'message' => 'Failed to get details of last added task'
        );
		wp_send_json_error($return, 500);
	}
}