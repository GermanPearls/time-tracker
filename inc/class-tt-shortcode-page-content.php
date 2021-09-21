<?php
/**
 * Class Time_Tracker_Shortcode_Page_Content
 *
 * Shortcode to display page content on front end pages
 * Allows dynamic generation of pages - to allow for updates and revisions
 * 
 * @param array $args  Only first one used, index 0
 * @return string       Shortcode output, page content
 * 
 * Since 1.6.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_Shortcode_Page_Content') ) {

    /**
     * Class
     * 
     */  
    class Time_Tracker_Shortcode_Page_Content {


        /**
         * Class variables
         * 
         */
        public $shortcode = 'time_tracker_page_content';
        private $content;

        /**
         * Constructor
         * 
         */
        public function __construct() {
            add_shortcode( $this->shortcode, array( $this, 'page_content_shortcode' ) );
        }


        /**
         * Callback
         * 
         */
        public function page_content_shortcode($args) {
            $this->set_content();
            $page_content = $this->get_content($args[0]);
            return $page_content;
        }


        /**
         * Set Content
         * 
         */
        private function set_content() {
            $content = [
                "tt-home" => "",
                "pending-time" => "",
                "new-client" => "",
                "all-clients" => "",
                "new-task" => "",
                "task-detail" => "",
                "all-tasks" => "",
                "new-project" => "",
                "all-projects" => "",
                "new-recurring-task" => "",
                "all-recurring-tasks" => "",
                "new-time-entry" => $this->get_time_entry_content(),
                "all-time-entries" => "",
                "open-task-list" => ""
            ];
            $this->content = $content;
        }


        /**
         * Time Entry Page
         * 
         */
        private function get_time_entry_content() {
            $tip = New Time_Tracker_Tool_Tip;
            $tool_tip = $tip->get_tip("tip-end-work-timer");
            $time_entry_content = "<button class=\"end-work-timer float-right no-border-radius\" onclick=\"update_end_timer()\">";
            $time_entry_content .= "Set End Time";
            $time_entry_content .= $tool_tip;
            $time_entry_content .= "</button>";
            $time_entry_content .= do_shortcode("[contact-form-7 id=\"" . tt_get_form_id("Add Time Entry") . "\" title=\"Add Time Entry\" html_class=\"tt-form\"]");
            return $time_entry_content;
        }


        /**
         * Get Content
         * 
         */
        private function get_content($page) {
            $html = $this->content[$page];
            return $html;
        }
    

        /**
         * 
         * Return
         * 
         */
        public function get_shortcode() {
            return $this->shortcode;
        }

    } //class
} //if class exists

$Time_Tracker_Shortcode_Page_Content = new Time_Tracker_Shortcode_Page_Content();