<?php
/**
 * Class Time_Tracker_Activator
 *
 * Initial activation of Time Tracker Plugin
 * 
 * @since 1.0.0
 * @since 3.0.12 removed window.alert and die functions as they were causing fatal activation error, replaced with wp_die alert
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Check if class exists
 * 
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_Activator') ) {

    
    /**
     * Class
     * 
     * @since 1.0.0
     */
    class Time_Tracker_Activator {

        private static $default_client = null;
        private static $default_task = null;

        /**
         * Activate plugin
         * 
         * @since 1.0.0
         * @since 3.0.12 removed window.alert and die functions as they were causing fatal activation error, replaced with wp_die alert
         */
        public static function activate() {
            self::define_plugin_variables();
            if (self::confirm_form_dependency_active() == false) {	
				wp_die("TIME TRACKER PLUGIN NOT ACTIVATED<br/><br/>Time Tracker requires either Contact Form 7 or WP Forms plugin be installed and activated. Please activate the Contact Form 7 or WP Forms plugin and try again.<br/><br/>Return to the <a href='" . esc_url( admin_url( "plugins.php" ) ) . "'>plugins page</a>.");
			}
			if (self::check_is_block_theme()) {
				wp_die("TIME TRACKER PLUGIN NOT ACTIVATED<br/><br/>Time Tracker is not yet configured to work with block themes. Please check back for a revision soon.<br/><br/>Return to the <a href='" . esc_url( admin_url( "plugins.php" ) ) . "'>plugins page</a>.");
			}			
			self::setup();                                                                          
        }


        /**
         *  Confirm form dependency active
         *  
         * @since 3.0.10
         * 
         * @return boolean True if Contact Form 7 or WPForms is installed and active, false if neither form dependency found.
		 */
		private static function confirm_form_dependency_active() {
			if (TT_PLUGIN_FORM_TYPE == "CF7") {
				return true;
            } elseif (TT_PLUGIN_FORM_TYPE == "WPF") {
                return true;
            } else {
				return false;
            }
		}
		
        
		/**
		* Check for block theme
        * 
        * @since 3.0.10 Check for users using block theme. Time Tracker template not yet working with block theme and throw error about header/footer not yet configured.
		*
        * @return boolean True if user is using block theme, false if they are not.
		*/
		private static function check_is_block_theme() {
			if (function_exists('wp_is_block_theme')) {
				if (wp_is_block_theme()) {
					return true;
				}
			}
			return false;
		}


        /**
         * Definitions
         * 
         * @since 1.0.0
         */
        private static function define_plugin_variables() {

        }


        /**
         * Setup Time Tracker plugin
         * 
         * @since 1.0.0
         */
        private static function setup() {
            self::log_plugin_installation();
            include_once(TT_PLUGIN_DIR_INC . 'function-tt-cron-recurring-tasks.php');
            require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-tables.php');
            require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-forms.php');
            require_once(TT_PLUGIN_DIR_INC . TT_PLUGIN_FORM_TYPE . '/class-time-tracker-activator-forms-' . strtolower(TT_PLUGIN_FORM_TYPE) . '.php');
            require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-pages.php');
            Time_Tracker_Activator_Tables::setup();
            Time_Tracker_Activator_Forms::setup();
            Time_Tracker_Activator_Pages::setup();
            self::check_plugin_version();
            self::add_default_client();
            self::add_default_task();
            self::set_initial_database_options();
        }


        /**
         * Log Install Time
         *
         * @since 3.0.11 Moved from class-time-tracker.php.
         */
        private static function log_plugin_installation() {
            if (! get_option('time_tracker_install_time')) {
                add_option('time_tracker_install_time', new \DateTime());
            }
        }
        
            
        /**
         * Check Plugin Version
         * 
         * @since 3.0.11 moved from class-time-tracker.php
         */  
        private static function check_plugin_version() {
            $installed_version = get_option('time_tracker_version');
            if ($installed_version) {
                //updates
                if ($installed_version != TIME_TRACKER_VERSION) {
                    include_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-updater.php');
                    $updater = New Time_Tracker_Updater;
                    $new_version = $updater->tt_update_from($installed_version);
                }
            } else {
                //new installations
                add_option('time_tracker_version', TIME_TRACKER_VERSION);
            }
        }
		
		
        /**
         * Set initial database options
         * 
         * @since 2.4.0
         */
        private static function set_initial_database_options() {
            $now = new \DateTime;
            if ( ! (get_option('time_tracker_sql_result')) ) {
                add_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'none', 'file'=>'none', 'function'=>'none'));
            } else {
                update_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'none', 'file'=>'none', 'function'=>'none'));
            }

            //initial setup
            if ( ! (get_option('time_tracker_categories')) ) {
                add_option('time_tracker_categories', array('bill-to-names'=>'Client', 'work-categories'=>'Uncategorized', 'client-categories'=>'Uncategorized', 'client-sub-categories'=>'Uncategorized', 'default-client'=>print_r(self::$default_client), 'default_task'=>print_r(self::$default_task), 'default_rate'=>null));
            } else {
                $optns = get_option('time_tracker_categories');
                //if it is there but blank add defaults
                if ($optns == null || $optns == "") {
                    add_option('time_tracker_categories', array('bill-to-names'=>'Client', 'work-categories'=>'Uncategorized', 'client-categories'=>'Uncategorized', 'client-sub-categories'=>'Uncategorized', 'default-client'=>print_r(self::$default_client), 'default_task'=>print_r(self::$default_task), 'default_rate'=>null));
                } else {
                    //add default client
                    if (array_key_exists("default_client", $optns)) {
                        if ($optns['default_client'] == null and self::$default_client != null) {
                            $optns['default_client'] = self::$default_client;
                        }
                    } else {
                        if (self::$default_client != null) {
                            $optns['default_client'] = self::$default_client;
                        }
                    }
                    //add default task
                    if (array_key_exists("default_task", $optns)) {
                        if ($optns['default_task'] == null and self::$default_task != null) {
                            $optns['default_task'] = self::$default_task;
                        }
                    } else {
                        if (self::$default_task != null) {
                            $optns['default_task'] = self::$default_task;
                        }                        
                    }
                    //added in 2.5.0 - add default rate
                    if (!array_key_exists('default_rate', $optns)) {
                        $optns['default_rate'] = null;
                    }
                    //add currency sign
                    if (!array_key_exists('currency_sign', $optns)) {
                        $optns['currency_sign'] = '$';
                    }
                    update_option('time_tracker_categories', $optns);
                }                
            }
        }
        

        /**
         * Add default client to databsae
         * 
         * @since 2.4.0
         */
        private static function add_default_client() {
            self::get_default_client();
            $i = 0;
            while (self::$default_client == null and $i<5) {
                self::try_to_add_default_client();
                $i = $i + 1;
            }          
        }

        /**
         * Get default client from database
         * 
         * @since 2.4.0
         */
        private static function get_default_client() {
            $client_lookup = self::lookup_record("SELECT ClientID FROM tt_client WHERE Company='Undefined'");
            if ($client_lookup) {
                if (array_key_exists("ClientID", $client_lookup)) {
                    self::$default_client = $client_lookup["ClientID"];
                } else {
                    foreach ($client_lookup as $rslt) {
                        foreach ($rslt as $col=>$val) {
                            if ($col == "ClientID") {
                                self::$default_client = $val;
                            }
                        }
                    }
                }
            }
        }

        /**
         * Attempt to add default client
         * 
         * @since 2.4.0
         */
        private static function try_to_add_default_client() {
            $rst = self::insert_record('tt_client', array('Company'=>'Undefined', 'Billable'=>1, 'Source'=>'Default Client'), array('%s', '%d', '%s'));
            if ($rst > 0) {
                self::get_default_client();
            } 
        }


        /**
         * Add default task
         * 
         * @since 2.4.0
         */
        private static function add_default_task() {
            self::get_default_task();
            $i = 0;
            while (self::$default_task == null and $i<5) {
                self::try_to_add_default_task();
                $i = $i + 1;
            }
        }
        
        /**
         * Get default task from database
         * 
         * @since 2.4.0
         */
        private static function get_default_task() {
            $task_lookup = self::lookup_record("SELECT TaskID FROM tt_task WHERE TDescription='Undefined'");
            if ($task_lookup) {
                if (array_key_exists("TaskID", $task_lookup)) {
                    self::$default_task = $task_lookup["TaskID"];
                } else {
                    foreach ($task_lookup as $rslt) {
                        foreach ($rslt as $col=>$val) {
                            if ($col == "TaskID") {
                                self::$default_task = $val;
                            }
                        }
                    }
                }
            }
        }
        

        /**
         * Attempt to add default task to database
         * 
         * @since 2.4.0
         */
        private static function try_to_add_default_task() {
            $rst = self::insert_record('tt_task', array('TDescription'=>'Undefined', 'ClientID'=> self::$default_client, 'TNotes'=>'Default Task'), array('%s', '%d', '%s'));
            log_tt_misc('rst is ' . var_export($rst, true));
            if ($rst > 0) {
                self::get_default_task();
                log_tt_misc('rst is ' . var_export($rst, true) . 'and default client is ' . self::$default_client);
            }
        }
        

        /**
         * Insert record into database
         * TODO: Move this function outside this class as a general plugin function
         * 
         * @since 2.4.0
         */
        private static function insert_record($tbl, $flds, $frmts) {
            global $wpdb;
            $wpdb->insert($tbl, $flds, $frmts);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $wpdb->last_result;
        }


        /**
         * Lookup record from database
         * TODO: Move this function outside this class as a general plugin function
         * 
         * @since 2.4.0
         */
        private static function lookup_record($sql) {
            global $wpdb;
            $rslts = $wpdb->get_results($sql, ARRAY_A);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $rslts;
        }	

    }  //close class
 }