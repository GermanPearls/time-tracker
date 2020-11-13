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


 function tt_export_tables() {

    /**
     * If wordpress isn't loaded load it up
     * 
     */
    if ( !defined('ABSPATH') ) {
        $path = $_SERVER['DOCUMENT_ROOT'];
        include_once $path . '/wp-load.php';
        require_once $path . '/wp-content/plugins/time-tracker/inc/class-time-tracker-activator-tables.php';
    }

    $table_list = Time_Tracker_Activator_Tables::get_table_list();

    $path = ABSPATH . "../tt_logs/";
    $filename = 'mysqldump';
    $file = tempnam($path, $filename);

    $mysqldump_cmd_str = "mysqldump --user=" . DB_USER . " --password=" . DB_PASSWORD . " --host=" . DB_HOST . " " . DB_NAME . " --tables ";
    foreach ($table_list as $table_name) {
        $mysqldump_cmd_str .= $table_name . " ";
    }
    $mysqldump_cmd_str = substr($mysqldump_cmd_str,0,-1);
    $mysqldump_cmd_str .= " > " . $path . "time_tracker_table_export_" . date('Y_m_d') . ".sql";

    file_put_contents($file, $mysqldump_cmd_str);
    chmod($file, 0744);

    passthru($file);

    unlink($file);           
}