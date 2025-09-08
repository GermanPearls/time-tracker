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
 * @since 3.2.0 Cleaned up code
 * 
 * @return array Results including success, msg, and error or files, depending on result.
 */
function tt_export_pending_time() {
	if ( ($_SERVER['REQUEST_METHOD'] !== 'POST') || ( ! isset($_POST['export_to_csv']) ) ) {
        $return = array(
            'success' => false,
            'msg' => 'Incorrect request type, action aborted.'
        );
        wp_send_json_error($return, 500);
    }

    if ( ! check_ajax_referer('tt_export_pending_time_nonce', 'security')) {
        $return = array(
            'success' => false,
            'msg' => 'Failed security check, action aborted.'
        );
        wp_send_json_error($return, 500);
    }

    //update the settings
    $export = new Pending_Time_Export();
    $files_array = $export->export_each_billto();
    
    if ( $files_array ) {        
        $return = array(
            'success' => true,
            'files' => $files_array,
            'msg' => 'Data has been saved'
        );
        wp_send_json_success($return, 200);
    } else {
        $return = array(
            'success' => false,
            'msg' => 'Error exporting pending time.'
        );
        wp_send_json_error($return, 500);        
    }
}

/**
 * Export and download pending time as iif file ready to import into Quickbooks to create invoices.
 * 
 * @since 3.0.13
 * @since 3.2.0 Cleaned up code
 * 
 * @return array Results including success, msg, and error or files, depending on result.
 */
function tt_export_pending_time_for_qb() {
	if ( ($_SERVER['REQUEST_METHOD'] !== 'POST') || ( ! isset($_POST['export_to_iif']) ) ) {
        $return = array(
            'success' => false,
            'msg' => 'Incorrect request type, action aborted.'
        );
        wp_send_json_error($return, 500); 
    }

    if ( ! check_ajax_referer('tt_export_pending_time_for_qb_nonce', 'security') ) {
        $return = array(
            'success' => false,
            'msg' => 'Failed security check, action aborted.'
        );
        wp_send_json_error($return, 500); 
    }

    $export = new Pending_Time_Export();
    $files_array = $export->export_each_billto_for_qb();

    if ( $files_array ) {
        $return = array(
            'success' => true,
            'files' => $files_array,
            'msg' => 'Data has been saved'
        );
        wp_send_json_success($return, 200);
    } else {
        $return = array(
            'success' => false,
            'files' => $files_array,
            'msg' => 'Error exporting pending time'
        );
        wp_send_json_error($return, 500);        
    }
}