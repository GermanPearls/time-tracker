<?php
/**
 * Functions to add custom field to Contact Form 7
 * Field Task Name Dropdown - List Sourced from SQL Table
 * 
 * @since 1.0
 * 
 */


/**
 * Create Custom CF7 Form Tag, Task Name Dropdown
 * 
 */
add_action( 'wpcf7_init', 'custom_add_form_tag_task_name');


/**
 * Initialize task_name as a custom CF7 form tag
 * 
 */
function custom_add_form_tag_task_name() {
  wpcf7_add_form_tag( 'task_name', 'custom_task_name_form_tag_handler', array('name-attr'=>true));
}


/**
 * Define callback for task_name form tag
 * 
 */
function custom_task_name_form_tag_handler( $tag ) {

  //Query time tracker database to get list of current tasks and task id's
  //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
  global $wpdb;
  $task_list_search_string = "SELECT TaskID, TDescription FROM tt_task WHERE TStatus <> 'Completed' AND TStatus <> 'Canceled' AND TStatus <> 'Closed' ORDER BY TaskID ASC";
  $task_list = $wpdb->get_results($task_list_search_string);
  catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);

  $atts = array(
        'type' => 'text',
        'name' => $tag->name,
        'id' => $tag->get_id_option(),
        'class' => $tag->get_class_option(),
        'default' => $tag->get_default_option(),
        'list' => $tag->name . '-options',
    );
    
    $input = sprintf(
        '<select %s />',
        wpcf7_format_atts( $atts)
    );

    $task_name = '<option value=null></option>';

    foreach ($task_list as $val) {
      $task_identifier_string = sanitize_text_field($val->TaskID) . "-" . sanitize_text_field($val->TDescription);     
      if ((isset($_GET['task-name'])) AND ( sanitize_text_field($_GET['task-name']) == $task_identifier_string )) {
         $task_name .= sprintf('<option value="%s" selected=\"selected\">%s</option>', esc_html($task_identifier_string), esc_html($task_identifier_string));
      } else {
        $task_name .= sprintf('<option value="%s">%s</option>', esc_html($task_identifier_string), esc_html($task_identifier_string));
      }
    }
    //close out select tag
    $task_name .= '</select>';
    return $input . $task_name;
}