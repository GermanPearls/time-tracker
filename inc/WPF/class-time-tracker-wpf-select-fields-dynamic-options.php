<?php
/**
 * Class Time_Tracker_WPF_Select_Fields_Dynamic_Options
 *
 * Modify select option field dynamic choices by using WPFornms filter before form front end display
 * Specific to WPF installations
 * Filter wpforms_field_data (in wpforms-lite/includes/class-frontend.php:2136)
 * $field = (array) apply_filters( 'wpforms_field_data', $field, $form_data );
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\WPF;

use function Logically_Tech\Time_Tracker\Inc\tt_get_projects;

/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_WPF_Select_Fields_Dynamic_Options') ) {

    class Time_Tracker_WPF_Select_Fields_Dynamic_Options {

        /**
         * Add dynamic choices to Time Tracker Select Fields
         * 
         */
        public function tt_add_dynamic_choices($fld, $frm) {
            //require_once(TT_PLUGIN_DIR_INC . 'function-tt-utilities.php');
            if (\Logically_Tech\Time_Tracker\Inc\tt_is_tt_form("", $frm["id"]) && $fld["type"] == "select") {
                if ($fld["label"] == "Client") {
                    return $this->add_dynamic_select_choices($fld, $this->get_client_name_array());

                } elseif ($fld["label"] == "Task" || $fld["label"] == "Ticket") {
                    return $this->add_dynamic_select_choices($fld, $this->get_task_list_array());

                } elseif ($fld["label"] == "Project") {
                    return $this->add_dynamic_select_choices($fld, $this->get_project_name_array());

                } elseif ($fld["label"] == "Bill To") {
                    return $this->add_dynamic_select_choices($fld, $this->get_choices_array_from_users_options("bill_to_names"));

                } elseif ($fld["label"] == "Client Source") {
                    return $this->add_dynamic_select_choices($fld, $this->get_choices_array_from_users_options("client_categories"));

                } elseif ($fld["label"] == "Client Source Details") {
                    return $this->add_dynamic_select_choices($fld, $this->get_choices_array_from_users_options("client_sub_categories"));
                
                } elseif ($fld["label"] == "Category") {
                    return $this->add_dynamic_select_choices($fld, $this->get_choices_array_from_users_options("work_categories"));
                }
            } else {
                return $fld;
            }
        }


        /**
         * Create choices array
         * 
         */
        private static function add_dynamic_select_choices(&$fld, $optns=null) {
            //first option blank - avoid empty select box
            $fld["choices"] = array(
                1 => array(
                    'label' => "",
                    'value' => ""
                )
            );
            if ( is_array($optns) ) {
                $i = 2;
                foreach ($optns as $optn) {
                    $fld["choices"][$i] = array(
                        'label' => esc_html($optn),
                        'value' => esc_html($optn)
                    );
                    $i = $i + 1;
                }
            } elseif ( is_string($optns) && $optns !== "" ) {
                $fld["choices"] = array(
                    2 => array(
                        'label' => esc_html($optns),
                        'value' => esc_html($optns)
                    )
                );                
            }
            return $fld;
        }


        /**
         * Get Client Name Array
         * 
         */
        private static function get_client_name_array() {
            $arr = [];
            $clients = \Logically_Tech\Time_Tracker\Inc\tt_get_clients();
            if (is_array($clients) && count($clients) > 0) {
                foreach ($clients as $client) {
                    if (is_array($client)) {
                        array_push($arr, sanitize_text_field($client['Company']));
                    } else {
                        array_push($arr, sanitize_text_field($client->Company));
                    }
                }
            }
            return $arr;
        }


        /**
         * Get Task List Array
         * 
         */
        private static function get_task_list_array() {
            $arr = [];
            $tasks = \Logically_Tech\Time_Tracker\Inc\tt_get_tasks();
            if (is_array($tasks) && count($tasks) > 0) {
                foreach ($tasks as $task) {
                    if (is_array($task)) {
                        array_push($arr, sanitize_text_field($task['TaskID'] . "-" . $task['TDescription']));
                    } else {
                        array_push($arr, sanitize_text_field($task->TaskID . "-" . $task->TDescription));
                    }
                }
            }
            return $arr;
        }


        /**
         * Get Project Name Array
         * 
         */
        private static function get_project_name_array() {
            $arr = [];
            $projects = \Logically_Tech\Time_Tracker\Inc\tt_get_projects();
            if (is_array($projects) && count($projects) > 0) {
                foreach ($projects as $project) {
                    if (is_array($project)) {
                        array_push($arr, sanitize_text_field($project['PName']));
                    } else {
                        array_push($arr, sanitize_text_field($project->PName));
                    }
                }
            }
            return $arr;
        }


        /**
         * Get Options Array from user defined categories
         * 
         */
        private static function get_choices_array_from_users_options($typ) {
            $arr = [];
            $setting = \Logically_Tech\Time_Tracker\Inc\tt_get_user_options('time_tracker_categories', $typ);
            if (is_string($setting) && $setting != "" && !(strpos($setting, PHP_EOL))) {
                return $setting;
            }
            if (is_array($setting)) {
                $optns = $setting;
            } elseif (is_string($setting) && strpos($setting, PHP_EOL)) {
                $optns = explode(PHP_EOL, $setting);
            }
            if ( is_array($optns) && count($optns) > 0) {
                foreach ($optns as $optn) {
                    array_push($arr, sanitize_text_field($optn));
                }
            }
            return $arr;
        }
    }   //end class

}   //class exists


$cls_choices = new Time_Tracker_WPF_Select_Fields_Dynamic_Options();
add_action ('wpforms_field_data', array($cls_choices, 'tt_add_dynamic_choices'), 10, 5);
