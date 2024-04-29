<?php
/**
 * Class Time_Tracker_Activator_Forms_CF7
 *
 * Initial activation of Time Tracker Plugin - CREATE FRONT END FORMS
 * Specific to CF7 installations
 * 
 * @since 3.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\CF7;


/**
 * Check if class exists
 * 
 * @since 3.0.0
 */
if ( ! class_exists('Time_Tracker_Activator_Forms_CF7') ) {

    class Time_Tracker_Activator_Forms_CF7 {

        public static $form_content = array();
        public static $mail_meta = array();
        public static $mail_2_meta = array();
        public static $msg_meta = array();
		public static $additional_settings = "";
        
        /**
         * Constructor
         * 
         * @since 3.0.0
         */
        public function __construct() {
        }


        /**
         * Setup
         * 
         * @since 3.0.0
         */
        public static function setup() {
            self::create_form_content_array();
            self::get_mail_meta();
            self::get_mail_2_meta();
            self::get_msg_meta();
			self::get_additional_settings();
        }


        /**
         * Add Post Meta - specific to CF7
         * 
         * @since 3.0.0
         * 
         * @param double $post_id Wordpress post id of Contact Form 7 form
         * @param integer $i Identifier showing which form in list we are working on
         */
        public static function add_form_post_meta($post_id, $i) {
            if (self::$form_content == array() || self::$mail_meta == array() || self::$mail_2_meta == array()
                || self::$msg_meta == array() || self::$additional_settings = "") {
                self::setup();
            }
            add_post_meta($post_id, '_form', self::$form_content[$i]);
            add_post_meta($post_id, '_mail', self::$mail_meta);
            add_post_meta($post_id, '_mail_2', self::$mail_2_meta);
            add_post_meta($post_id, '_messages', self::$msg_meta);
            add_post_meta($post_id, '_additional_settings', self::$additional_settings);                    
            add_post_meta($post_id, '_locale', self::get_user_location() );
        }


        /**
         * Create content details array
         * 
         * @since 3.0.0
         */
        public static function create_form_content_array() {
            if (self::$form_content == array()) {
                $content = array();
                array_push($content, self::get_form_content_new_client());
                array_push($content, self::get_form_content_new_project());
                array_push($content, self::get_form_content_new_recurring_task());
                array_push($content, self::get_form_content_new_task());
                array_push($content, self::get_form_content_add_time_entry());
                array_push($content, self::get_form_content_filter_time());
                self::$form_content = $content;
            }
        }
        
        
        /**
         * post locale information for post meta
         * 
         * @since 3.0.0
         * 
         * @return string Users location from Wordpress database (default en_US)
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
         * Create form content - New Client Form
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_new_client() {
            $html = "";
            $html .= self::create_33_33_33_row(
                "<label> Company (required)</label>[text* company maxlength:100]",
                "<label> Bill To (required)</label>[bill_to_name bill-to id:bill-to-name-dropdown]",
                "<label> Billing Rate</label>[number billing-rate min:0 max:99999999999 \"" . \Logically_Tech\Time_Tracker\Inc\tt_get_default_billing_rate() . "\"]"
            );
            $html .= self::create_50_50_row(
                "<label> Source (required)</label>[client_category client-source id:client-source-dropdown]",
                "<label> Source Details</label>[client_sub_category client-source-details id:client-source-details-dropdown]"
            );
            $html .= self::create_33_33_33_row(
                "<label> Contact Name</label>[text contact-name maxlength:100]",
                "<label> Contact Email </label>[email contact-email maxlength:100]",
                "<label> Telephone #</label>[text contact-telephone]"
            );
            $html .= "<label> Comments</label>[textarea comments maxlength:1000 x3]";
            $html .= "[submit id:add-client-submit \"Submit\"]";
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }


        /**
         * Create form content - New Project
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_new_project() {
            $html = "";
            $html .= "<label> Project Name (required)</label>[text* project-name maxlength:100]";
            $html .= self::create_50_50_row(
                "<label> Client (required)</label>[client_name client-name]",
                "<label> Category</label>[work_category project-category id:project-category-dropdown]"
            );
            $html .= self::create_50_50_row(
                "<label>Time Estimate (hrs)</label>[number time-estimate]",
                "<label>Due Date (required)</label>[date* due-date]"
            );
            $html .= "<label> Details</label>[textarea project-details maxlength:500 x3]";
            $html .= "[submit id:add-project-submit \"Submit\"]";
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }


        /**
         * Create form content - New Recurring Task
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_new_recurring_task() {
            $html = "";
            $html .= self::create_66_33_row(
                "<label> Task Name (required)</label>[textarea* task-name 20x1 maxlength:1500]",
                "Recurring Frequency (required)[select* recur-freq use_label_element \"Weekly\" \"Monthly\" \"Yearly\"]"
            );
            $html .= self::create_50_50_row(
                "<label> Client (required)</label>[client_name client-name]",
                "<label> Project</label>[project_name project-name id:project-dropdown]"
            );
            $html .= self::create_33_33_33_row(
                "<label> Category</label>[work_category task-category id:task-category-dropdown]",
                "<label> Time Estimate in Hours (required)</label>[text* time-estimate]",
                "<label> End Repeat</label>[date end-repeat]"
            );  
            $html .= "<label> Task Notes</label>[textarea task-desc x3]";
            $html .= "[submit id:add-task-submit \"Send\"]";
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }


        /**
         * Create form content - New Task
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_new_task() {
            $html = "";
            $html .= self::create_66_33_row(
                "<label> Task Description (required)</label>[textarea* task-description 20x1 maxlength:500]",
                "<label> Due Date</label>[date due-date \"today\"]"
            );
            $html .= self::create_50_50_row(
                "<label> Client (required)</label>[client_name client-name]",
                "<label> Project</label>[project_name project-name id:project-dropdown]"
            );
            $html .= self::create_50_50_row(
                "<label> Category</label>[work_category task-category id:task-category-dropdown]",
                "<label> Time Estimate (hrs)</label>[text time-estimate]"
            );
            $html .= "<label> Notes </label>[textarea notes x3]";
            $html .= "[hidden what-next default:\"SaveTask\"]<input type=\"submit\" name=\"submit-save\" class=\"tt-button tt-form-button tt-inline-button\" value=\"Save Task\"><input type=\"submit\" name=\"submit-start\" class=\"tt-button tt-form-button tt-inline-button\" value=\"Start Working\" onclick=\"save_new_task_and_start_timer()\">";
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }


        /**
         * Create form content - New Time Entry
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_add_time_entry() {
            $html = "";
            $html .= self::create_50_50_row(
                "<label> Client (required)</label>[client_name client-name default:get]",
                "<label> Ticket (required)</label>[task_name task-name default:get id:task-dropdown]"
            );
            $html .= self::create_33_33_33_row(
                "<label> Start Time (required)</label>[datetime start-time id:start-time]",
                "<label> End Time (required)</label>[datetime end-time id:end-time]",
                "<label> New Task Status</label>[select new-task-status id:new-task-status include_blank " . self::get_task_status_options() . "]"
            );
            $html .= "<label> Notes (required)</label>[textarea* time-notes maxlength:1999 x7]";
            $html .= self::create_33_33_33_row(
                "<label> Invoiced?</label> [text invoiced id:invoiced]",
                "<label> Invoice #</label> [text invoice-number id:invoice-number]",
                "<label> Invoiced Time</label> [text invoiced-time id:invoiced-time]"
            );
            $html .= self::create_50_50_row(
                "<label> Invoice Notes</label> [text invoice-notes id:invoice-notes]",
                "<label> Follow Up (Create New Task)</label>[text follow-up maxlength:500]"
            );
            $html .= "[submit id:add-time-submit \"Send\"]";
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }

        /**
         * Get task status list
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        private static function get_task_status_options() {
            $task_status = \Logically_Tech\Time_Tracker\Inc\tt_get_user_options('time_tracker_categories', 'task_status');
            if ($task_status != "" && $task_status != null) {
                $arr = explode(chr(13), $task_status);
            } else {
                $arr = [
                    "New",
                    "In Process",
                    "Waiting Client",
                    "Complete",
                    "Canceled"
                ];
            }
            $out = "";
            foreach ($arr as $optn) {
                $out .= "\"" . $optn . "\" ";
            }
            return trim($out);
        }


        /**
         * Create form content - Time Entry Filter
         * 
         * @since 3.0.0
         * 
         * @return string Form structure and details in html Contact Form 7 form (post content for storage in WP db)
         */
        public static function get_form_content_filter_time() {
            $html = "";
            $html .= self::create_33_66_row(
                "<label> First Date</label>[date first-date id:first-date default:get]",
                "<label> Client</label>[client_name client-name id:client-name default:get]"
            );
            $html .= self::create_33_66_row(
                "<label> Last Date</label>[date last-date id:last-date default:get]",
                "<label> Project</label>[project_name project-name id:project-name default:get]"
            );
            $html .= self::create_33_66_row(
                "<label> Ticket</label>[task_name task-name id:task-name default:get]",
                "<label> Notes </label>[text notes id:time-notes default:get]"
            );
            $html .= self::create_100_row (
                "[hidden form-type default:\"filter\"][submit id:filter-time-submit \"Filter Time Entries\"]"
            );
            return $html . "\r\n" . implode("\r\n", self::$mail_meta) . "\r\n" . implode("\r\n", self::$msg_meta);
        }


        /**
         * Layout Classes and Divs - 30/30/30 row
         * 
         * @since 3.0.0
         * 
         * @param string $first CF7 form field details for first field in row
         * @param string $second CF7 form field details for second field in row
         * @param string $third CF7 form field details for third field in row
         * 
         * @return Form structure in CF7 format for 3 form fields across html row
         */
        private static function create_33_33_33_row($first, $second, $third) {
            $out = self::start_form_row();
            $out .= self::start_col_one_third("left") . $first . self::end_form_column();
            $out .= self::start_col_one_third("middle") . $second . self::end_form_column();
            $out .= self::start_col_one_third("right") . $third . self::end_form_column();
            $out .= self::end_form_row();
            return $out;
        }

        
        /**
         * Layout Classes and Divs - 100 row
         * 
         * @since 3.0.0
         * 
         * @param string $first CF7 form field details for first field in row
         * 
         * @return Form structure in CF7 format for 1 form fields across html row
         */
        private static function create_100_row($first) {
            $out = self::start_form_row() . $first . self::end_form_row();
            return $out;
        }


        /**
         * Layout Classes and Divs - 33/66 row
         * 
         * @since 3.0.0
         * 
         * @param string $first CF7 form field details for first field in row
         * @param string $second CF7 form field details for second field in row
         * 
         * @return Form structure in CF7 format for 2 form fields across html row
         */
        private static function create_33_66_row($first, $second) {
            $out = self::start_form_row();
            $out .= self::start_col_one_third("left") . $first . self::end_form_column();
            $out .= self::start_col_two_thirds("right") . $second . self::end_form_column();
            $out .= self::end_form_row();
            return $out;
        }
        

        /**
         * Layout Classes and Divs - 66/33 row
         * 
         * @since 3.0.0
         * 
         * @param string $first CF7 form field details for first field in row
         * @param string $second CF7 form field details for second field in row
         * 
         * @return Form structure in CF7 format for 2 form fields across html row
         */
        private static function create_66_33_row($first, $second) {
            $out = self::start_form_row();
            $out .= self::start_col_two_thirds("left") . $first . self::end_form_column();
            $out .= self::start_col_one_third("right") . $second . self::end_form_column();
            $out .= self::end_form_row();
            return $out;
        }


        /**
         * Layout Classes and Divs - 50/50 row
         * 
         * @since 3.0.0
         * 
         * @param string $first CF7 form field details for first field in row
         * @param string $second CF7 form field details for second field in row
         * 
         * @return Form structure in CF7 format for 2 form fields across html row
         */
        private static function create_50_50_row($first, $second) {
            $out = self::start_form_row();
            $out .= self::start_col_half("left") . $first . self::end_form_column();
            $out .= self::start_col_half("right") . $second . self::end_form_column();
            $out .= self::end_form_row();
            return $out;
        }
        
        
        
        /**
         * Layout Classes and Divs - Start CF7 row
         * 
         * @since 3.0.0
         * 
         * @return string Form structure for start of a row
         */
        private static function start_form_row() {
            return "<div class=\"tt-form-row\">";
        }


        /**
         * Layout Classes and Divs - End CF7 row
         * 
         * @since 3.0.0
         * 
         * @return string Form structure for start of a row
         */
        private static function end_form_row() {
            return "</div>";
        }


        /**
         * Layout Classes and Divs - End CF7 column
         * 
         * @since 3.0.0
         * 
         * @return string Form structure for end of a column
         */
        private static function end_form_column() {
            return "</div>";
        }


        /**
         * Start Half width column - Start CF7 column - 50%
         * 
         * @since 3.0.0
         * 
         * @param string $side Text for class specifying which column
         * 
         * @return string Form structure for start of a column
         */
        private static function start_col_half($side) {
            return "<div class=\"tt-form-element tt-one-half tt-col-" . $side . "\">";
        }
        

        /**
         * Start one third width column - Start CF7 column - 33%
         * 
         * @since 3.0.0
         * 
         * @param string $side Text for class specifying which column
         * 
         * @return string Form structure for start of a column
         */
        private static function start_col_one_third($side) {
            return "<div class=\"tt-form-element tt-one-third tt-col-" . $side . "\">";
        }


        /**
         * Start two thirds width column
         * 
         * @since 3.0.0
         * 
         * @param string $side Text for class specifying which column
         * 
         * @return string Form structure for start of a column
         */
        private static function start_col_two_thirds($side) {
            return "<div class=\"tt-form-element tt-two-thirds tt-col-" . $side . "\">";
        }
        
        /**
         * Get Additional Settings
         * 
         * @since 3.0.0
         */
        public static function get_additional_settings() {
            $settings = "skip_mail: on";  
            self::$additional_settings = $settings;
        }
		
		
		/**
         * Get Mail Meta
         * 
         * @since 3.0.0
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
            $body .= "This e-mail was sent from a contact form on " . \Logically_Tech\Time_Tracker\Inc\tt_get_site_name() . " (" . \Logically_Tech\Time_Tracker\Inc\tt_get_site_url() . ")";            
            
            $mail = array();
            $mail["active"] = true;
            $mail["subject"] = \Logically_Tech\Time_Tracker\Inc\tt_get_site_name() . " \"[your-subject]\"";
            $mail["sender"] = \Logically_Tech\Time_Tracker\Inc\tt_get_site_name() . " <" . \Logically_Tech\Time_Tracker\Inc\tt_get_wordpress_email() . ">";
            $mail["recipient"] = \Logically_Tech\Time_Tracker\Inc\tt_get_site_admin_email();
            $mail["body"] = $body;
            $mail["additional headers"] = "Reply-To: " . \Logically_Tech\Time_Tracker\Inc\tt_get_site_admin_email() . "\r\n";
            $mail["attachments"] = "\r\n";
            $mail["use_html"] = false;
            $mail["exclude_blank"] = false;
            self::$mail_meta = $mail;
        }


        /**
         * Get Mail 2 Meta
         * 
         * @since 3.0.0
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
         * @since 3.0.0
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

    }
}