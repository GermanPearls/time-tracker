<?php
/**
 * Functions to add custom field to Contact Form 7
 * Field Work Category Dropdown - List Sourced from Plugin Settings in WP Database
 * 
 * @since 1.1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\CF7;


/**
 * Create Custom CF7 Form Tags
 * 
 */
add_action( 'wpcf7_init', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_add_form_tag_work_category');
add_action( 'wpcf7_init', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_add_form_tag_client_category');
add_action( 'wpcf7_init', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_add_form_tag_client_sub_category');
add_action( 'wpcf7_init', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_add_form_tag_bill_to_name');


/**
 * INITIALIZE FUNCTIONS
 * 
 */
/**
 * Initialize work_category as a custom CF7 form tag
 * 
 * @since 1.1.0
 */
function custom_add_form_tag_work_category() {
  \wpcf7_add_form_tag( 'work_category', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_work_category_form_tag_handler', array('name-attr'=>true));
}


/**
 * Initialize client as a custom CF7 form tag
 * 
 * @since 1.1.0
 */
function custom_add_form_tag_client_category() {
    \wpcf7_add_form_tag( 'client_category', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_client_category_form_tag_handler', array('name-attr'=>true));
}


/**
 * Initialize client sub_category as a custom CF7 form tag
 * 
 * @since 1.1.0
 */
function custom_add_form_tag_client_sub_category() {
    \wpcf7_add_form_tag( 'client_sub_category', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_client_sub_category_form_tag_handler', array('name-attr'=>true));
}


/**
 * Initialize bill to name as a custom CF7 form tag
 * 
 * @since 1.1.0
 */
function custom_add_form_tag_bill_to_name() {
    \wpcf7_add_form_tag( 'bill_to_name', 'Logically_Tech\Time_Tracker\Inc\CF7\custom_bill_to_name_form_tag_handler', array('name-attr'=>true));
}


/**
 * CALLBACK FUNCTIONS
 * 
*/
/**
 * Callback for work category dropdown
 * 
 * @since 1.1.0
 * 
 * @param string $tag ??
 * 
 * @return 
 */
function custom_work_category_form_tag_handler($tag) {
    $form_field = custom_category_form_tag_handler($tag, 'work_categories');    
    return $form_field;
}


/**
 * Callback for client category dropdown
 * 
 * @since 1.1.0
 */
function custom_client_category_form_tag_handler($tag) {
    $form_field = custom_category_form_tag_handler($tag, 'client_categories');    
    return $form_field;
}


/**
 * Callback for client sub_category dropdown
 * 
 * @since 1.1.0
 */
function custom_client_sub_category_form_tag_handler($tag) {
    $form_field = custom_category_form_tag_handler($tag, 'client_sub_categories');    
    return $form_field;
}


/**
 * Callback for bill to name dropdown
 * 
 * @since 1.1.0
 */
function custom_bill_to_name_form_tag_handler($tag) {
    $form_field = custom_category_form_tag_handler($tag, 'bill_to_names');    
    return $form_field;
}


/**
 * GET INFORMATION FROM PLUGIN SETTINGS
 * 
 */
/**
 * Get details for category form tags from Plugin settings
 * 
 * @since 1.1.0
 * @since 3.0.15 Allowed for either underscores (_) or hyphens (-) in option names for backwards compatibility.
 */
function custom_category_form_tag_handler( $tag, $type ) {
    //Get categories from Time Tracker settings page
    $settings = get_option('time_tracker_categories');
	if ($settings) {
        if (array_key_exists($type, $settings)) {
            $list = $settings[$type];
        } elseif (array_key_exists(str_replace('_', '-', $type), $settings)) {
            $list = $settings[str_replace('_', '-', $type)];        
        }
		$array = explode("\r\n", $list);
	} else {
		$array = array("N/A");
	}

    $atts = array(
        'type' => 'text',
        'name' => $tag->name,
        'id' => $tag->get_id_option(),
        'class' => $tag->get_class_option(),
        'default' => $tag->get_default_option(),
        'list' => $tag->name . '-options',
    );
    
    //open select tag
    $input = sprintf(
        '<select %s />',
        \wpcf7_format_atts( $atts)
    );

    //start with blank in select box
    $form_options = "<option value=\"\"></option>";

    //add each option in array
    foreach ($array as $option) {
        $name = sanitize_text_field($option);
        $form_options .= '<option value="' . esc_html($name) . '">' . esc_html($name) . '</option>';
    }

    //close out select tag
    $form_options .= '</select>';

    //return entire field
    return $input . $form_options;
}
