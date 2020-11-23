<?php 
/**
 * Function dynamic-task-dropdown
 *
* Dynamically update the task dropdown list depending on client chosen
* Called from update_task_list Javascript function triggered by client onchange event
 * 
 * @since 1.0
 * 
 */


/**
 * Fixes call to undefined function error when calling plugin_dir_url below
 * 
 */
//if ( !defined('ABSPATH') ) {
    //If wordpress isn't loaded load it up.
    //$path = $_SERVER['DOCUMENT_ROOT'];
    //include_once $path . '/wp-load.php';
//}


/**
 * Fixes call to undefined function error when calling get_client_id_from_name function
 * 
 */
if(!function_exists('get_client_id_from_name')) {
    include_once(plugin_dir_url( __FILE__ ) . "/time-tracker.php"); 
}

//Which client was chosen by the user in the previous dropdown?
//pull the variable from the url and remove the % encoding
$client_name = sanitize_text_field($_REQUEST['client']);
$client_id = get_client_id_from_name($client_name);

//Query time tracker database to get list of current tasks and task id's
//$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
global $wpdb;
$task_list_search_string = $wpdb->prepare('SELECT TaskID, TDescription FROM tt_task WHERE ClientID="%s" AND TStatus <> \'Completed\' AND TStatus <> \'Canceled\' AND TStatus <> \'Closed\'',$client_id);
$task_list = $wpdb->get_results($task_list_search_string);
catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);

$task_options = '<option value=null></options>';

//Create new options for dropdown based on narrowed search results
foreach ($task_list as $val) {
    $task_identifier_string = sanitize_text_field($val->TaskID) . "-" . sanitize_text_field($val->TDescription);
    $task_options .= sprintf('<option value="%s">%s</option>', esc_html($task_identifier_string), esc_html($task_identifier_string));
}

//close out select tag
$task_options .= '</select>';

//display response
echo $task_options;