<?php
/**
 * Class Time_Tracker_WPF_Fields_Add_Properties
 *
 * Add additional properties to WPF fields
 * Specific to WPF installations
 * Filter wpforms_field_properties (in wpforms-lite/includes/class-frontend.php:877)
 * $properties = apply_filters( 'wpforms_field_properties', $properties, $field, $form_data );
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\WPF;

/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_WPF_Fields_Add_Properties') ) {

    class Time_Tracker_WPF_Fields_Add_Properties {

        /**
         * Add a 2nd submit button to add new task 
         * 
         */
        public function tt_add_second_submit_button($form_data) {
            $formid = absint($form_data['id']);
            if (\Logically_Tech\Time_Tracker\Inc\tt_get_form_name($formid) == "Add New Task") {
                $btn = "<button type=\"submit\"";
                $btn .= " name=\"wpforms[submit]\"";
                $btn .= " id=\"wpforms-submit-and-start-" . $formid . "\"";
                $btn .= " class=\"wpforms-submit tt-button tt-inline-button\"";
                $btn .= " data-alt-text=\"Saving...\"";
                $btn .= " data-submit-text=\"Start Working\"";
                $btn .= " aria-live=\"assertive\"";
                $btn .= " value=\"wpforms-submit\"";
                $btn .= ">Start Working</button>";
                echo $btn;
            }
        }


        /**
         * Add additional properties Time Tracker Field properties
         * 
         */
        public function tt_add_wpf_field_properties($properties, $field, $form_data) {
            if (\Logically_Tech\Time_Tracker\Inc\tt_is_tt_form("", $form_data['id'])) {

                if ($field['label'] == 'Client') {
                    //update project and task based on client selected
                    $properties = $this->add_container_property_to_select_field($properties, "onChange", "tt_update_task_dropdown(); tt_update_project_dropdown();");

                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "client");

                    //allow for auto fill from url query parameters
                    $properties = $this->add_query_var_to_select_field($properties, $form_data, "client-name");
                    
                    return $properties;
                } elseif ($field['label'] == "Task" || $field['label'] == "Ticket") {
                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "task");

                    //auto populate from url query param
                    return $this->add_query_var_to_select_field($properties, $form_data, "task-name");
                    
                } elseif ($field['label'] == "Project") {
                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "project");

                    //auto populate form url query param
                    return $this->add_query_var_to_select_field($properties, $form_data, "project-name");
                
                } elseif ($field['label'] == "Notes") {
                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "notes");
                
                } elseif ($field['label'] == "First Date") {
                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "first-date");
                
                } elseif ($field['label'] == "Last Date") {
                    //add html property to identify field later
                    $properties = $this->add_container_property_to_select_field($properties, "data-tt-field", "last-date");
                
                }
            }
            return $properties;
        }


        /**
         * Add properties to select field
         * Note this will not work on other field types - their wpforms structure is different
         * 
         */
        private function add_container_property_to_select_field($properties, $nm, $val) {
            if (is_array($properties)) {
                if (array_key_exists('input_container', $properties)) {
                    if (is_array($properties['input_container'])) {
                        if (array_key_exists('attr', $properties['input_container'])) {
                            if (is_array($properties['input_container']['attr'])) {
                                if (array_key_exists($nm, $properties['input_container']['attr'])) {
                                    $val = $properties['input_container']['attr'][$nm] . " " . $val;
                                }
                            }
                        }
                    }
                }
            }
            $properties['input_container']['attr'][$nm] = $val;
            return $properties;
        }

        
        /**
         * Add default via query_var to select field
         * Note this will not work on other field types - their wpforms structure is different
         * 
         */
        private function add_query_var_to_select_field($properties, $form_data, $query_var_name) {
            $val = \wpforms_process_smart_tags( "{query_var key=\"" . $query_var_name . "\"}", $form_data );
            $i = 1;
            while (array_key_exists(strval($i), $properties['inputs'])) {
                if ($properties['inputs'][strval($i)]['attr']['value'] == $val) {
                    $properties['inputs'][strval($i)]['default'] = $val;
                    return $properties;
                }
                $i = $i + 1;
            }
            return $properties;
        }

    }   //end class

}   //class exists


$new_props = new Time_Tracker_WPF_Fields_Add_Properties();
add_action ('wpforms_field_properties', array($new_props, 'tt_add_wpf_field_properties'), 10, 5);
//add_action ('wpforms_frontend_output', array($new_props, 'tt_add_second_submit_button'), 50, 5);
add_action ('wpforms_display_submit_after', array($new_props, 'tt_add_second_submit_button'), 10, 1);
