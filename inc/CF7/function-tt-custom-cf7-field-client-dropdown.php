<?php
/**
 * Functions to add custom field to Contact Form 7
 * Field Client Name Dropdown - List Sourced from SQL Table
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\CF7;


/**
 * Create Custom CF7 Form Tag, Client Name Dropdown
 * 
 */
add_action( 'wpcf7_init', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_add_form_tag_client_name');


/**
 * Initialize client_name as a custom CF7 form tag
 * 
 */
function custom_add_form_tag_client_name() {
  wpcf7_add_form_tag( 'client_name', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_client_name_form_tag_handler', array('name-attr'=>true));
}


/**
 * Define callback for client_name form tag
 * 
 */
function custom_client_name_form_tag_handler( $tag ) {

  $client_list = \Logically_Tech\Time_Tracker\Inc\tt_get_clients();

  $atts = array(
        'type' => 'text',
        'name' => $tag->name,
        'id' => $tag->get_id_option(),
        'class' => $tag->get_class_option(),
        'default' => $tag->get_default_option(),
        'list' => $tag->name . '-options',
        'onchange' => 'tt_update_task_dropdown(); tt_update_project_dropdown();'
    );
    
    $input = sprintf(
        '<select %s />',
        wpcf7_format_atts( $atts)
    );

    $client_name = "<option value=\"\"></option>";

    foreach ($client_list as $val) {
      $company_name = $val->Company;
      if ((isset($_GET['client-name'])) AND ( stripslashes($_GET['client-name']) == $company_name )) {
        $client_name .= '<option value="' . esc_textarea($company_name) . '" selected="selected">' . esc_textarea($company_name) . '</option>';
      } else {
        $client_name .= '<option value="' . esc_textarea($company_name) . '">' . esc_textarea($company_name) . '</option>';
      }
    }

    //close out select tag
    $client_name .= '</select>';

    return $input . $client_name;
}