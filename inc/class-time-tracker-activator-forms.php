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
            $html = "<label> Company (required)</label>
            [text* company maxlength:100] <br/>
            <label> Contact Name</label>
                [text contact-name maxlength:100]  <br/>
            <label> Contact Email </label>
                [email contact-email maxlength:100] <br/>
            <label> Telephone #</label>
                [tel contact-telephone]  <br/>
            <label> Bill To (required)</label>
                [bill_to_name bill-to ie:bill-to-name-dropdown]  <br/>
            <label> Source (required)</label>
                [client_category client-source id:client-source-dropdown] <br/>
            <label> Source Details</label>
                [client_sub_category client-source-details id:client-source-details-dropdown]  <br/>
            <label> Comments</label>
                [textarea comments maxlength:1000]  <br/>
            [submit id:add-client-submit \"Submit\"]";
            return $html;
        }


        /**
         * Create form content - New Project
         * 
         */
        public static function get_form_content_new_project() {
            $html = "<label> Project Name (required)</label>\r\n
                [text* project-name maxlength:100] \r\n
                \r\n
                <label> Client (required)</label>\r\n
                [client_name client-name]\r\n
                \r\n
                <label> Category</label>\r\n
                [work_category project-category id:project-category-dropdown]\r\n
                \r\n
                <label>Time Estimate</label>\r\n
                [number time-estimate]\r\n
                \r\n
                <label>Due Date (required)</label>\r\n
                [date* due-date]\r\n
                \r\n
                <label> Details</label>\r\n
                [textarea project-details maxlength:500]\r\n
                \r\n
                [submit id:add-project-submit \"Submit\"]\r\n";
            return $html;
        }


        /**
         * Create form content - New Recurring Task
         * 
         */
        public static function get_form_content_new_recurring_task() {
            $html = "<label> Task Name (required)</label>
            [textarea* task-name 20x1 maxlength:1500]  <br/>
            <label> Client (required)</label>
            [client_name client-name] <br/> 
            <label> Project</label>
            [project_name project-name id:project-dropdown] <br/> 
            <label> Category</label>
            [work_category task-category id:task-category-dropdown] <br/>
            <label> Time Estimate (required)</label>
            [text* time-estimate]  <br/>
            Recurring Frequency (required)
            [select* recur-freq use_label_element \"Monthly\" \"Weekly\"] <br/>
            <label> Task Notes</label>
            [textarea task-desc]  <br/>
            <label> End Repeat</label>
            [date end-repeat] <br/>
            [submit id:add-task-submit \"Send\"]";
            return $html;
        }


        /**
         * Create form content - New Task
         * 
         */
        public static function get_form_content_new_task() {
            $html = "<label> Task Description (required)</label>
            [textarea* task-description 20x1 maxlength:500]  <br/>
            <label> Client (required)</label>
            [client_name client-name]  <br/>
            <label> Project</label>
            [project_name project-name id:project-dropdown]  <br/>
            <label> Category</label>
            [work_category task-category id:task-category-dropdown] <br/>
            <label> Time Estimate </label>
            [text time-estimate] <br/>
            <label> Due Date</label>
            [date due-date \"today\"]  <br/>
            <label> Notes </label>
            [textarea notes]  <br/>
            [hidden what-next default:\"SaveTask\"]
            <input type=\"submit\" name=\"submit-save\" value=\"SaveTask\">   <input type=\"submit\" name=\"submit-start\" value=\"StartWorking\" onclick=\"save_new_task_and_start_timer()\">";
            return $html;
        }


        /**
         * Create form content - New Time Entry
         * 
         */
        public static function get_form_content_add_time_entry() {
            $html = "<label> Start Time (required)</label>
            [datetime start-time id:start-time]  <br/>
            <label> Client</label>
                [client_name client-name default:get]  <br/>
            <label> Ticket</label>
                [task_name task-name default:get id:task-dropdown]  <br/>
            <label> Notes (required)</label>
                [textarea* time-notes maxlength:1999] <br/> 
            <label> New Task Status</label>
                [select new-task-status id:new-task-status include_blank \"In Process\" \"Not Started\" \"Ongoing\" \"Waiting Client\" \"Complete\" \"Canceled\"]  <br/>
            <label> End Time(required)</label>
                [datetime end-time id:end-time] <br/> 
            <label> Follow Up (Create New Task)</label>
                [text follow-up maxlength:500]  <br/>
            [submit id:add-time-submit \"Send\"]";
            return $html;
        }


        /**
         * Create form content - Time Entry Filter
         * 
         */
        public static function get_form_content_filter_time() {
            $html = "<label> First Date</label>
            [date first-date id:first-date]  <br/>
            <label> Last Date</label>
                [date last-date id:last-date]  <br/>
            <label> Client</label>
                [client_name client-name id:client-name default:get]  <br/>
            <label> Notes </label>
                [text time-notes id:time-notes]  <br/>
            <label> Project</label> 
                [project_name project-name id:project-name default:get] <br/> 
            <label> Ticket</label>
                [task_name task-name id:task-name default:get]  <br/>
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
            $msg["mail_sent_ok"] = "Form submitted successfully.";
            $msg["mail_sent_ng"] = "There was an error submitting this form. Please try again later.";
            $msg["validation_error"] = "One or more fields have an error. Please check and try again.";
            $msg["spam"] = "There was an error trying to send your message. Please try again later.";
            $msg["accept_terms"] = "You must accept the terms and conditions before sending your message.";
            $msg["invalid_required"] = "Please verify all required fields have been filled in.";
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