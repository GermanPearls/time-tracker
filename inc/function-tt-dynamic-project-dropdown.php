<?php 
/**
 * Function dynamic-project-dropdown
 *
* Dynamically update the project dropdown list depending on client chosen
* Called from updateProjectList Javascript function triggered by client onchange event
 * 
 * @since 1.0
 * 
 */


/**
 * Fixes call to undefined function error when calling get_client_id_from_name function
 * 
 */
if(!function_exists('get_client_id_from_name')) {
    include_once(plugin_dir_url( __FILE__ ) . "/time-tracker.php"); 
}


/**
 * Which client was chosen by the user in the previous dropdown?
 * pull the variable from the url and remove the % encoding
 * 
 */
$client_name = urldecode($_REQUEST["client"]);
$client_id = get_client_id_from_name($client_name);

/**
 * Query time tracker database to get list of current projects and project id's
 * 
 */
//$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
global $wpdb;
$project_list_search_string = $wpdb->prepare('SELECT ProjectID, PName FROM tt_project WHERE ClientID= "%s"', $client_id);
$project_list = $wpdb->get_results($project_list_search_string);
catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);

$project_options = '<option value=null></options>';

/**
 * Create new options for dropdown based on narrowed search results
 * 
 */
foreach ($project_list as $val) {
    $project_options .= sprintf('<option value="%s">%s</option>', esc_html($val->PName), esc_html($val->PName));
}

/**
 * close out select tag
 * 
 */
$project_options .= '</select>';

/**
 * display response
 * 
 */
echo $project_options;