<?php
/**
 * Class Time_Tracker_Deativator
 *
 * Deactivation of Time Tracker Plugin
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * If class doesn't exist already
 * 
 */
if ( ! class_exists('Time_Tracker_Deactivator') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */
    class Time_Tracker_Deactivator {
 
        /**
         * Deactivation main function
         * 
         * @since 1.0.0
         */
        public static function deactivate() {
            //self::send_deletion_warning();  WON'T NEED TO DO THIS, ONLY DURING DELETION
            self::define_plugin_variables();
            //self::delete_tables();  DON'T REMOVE TABLES, ONLY DO THIS DURING PLUGIN DELETION
            self::deactivate_crons();
            //self::deactivate_pages();
            self::delete_pages(); 
            //self::delete_forms();   DON'T REMOVE FORMS, ONLY DO THIS DURING PLUGIN DELETION
        }


        /**
         * Warn user
         * 
         * @since 1.0.0
         */
        public static function send_deletion_warning() {
            //WARNING: Deactivating 
        }
        
        
        
        /**
         * Definitions
         * 
         * @since 1.0.0
         */
        public static function define_plugin_variables() {
            require_once 'class-time-tracker-activator-pages.php';
        }


        /**
         * Deactivate Crons
         * 
         * @since 1.0.0
         */
        public static function deactivate_crons() {
            wp_clear_scheduled_hook( 'tt_recurring_task_check' );
        }
        
        
        /**
         * Delete tables
         * 
         * @since 1.0.0
         */
        public static function deactivate_tables() {
            //don't do anything with tables on deactivation
        }


        /**
		 * Get Page List in Deletion Order
		 *
         * @since 3.0.10
         * 
         * @return array Array of pages to delete, in deletion order to avoid any dependency errors.
         */
		public static function get_page_list_in_deletion_order() {
            $tt_pages = Time_Tracker_Activator_Pages::create_subpage_details_array(1);
            return array_reverse($tt_pages);			
		}
		
		
		/**
         * Delete all pages.
         * 
         * @since 3.0.10
         */
        public static function delete_pages() {
			$tt_pages_delete_order = self::get_page_list_in_deletion_order();
            foreach ($tt_pages_delete_order as $tt_page) {
				self::delete_page($tt_page['Title']);
            }
			$tt_homepage = Time_Tracker_Activator_Pages::create_homepage_details_array();
			self::delete_page($tt_homepage['Title']);
        }


		/**
		 * Delete one page.
		 *
         * @since 3.0.10
         * 
         * @param string $pagename Friendly name of page to delete.
         */
		private static function delete_page($pagename) {
			$post_id = tt_get_page_id($pagename);
			if ($post_id > 0) {
				$result = wp_delete_post($post_id);
			}			
		}


        /**
         * Deactivate all pages.
         * 
         * @since 1.0.0
         * @since 3.1.0 Deprecated? Pages are being deleted on deactivation instead of moving to draft status.
         */
        public static function deactivate_pages() {
            $tt_pages_delete_order = self::get_page_list_in_deletion_order();
            foreach ($tt_pages_delete_order as $tt_page) {
                //self::change_page_to_draft($tt_page['Slug']);
                self::delete_page($tt_page['Slug']);
            }
            $tt_homepage = Time_Tracker_Activator_Pages::create_homepage_details_array(); 
            //self::change_page_to_draft($tt_homepage['Slug']);
            self::delete_page($tt_homepage['Slug']);
        }


        /**
         * Deactivate one page.
         * 
         * @since 1.0.0
         * 
         * @return int Page ID of page deleted, or 0 on error.
         */
        private static function change_page_to_draft($pagename) {
            $post_id = get_page_by_path('time-tracker/' . $pagename, ARRAY_A, 'page');
            if ($post_id) {    
                $post_id['post_status'] = 'draft';
                return wp_update_post($post_id);
            }
        }


        /**
         * Delete forms
         * 
         * @since 1.0.0
         */
        public static function deactivate_forms() {
            //don't do anything with forms on deactivation
        }

    }  //close class
 }