<?php 
/**
 * Function tt_export_pending_time
 *
 * Export and download pending time as csv file
 * 
 * @since 2.2.0
 * @since 3.0.13 Added function for exporting in Quickbooks format
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Export and download pending time as csv file.
 * 
 * @since 2.2.0
 * 
 * @return array Results including success, msg, and error or files, depending on result.
 */
function tt_export_pending_time() {
	if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST['export_to_csv'])) ) {

       		if ( check_ajax_referer('tt_export_pending_time_nonce', 'security')) {
				//security check passed
				//update the settings
                $export = new Pending_Time_Export();
                $files_array = $export->export_each_billto();
				
                $return = array(
                    'success' => true,
                    'files' => $files_array,
                    'msg' => 'Data has been saved'
                );
                wp_send_json_success($return, 200);
				die();
			
			} else {
                $return = array(
                    'success' => false,
                    'error' => true,
                    'msg' => 'Security check failed'
                );
                wp_send_json_error($return, 500); 
                die();  
                
            }

	}  //if post and update set
}

/**
 * Export and download pending time as iif file ready to import into Quickbooks to create invoices.
 * 
 * @since 3.0.13
 * 
 * @return array Results including success, msg, and error or files, depending on result.
 */
function tt_export_pending_time_for_qb() {
	if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and (isset($_POST['export_to_iif'])) ) {

       		if ( check_ajax_referer('tt_export_pending_time_for_qb_nonce', 'security')) {
				//security check passed
				//update the settings
                $export = new Pending_Time_Export();
                $files_array = $export->export_each_billto_for_qb();
				
                $return = array(
                    'success' => true,
                    'files' => $files_array,
                    'msg' => 'Data has been saved'
                );
                wp_send_json_success($return, 200);
				die();
			
			} else {
                $return = array(
                    'success' => false,
                    'error' => true,
                    'msg' => 'Security check failed'
                );
                wp_send_json_error($return, 500); 
                die();  
                
            }

	}  //if post and update set
}