<?php
/**
 * Functions to query db and get table IDs from common names chosen by user in form
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * get client ID to load into table from the client name chosen by the user
 * 
 */
function get_client_id_from_name($client_name) {
  global $wpdb;
  $client_id_search_string = $wpdb->prepare('SELECT ClientID FROM tt_client WHERE Company= "%s"', $client_name);
  $client_id_search_result = tt_query_db($client_id_search_string);
  if ($client_id_search_result) {
    $client_id = $client_id_search_result[0]->ClientID;
    return $client_id;    
  } else {
    return;
  }
}


/**
 * get project ID to load into table from the project name chosen by the user
 * 
 */
function get_project_id_from_name($project_name) {
  if ( ($project_name=="") or ($project_name == null)) {
    $project_id = null;
  } else {
    global $wpdb;
    $project_id_search_string = $wpdb->prepare('SELECT ProjectID FROM tt_project WHERE PName= %s', $project_name);
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
 * get task ID to load into table from the task name chosen by the user
 * 
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