<?php

/**
 * Function delete record
 *
 *
 * @since 2.2.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Delete record from Time Tracker table
 * 
 * @since 2.2.0
 * @since 3.2.0 Added security check to confirm editing tt table.
 * @since 3.2.0 Added security check to only allow deletion of item shown to user in browser, to avoid rogue deletions.
 * @since 3.2.0 Improved security check confirming time tracker table, cleaned up code.
 * 
 * @return array Results including success, details, and message fields. 
 */
function tt_delete_record_function() {
	
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! (isset($_POST["table"])) || ! (isset($_POST["field"])) || ! (isset($_POST["id"])) ) {
        $return = array(
            'success' => 'false',
            'message' => 'Incorrect request, action aborted'
        );
        wp_send_json_error($return, 500);
    }

    if ( ! check_ajax_referer( 'tt_delete_record_nonce', 'security' ) ) {
            $return = array(
            'success' => 'false',
            'message' => 'Failed security check'
        );
        wp_send_json_error($return, 500);
    }

    //confirm request is for a tt table
    $tbl_requested = sanitize_text_field($_POST["table"]);
    if ( ! (tt_is_tt_table( $tbl_requested)) ) {
        $return = array(
            'success' => 'false',
            'message' => "Cannot delete records from " . $tbl_requested . " as it is not a time tracker table!",
        );
        wp_send_json_error($return, 500);
    }

    //confirm request matches what is currently shown to user in browser, table and id match
    $id_requested = intval($_POST["id"]);
    $id_fld_requested = str_replace("_", "-", str_replace("tt_", "", $tbl_requested)) . "-id";    
    if (strpos($_SERVER['HTTP_REFERER'], $id_fld_requested . "=" . $id_requested) == false) {
        $return = array(
            'success' => 'false',
            'message' => "This is not the record for which the user requested deletion. Ajax requested id field and id were: " . $id_fld_requested . "=" . $id_requested . " but active url is " . $_SERVER['HTTP_REFERER'],
        );
        wp_send_json_error($return, 500);		
    }     

    $fld_requested = sanitize_text_field($_POST["field"]);
    $record = [
        $fld_requested => $id_requested
    ];
    global $wpdb;

    //cascade delete functionality, time=4....client=0, start at 4 (time), delete from each table until get to current table
    $tbls = New Time_Tracker_Activator_Tables();
    $tbl_list = $tbls->get_table_list();
    $current_table = array_search($tbl_requested, $tbl_list, true);
    for ($i = (count($tbl_list)-1); $i >= $current_table; $i--) {                      
        //we have to recursively delete time entries manually for projects and recurring tasks because time table does not include their IDs
        //so have to get the tasks from these items first and delete all time entries for each task
        //be careful - if there are no tasks when we go to get the time entries there will be no where clauses and it will return ALL time entries, deleting ALL time entries                     
        if ( ($tbl_list[$i] == "tt_time") && ($tbl_requested == "tt_recurring_task" || $tbl_requested == "tt_project") ) {
            if ( $tbl_requested == "tt_project" ) {
                $wpdb->query($wpdb->prepare("DELETE tt_time.* FROM tt_time INNER JOIN tt_task ON tt_time.TaskID = tt_task.TaskID WHERE tt_task.ProjectID = %d", intval($id_requested)));
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);  
                if ($wpdb->last_error !== "") {
                    $return = array(
                        'success' => 'false',
                        'details' => 'Record deletion FAILED for table: ' . $tbl_requested . ', where  ' . $fld_requested . "=" . sanitize_text_field($id_requested) . ' cascade delete failed for table ' . tbl_list[$i],
                        'message' => $wpdb->last_error
                    );
                    wp_send_json_error($return, 500);
                }
            } elseif ( $tbl_requested == "tt_recurring_task" ) {
                $wpdb->query($wpdb->prepare("DELETE tt_time.* FROM tt_time INNER JOIN tt_task ON tt_time.TaskID = tt_task.TaskID WHERE tt_task.RecurringTaskID = %d", intval($id_requested)));
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                if ($wpdb->last_error !== "") {
                    $return = array(
                        'success' => 'false',
                        'details' => 'Record deletion FAILED for table: ' . $tbl_requested . ', where  ' . sanitize_text_field($fld_requested) . "=" . sanitize_text_field($id_requested) . ' cascade delete failed for table ' . tbl_list[$i],
                        'message' => $wpdb->last_error
                    );
                    wp_send_json_error($return, 500);
                }
            }

        } else {
            if ( tt_is_tt_table($tbl_list[$i]) ) {
                $result = $wpdb->delete($tbl_list[$i], $record);
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                if ($wpdb->last_error !== "") {
                    $return = array(
                        'success' => 'false',
                        'details' => 'Record deletion FAILED for table: ' . $tbl_requested. ', where  ' . $fld_requested . "=" . $id_requested . ' cascade delete failed for table ' . tbl_list[$i],
                        'message' => $wpdb->last_error
                    );
                    wp_send_json_error($return, 500);
                }
            }   //confirm tt table
        }   //projects and recurring tasks handled differently
    }   //loop through tables for cascade delete

    //finished recursive deletion, return result to ajax call
    if ($wpdb->last_error !== "") {
        $return = array(
            'success' => 'false',
            'details' => 'Record deletion FAILED for table: ' . $tbl_requested . ', where  ' . $fld_requested . "=" . $id_requested,
            'message' => $wpdb->last_error
        );
        wp_send_json_error($return, 500);
    } else {
        $return = array(
            'success' => 'true',
            'details' => 'private',
            'message' => 'SUCCESS: Record deleted for table: ' . $tbl_requested . ', where  ' . $fld_requested . "=" . $id_requested,
        );
        wp_send_json_success($return, 200);		
    }
}