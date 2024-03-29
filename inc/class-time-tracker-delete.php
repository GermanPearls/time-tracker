<?php
/**
 * Class Time_Tracker_Deletor
 *
 * Deactivation of Time Tracker Plugin
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

use Logically_Tech\Time_Tracker\Admin\tt_export_data_function as tt_export_data_function;


/**
 * If class doesn't exist already
 * 
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_Deletor') ) {

    /**
     * Class
     * 
     * @since 1.0.0
     */
    class Time_Tracker_Deletor {
 
        /**
         * Delete Main Function
         * 
         * @since 1.0.0
         */
        public static function delete_all() {
            self::define_dependents();
            self::backup_everything();
            self::delete_tables();
            self::delete_pages();
            self::delete_forms();
        }


        /**
         * Warn user
         * 
         * @since 1.0.0
         */
        public static function send_deletion_warning() {
            //WARNING: Deleting!
        }
        
        
        
        /**
         * Definitions and Dependencies
         * 
         * @since 1.0.0
         * @since 3.0.12 include form subclass dependency to fix fatal deletion error
         */
        public static function define_dependents() {
            require_once 'class-time-tracker-activator-tables.php';
            require_once 'class-time-tracker-activator-pages.php';
            require_once 'class-time-tracker-activator-forms.php';
            require_once TT_PLUGIN_FORM_TYPE . '/class-time-tracker-activator-forms-' . strtolower(TT_PLUGIN_FORM_TYPE) . '.php';
            require_once __DIR__ . '/../admin/function-tt-export-tables.php';
        }


        /**
         * Backup before deleting...just in case.
         * 
         * @since 1.0.0
         */
        public static function backup_everything() {
            \Logically_Tech\Time_Tracker\Admin\tt_export_data_function();
        }


        /**
         * Delete tables (ie: user data). From button on admin screen.
         * 
         * @since 1.0.0
         */
        public static function delete_tables_only() {
            self::define_dependents();
            self::backup_everything();
            self::delete_tables(); //done with wpdb tables
        }
		
		
		/**
         * Delete all tables.
         * 
         * @since 1.0.0
         */
        public static function delete_tables() {
            self::remove_foreign_keys_from_tables();
            global $wpdb;
            $tt_tables = Time_Tracker_Activator_Tables::get_table_list();
            $tt_tables_delete_order = array_reverse($tt_tables);
            foreach ($tt_tables_delete_order as $tt_table) {
                $table_exists = $wpdb->query($wpdb->prepare('SHOW TABLES LIKE %s', $tt_table));
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                if ($table_exists) {
                    $sql = "DROP TABLE " . $tt_table;
                    $wpdb->query( $sql );
                    catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                }
            }
        }


        /**
         * Remove Foreign Keys in preparation for deleting tables.
         * 
         * @since 1.0.0
         */
        public static function remove_foreign_keys_from_tables() {
            global $wpdb;
            //require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $foreign_keys = array(
                "tt_time" => array("FK_TimeTableToTaskTable", "FK_TimeTableToClientTable"),
                "tt_recurring_task" => array("FK_RecurringTaskTableToProjectTable", "FK_RecurringTaskTableToClientTable"),
                "tt_task" => array("FK_TaskTableToRecurringTaskTable", "FK_TaskTableToProjectTable", "FK_TaskTableToClientTable"),
                "tt_project" => array("FK_ProjectTableToClientTable") 
            );
            foreach($foreign_keys as $table => $keys) {
                $table_exists = $wpdb->query($wpdb->prepare('SHOW TABLES LIKE %s', $table));
                catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                if ($table_exists) {
                    foreach($keys as $key) {
                        $altertable = "ALTER TABLE " . $table;
                        $altertable .= " DROP FOREIGN KEY IF EXISTS " . $key;
                        //dbDelta($removeFK);
                        $wpdb->query($altertable);
                        catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
                    }
                }
            }
        }
        
        
        /**
         * Delete all pages.
         * 
         * @since 1.0.0
         */
        public static function delete_pages() {
            $tt_pages = Time_Tracker_Activator_Pages::create_subpage_details_array(0);
            foreach ($tt_pages as $tt_page) {
                self::delete_tt_subpage($tt_page['Slug']);
            }
            self::delete_tt_main_page();
        }


        /**
         * Delete one page.
         * 
         * @since 1.0.0
         * 
         * @param string $pagename Friendly name of page to be deleted.
         * 
         * @return int The Wordpress post ID of the page deleted, or 0 on error.
         */
        private static function delete_tt_subpage($pagename) {
            $post = get_page_by_path('time-tracker/' . $pagename, ARRAY_A, 'page');
            if ($post) {
                return wp_delete_post($post['ID']);
            }
        }


        /**
         * Delete main Time Tracker page.
         * 
         * @since 1.0.0
         * 
         * @return int The post ID of the main homepage of the Time Tracker plugin, or 0 on error.
         */
        private static function delete_tt_main_page() {
            $post = get_page_by_path('time-tracker', ARRAY_A, 'page');
            if ($post) {
                return wp_delete_post($post['ID']);
            }
        }


        /**
         * Delete all forms.
         * 
         * @since 1.0.0
         */
        public static function delete_forms() {
            $tt_forms = Time_Tracker_Activator_Forms::create_form_details_array();
            foreach ($tt_forms as $tt_form) {
                //delete form
                $form = tt_get_form_id($tt_form['Title']);
                if ($form) {
                    self::delete_form($form);
                }
            }
        }


        /**
         * Delete one form.
         * 
         * @since 1.0.0
         * 
         * @param int $post_id The Wordpress post ID of the form to be deleted.
         * 
         * @return int The post ID of the form deleted, or 0 on error.
         */
        private static function delete_form($post_id) {
            return wp_delete_post($post_id);
        }

    }  //close class
 }