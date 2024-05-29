<?php
/**
 * Time Tracker Utility Functions
 *
 * Misc functions used throughout plugin
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Takes a fraction time and converts it for use in SQL time format (hh:mm:ss)
 * Example: 2.75 would return 24500, (ie: 02:45:00), 24500 can be inserted into time field in SQL
 * 
 * @since 1.0.0
 * 
 * @param double $fraction_time Time in x.xx hours.
 * 
 * @return int Time in hhmmss, which can be used in sql.
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
 * @since 1.0.0
 * 
 * @param int $hours Hours in integer format.
 * @param int $minutes Minutes in integer format.
 * 
 * @return string Time in hh:mm format.
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
 * @since 1.0.0
 * 
 * @param int $hours Hours in integer format.
 * @param int $minutes Minutes in integer format.
 * 
 * @return double Time in decimal format, x.x hours.
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
            
    $minutes_decimal = $minutes/60;
    $time_decimal_string = round($hours + $minutes_decimal,2);
    return $time_decimal_string;
}


/**
 * Takes a date (or date and time) and formats it for front end display, (ie: short date, with or without time)
 * Can be formatted to date and time or date, depending on user passed variable
 * type = output type
 * 
 * @since 1.0.0
 * 
 * @param string $date_entry Date in various formats.
 * @param string $type What format you'd like to output.
 * 
 * @return string Date and time in format desired.
 */
function tt_format_date_for_display($date_entry, $type) {
    //if date is empty - return nothing
    if ( ($date_entry == "") or ($date_entry == "0000-00-00%") or ($date_entry == "0000-00-00") or ($date_entry == "0000-00-00 00:00:00")) {
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
 * Get Time Estimate Formatted
 * 
 * @since 1.4.0
 * 
 * @param string $timeestimate Time estimate in xx:xx format.
 * 
 * @return double Time in decimal format, x.x hours.
 */
function get_time_estimate_formatted($timeestimate) {
    if (($timeestimate == 0 ) or ($timeestimate == null)) {
        return null;
    } else {
        $time_estimate_parts = explode(":", $timeestimate);
        $time_estimate_formatted = tt_convert_to_decimal_time($time_estimate_parts[0], $time_estimate_parts[1]);  
        return $time_estimate_formatted;              
    }
}


/**
 * Get Time Worked vs Estimate Class
 * 
 * @since 1.4.0
 * 
 * @param double $percent_time_logged Time logged as a percentage, without the percent sign.
 * 
 * @return string Class name to be displayed in data cell.
 */
function get_time_estimate_class($percent_time_logged) {
    if ( ($percent_time_logged <> "") and ($percent_time_logged > 100) ) {
        $time_worked_vs_estimate_class = "over-time-estimate";
    } else {
        $time_worked_vs_estimate_class = "";
    }
    return $time_worked_vs_estimate_class;
}


/**
 * Get Percentage of Time Logged vs Time Estimate
 * 
 * @since 1.4.0
 * 
 * @param double Estimate of time.
 * @param double Hours worked.
 * 
 * @return string Html string showing percent time logged with red/green status bar.
 */
function get_percent_time_logged($time_estimate_formatted, $hours_logged) {
    //evaluate time worked vs estimate, format data to display and apply css class based on result

    if (($time_estimate_formatted == 0 ) or ($time_estimate_formatted == null)) {
        $percent_time_logged = "";
        $details_for_display = "";
    } else {
        $percent_time_logged = round($hours_logged / $time_estimate_formatted * 100);
        //$percent_time_logged = "<br/>" . round($hours_logged / $time_estimate_formatted * 100) . "%";
        $details_for_display = " / " . $time_estimate_formatted . "<br/>" . $percent_time_logged . "%<br/>";

		if ($percent_time_logged > 100) {
			$percent_time_logged = 100;
		}
		$details_for_display .= "<div style='display:inline-block;width:100%;height:20px;border:1px solid black;'>";
		$details_for_display .= "<div style='background-color:green;height:20px;float:left;width:" . $percent_time_logged . "%;'></div>";
		$details_for_display .= "<div style='background-color:red;height:20px;'></div></div>"; 		
    }     
    return $details_for_display;
}


/**
 * Get Due Date Class - Are We On Time, Late, Or Getting Late
 * 
 * @since 1.0.0
 * @since 3.0.13 Added class for due today.
 * 
 * @param ??? $duedate Due date.
 * @param string $status Status of item.
 * 
 * @return string Class name to display with data, based on if it is on time, late, or getting late.
 */
function get_due_date_class($duedate, $status) {
    if ( ($duedate == "0000-00-00") || ($duedate == null) ) {
        //$due_date_formatted = "";
        $due_date_class = "no-date";
    } else if ( ($status == "Canceled") || ($status == "Complete") ) {
        $due_date_class = "ok-date";
    } else {
        //$due_date_formatted = date_format(\DateTime::createFromFormat("Y-m-d", $duedate), "n/j/y");
        if (\date_format(\DateTime::createFromFormat("Y-m-d", $duedate), "Ymd") == \date_format(new \DateTime(), "Ymd")) {
            $due_date_class = "today-date";
        } elseif (\DateTime::createFromFormat("Y-m-d", $duedate) < new \DateTime()) {
			$due_date_class = "late-date";
        } elseif (\DateTime::createFromFormat("Y-m-d", $duedate) <= new \DateTime(date("Y-m-d", strtotime("+7 days")))) {
            $due_date_class = "soon-date";
		} elseif (\DateTime::createFromFormat("Y-m-d", $duedate) >= new \DateTime(date("Y-m-d", strtotime("+365 days")))) {
            $due_date_class = "far-future-date";
		} elseif (\DateTime::createFromFormat("Y-m-d", $duedate) >= new \DateTime(date("Y-m-d", strtotime("+90 days")))) {
            $due_date_class = "on-hold-date";
        } else {
            $due_date_class = "ok-date";
        }
    }
    return $due_date_class;
}


/**
 * Return month name from month number
 * Example: 1 returns January
 * 
 * @since 1.0.0
 * 
 * @param integer $monthnumber Number of month (1=January).
 * 
 * @return string Full month name.
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
 * @since 1.0.0
 * 
 * @param string $form_name Name of formm.
 * 
 * @return integer Post ID of form in wp database.
 */
function tt_get_form_id($form_name) {
    //$forms = WPCF7_ContactForm::find(array("title" => $form_name));
    require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-forms.php');
	$forms = get_posts(array(
        'title'=> $form_name,
        'post_type' => Time_Tracker_Activator_Forms::get_post_type()
    ), ARRAY_A);
    if ($forms) {
        return $forms[0]->ID;
    } else {
        return;
    }
}


/**
 * Get form name from form ID
 * 
 * @since 2.4.2
 * 
 * @param integer $form_id Post ID of form
 * 
 * @return string Name of form.
 */
function tt_get_form_name($form_id) {
    if ( ($form_id != null) && ($form_id > 0) ) {
        return get_the_title($form_id);
    }
}


/**
 * Check if this is a Time Tracker form
 * 
 * @since 2.4.2
 * 
 * @param string $form_name Name of form
 * @param integer $form_id WP Post ID of form
 * 
 * @return boolean True if this is a Time Tracker form, false if form not associated with TT
 */
function tt_is_tt_form($form_name='', $form_id=0) {
    if ($form_id != 0) {
        $form_name = tt_get_form_name($form_id);
    }
    if ( ($form_name == '') or ($form_name == null) ) {
        return false;
    }
    require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-forms.php');
    require_once(TT_PLUGIN_DIR_INC . '/' . TT_PLUGIN_FORM_TYPE . '/class-time-tracker-activator-forms-' . strtolower(TT_PLUGIN_FORM_TYPE) . '.php');
    //$tt_forms = new Time_Tracker_Activator_Forms();
	$tt_forms_arr = Time_Tracker_Activator_Forms::create_form_details_array();
    foreach ($tt_forms_arr as $tt_form) {
        if ($form_name == $tt_form['Title']) {
            return true;
        }
    }
    return false;
}


/**
 * Check page status to verify it's private
 * 
 * @since 1.4.0
 * 
 * @param integer $page_id WP Post ID of page
 * 
 * @return string WP Post status of post
 */
function check_page_status($page_id) {
    $status = get_post_status ( $page_id );
    if ( ($status == 'private') || ($status == 'protected') ) {
        return 'private';
    }
    return $status;
}        
 

/**
 * Find the page ID for a page with the given name (and verify it's not in trash)
 * 
 * @since 1.0.0
 * 
 * @param string $page_name Name of page.
 * 
 * @return integer WP Post ID of page, or null if does not exist
 */
function tt_get_page_id($page_name) {
    //$forms = WPCF7_ContactForm::find(array("title" => $form_name));
    //$pages = get_page_by_title($page_name, ARRAY_A);
    //rev 3.0.8 get_page_by_title deprecated in WordPress 6.2
    //get_posts only available after plugins loaded
    //status must be specified or only published posts returned (not private)
    //to search by title use title NOT post_title
    $pages = get_posts(
        array(
            'post_type' => 'page',
            'post_status' => get_post_statuses(),
            'title' => $page_name
        )
    );

    //updated for get_posts (returns array of posts)
    if ($pages) {
        foreach ($pages as $page) {
            if (($page->post_status == 'publish') or ($page->post_status == 'draft') or ($page->post_status == 'private') or ($page->post_status == 'inherit')) {
                return $page->ID;
            }
        }
    }
    return;
}


/**
 * Get sitename
 * 
 * @since 1.0.0
 * 
 * @return Name of site from Wordpress options
 */
function tt_get_site_name() {
    $site = esc_html(get_bloginfo( 'name' ));
    return $site;
}


/**
 * Get site.com
 * 
 * @since 1.0.0
 * 
 * @return Url of site from Wordpress options
 */
function tt_get_site_url() {
    $url = esc_html(get_bloginfo( 'url' ));
    return $url;
}


/**
 * Return wordpress@site.com for use with form notification settings
 * 
 * @since 1.0.0
 * 
 * @return string Email address with username of wordpress and site's domain, for use as from address with email notifications
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
 * @since 1.0.0
 * 
 * @return string Admin email from Wordpress options
 */
function tt_get_site_admin_email() {
    $email = esc_html(get_bloginfo( 'admin_email' ));
    return $email;
}


/**
 * Add Recurring Task Icon
 * 
 * @since 1.1.0
 * 
 * @return string Html of recurring task icon
 */
function tt_add_recurring_task_icon() {
    $html = "<img src=" . TT_PLUGIN_WEB_DIR_INC . "img/recurring-task-icon-150sq-blue.png class=\"tt-recurring-task-icon\" width=25 height=25>";
    return $html;
}


/**
 * Get Currency Type Specified by User
 * 
 * @since 2.4.3
 * 
 * @return string Currency sign identified by user
 */
function tt_get_currency_type() {
    $curr = tt_get_user_options("time_tracker_categories", "currency_sign");
    if ($curr != null and $curr != "") {
        return esc_html($curr);
    }
    return "";
}


/**
 * Get default billing rate
 * 
 * @since 2.4.3
 * 
 * @return integer Default billing rate as defined by user
 */
function tt_get_default_billing_rate() {
    $rate = tt_get_user_options("time_tracker_categories", "default_rate");
    if ($rate != null && $rate != "" && is_numeric($rate)) {
        return intval($rate);
    }
    return;
}


/**
 * Check for Pagination
 * 
 * @since 1.4.0
 * 
 * @return array Pagination details
 */
function check_for_pagination() {
	$pages = new Time_Tracker_Activator_Pages();
	$pages_detail = $pages->create_subpage_details_array(0);
	$slug = get_post_field('post_name');
	$current_page = null;
	$i = 0;
	do {
		if ($pages_detail[$i]['Slug'] == $slug) {
			$current_page = $i;
		}
		$i = $i + 1;
	} while ($current_page == null and $i < count($pages_detail) );
	if (is_null($current_page)) {
        $pagination['Flag'] = false;
    } elseif (key_exists('Paginate', $pages_detail[$current_page])) {
        $pagination = $pages_detail[$current_page]['Paginate'];
        if ($pagination['Flag'] == true) {
            if ($slug == "time-log") {
                $timelog = new Time_Log();
                $total_records = $timelog->get_record_count();
            } else {
                global $wpdb;
                $sql_string = sanitize_text_field($pagination['TotalRecordsQuery']);
                $total_records = $wpdb->get_var($sql_string);
            }            
            $pagination['RecordCount'] = $total_records;
        }
    } else {
        $pagination['Flag'] = false;
    }
	return $pagination;
}


/**
 * Check for Pagination - Records Per Page
 * 
 * @since 2.2.0
 * 
 * @return integer Number of records to show per page for pagination
 */
function get_pagination_qty_per_page() {
    require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-pages.php');
	$pages = new Time_Tracker_Activator_Pages();
	$pages_detail = $pages->create_subpage_details_array(0);
	$slug = get_post_field('post_name');
	$current_page = null;
	$i = 0;
	do {
		if ($pages_detail[$i]['Slug'] == $slug) {
			$current_page = $i;
		}
		$i = $i + 1;
	} while ($current_page == null and $i <= count($pages_detail) );
	$pagination = $pages_detail[$current_page]['Paginate'];
	if ($pagination['Flag'] == true) {
		return $pagination['RecordsPerPage'];
	} else {
		return 1000;
	}
}


/**
 * Add Pagination to Page
 * 
 * @since 1.4.0
 * 
 * @param double $data_count Total number of records to be displayed
 * @param double $max_per_page Pagination setting of max number of records per page
 * @param double $current_page_num Current page number to get records for
 * @param string $prevtext Text to display for user to choose previous pages
 * @param string $nexttext Text to display for user to choose next pages
 * 
 * @return string Echos the pagination links to display on the front end 
 */
function add_pagination($data_count, $max_per_page, $current_page_num, $prevtext, $nexttext) {
	//add_pagination($data_count, $records_in_table, $current_page_num, '« Newer', '« Older')
	global $wp_query;
	if (is_null($max_per_page) or $max_per_page == 0) {
		$max_per_page = 100;
	}
	$args = array(
		'total' => ceil($data_count/$max_per_page),
		'current' => $current_page_num,
		'prev_text' => $prevtext,
		'next_text' => $nexttext,
		'mid_size' => 3
	);
	echo paginate_links($args);
}


/**
 * Get Record Numbers Based on Page Number for Pagination
 * 
 * @since 2.2.0
 * 
 * @return array Array defining which records to display on this page (ie: number of records to display and offset from first record)
 */
function get_record_numbers_for_pagination_sql_query() {
	$records_per_page = get_pagination_qty_per_page();
	$page_num = tt_get_page_number_from_url();
	if (!(is_numeric($page_num))) {
		$records = array(
			'limit' => $records_per_page,
			'offset' => 0
		);
	} else {
		$records = array(
			'limit' => $records_per_page,
			'offset' => $page_num == 1 ? 0 : (($page_num-1)*intval($records_per_page))-1
		);
	}
	return $records;
}


/**
 * Get page number from url if paginated
 * 
 * @since 3.0.5
 * 
 * @return integer Page number of current page if it is paginated
 **/
function tt_get_page_number_from_url() {
	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri, "page") !== false) {
		$uri_parts = explode("/", $uri);
		for ($i = 0; $i < count($uri_parts); $i++) {
			if ($uri_parts[$i] == "page") {
				return $uri_parts[$i+1];
			}
		}
	}
}
	

/**
 * Record to sql_log on server, and save to options table to alert user
 * 
 * @since 1.0.0
 * 
 * @param string $filename Name of file that ran sql expression
 * @param string $functiontname Name of function that ran sql expression
 * @param string $lastquery Sql expression that failed
 * @param string $lasterror Error details of sql failure
 */
function catch_sql_errors($filename, $functionname, $lastquery, $lasterror) {           
    $now = new \DateTime("now", new \DateTimeZone('America/New_York'));
    //there was a sql error
    if( ($lasterror !== "") and ($lasterror !== null) ) {
		if (WP_DEBUG_LOG) {
        	log_sql('SQL ERROR: In file ' . $filename . ', function: ' . $functionname);
        	log_sql('SQL String: ' . $lastquery);
        	log_sql('SQL Error Details: ' . $lasterror);
		}
        
		//save to options table to alert user
        if (get_option('time_tracker_sql_result')) {
			update_option('time_tracker_sql_result', array('result'=>'failure','updated'=>$now->format('m-d-Y g:i A'),'error'=>$lasterror, 'file'=>$filename, 'function'=>$functionname));
		} else {
			add_option('time_tracker_sql_result', array('result'=>'failure','updated'=>$now->format('m-d-Y g:i A'),'error'=>$lasterror, 'file'=>$filename, 'function'=>$functionname));
		}
    
    //it was a success!
    } else {
        if (WP_DEBUG_LOG) {
			log_sql('SQL SUCCESS. SQL String: ' . $lastquery);
		}
        
        //if there are no results in the db, add them
        if (get_option('time_tracker_sql_result')) {
			
			$option = get_option('time_tracker_sql_result');			
			
			//if option already was a success, just update the date
			if ($option['result'] == 'success') {			
				update_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'N/A', 'file'=>$filename, 'function'=>$functionname));
				
        	//if option was a failure, leave it there for 7 days and only update if it's at least 7 days old
        	} else {
            	$last_updated = date_create_from_format('m-d-Y g:i A', $option['updated']);
            	if ( date_diff($last_updated, $now)->format('%a') > 7 ) {
                	update_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'N/A', 'file'=>$filename, 'function'=>$functionname));
            	}
			}
		
		//option doesn't exist in db, create it
		} else {
			add_option('time_tracker_sql_result', array('result'=>'failure','updated'=>$now->format('m-d-Y g:i A'),'error'=>$lasterror, 'file'=>$filename, 'function'=>$functionname));
		}
    }
}


/**
 * Record to sql_log on server, for error handling
 * 
 * @since 1.0.0
 * 
 * @param string $msg Note to record in Time Tracker sql log
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
 * @since 1.0.0
 * 
 * @param string $msg Note to record in Time Tracker cron log
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
 * @since 1.0.0
 * 
 * @param string $msg Note to log in general Time Tracker log
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


/**
 * Query db
 * 
 * @since 2.4.7
 * 
 * @param string $sql_string Sql string to run on WP database
 * @param string optional Type of response requested (ie: object or array)
 * 
 * @return object|array Response from sql request
 */
function tt_query_db($sql_string, $return_type="object") {
    global $wpdb;
    $sql_string_cleaned = stripslashes(str_replace("\n", "", $sql_string));
	if ($return_type == "array") {
    	$sql_result = $wpdb->get_results($sql_string_cleaned, ARRAY_A );
	} else {
        //default return type for get_results is object
		$sql_result = $wpdb->get_results($sql_string_cleaned);
	}
    \Logically_Tech\Time_Tracker\Inc\catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
    return $sql_result;
}


/**
 * Get user options
 * 
 * @since 2.4.7
 * 
 * @param string $option_name Name of Wordpress database option
 * @param string $sub_option_name If option is array of key-value pairs, key to search
 * 
 * @return variant|string|double|date Option setting from database
 */
function tt_get_user_options($option_name, $sub_option_name) {
    $optns = get_option($option_name);
    if ($optns) {
        if (array_key_exists($sub_option_name, $optns)) {
            //var_dump(nl2br($optns[$sub_option_name]));
            //var_dump(sanitize_text_field(nl2br($optns[$sub_option_name])));
            return $optns[$sub_option_name];
        }
    }
    return;
}


/**
 * Get clients
 * 
 * @since 2.4.7
 * 
 * @return object All clients in database
 */
function tt_get_clients() {
    return tt_query_db("SELECT ClientID, Company FROM tt_client ORDER BY Company ASC");
}


/**
 * Get tasks
 * 
 * @since 2.4.7
 * 
 * @return object All tasks in database
 */
function tt_get_tasks() {
    return tt_query_db("SELECT TaskID, TDescription FROM tt_task ORDER BY TaskID ASC");
}


/**
 * Get projects
 * 
 * @since 2.4.7
 * 
 * @return object All projects in database
 */
function tt_get_projects() {
    return tt_query_db("SELECT ProjectID, PName FROM tt_project ORDER BY ProjectID ASC");
}


/**
 * Check if string ends with item from array
 * 
 * @since 3.0.13
 * 
 * @param string $haystack String to check.
 * @param array $arr Array of items to check for at end of string.
 * 
 * @return boolean True if the original string ends with an item from the array.
 */
function tt_string_ends_with($haystack, $arr) {
    foreach ($arr as $needle) {
        if ( str_ends_with($haystack, $needle) ) {
            return true;
        }
    }
    return false;
}

/**
 * Remove trailing line breaks
 * 
 * @since 3.0.13
 * 
 * @param string $str String to update.
 * 
 * @return string Updated string with trailing line breaks removed.
 */
function tt_remove_trailing_line_breaks($str) {
    $linebreaks = ['<br>', '<br/>', '<br />'];
    while (tt_string_ends_with($str, $linebreaks)) {
        foreach ($linebreaks as $linebreak) {
            if ( str_ends_with($str, $linebreak) ) {
                $str = substr($str, 0, strlen($str) - strlen($linebreak));
            }
        }
    }
    return trim($str);
}
