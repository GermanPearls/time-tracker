<?php 
/**
 * Funciton Time_Tracker_Export_Tables
 *
 * Export Time tracker tables for backing up or prior to deleting
 * Called by button on admin screen and run automatically upon plugin deletion
 * 
 * @since 1.0
 * 
 */


function tt_export_data_function() {

	if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST['type'])) ) {
		
		if (check_ajax_referer('tt_export_data_nonce', 'security')) {
	
			require_once WP_CONTENT_DIR . '/plugins/time-tracker/inc/class-time-tracker-activator-tables.php';
  		  	$table_list = Time_Tracker_Activator_Tables::get_table_list();

			$e = error_get_last();
			
 		   	$path = ABSPATH . "../tt_logs/";
		    $filename = 'mysqldump';
			$export_file = $path . "time_tracker_table_export_" . date('Y_m_d') . ".sql";
			
		    $mysqldump_cmd = "mysqldump --user=" . DB_USER . " --password=" . DB_PASSWORD . " --host=" . DB_HOST . " " . DB_NAME . " --tables ";
		    foreach ($table_list as $table_name) {
		        $mysqldump_cmd .= $table_name . " ";
 		  	 }
 		  	 $mysqldump_cmd = substr($mysqldump_cmd,0,-1);
  		  	
			if (PHP_OS_FAMILY === "Windows") {
				//echo "Running on Windows";
				$opened_file = fopen($export_file, "w");
				chmod($export_file, 0744);
				exec($mysqldump_cmd . " > " . $export_file);
				fclose($opened_file);
				chmod($export_file, 0644);
			} elseif (PHP_OS_FAMILY === "Linux") {
				//echo "Running on Linux";
 		  	 	$mysqldump_cmd_str = $mysqldump_cmd . " > " . $export_file;
		    	$file = tempnam($path, $filename);
 		   		file_put_contents($file, $mysqldump_cmd_str);
  		  		chmod($file, 0744);
				passthru($file);
  		  		unlink($file);
			}
			
			$e_now = error_get_last();
			
		  	//send response
			if ( $e_now !== $e) {
				$return = array(
					'success' => false,
					'msg' => 'There was a problem. Error: ' . $e_now['message'] . ', in File: ' . $e_now['file'] . ' on line ' . $e_now['line'] 
				);
				wp_send_json_error($return, 500);
			} elseif (PHP_OS_FAMILY === "Windows") {
				$return = array(
					'success' => false,
					'msg' => 'MySQL Backups aren\'t always successful in Windows environments. Check your files to confirm the backup file exists and has data in it.'
				);
				wp_send_json_error($return, 500);
			} else {
				//success
				$return = array(
					'success' => true,
					'msg' => 'Your data was exported.'
				);
				wp_send_json_success($return, 200);
			} //send error/success response to json
				
			die();	

		} //if passed security check

	} //if post request where type is set	
}