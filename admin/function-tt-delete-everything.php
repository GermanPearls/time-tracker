<?php 
/**
 * Funciton Time_Tracker_Delete_Everything
 *
 * Delete everything related to Time Tracker
 * Called by button on admin screen, after user confirmed
 * 
 * @since 1.0
 * 
 */


if ( ($_SERVER['REQUEST_METHOD'] = 'POST') && isset($_POST["type"]) ){

    if ($_POST["type"] == "confirmed") {
    	
		require_once '/../inc/class-time-tracker-delete.php';
        Time_Tracker_Deletor::delete_all();
        
    }

}      