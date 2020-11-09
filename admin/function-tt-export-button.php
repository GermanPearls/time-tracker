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


/**
 * If wordpress isn't loaded load it up
 * 
 */
if ( !defined('ABSPATH') ) {
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-load.php';
    require_once $path . '/wp-content/plugins/time-tracker/inc/class-time-tracker-activator-tables.php';
}


if ( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST["type"]) ){

    require_once $path . '/wp-content/plugins/time-tracker/admin/function-tt-export-tables.php';

    tt_export_tables();
    
}      