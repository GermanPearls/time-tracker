<?php
/**
 * Class Time_Tracker_Activator
 *
 * Initial activation of Time Tracker Plugin
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Activator') ) {

    
    /**
     * Class
     * 
     */
    class Time_Tracker_Activator {

        private static $cf7_active = false;
        private static $default_client = null;
        private static $default_task = null;


        public static function activate() {
            self::define_plugin_variables();
            self::cf7_plugin_activated();
            if (self::$cf7_active) {
                include_once(TT_PLUGIN_DIR_INC . 'function-tt-cron-recurring-tasks.php');
                require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-tables.php');
                require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-forms.php');
                require_once(TT_PLUGIN_DIR_INC . 'class-time-tracker-activator-pages.php');
                Time_Tracker_Activator_Tables::setup();
                Time_Tracker_Activator_Forms::setup();
                Time_Tracker_Activator_Pages::setup();
                self::add_default_client();
                self::add_default_task();
				self::set_initial_database_options();
            } else {
                ?>
                <script type="text/javascript">
                window.alert('Time Tracker requires Contact Form 7 plugin to work properly. Please install the Contact Form 7 plugin before activating Time Tracker.');
                </script>
                <?php
                die('Please install the Contact Form 7 plugin before activating Time Tracker.');
            }
        }


        /**
         * Definitions
         * 
         */
        private static function define_plugin_variables() {

        }


        private static function cf7_plugin_activated() {
            if (class_exists('WPCF7')) {
                self::$cf7_active = true;
            }
        }
		
		
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
                //updates
                $optns = get_option('time_tracker_categories');
                if ($optns['default_client'] == null and self::$default_client != null) {
                    $optns['default_client'] = self::$default_client;
                }
                if ($optns['default_task'] == null and self::$default_task != null) {
                    $optns['default_task'] = self::$default_client;
                }
                //added in 2.5.0
                if (!array_key_exists('default_rate', $optns)) {
                    $optns['default_rate'] = null;
                }
                if (!array_key_exists('currency_sign', $optns)) {
                    $optns['currency_sign'] = '$';
                }
                update_option('time_tracker_categories', $optns);
            }
        }
        

        private static function add_default_client() {
            self::get_default_client();
            $i = 0;
            while (self::$default_client == null and $i<5) {
                self::try_to_add_default_client();
                $i = $i + 1;
            }          
        }

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

        private static function try_to_add_default_client() {
            $rst = self::insert_record('tt_client', array('Company'=>'Undefined', 'Billable'=>1, 'Source'=>'Default Client'), array('%s', '%d', '%s'));
            if ($rst > 0) {
                self::get_default_client();
            } 
        }


        private static function add_default_task() {
            self::get_default_task();
            $i = 0;
            while (self::$default_task == null and $i<5) {
                self::try_to_add_default_task();
                $i = $i + 1;
            }
        }
        
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
        
        private static function try_to_add_default_task() {
            $rst = self::insert_record('tt_task', array('TDescription'=>'Undefined', 'ClientID'=> self::$default_client, 'TNotes'=>'Default Task'), array('%s', '%d', '%s'));
            log_tt_misc('rst is ' . $rst);
            if ($rst > 0) {
                self::get_default_task();
                log_tt_misc('rst is ' . $rst . 'and default client is ' . self::$default_client);
            }
        }
        
        private static function insert_record($tbl, $flds, $frmts) {
            global $wpdb;
            $wpdb->insert($tbl, $flds, $frmts);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $wpdb->last_result;
        }

        private static function lookup_record($sql) {
            global $wpdb;
            $rslts = $wpdb->get_results($sql, ARRAY_A);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            return $rslts;
        }	

    }  //close class
 }