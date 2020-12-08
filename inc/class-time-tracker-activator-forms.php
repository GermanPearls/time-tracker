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
        public static $mail_meta = array();
        public static $mail_2_meta = array();
        public static $msg_meta = array();
		public static $additional_settings = "";
        
        /**
         * Constructor
         * 
         */
        public function __construct() {
            //$form_details = self::create_form_details_array();
            //self::create_forms();
        }


        /**
         * Setup
         * 
         */
        public static function setup() {
            self::create_form_details_array();
            self::create_form_content_array();
            self::get_mail_meta();
            self::get_mail_2_meta();
            self::get_msg_meta();
			self::get_additional_settings();
            self::create_forms();
        }


        /**
         * Create all forms in array
         * 
         */
        public static function create_forms() {
            $i = 0;
            $number_forms = count(self::$form_details);
            for ($i==0; $i<$number_forms; $i++) {
                $form_arr = self::get_form_details($i);
                
                //check if form exists already
                $form_exists = get_posts(array(
                    'title'=> $form_arr['post_title'],
                    'post_type' => 'wpcf7_contact_form'
                ), ARRAY_A);
                if (empty($form_exists)) {
                    $post_id = wp_insert_post($form_arr);
                    if ($post_id) {
                        add_post_meta($post_id, '_form', self::$form_content[$i]);
                        add_post_meta($post_id, '_mail', self::$mail_meta);
                        add_post_meta($post_id, '_mail_2', self::$mail_2_meta);
                        add_post_meta($post_id, '_messages', self::$msg_meta);
                        add_post_meta($post_id, '_additional_settings', self::$additional_settings);                    
                        add_post_meta($post_id, '_locale', self::get_user_location() );
                    }
                } //check form doesn't already exist
            }
        }


        /**
         * post locale information for post meta
         * 
         */
        public static function get_user_location() {
            $users_location = "";
            $users_location = get_user_locale();
            if ($users_location) {
                return $users_location;
            } else {
                return "en_US";
            }
        }
        
        
        /**
         * Define arguments for creating form (CF7 post type)
         * 
         */
        public static function get_form_details($arr_index) {
            $arr = array(
                'post_author'           => '',
                'post_content'          => self::$form_details[$arr_index]['Content'],
                'post_content_filtered' => '',
                'post_title'            => self::$form_details[$arr_index]['Title'],
                'post_name'             => self::$form_details[$arr_index]['Slug'],
                'post_excerpt'          => '',
                'post_status'           => 'publish',
                'post_type'             => 'wpcf7_contact_form',
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
          
            //add new client
            $details = array(
                "Title" => "Add New Client",
                "Slug" => "form-add-new-client",
                "Content" => self::get_form_content_new_client() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
            );
            array_push($all_details, $details);

            //add new project
            $details = array(
                "Title" => "Add New Project",
                "Slug" => "form-add-new-project",
                "Content" => self::get_form_content_new_project() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
            );
            array_push($all_details, $details);

            //add new recurring task
            $details = array(
                "Title" => "Add New Recurring Task",
                "Slug" => "form-add-new-recurring-task",
                "Content" => self::get_form_content_new_recurring_task() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
            );
            array_push($all_details, $details);
  
            //add new task
            $details = array(
                "Title" => "Add New Task",
                "Slug" => "form-add-new-task",
                "Content" => self::get_form_content_new_task() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
            );
            array_push($all_details, $details);

            //add time entry
            $details = array(
                "Title" => "Add Time Entry",
                "Slug" => "form-add-time-entry",
                "Content" => self::get_form_content_add_time_entry() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
            );
            array_push($all_details, $details);

            //filter time
            $details = array(
                "Title" => "Filter Time",
                "Slug" => "form-filter-time",
                "Content" => self::get_form_content_filter_time() . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta)
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
            $content = array();
            array_push($content, self::get_form_content_new_client());
            array_push($content, self::get_form_content_new_project());
            array_push($content, self::get_form_content_new_recurring_task());
            array_push($content, self::get_form_content_new_task());
            array_push($content, self::get_form_content_add_time_entry());
            array_push($content, self::get_form_content_filter_time());
            self::$form_content = $content;
        }           



        /**
         * Create form content - New Client Form
         * 
         */
        public static function get_form_content_new_client() {
            $html = "<label> Company
            [text company maxlength:100] </label>
        
            <label> Contact Name
                [text contact-name maxlength:100] </label>
            
            <label> Contact Email
                [email contact-email maxlength:100] </label>
            
            <label> Telephone #
                [tel contact-telephone] </label>
            
            <label> Bill To
                [bill_to_name bill-to ie:bill-to-name-dropdown] </label>
            
            <label> Source
                [client_category client-source id:client-source-dropdown] </label>
            
            <label> Source Details
                [client_sub_category client-source-details id:client-source-details-dropdown] </label>
            
            <label> Comments
                [textarea comments maxlength:1000] </label>
            
            [submit id:add-client-submit \"Submit\"]";
            return $html;
        }


        /**
         * Create form content - New Project
         * 
         */
        public static function get_form_content_new_project() {
            $html = "<label> Project Name (required)\r\n";
            $html .= "[text project-name maxlength:100] </label>\r\n";
            $html .= "\r\n";
            $html .= "<label> Client\r\n";
            $html .= "[client_name client-name] </label>\r\n";
            $html .= "\r\n";
            $html .= "<label> Category\r\n";
            $html .= "[work_category project-category id:project-category-dropdown]</label>\r\n";
            $html .= "\r\n";
            $html .= "<label>Time Estimate\r\n";
            $html .= "[number time-estimate] </label>\r\n";
            $html .= "\r\n";
            $html .= "<label> Details\r\n";
            $html .= "[textarea project-details maxlength:500] </label>\r\n";
            $html .= "\r\n";
            $html .= "[submit id:add-project-submit \"Submit\"]\r\n";
            return $html;
        }


        /**
         * Create form content - New Recurring Task
         * 
         */
        public static function get_form_content_new_recurring_task() {
            $html = "<label> Task Name (required)
            [textarea task-name 20x1 maxlength:1500] </label>
            <label> Client
            [client_name client-name] </label>
            <label> Project
            [project_name project-name id:project-dropdown] </label>
            <label> Category
            [work_category task-category id:task-category-dropdown]</label>
            <label> Time Estimate
            [text time-estimate] </label>
            Recurring Frequency
            [select recur-freq use_label_element \"Monthly\" \"Weekly\"]
            <label> Task Notes
            [textarea task-desc] </label>
            <label> End Repeat
            [date end-repeat]</label>
            [submit id:add-task-submit \"Send\"]";
            return $html;
        }


        /**
         * Create form content - New Task
         * 
         */
        public static function get_form_content_new_task() {
            $html = "<label> Task Description (required)
            [textarea task-description 20x1 maxlength:500] </label>
            <label> Client
            [client_name client-name] </label>
            <label> Project
            [project_name project-name id:project-dropdown] </label>
            <label> Category
            [work_category task-category id:task-category-dropdown]</label>
            <label> Time Estimate
            [text time-estimate] </label>
            <label> Due Date
            [date due-date \"today\"] </label>
            <label> Notes 
            [textarea notes] </label>
            [hidden what-next default:\"SaveTask\"]
            <input type=\"submit\" name=\"submit-save\" value=\"SaveTask\">   <input type=\"submit\" name=\"submit-start\" value=\"StartWorking\" onclick=\"save_new_task_and_start_timer()\">";
            return $html;
        }


        /**
         * Create form content - New Time Entry
         * 
         */
        public static function get_form_content_add_time_entry() {
            $html = "<label> Start Time
            [datetime start-time id:start-time] </label>
        
            <label> Client
                [client_name client-name default:get] </label>
            
            <label> Ticket
                [task_name task-name default:get id:task-dropdown] </label>
            
            <label> Notes 
                [textarea time-notes maxlength:1999] </label>
            
            <label> New Task Status
                [select new-task-status id:new-task-status include_blank \"In Process\" \"Not Started\" \"Ongoing\" \"Waiting Client\" \"Complete\" \"Canceled\"] </label>
            
            <label> End Time
                [datetime end-time id:end-time] </label>
            
            <label> Follow Up (Create New Task)
                [text follow-up maxlength:500] </label>
            
            [submit id:add-time-submit \"Send\"]";
            return $html;
        }


        /**
         * Create form content - Time Entry Filter
         * 
         */
        public static function get_form_content_filter_time() {
            $html = "<label> First Date
            [date first-date id:first-date] </label>
        
            <label> Last Date
                [date last-date id:last-date] </label>
            
            <label> Client
                [client_name client-name id:client-name default:get] </label>
            
            <label> Notes 
                [text time-notes id:time-notes] </label>
            
            <label> Ticket
                [task_name task-name id:task-name default:get] </label>
            
            [hidden form-type default:\"filter\"]
            [submit id:filter-time-submit \"Filter Time Entries\"]";
            return $html;
        }


        /**
         * Get Additional Settings
         * 
         */
        public static function get_additional_settings() {
            $settings = "skip_mail: on";  
            self::$additional_settings = $settings;
        }
		
		
		/**
         * Get Mail Meta
         * 
         */
        public static function get_mail_meta() {
            $body = "";
            $body = "From: [your-name] <[your-email]>\r\n";
            $body .= "Subject: [your-subject]\r\n";
            $body .= "\r\n";
            $body .= "Message Body:\r\n";
            $body .= "[your-message]\r\n";
            $body .= "\r\n";
            $body .= "-- \r\n";
            $body .= "This e-mail was sent from a contact form on " . tt_get_site_name() . " (" . tt_get_site_url() . ")";            
            
            $mail = array();
            $mail["active"] = true;
            $mail["subject"] = tt_get_site_name() . " \"[your-subject]\"";
            $mail["sender"] = tt_get_site_name() . " <" . tt_get_wordpress_email() . ">";
            $mail["recipient"] = tt_get_site_admin_email();
            $mail["body"] = $body;
            $mail["additional headers"] = "Reply-To: " . tt_get_site_admin_email() . "\r\n";
            $mail["attachments"] = "\r\n";
            $mail["use_html"] = false;
            $mail["exclude_blank"] = false;
            self::$mail_meta = $mail;
        }


        /**
         * Get Mail 2 Meta
         * 
         */
        public static function get_mail_2_meta() {
            $mail2 = array();
            $mail2 = self::$mail_meta;
            $mail2["active"] = false;
            self::$mail_2_meta = $mail2;
        }


        /**
         * Get Message Meta
         * 
         */
        public static function get_msg_meta() {
            $msg = array();
            $msg["mail_sent_ok"] = "OK.";
            $msg["mail_sent_ng"] = "There was an error submitting this form. Please try again later.";
            $msg["validation_error"] = "One or more fields have an error. Please check and try again.";
            $msg["spam"] = "There was an error trying to send your message. Please try again later.";
            $msg["accept_terms"] = "You must accept the terms and conditions before sending your message.";
            $msg["invalid_required"] = "The field is required.";
            $msg["invalid_too_long"] = "The field is too long.";
            $msg["invalid_too_short"] = "The field is too short.";
            $msg["invalid_date"] = "The date format is incorrect.";
            $msg["date_too_early"] = "The date is before the earliest one allowed.";
            $msg["date_too_late"] = "The date is after the latest one allowed.";
            $msg["upload_failed"] = "There was an unknown error uploading the file.";
            $msg["upload_file_type_invalid"] = "You are not allowed to upload files of this type.";
            $msg["upload_file_too_large"] = "The file is too big.";
            $msg["upload_failed_php_error"] = "There was an error uploading the file.";
            $msg["invalid_number"] = "The number format is invalid.";
            $msg["number_too_small"] = "The number is smaller than the minimum allowed.";
            $msg["number_too_large"] = "The number is larger than the maximum allowed.";
            $msg["quiz_answer_not_correct"] = "The answer to the quiz is incorrect.";
            $msg["invalid_email"] = "The e-mail address entered is invalid.";
            $msg["invalid_url"] = "The URL is invalid.";
            $msg["invalid_tel"] = "The telephone number is invalid.";   
            self::$msg_meta = $msg;
        }

    }  //close class

}  //close if class exists