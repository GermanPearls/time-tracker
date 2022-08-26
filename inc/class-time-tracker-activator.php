<?php
/**
 * Class Time_Tracker_ACtivator
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

    //global $TT_DB_NAME;
    
    /**
     * Class
     * 
     */
    class Time_Tracker_Activator {

        private static $cf7_active = false;
        private $default_client = "";
        private $default_task = "";


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
            //Time Tracker Database Name
            //if (! defined('TT_DB_NAME')) {
                //define('TT_DB_NAME', DB_NAME . "_tt"); //time tracker database name
            //}
        }


        private static function cf7_plugin_activated() {
            //if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
            if (class_exists('WPCF7')) {
                //plugin is activated
                self::$cf7_active = true;
            }
        }
		
		
		private static function set_initial_database_options() {
			$now = new \DateTime;
            if ( ! (get_option('time-tracker-sql-result')) ) {
			    add_option('time-tracker-sql-result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'none', 'file'=>'none', 'function'=>'none'));
            } else {
                update_option('time-tracker-sql-result', array('result'=>'success','updated'=>$now->format('m-d-Y g:i A'),'error'=>'none', 'file'=>'none', 'function'=>'none'));
            }

            if ( ! (get_option('time-tracker')) ) {
                add_option('time-tracker', array('bill-to-names'=>'Client', 'work-categories'=>'Uncategorized', 'client-categories'=>'Uncategorized', 'client-sub-categories'=>'Uncategorized', 'default-client'=>$this->default_client == "" ? null : $this->default_client, 'default_task'=>$this->default_task == "" ? null : $this->default_task));
            }
		}


        private static function add_default_task() {
            $task_lookup = self::lookup_record("SELECT tt_task.TaskID FROM tt_task WHERE tt_task.TDescription = 'Undefined'");
            if ($task_lookup[0] > 0 and ($task_lookup[1]->tt_task.TaskID == 0 or $task_lookup[1]->tt_task.TaskID == 9999)) {
                $this->default_task = $task_lookup[1]->tt_task.TaskID;
            } else {
                self::try_to_add_default_task(0);
                if ($this->default_task == "" or $this->default_task = null) {
                    self::try_to_add_default_task(9999);
                } else {
                    //we still do not have a default client!
                }            
            }
        }

        private static function add_default_client() {
            $possible_default_ids = array(0, 9999, 999999);
            $client_lookup = self::lookup_record("SELECT ClientID FROM tt_client WHERE Company='Undefined'");
            if ($client_lookup[0] > 0 and in_array($client_lookup[1]->tt_client.ClientID, $possible_default_ids)) {
                $this->default_client = $client_lookup[1]->tt_client.ClientID;
            } else {
                $i = 0;
                while ($this->default_client == "" and $i >= count($possible_default_ids)) {
                    self::try_to_add_default_client($possible_default_ids($i));
                    $i = $i + 1;
                }          
            }
        }

        private static function try_to_add_default_task($num) {
            $task_lookup = self::lookup_record("SELECT tt_task.TDescription FROM tt_task WHERE tt_task.TaskID=" . intval($num));
            if ($task_lookup[0] == 0) {
                $rst = self::insert_record('tt_task', array('TaskID'=>$num, 'TDescription'=>'Undefined', 'ClientID'=> $this->default_client, 'TNotes'=>'Default Task'), array('%d', '%s', '%d', '%s'));
                if ($rst > 0) {
                    $this->default_task = $num;
                }
            } 
        }
        
        private static function try_to_add_default_client($num) {
            $client_lookup = self::lookup_record("SELECT tt_client.Company FROM tt_client WHERE tt_client.ClientID=" . intval($num));
            if ($client_lookup[0] == 0) {
                $rst = self::insert_record('tt_client', array('ClientID'=>$num, 'Company'=>'Undefined', 'Billable'=>1, 'Source'=>'Default Client'), array('%d', '%s', '%d', '%s'));
                if ($rst > 0) {
                    $this->default_client = $num;
                }
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
            $rslts = $wpdb->get_results($sql);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            if ($rslts) {
                return array($rslts->num_rows, $rslts[0]);
            } else {
                return array(0,null);
            }
        }



		

    }  //close class
 }  //close if class exists
