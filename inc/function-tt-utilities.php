<?php
/**
 * Time Tracker Utility Functions
 *
 * Misc functions used throughout plugin
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Takes a fraction time and converts it for use in SQL time format (hh:mm:ss)
 * Example: 2.75 would return 24500, (ie: 02:45:00), 24500 can be inserted into time field in SQL
 * 
 */
function tt_convert_fraction_to_time($fraction_time) {
    if (($fraction_time == 0) or ($fraction_time == "") or ($fraction_time == null)) {
        return 0;
    } else {    
        $hours_part = intval($fraction_time) * 100 * 100;
        $minutes_part = ($fraction_time - intval($fraction_time)) * 60 * 100;
        $time_format = $hours_part + $minutes_part;
        return $time_format;
    }
}


/**
 * Takes hours and minutes and converts to hh:mm format
 * Correctly handles minutes over 60
 * Example: 1h + 90min would convert to: 2:30
 * 
 */
function tt_convert_to_string_time($hours, $minutes) {
    if ( (($hours == 0) or ($hours == null) or ($hours == "")) and (($minutes == 0) or ($minutes == null) or ($minutes == "")) ) {
        return 0;
    }
    
    //if minutes is over 60, convert to extra hours
    if ( $minutes >= 60 ) {
        $minutes_to_hours = intdiv($minutes, 60);
        $hours = $hours + $minutes_to_hours;
        $minutes = $minutes - ($minutes_to_hours * 60);
    } //if minutes over 60    
            
    //minutes are only an integer, add a preceeding 0 if necessary to make it look like a time format
    if (strlen($minutes) == 1) {
        $time_string = $hours . ":0" . $minutes;
    } else {
        $time_string = $hours . ":" . $minutes;
    } 
    return $time_string;
}


/**
 * Takes hours and minutes and converts to decimal hours
 * Correctly handles minutes over 60
 * Example: 1h + 90min would convert to: 2.5
 * 
 */
function tt_convert_to_decimal_time($hours, $minutes) {
    if ( (($hours == 0) or ($hours == null) or ($hours == "")) and (($minutes == 0) or ($minutes == null) or ($minutes == "")) ) {
        return 0;
    }
    
    //if minutes is over 60, convert to extra hours
    if ( $minutes >= 60 ) {
        $minutes_to_hours = intdiv($minutes, 60);
        $hours = $hours + $minutes_to_hours;
        $minutes = $minutes - ($minutes_to_hours * 60);
    } //if minutes over 60    
            
    $minutes_decimal = round($minutes/60,2);
    $time_decimal_string = $hours + $minutes_decimal;
    return $time_decimal_string;
}


/**
 * Takes a date (or date and time) and formats it for front end display, (ie: short date, with or without time)
 * Can be formatted to date and time or date, depending on user passed variable
 * type = output type
 * 
 */
function tt_format_date_for_display($date_entry, $type) {
    //if date is empty - return nothing
    if ( ($date_entry == null) or ($date_entry == "0000-00-00%") or ($date_entry == "0000-00-00") ) {
        return "";
    } else {    
        //check if it's only a date coming in, create date object
        $dateinput = \DateTime::createFromFormat("Y-m-d", $date_entry);
        
        //if that didn't work, check if you can create a date object from a date and time format
        if (!$dateinput) {
            $dateinput = \DateTime::createFromFormat("Y-m-d H:i:s", $date_entry);

            //if that didn't work, not sure what it is, return nothing
            if (!$dateinput) {
                return "";
            }
        }
    }

    //format based on output choice
    if ($type == "date_and_time") {
        return date_format($dateinput, "n/j/y g:i a"); 
    } //date and time

    if ($type == "date_only") {
        return date_format($dateinput, "n/j/y"); 
    } //date only

    return "";
}


/**
 * Return month name from month number
 * Example: 1 returns January
 * 
 */
function get_month_name_from_number($monthnumber) {
    //create date object from month integer
    $dateObj   = \DateTime::createFromFormat('!m', $monthnumber);
    if ($dateObj) {
        $monthName = $dateObj->format('F'); // Full name
        return $monthName;
    }
    return "";
}


/**
 * Find the form ID for a form with the given name
 * 
 */
function tt_get_form_id($form_name) {
    //$forms = WPCF7_ContactForm::find(array("title" => $form_name));
	$forms = get_posts(array(
        'title'=> $form_name,
        'post_type' => 'wpcf7_contact_form'
    ), ARRAY_A);
    if ($forms) {
        return $forms[0]->ID;
    } else {
        return;
    }
}


/**
 * Find the page ID for a form with the given name (and verify it's not in trash)
 * 
 */
function tt_get_page_id($page_name) {
    //$forms = WPCF7_ContactForm::find(array("title" => $form_name));
    $pages = get_page_by_title($page_name, ARRAY_A);

    //no pages are returned
    if (empty($pages)) {
        return;

    //one page is returned
    } elseif (array_key_exists('post_status', $pages)) {
        if (($pages['post_status'] == 'publish') or ($pages['post_status'] == 'draft') or ($pages['post_status'] == 'private') or ($pages['post_status'] == 'inherit')) {
            return $pages['ID'];
        }

    //several pages must have been found - return first
    } else {
        foreach ($pages as $page) {
            if (($page['post_status'] == 'publish') or ($page['post_status'] == 'draft') or ($page['post_status'] == 'private') or ($page['post_status'] == 'inherit')) {
                return $page['ID'];
            }
        }
    }

    //nothing is published
    return;
}


/**
 * Get sitename
 * 
 */
function tt_get_site_name() {
    $site = esc_html(get_bloginfo( 'name' ));
    return $site;
}


/**
 * Get site.com
 * 
 */
function tt_get_site_url() {
    $url = esc_html(get_bloginfo( 'url' ));
    return $url;
}


/**
 * Return wordpress@site.com for use with form notification settings
 * 
 */
function tt_get_wordpress_email() {
    $wp_email = esc_html(get_bloginfo( 'url' ));
    $slash_loc = strpos($wp_email,"//");
    if ( $slash_loc > 0 ) {
        $wp_email = substr($wp_email,$slash_loc+2);
    }
    $www_loc = strpos($wp_email,"www");
    if ( $www_loc > 0 ) {
        $wp_email = substr($wp_email,$www_loc+4);
    }
    $wp_email = "wordpress@" . $wp_email;
    return $wp_email;
}


/**
 * Get admin email for website
 * 
 */
function tt_get_site_admin_email() {
    $email = esc_html(get_bloginfo( 'admin_email' ));
    return $email;
}


/**
 * Add Recurring Task Icon
 * 
 */
function tt_add_recurring_task_icon() {
    $html = "<img src=" . TT_PLUGIN_WEB_DIR_INC . "img/recurring-task-icon-150sq-blue.png class=\"tt-recurring-task-icon\" width=25 height=25>";
    return $html;
}


/**
 * Record to sql_log on server, and save to options table to alert user
 * 
 */
function catch_sql_errors($filename, $functionname, $lastquery, $lasterror) {           
    $now = new \DateTime;
    //there was a sql error
    if( ($lasterror !== "") and ($lasterror !== null) ) {
		if (WP_DEBUG_LOG) {
        	log_sql('SQL ERROR: In file ' . $filename . ', function: ' . $functionname);
        	log_sql('SQL String: ' . $lastquery);
        	log_sql('SQL Error Details: ' . $lasterror);
		}
        //save to options table to alert user
        update_option('time-tracker-sql-result', array('result'=>'failure','updated'=>$now->format('m-d-Y g:i A'),'error'=>$lasterror, 'file'=>$filename, 'function'=>$functionname));
    
    //it was a success!
    } else {
        if (WP_DEBUG_LOG) {
			log_sql('SQL SUCCESS. SQL String: ' . $lastquery);
		}
		
		$option = get_option('time-tracker-sql-result');
        
        //if option already was a success, just update the date
        if ($option['result'] == 'success') {
            update_option('time-tracker-sql-result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'N/A', 'file'=>$filename, 'function'=>$functionname));
        
        //if option was a failure, leave it there for 7 days and only update if it's at least 7 days old
        } else {
            $last_updated = date_create_from_format('m-d-Y g:i A', $option['updated']);
            $days = $last_updated->diff($now);
            if ( date_diff($last_updated, $now)->format('%a') > 7 ) {
                update_option('time-tracker-sql-result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'N/A', 'file'=>$filename, 'function'=>$functionname));
            }
        }
    }
}


/**
 * Record to sql_log on server, for error handling
 * 
 */
function log_sql($msg) {
    if (WP_DEBUG_LOG) {
		$log_folder = ABSPATH . "../tt_logs/sql_log";
		if (!file_exists($log_folder)) {
			mkdir($log_folder, 0777, true);
		}
		$log_filename = $log_folder . '/sql_log_' . date('d-M-Y') . '.log';
		$now = new \DateTime();
		$now->setTimeZone(new \DateTimeZone(wp_timezone_string()));
		$log_str = date_format($now, 'M d, Y h:i:s A (T)') . ": " . $msg;
		file_put_contents($log_filename, $log_str . "\n", FILE_APPEND);
	}
}


/**
 * Record to cron_log on server, for error handling
 * 
 */
function log_cron($msg) {
    if (WP_DEBUG_LOG) {
		$log_folder = ABSPATH . "../tt_logs/cron_log";
		if (!file_exists($log_folder)) {
			mkdir($log_folder, 0777, true);
		}
		$log_filename = $log_folder . '/cron_log_' . date('d-M-Y') . '.log';
		$now = new \DateTime();
		$now->setTimeZone(new \DateTimeZone(wp_timezone_string()));
		$log_str = date_format($now, 'M d, Y h:i:s A (T)') . ": " . $msg;
		file_put_contents($log_filename, $log_str . "\n", FILE_APPEND);
	}
}


/**
 * Record to misc tt_log
 * 
 */
function log_tt_misc($msg) {
    if (WP_DEBUG_LOG) {
		$log_folder = ABSPATH . "../tt_logs/misc_log";
		if (!file_exists($log_folder)) {
			mkdir($log_folder, 0777, true);
		}
		$log_filename = $log_folder . '/misc_log_' . date('d-M-Y') . '.log';
		$now = new \DateTime();
		$now->setTimeZone(new \DateTimeZone(wp_timezone_string()));
		$log_str = date_format($now, 'M d, Y h:i:s A (T)') . ": " . $msg;
		file_put_contents($log_filename, $log_str . "\n", FILE_APPEND);
	}
}