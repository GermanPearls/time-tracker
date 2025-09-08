<?php 
/**
 * Function dynamic-task-dropdown
 *
 * Dynamically update the task dropdown list depending on client chosen
 * Called from update_task_list Javascript function triggered by client onchange event
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Update task list based on client chosen by user.
 * 
 * @since 1.0.0
 * @since 3.2.0 Cleaned up code
 * 
 * @return array Results of update including success, details, and message fields.
 */
function tt_update_task_list_function() {
	
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! (isset($_POST['client'])) ) {
        $return = array(
            'success' => 'false',
            'message' => 'Incorrect request, action aborted'
        );
		wp_send_json_error($return, 500);
    }

    if ( ! check_ajax_referer( 'tt_update_task_list_nonce', 'security' )) {
        $return = array(
            'success' => 'false',
            'message' => 'Failed security check, action aborted'
        );
		wp_send_json_error($return, 500);
    }

    //Which client was chosen by the user in the previous dropdown?
    //pull the variable from the url and remove the % encoding, and strip slashes before apostrophes, then clean
    $client_name = sanitize_text_field(stripslashes(urldecode($_POST['client'])));
    $client_id = get_client_id_from_name($client_name);

    //Query time tracker database to get list of current tasks and task id's
    global $wpdb;
    //$task_list_search_string = $wpdb->prepare('SELECT TaskID, TDescription FROM tt_task WHERE ClientID="%s" AND TStatus <> \'Complete\' AND TStatus <> \'Canceled\' AND TStatus <> \'Closed\' ORDER BY TaskID DESC',$client_id);
    $task_list_search_string = $wpdb->prepare('SELECT TaskID, TDescription FROM tt_task WHERE ClientID="%s" ORDER BY TaskID DESC',$client_id);
    $task_list = tt_query_db($task_list_search_string);

    $task_options = '<option value=null></options>';

    //Create new options for dropdown based on narrowed search results
    if ( $task_list ) {
        foreach ($task_list as $val) {
            $task_identifier_string = sanitize_text_field($val->TaskID) . "-" . sanitize_text_field($val->TDescription);
            $task_options .= '<option value="' . esc_html($task_identifier_string) . '">' . esc_html($task_identifier_string) . '</option>';
        }
    }
    $task_options .= '</select>';

    //return result to ajax call
    if ($task_options == "") {
        $return = array(
            'success' => 'false',
            'details' => 'Error getting task options for this client.',
            'message' => $wpdb->last_error
        );
        wp_send_json_error($return, 500);
    } else {
        $return = array(
            'success' => 'true',
            'details' => $task_options,
            'message' => 'Success'
        );
        wp_send_json_success($return, 200);			
    }        
}