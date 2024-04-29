<?php 
/**
 * Function dynamic-project-dropdown
 *
 * Dynamically update the project dropdown list depending on client chosen
 * Called from updateProjectList Javascript function triggered by client onchange event
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Update the project list based on client selected by user.
 * 
 * @since 1.0.0
 * 
 * @return array Results of update including success, details, and message fields.
 */
function tt_update_project_list_function() {
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['client']) ) {

		if ( check_ajax_referer( 'tt_update_project_list_nonce', 'security' )) {

            //Which client was chosen by the user in the previous dropdown?
            //pull the variable from the url and remove the % encoding, and strip slashes before apostrophes, then clean
            $client_name = sanitize_text_field(stripslashes(urldecode($_POST['client'])));
            $client_id = get_client_id_from_name($client_name);

            //Get list of current projects and project id's
            global $wpdb;
            $project_list_search_string = $wpdb->prepare('SELECT ProjectID, PName FROM tt_project WHERE ClientID= "%s"', $client_id);
            $project_list = tt_query_db($project_list_search_string);

            $project_options = '<option value=null></options>';

            //Create new options for dropdown based on narrowed search results
            foreach ($project_list as $val) {
                $project_options .= '<option value="' . esc_html($val->PName) . '">' . esc_html($val->PName) . '</option>';
            }

            //close out select tag
            $project_options .= '</select>';

            //display response
            //echo $project_options;

            //return result to ajax call
			if ($project_options == "") {
				$return = array(
					'success' => 'false',
					'details' => 'No projects returned',
					'message' => $wpdb->last_error
				);
				wp_send_json_error($return, 500);
			} else {
				$return = array(
					'success' => 'true',
					'details' => $project_options,
					'message' => 'Success'
				);
				wp_send_json_success($return, 200);			
			}
        
        } //security check
    } //post where client set
} //function