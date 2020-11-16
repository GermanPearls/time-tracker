<?php

/**
 * Function update table data based on user input
 *
 * Update data in SQL table based on user input in updateable html display table
 * Ref: https://phppot.com/php/php-mysql-inline-editing-using-jquery-ajax/
 *
 *
 * @since 1.0
 * 
 */

////date_default_timezone_set((wp_timezone_string()));


/**
 * 
 * 
 */
if ( $_SERVER['REQUEST_METHOD'] = 'POST' and isset($_POST["id_field"]) ) {

    //Connect to 2nd Database
    //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
    global $wpdb;

    $record = [
        $_POST["id_field"] => $_POST["id"]
    ];

    //deal with date entries, must be inserted into database in yyyy-mm-dd format
    if ( strpos(strtolower($_POST["field"]), "date") ) {

        //convert the date entered from t a string to a date/time object
        $date_entered = new DateTime($_POST["value"]);

        //use date/time object to convert back to a string of standard SQL format yyyy-mm-dd
        $date_in_sql_format = $date_entered->format('Y') . "-" . $date_entered->format('m') . "-" . $date_entered->format('d');
        
        $data = [
            $_POST["field"] => $date_in_sql_format
        ];
        //the last argument, %s, tells the function to keep the data in string format
        $result = $wpdb->update($_POST["table"], $data, $record);
        catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
      
    //pass everything else along to the wp update function
    } else {

        //if updated value includes <br> that were automatically inserted remove them to avoid doulbe line breaks
        if ( strpos($_POST["value"], "<br>")) {
            $updated_value = str_replace("<br>", "", $_POST["value"]);
        } else {
            $updated_value = $_POST["value"];
        }
        
        $data = [
            $_POST["field"] => $updated_value
        ];
        $result = $wpdb->update($_POST["table"], $data, $record);
        catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
    }

} //was _POST reqeust