<?php 
/**
 * Funciton Time_Tracker_Clear_SQL_Error
 *
 * Update setting in options table indicating there's been a recent SQL error
 * Called by button next to error message on all TT screens
 * 
 * @since 1.0
 * 
 */


/**
 * If wordpress isn't loaded load it up
 * 
 */
if ( !defined('ABSPATH') ) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-load.php';
}


if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    if ( !empty($_POST['update']) ) {
        if ( $_POST['update'] == "clear") {
            $now = new DateTime;
            $now->setTimezone(new DateTimeZone(get_option('timezone_string')));
            update_option('time-tracker-sql-result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'N/A', 'file'=>"", 'function'=>""));
        }
    }
}  