<?php 
/**
 * Funciton Time_Tracker_Export_Button
 *
 * Function that runs when user clicks export button in admin area
 * Called by button on admin screen
 * 
 * @since 1.0
 * 
 */


if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST["type"]) ){
	
	require_once WP_CONTENT_DIR . '/plugins/time-tracker/inc/class-time-tracker-activator-tables.php';
    require_once WP_CONTENT_DIR . '/plugins/time-tracker/admin/function-tt-export-tables.php';

    tt_export_data_function();
    
}      