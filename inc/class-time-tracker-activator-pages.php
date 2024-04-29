<?php
/**
 * Class Time_Tracker_Activator_Pages
 *
 * Initial activation of Time Tracker Plugin. Creates front end pages in Wordpress.
 * 
 * @since 1.0
 * @since 2.2.0 9-20-2021 - modified to allow for updating pages for version updates
 * @since 2.2.0 9-20-2021 - added timesheet detail page
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */       
if ( ! class_exists('Time_Tracker_Activator_Pages') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */ 
    class Time_Tracker_Activator_Pages {

        private static $page_details = array();

        /**
         * Constructor
         * 
         * @since 1.0.0
         */ 
        public function __construct() {
            //self::create_pages();
        }


        /**
         * Setup
         * 
         * @since 1.0.0
         */ 
        public static function setup() {
            self::create_pages();
        }


        /**
         * Update Version
         *
         * @since 2.2.0
         */ 
        public static function check_pages_match_current_version() {
            self::create_pages();
        }
        
        
        /**
         * Add Pages to WP
         * 
         * @since 1.0.0
         */
        private static function create_pages() {
            $i = 0;
            self::create_homepage_details_array();
            $homepage_id = self::check_page_exists_and_is_up_to_date($i);

            self::create_subpage_details_array($homepage_id);
            $num_of_pages = count(self::$page_details);
            for ($i = 1; $i < $num_of_pages; $i++) {
                self::check_page_exists_and_is_up_to_date($i);
            }
        }


        /**
         * Check page exists, has correct status and matches current version
         * 
         * @since 2.2.0
         * @since 3.0.10 Delete any existing pages (in case multiple exist due to activation errors) and create new ones.
         * 
         * @param int $id Identifies index of page within page array created in create_subpage_details_array.
         * 
         * @return int Page ID, if it exists in Wordpress.
         */
        private static function check_page_exists_and_is_up_to_date($i) {
            //delete any existing pages - check for multiple
            $page_id = tt_get_page_id(self::get_page_details($i)['post_title']);
			while ($page_id > 0){
				$result = wp_delete_post($page_id, true);
                $page_id = tt_get_page_id(self::get_page_details($i)['post_title']);
				log_tt_misc('page ' . self::get_page_details($i)['post_title'] . ', #' . $page_id . ' should have been deleted on plugin activation');
				//$page_id = tt_get_page_id(self::get_page_details($i)['post_title']);
			}
            
            //create new page
			$page_id = self::create_page(self::get_page_details($i));
			log_tt_misc(' page ' . self::get_page_details($i)['post_title'] . ' should have been created, now is #' . $page_id);
            return $page_id;
        }


        /**
         * Create Individual Page
         * 
         * @since 1.0.0
         * 
         * @param array $details Array containing details of page required to send to Wordpress for page creation.
         * 
         * @return int Page ID from Wordpress, 0 if it fails.
         */
        private static function create_page($details) {
            $new_page_id = wp_insert_post($details);
            return $new_page_id;
        }


        /**
         * Update Page Status
         * 
         * @since 2.2.0
         * @since 3.0.10 Deprecated. Pages delete on deactivation and are recreated on activation. 
         * 
         * @param int $page_id Wordpress page ID, if it exists.        
         */
        private static function update_page_status($page_id) {
            add_action('init', wp_update_post(array(
                'ID' => $page_id,
                'post_status' => 'private'
            )), 10, 1);
        }

        
        /**
         * Get all arguments to add page to WP
         * 
         * @since 1.0.0 
         * 
         * @param int $arr_index Identifies index of page within page array created in create_subpage_details_array.
         * 
         * @return array Array of details required to create page in Wordpress.
         */
        private static function get_page_details($arr_index) {
            $arr = array(
                'post_author'           => '',
                'post_content'          => self::$page_details[$arr_index]['Content'],
                'post_content_filtered' => '',
                'post_title'            => self::$page_details[$arr_index]['Title'],
                'post_name'             => self::$page_details[$arr_index]['Slug'],
                'post_excerpt'          => '',
                'post_status'           => 'private',
                'post_type'             => 'page',
                'page_template'         => 'tt-page-template.php',
                'comment_status'        => 'closed',
                'ping_status'           => 'closed',
                'post_password'         => '',
                'to_ping'               => '',
                'pinged'                => '',
                'post_parent'           => self::$page_details[$arr_index]['Parent'],
                'menu_order'            => 0,
                'guid'                  => '',
                'import_id'             => 0,
                'context'               => '',
            );  
            return $arr;          
        }

        
        /**
         * Create homepage page-specific properties
         * 
         * @since 1.0.0 
         */
        public static function create_homepage_details_array() {
            $details_all = array();
            //tt-home
            $details = array(
                "Title" => "Time Tracker Home",
                "Parent" => 0,
                "Slug" => "time-tracker",
                "Content" => "[tt_month_summary][tt_year_summary]"
            );
            array_push($details_all, $details);
            self::$page_details = $details_all;
        }


        /**
         * Get shortcode for form
         * 
         * @since 3.0.0
         * 
         * @param string $nm Readable name of form.
         * 
         * @return string Shortcode to display form, depending on which form plugin used, or error notice if no form plugin found.
         */
        private static function get_form_shortcode($nm) {
            if (TT_PLUGIN_FORM_TYPE == "CF7") {
                return "[contact-form-7 id=\"" . tt_get_form_id($nm) . "\" title=\"" . $nm . "\" html_class=\"tt-form\"]";
            }
            elseif (TT_PLUGIN_FORM_TYPE == "WPF") {
                return "[wpforms id=\"" . tt_get_form_id($nm) . "\"]";
            }
            else {
                return "Error: Either Contact Form 7 or WPForms must be activated to use the Time Tracker plugin.";
            }
        }

        /**
         * Create button for exporting pending time
         * 
         * @since 3.0.13
         * 
         * @return string HTML of button to export pending time as csvs.
         */
        private static function get_pending_time_export_button() {
            return "<input type=\"submit\" class=\"button tt-export-pending-time tt-button tt-midpage-button float-right no-border-radius\" name=\"tt-export-pending-time\" value=\"Download Data\" />";
        }

        /**
         * Create button for exporting pending time in Quickbooks import format
         * 
         * @since 3.0.13
         * 
         * @return string HTML of button to export pending time as iif ready for import to QB as invoices
         */
        private static function get_pending_time_export_for_qb_button() {
            return "<input type=\"submit\" class=\"button tt-export-pending-time-for-qb tt-button tt-midpage-button float-right no-border-radius\" name=\"tt_export_pending_time_for_qb\" value=\"Download Quickbooks Import File\" />";
        }
            
            
        /**
         * Create array of pages and page-specific properties
         * 
         * @since 1.0.0
         * 
         * @param int $id Identifies index of page within page array created in create_subpage_details_array.
         * 
         * @return array Multi-dimensional array containing details for creation of each page. 
         */
        public static function create_subpage_details_array($id) {
            $details_all = self::$page_details;
            $parent = $id;
            
            //clients
            $details = array(
                "Title" => "Clients",
                "Parent" => $parent,
                "Slug" => "clients",
                "Content" => "[tt_client_list_table]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //new client
            $details = array(
                "Title" => "New Client",
                "Parent" => $parent,
                "Slug" => "new-client",
                //"Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Client") . "\" title=\"Add New Client\" html_class=\"tt-form\"]",
                "Content" => self::get_form_shortcode("Add New Client"),
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //new project
            $details = array(
                "Title" => "New Project",
                "Parent" => $parent,
                "Slug" => "new-project",
                //"Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Project") . "\" title=\"Add New Project\" html_class=\"tt-form\"]",
                "Content" => self::get_form_shortcode("Add New Project"),
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //new recurring task
            $details = array(
                "Title" => "New Recurring Task",
                "Parent" => $parent,
                "Slug" => "new-recurring-task",
                //"Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Recurring Task") . "\" title=\"Add New Recurring Task\" html_class=\"tt-form\"]",
                "Content" => self::get_form_shortcode("Add New Recurring Task"),
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //new task
            $details = array(
                "Title" => "New Task",
                "Parent" => $parent,
                "Slug" => "new-task",
                //"Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Task") . "\" title=\"Add New Task\" html_class=\"tt-form\"]",
                "Content" => self::get_form_shortcode("Add New Task"),
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //new time entry
            $details = array(
                "Title" => "New Time Entry",
                "Parent" => $parent,
                "Slug" => "new-time-entry",
                //"Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add Time Entry") . "\" title=\"Add Time Entry\" html_class=\"tt-form\"]",
                "Content" => self::get_form_shortcode("Add Time Entry"),
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //open task list
            $details = array(
                "Title" => "Open Task List",
                "Parent" => $parent,
                "Slug" => "open-task-list",
                "Content" => "[tt_open_task_list_table]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //pending time
            $details = array(
                "Title" => "Pending Time",
                "Parent" => $parent,
                "Slug" => "pending-time",
                "Content" => self::get_pending_time_export_button() . self::get_pending_time_export_for_qb_button() . "[tt_pending_time_table]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //project list
            $details = array(
                "Title" => "Project List",
                "Parent" => $parent,
                "Slug" => "projects",
                "Content" => "[tt_project_list_table]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //task detail
            $details = array(
                "Title" => "Task Detail",
                "Parent" => $parent,
                "Slug" => "task-detail",
                "Content" => "[tt_show_task_details]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //task list
            $details = array(
                "Title" => "Task List",
                "Parent" => $parent,
                "Slug" => "task-list",
                "Content" => "[tt_task_list_table]",
				"Paginate" => array(
					"Flag" => true,
					"RecordsPerPage" => 100,
					"PreviousText" => '« Newer',
					"NextText" => 'Older »',
					"TotalRecordsQuery" => 'SELECT COUNT(*) FROM tt_task'
				)
            );
            array_push($details_all, $details);

            //recurring task list
            $details = array(
                "Title" => "Recurring Task List",
                "Parent" => $parent,
                "Slug" => "recurring-task-list",
                "Content" => "[tt_recurring_task_list_table]",
				"Paginate" => array(
					"Flag" => false
				)
            );
            array_push($details_all, $details);

            //time log
            $details = array(
                "Title" => "Time Log",
                "Parent" => $parent,
                "Slug" => "time-log",
                //"Content" => "<div class=\"tt-accordion\">Search</div><div class=\"tt-accordion-panel\">[contact-form-7 id=\"" . tt_get_form_id("Filter Time") . "\" title=\"Filter Time\" html_class=\"filter-time-form\" html_class=\"tt-form\"]</div>[tt_time_log_table type=\'summary\']<br/>[tt_year_summary]<h3>Time Details</h3>[tt_time_log_table type=\'detail\']",
				"Content" => "<div class=\"tt-accordion\">Search</div><div class=\"tt-accordion-panel\">" . self::get_form_shortcode("Filter Time") . "</div>[tt_time_log_table type=\'summary\']<br/>[tt_year_summary]<h3>Time Details</h3>[tt_time_log_table type=\'detail\']",
                "Paginate" => array(
					"Flag" => true,
					"RecordsPerPage" => 100,
					"PreviousText" => '« Newer',
					"NextText" => 'Older »',
					"TotalRecordsQuery" => 'SELECT COUNT(*) FROM tt_time'
				)
            );
            array_push($details_all, $details);

            //delete confirmation page
            $details = array(
                "Title" => "Delete Item",
                "Parent" => $parent,
                "Slug" => "delete-item",
                "Content" => "[tt_delete_confirmation_content]",
                "Paginate" => array(
                    "Flag" => false
                )
            );
            array_push($details_all, $details);

            //time entry detail
            //@since 3.1.0
            $details = array(
                "Title" => "Time Detail",
                "Parent" => $parent,
                "Slug" => "time-detail",
                "Content" => "[tt_show_time_details]",
                "Paginate" => array(
                    "Flag" => false
                )
            );
            
            self::$page_details = $details_all;
            return $details_all;
        }

    }  //close class

}