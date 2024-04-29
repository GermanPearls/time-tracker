<?php
/**
 * Functions to query db and get table IDs from common names chosen by user in form
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Get client ID to load into table from the client name chosen by the user
 * 
 * @since 1.0.0
 * @since 3.0.5  added escape for single quotation marks in client name
 * 
 * @param $client_name Name of client.
 *
 * @return ??? Client ID, or null if not found.
 */
function get_client_id_from_name($client_name) {
  global $wpdb;
  $client_id_search_string = $wpdb->prepare('SELECT ClientID FROM tt_client WHERE Company= "%s"', str_replace("'", "''", $client_name));
  $client_id_search_result = tt_query_db($client_id_search_string);
  if ($client_id_search_result) {
    $client_id = $client_id_search_result[0]->ClientID;
    return $client_id;    
  } else {
    return;
  }
}


/**
 * Get project ID to load into table from the project name chosen by the user
 * 
 * @since 1.0.0
 * @since 3.0.5 added escape for single quotation marks in project name
 * 
 * @param string $project_name Name of project.
 *
 * @return ??? Project ID, or null if not found.
 */
function get_project_id_from_name($project_name) {
  if ( ($project_name=="") or ($project_name == null)) {
    $project_id = null;
  } else {
    global $wpdb;
    $project_id_search_string = $wpdb->prepare('SELECT ProjectID FROM tt_project WHERE PName= %s', str_replace("'", "''", $project_name));
    $project_id_search_result = tt_query_db($project_id_search_string);
    if (!empty($project_id_search_result)) {    
      $project_id = $project_id_search_result[0]->ProjectID;
    } else {
      $project_id = null;
    } 
  }
  return $project_id;    
}


/**
 * Get task ID to load into table from the task name chosen by the user
 * 
 * @since 1.0.0
 * 
 * @param string $task_name Name of task.
 *
 * @return ??? Task ID, or null if not found.
 */
function get_task_id_from_name($task_name) {
  global $wpdb;
  $task_id_search_string = $wpdb->prepare('SELECT TaskID FROM tt_task WHERE TDescription= "%s"', $task_name);
  $task_id_search_result = tt_query_db($task_id_search_string);
  if ($task_id_search_result) {
    return $task_id_search_result[0]->TaskID;
  } else {
    return;
  }
}