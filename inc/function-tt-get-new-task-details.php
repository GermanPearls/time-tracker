<?php 
/**
 * Funciton Time_Tracker_Get_New_Task_Details
 *
 * Get details of most recent task added so we can pass it along to time entry form and start entering time
 * Called by ajax when user enters task and clicks 'save and start working' button
 * 
 * @since 2.4
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

function tt_get_new_task_details_function() {
	if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST['update'])) ) {

       		if ( check_ajax_referer('tt_start_timer_for_new_task', 'security')) {
				
                global $wpdb;
                $task_row = $wpdb->get_results('SELECT max(TaskID) as TaskID, TDescription, ClientID FROM tt_task');
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
				
                $return = array(
                    'success' => true,
                    'msg' => 'The sql error was cleared',
                    'ticket' => $task_row[0]->TaskID . "-" . $task_row[0]->TDescription,
                    'client' => $task_row[0]->ClientID
                );
                wp_send_json_success($return, 200);
				
				die();
			
			} //security check passed

	}  //if post and update set
}