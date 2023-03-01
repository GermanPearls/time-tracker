<?php
/**
 * Class Time_Tracker_Activator_Forms
 *
 * Initial activation of Time Tracker Plugin - CREATE FRONT END FORMS
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Activator_Forms') ) {

    class Time_Tracker_Activator_Forms {

        public static $form_details = array();
        public static $form_content = array();
        public static $post_typ = "";
        public static $form_class = "";
        

        /**
         * Define form details
         * 
         */
        private static function define_forms() {
            if (self::$post_typ == "") {
                self::get_post_type();
            }
            if (self::$form_class == "") {
                self::get_form_class_name();
            }
            if (self::$form_content == array()) {
                self::create_form_content_array();
            }
            if (self::$form_details == array()) {
                self::create_form_details_array();
            }
        }


        /**
         * Setup
         * 
         */
        public static function setup() {
            self::define_forms();
            self::create_forms('false');
        }


        /**
         * Update Version - Check for updates
         * 
         */ 
        public static function check_forms_for_updates() {
            self::setup();
            self::create_forms('true');
        }


        /**
         * Update Version - Force updates
         * 
         */ 
        public static function force_form_updates() {
            self::setup();
            self::create_forms('true');
        }

        /**
         * Get expected form content based on definitions herein and post id, if exists
         * 
         */
        private static function get_expected_form_content($form_index, $post_id = 0) {
            if ($form_index < count(self::$form_content)) {
                if (TT_PLUGIN_FORM_TYPE == "CF7") {
                    return self::$form_content[$form_index];
                } elseif (TT_PLUGIN_FORM_TYPE == "WPF") {
                    $content = self::$form_class::decode_form_content(self::$form_content[$form_index]);
                    $content["id"] = $post_id;
                    return self::$form_class::encode_form_content($content);
                }
            }
        }
        
        
        /**
         * Check form exists and matches current version
         * 
         */
        private static function check_form_is_up_to_date($i, $form_post_id, $force_update) {
            $installed_form = get_post($form_post_id);
            $installed_form_content = $installed_form->post_content;
            self::define_forms();
            if (count(self::$form_content) > $i) {
                $updated_content = self::get_expected_form_content($i, $form_post_id);
                
                //does the content match the current version}              
                if ((wp_slash($installed_form_content) != $updated_content) || ($force_update == true)) {
                    $updated_form = array(
                        'ID' => $form_post_id,
                        'post_content' => $updated_content
                    );
                    $result = wp_update_post($updated_form);
                    $result_meta = update_post_meta($form_post_id, '_form', $updated_content);
                }   
            }             
            return $form_post_id;
        }


        /**
         * Create all forms in array
         * 
         */
        public static function create_forms($force_update) {
            $i = 0;
            self::define_forms();
            for ($i == 0; $i < count(self::$form_details); $i++) {
                $form_arr = self::get_form_details($i);
                
                //check if form exists already
                $form_exists = get_posts(array(
                    'title'=> $form_arr['post_title'],
                    'post_type' => $form_arr['post_type']
                ), ARRAY_A);

                //if form does not exist, create it
                if (empty($form_exists)) {
                    $post_id = wp_insert_post($form_arr);

                    //WPForms has the form id embedded in the post content, now that we have the id, update the content json
                    if (TT_PLUGIN_FORM_TYPE == "WPF") {
                        self::$form_class::update_form_id_in_database($form_arr, $post_id);
                    }

                    //post meta
                    if ($post_id) {
                        self::$form_class::add_form_post_meta($post_id, $i);
                    }
                
                //if form does exist, confirm it is up to date with current version
                } else {
                    self::check_form_is_up_to_date($i, $form_exists[0]->ID, $force_update);
                }
            }
        }
        
        
        /**
         * Define arguments for creating form
         * 
         */
        public static function get_form_details($arr_index) {
            if (self::$post_typ == "") {
                self::get_post_type();
            }
            $arr = array(
                'post_author'           => '',
                'post_content'          => self::$form_details[$arr_index]['Content'],
                'post_content_filtered' => '',
                'post_title'            => self::$form_details[$arr_index]['Title'],
                'post_name'             => self::$form_details[$arr_index]['Slug'],
                'post_excerpt'          => '',
                'post_status'           => 'publish',
                'post_type'             => self::$post_typ,
                'page_template'         => '',
                'comment_status'        => 'closed',
                'ping_status'           => 'closed',
                'post_password'         => '',
                'to_ping'               => '',
                'pinged'                => '',
                'post_parent'           => '',
                'menu_order'            => 0,
                'guid'                  => '',
                'import_id'             => 0,
                'context'               => '',
            );  
            return $arr;          
        }


        /**
         * Create array of properties that are form dependent
         * 
         */
        public static function create_form_details_array() {
            $details = array();
            $all_details = array();
            if (self::$form_class == "") {
                self::get_form_class_name();
            }
            //add new client
            $details = array(
                "Title" => "Add New Client",
                "Slug" => "form-add-new-client",
                "Content" => self::$form_class::get_form_content_new_client()
            );
            array_push($all_details, $details);

            //add new project
            $details = array(
                "Title" => "Add New Project",
                "Slug" => "form-add-new-project",
                "Content" => self::$form_class::get_form_content_new_project()
            );
            array_push($all_details, $details);

            //add new recurring task
            $details = array(
                "Title" => "Add New Recurring Task",
                "Slug" => "form-add-new-recurring-task",
                "Content" => self::$form_class::get_form_content_new_recurring_task()
            );
            array_push($all_details, $details);
  
            //add new task
            $details = array(
                "Title" => "Add New Task",
                "Slug" => "form-add-new-task",
                "Content" => self::$form_class::get_form_content_new_task()
            );
            array_push($all_details, $details);

            //add time entry
            $details = array(
                "Title" => "Add Time Entry",
                "Slug" => "form-add-time-entry",
                "Content" => self::$form_class::get_form_content_add_time_entry()
            );
            array_push($all_details, $details);

            //filter time
            $details = array(
                "Title" => "Filter Time",
                "Slug" => "form-filter-time",
                "Content" => self::$form_class::get_form_content_filter_time()
            );
            array_push($all_details, $details);

            self::$form_details = $all_details;
            return $all_details;
        }


        /**
         * Create content details array
         * 
         */
        public static function create_form_content_array() {
            if (self::$form_class == "") {
                self::get_form_class_name();
            }
            self::$form_class::create_form_content_array();
            self::$form_content = self::$form_class::$form_content;
        }
        

        /**
         * Get Post Type for WP-Posts Database Setting
         * 
         */
        public static function get_post_type() {
            if (TT_PLUGIN_FORM_TYPE == "CF7") {
                self::$post_typ = "wpcf7_contact_form";
            }
            elseif (TT_PLUGIN_FORM_TYPE == "WPF") {
                self::$post_typ = "wpforms";
            }
            return self::$post_typ;
        }


        /**
         * Get Form Class Namespace and Class Name Based on Form Plugin Being Used
         * 
         */
        private static function get_form_class_name() {
            self::$form_class = __NAMESPACE__ . "\\" . TT_PLUGIN_FORM_TYPE . "\\" . "Time_Tracker_Activator_Forms_" . TT_PLUGIN_FORM_TYPE;
        }

    }  //close class

}