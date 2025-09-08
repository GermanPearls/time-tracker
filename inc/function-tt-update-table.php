<?php

/**
 * Function update table data based on user input
 *
 * Update data in SQL table based on user input in updateable html display table
 * Ref: phppot.com/php/php-mysql-inline-editing-using-jquery-ajax
 *
 *
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Update data in table.
 * 
 * @since 1.0.0
 * @since 3.0.13 Updated to remove trailing line breaks from updated value.
 * @since 3.2.0 Updated to restrict db operations to time tracker tables.
 * @since 3.2.0 Improved security on time tracker table check, cleaned up code.
 * 
 * @return array Result including success, details, and message.
 */
function tt_update_table_function() {
	
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST["id_field"]) ) {
		$return = array(
			'success' => 'false',
			'message' => 'Incorrect request, action aborted'
		);
		wp_send_json_error($return, 500);
	}

	if ( ! check_ajax_referer( 'tt_update_table_nonce', 'security' )) {
		$return = array(
			'success' => 'false',
			'message' => 'Failed security check, action aborted'
		);
		wp_send_json_error($return, 500);
	}

	$tt_tbl = sanitize_text_field($_POST['table']);
	if ( ! tt_is_tt_table($tt_tbl)) {
		$return = array(
			'success' => 'false',
			'message' => 'We cannot update ' . $tt_tbl . ' as it is not a Time Tracker table!'
		);
		wp_send_json_error($return, 500);
	}
			
	global $wpdb;
	$id_fld = sanitize_text_field($_POST['id_field']);
	$id_val = sanitize_text_field($_POST['id']);
	$edit_fld = sanitize_text_field($_POST['field']);
	$record = [
		$id_fld => $id_val
	];

	//deal with date entries, must be inserted into database in yyyy-mm-dd format
	if ( ( strpos(strtolower($edit_fld), 'date') || strpos(strtolower($edit_fld), 'time') || strtolower($edit_fld) == 'endrepeat' ) && !($edit_fld == 'InvoicedTime') ) {

		//convert the date entered from a string to a date/time object
		$date_entered = new \DateTime(sanitize_text_field($_POST['value']));

		//use date/time object to convert back to a string of standard SQL format yyyy-mm-dd
		$date_in_sql_format = $date_entered->format('Y') . "-" . $date_entered->format('m') . "-" . $date_entered->format('d');
		
		//deal with date and time entires, must be inserted into db in yyyy-mm-dd hh:mm:ss format
		if ( strpos(strtolower($edit_fld), 'time') ) {
			$date_in_sql_format .= " " . $date_entered->format('H') . ":" . $date_entered->format('i') . ":" . $date_entered->format('s');
		}

		$data = [
			$edit_fld => $date_in_sql_format
		];
		//the last argument, %s, tells the function to keep the data in string format
		$result = $wpdb->update($tt_tbl, $data, $record);
		catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
			
	//pass everything else along to the wp update function
	} else {

		//if updated value includes <br> that were automatically inserted remove them to avoid double line breaks
		//we're using WPDB->update below so data should not be escaped
		$updated_value = tt_remove_trailing_line_breaks(str_replace('<br><br>','<br>',$_POST['value']));

		//if no data passed, update db with NULL instead of empty string
		if ($updated_value == '') { $updated_value = NULL; }

		$data = [
			$edit_fld => $updated_value
		];
		$result = $wpdb->update($tt_tbl, $data, $record);
		catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
	}

	//return result to ajax call
	if ( $wpdb->last_error !== "" ) {
		$return = array(
			'success' => 'false',
			'details' => 'update table: ' . $tt_tbl. ', where  ' . $id_fld . "=" . $id_val . ', update field: ' . $edit_fld . " to value: " . $updated_value,
			'message' => 'Error updating table, error: ' . $wpdb->last_error
		);
		wp_send_json_error($return, 500);
	} else {
		$return = array(
			'success' => 'true',
			'details' => 'private',
			'message' => 'Success, database updated.'
		);
		wp_send_json_success($return, 200);			
	}
}