<?php
/**
 * Class Time_Tracker_Activator_Pages
 *
 * Initial activation of Time Tracker Plugin - CREATE FRONT END PAGES
 * 
 * @since 1.0
 * 
 */


/**
 * If class doesn't already exist
 * 
 */       
if ( ! class_exists('Time_Tracker_Activator_Pages') ) {

    /**
     * Class
     * 
     */ 
    class Time_Tracker_Activator_Pages {

        private static $page_details = array();

        /**
         * Constructor
         * 
         */ 
        public function __construct() {
            //$page_details = self::create_page_details_array();
            //self::create_pages();
        }


        /**
         * Setup
         * 
         */ 
        public static function setup() {
            //self::create_page_details_array();
            //create_homepage_details_array();
            self::create_pages();
        }


        /**
         * Add Pages to WP
         * 
         */
        private static function create_pages() {
            $i = 0;
            self::create_homepage_details_array();
            
            //check if page exists already
            $page_exists = tt_get_page_id(self::get_page_details(0)['post_title']);
            //if page doesn't exist
            if (empty($page_exists) or $page_exists == null) {
                $homepage_id = wp_insert_post(self::get_page_details(0));
            
            //pages are changed to draft on plugin deactivation
            } elseif (get_post_status($page_exists) == 'draft') {
                $homepage_id = $page_exists;
                wp_update_post(array(
                    'ID' => $page_exists,
                    'post_status' => 'private'
                ));
            
            //page exists and is not in draft status
            } else {
                $homepage_id = $page_exists;
            }

            self::create_subpage_details_array($homepage_id);
            $num_of_pages = count(self::$page_details);
            for ($i = 1; $i < $num_of_pages; $i++) {
                $page_exists = tt_get_page_id(self::get_page_details($i)['post_title']);
                //if page doesn't exist
                if (empty($page_exists) or ($page_exists==null)) {
                    wp_insert_post( self::get_page_details($i) );
                
                //pages are changed to draft on plugin deactivation
                } elseif (get_post_status($page_exists) == 'draft') {
                    wp_update_post(array(
                        'ID' => $page_exists,
                        'post_status' => 'private'
                    ));
                }
            }
        }

        
        /**
         * Get all arguments to add page to WP
         * 
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
         */
        private static function create_homepage_details_array() {
            $details_all = array();
            //tt-home
            $details = array(
                "Title" => "Time Tracker Home",
                "Parent" => 0,
                "Slug" => "time-tracker",
                "Content" => "<h2>Current Month</h2>[tt_month_summary]<h2>Current Year Overview</h2>[tt_year_summary]"
            );
            array_push($details_all, $details);
            self::$page_details = $details_all;
        }
            
            
        /**
         * Create array of pages and page-specific properties
         * 
         */
        public static function create_subpage_details_array($id) {
            $details_all = self::$page_details;
            
            $parent = $id;
            
            //clients
            $details = array(
                "Title" => "Clients",
                "Parent" => $parent,
                "Slug" => "clients",
                "Content" => "[tt_client_list_table]"
            );
            array_push($details_all, $details);

            //new client
            $details = array(
                "Title" => "New Client",
                "Parent" => $parent,
                "Slug" => "new-client",
                "Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Client") . "\" title=\"Add New Client\"]"
            );
            array_push($details_all, $details);

            //new project
            $details = array(
                "Title" => "New Project",
                "Parent" => $parent,
                "Slug" => "new-project",
                "Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Project") . "\" title=\"Add New Project\"]"
            );
            array_push($details_all, $details);

            //new recurring task
            $details = array(
                "Title" => "New Recurring Task",
                "Parent" => $parent,
                "Slug" => "new-recurring-task",
                "Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Recurring Task") . "\" title=\"Add New Recurring Task\"]"
            );
            array_push($details_all, $details);

            //new task
            $details = array(
                "Title" => "New Task",
                "Parent" => $parent,
                "Slug" => "new-task",
                "Content" => "[contact-form-7 id=\"" . tt_get_form_id("Add New Task") . "\" title=\"Add New Task\"]"
            );
            array_push($details_all, $details);

            //new time entry
            $details = array(
                "Title" => "New Time Entry",
                "Parent" => $parent,
                "Slug" => "new-time-entry",
                "Content" => "<button class=\"end-work-timer float-right no-border-radius\" onclick=\"update_end_timer()\">Set End Time</button>[contact-form-7 id=\"" . tt_get_form_id("Add Time Entry") . "\" title=\"Add Time Entry\"]"
            );
            array_push($details_all, $details);

            //open task list
            $details = array(
                "Title" => "Open Task List",
                "Parent" => $parent,
                "Slug" => "open-task-list",
                "Content" => "[tt_open_task_list_table]"
            );
            array_push($details_all, $details);

            //pending time
            $details = array(
                "Title" => "Pending Time",
                "Parent" => $parent,
                "Slug" => "pending-time",
                "Content" => "[tt_pending_time_table]"
            );
            array_push($details_all, $details);

            //project list
            $details = array(
                "Title" => "Project List",
                "Parent" => $parent,
                "Slug" => "projects",
                "Content" => "[tt_project_list_table]"
            );
            array_push($details_all, $details);

            //task detail
            $details = array(
                "Title" => "Task Detail",
                "Parent" => $parent,
                "Slug" => "task-detail",
                "Content" => "[tt_show_task_details]"
            );
            array_push($details_all, $details);

            //task list
            $details = array(
                "Title" => "Task List",
                "Parent" => $parent,
                "Slug" => "task-list",
                "Content" => "[tt_task_list_table]"
            );
            array_push($details_all, $details);

            //time log
            $details = array(
                "Title" => "Time Log",
                "Parent" => $parent,
                "Slug" => "time-log",
                "Content" => "[contact-form-7 id=\"" . tt_get_form_id("Filter Time") . "\" title=\"Filter Time\" html_class=\"filter-time-form\"][tt_time_log_table]"
            );
            array_push($details_all, $details);
            
            self::$page_details = $details_all;
            return $details_all;
        }

    }  //close class

}  //close if class exists