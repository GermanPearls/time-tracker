<?php
/**
 * Class Time_Tracker_Activator_Forms_WPF
 *
 * Initial activation of Time Tracker Plugin - CREATE FRONT END FORMS
 * Specific to WPF installations
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\WPF;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Activator_Forms_WPF') ) {

    class Time_Tracker_Activator_Forms_WPF {

        public static $form_content = array();
        
        /**
         * Constructor
         * 
         */
        public function __construct() {
        }


        /**
         * Setup
         * 
         */
        public static function setup() {
            self::create_form_content_array();
        }


        /**
         * Add Post Meta - specific to WPForms
         * 
         */
        public static function add_form_post_meta($post_id, $i) {
            //wpforms does not create any post meta that we can see
        }


        /**
         * Update form id in post content (after inserting form we now have post id)
         * 
         */
        public static function update_form_id_in_database($dat, $id) {
            $form_content = self::decode_form_content($dat["post_content"]);
            $form_content["id"] = $id;
            $pst = array(
                'ID' => $id,
                'post_content' => self::encode_form_content($form_content)
            );
            $pst_id = wp_update_post($pst);
            if (! $pst_id == $id) {
                //something went wrong
                //TODO log error
            }
        }


        /**
         * Encode content for adding into database
         * 
         */
        public static function encode_form_content($dat) {
            if ( $dat == "" || $dat == null ) {
                return;
            }
            return wp_slash( wp_json_encode($dat) );
        }


        /**
         * Decode content from the database
         * 
         */
        public static function decode_form_content($dat) {
            if ( $dat == "" || $dat == null ) {
                return;
            }
            return json_decode( wp_unslash($dat), true );
        }


        /**
         * Create content details array
         * 
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


        //create_field_text($lbl, $desc, $reqd, $sz, $plchldr, $lmtd, $lmt, $lmtmd, $dflt, $inptmsk, $css)
        //create_field_textarea($lbl, $desc, $reqd, $sz, $plchldr, $lmtd, $lmt, $lmtmd, $dflt, $css)
        //create_field_select($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $inptmsk, $css)
        //create_field_number($lbl, $desc, $sz, $plchldr, $dflt, $css)
        //create_field_date($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $css)
        //create_field_datetime($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $css)

        /**
         * Create form content - New Client Form
         * TODO: How to add custom id labels to fields for js functions?!
         * 
         */
        public static function get_form_content_new_client() {
            $frm = self::create_form_base("Add New Client");
            $frm = self::add_field_to_form($frm, self::create_field_text("Company", "", "1", "medium", "", "1", "100", "characters", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Bill To", "", "1", "medium", "", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_number("Billing Rate", "", "small", "", strval(\Logically_Tech\Time_Tracker\Inc\tt_get_default_billing_rate()), "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Client Source", "", "1", "medium", "", "", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Client Source Details", "", "1", "medium", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Contact Name", "", "0", "medium", "", "1", "100", "characters", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Contact Email", "", "0", "medium", "", "1", "100", "characters", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Contact Telephone", "", "0", "medium", "", "1", "100", "characters", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_textarea("Comments", "", "0", "medium", "", "1", "1000", "characters", "", ""));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }


        /**
         * Create form content - New Project
         * 
         */
        public static function get_form_content_new_project() {
            $frm = self::create_form_base("Add New Project");
            $frm = self::add_field_to_form($frm, self::create_field_text("Project Name", "", "1", "medium", "", "1", "100", "characters", "", "", ""));
            $frm = self::add_field_to_form($frm, self::create_field_select("Client", "", "1", "medium", "", "{query_var key=\"client-name\"}", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Project Category", "", "0", "medium", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_number("Time Estimate", "", "small", "", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_date("Due Date", "", "1", "medium", "", date("m\/d\/Y"), "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_textarea("Project Details", "", "0", "medium", "", "1", "500", "characters", "", ""));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }


        /**
         * Create form content - New Recurring Task
         * 
         */
        public static function get_form_content_new_recurring_task() {
            $frm = self::create_form_base("Add New Recurring Task");
            $frm = self::add_field_to_form($frm, self::create_field_text("Task Name", "", "1", "medium", "", "1", "1500", "characters", "", "", "wpforms-two-thirds wpforms-first"));
            
            $fld_freq = self::create_field_select("Frequency", "", "1", "small", "", "", "", "wpforms-one-third");
            $fld_freq = self::add_dynamic_select_choices($fld_freq, '', ["Weekly", "Monthly", "Yearly"]);
            $frm = self::add_field_to_form($frm, $fld_freq);
            
            $frm = self::add_field_to_form($frm, self::create_field_select("Client", "", "1", "medium", "", "{query_var key=\"client-name\"}", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Project", "", "0", "medium", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Category", "", "0", "medium", "", "", "", "wpforms-one-third wpforms-first"));         
            $frm = self::add_field_to_form($frm, self::create_field_text("Time Estimate (hours)", "", "0", "small", "", "", "", "", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_date("End Repeat", "", "", "medium", "", "12/31/2099", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_textarea("Task Notes", "", "0", "medium", "", "1", "500", "characters", "", ""));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }


        /**
         * Create form content - New Task
         * 
         */
        public static function get_form_content_new_task() {
            $frm = self::create_form_base("Add New Task");
            $frm = self::add_field_to_form($frm, self::create_field_text("Task Description", "", "1", "medium", "", "1", "500", "characters", "", "", "wpforms-two-thirds wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_date("Due Date", "", "0", "medium", "", date("m\/d\/Y"), "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Client", "", "1", "medium", "", "{query_var key=\"client-name\"}", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Project", "", "0", "medium", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Category", "", "0", "medium", "", "", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Time Estimate (hrs)", "", "", "small", "", "", "", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_textarea("Notes", "", "0", "medium", "", "", "", "", "", ""));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }


        /**
         * Create form content - New Time Entry
         * 
         */
        public static function get_form_content_add_time_entry() {
            $frm = self::create_form_base("Add New Time Entry");
            $frm = self::add_field_to_form($frm, self::create_field_select("Client", "", "1", "medium", "", "{query_var key=\"client-name\"}", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Ticket", "", "1", "medium", "", "", "", "wpforms-one-half"));
            $frm = self::add_field_to_form($frm, self::create_field_datetime("Start Time", "", "1", "medium", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_datetime("End Time", "", "1", "medium", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_select("New Task Status", "", "0", "medium", "", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_textarea("Time Notes", "", "1", "medium", "", "1", "1999", "characters", "", ""));
            $frm = self::add_field_to_form($frm, self::create_field_text("Invoiced", "", "0", "small", "", "", "", "", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Invoice Number", "", "", "medium", "", "", "", "", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_number("Invoiced Time", "", "small", "", "", "wpforms-one-third"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Invoice Notes", "", "0", "medium", "", "", "", "", "", "", "wpforms-one-half wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Follow Up", "Create New Task", "0", "medium", "", "1", "500", "characters", "", "", "wpforms-one-half"));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }


        /**
         * Create form content - Time Entry Filter
         * 
         */
        public static function get_form_content_filter_time() {
            $frm = self::create_form_base("Time Entry Filter", "Search", "Searching...");
            $frm = self::add_field_to_form($frm, self::create_field_date("First Date", "", "0", "medium", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Client", "", "0", "medium", "", "{query_var key=\"client-name\"}", "", "wpforms-two-thirds"));
            $frm = self::add_field_to_form($frm, self::create_field_date("Last Date", "", "0", "medium", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Project", "", "0", "medium", "", "", "", "wpforms-two-thirds"));
            $frm = self::add_field_to_form($frm, self::create_field_select("Task", "", "0", "medium", "", "", "", "wpforms-one-third wpforms-first"));
            $frm = self::add_field_to_form($frm, self::create_field_text("Notes", "", "0", "medium", "", "", "", "", "", "", "wpforms-two-thirds"));
            $frm = self::update_field_count($frm);
            return self::encode_form_content($frm);
        }
      
        
        /**
         * Create form for WPForms
         * 
         */
        private static function create_form_base($ttl, $btn="Save", $txt="Saving...") {
            $frm = array(
                "fields" => array(),
                "id" => "0",              //will be post id - have to get later when adding to db
                "field_id" => "0",
                "settings" => array(
                    "form_title" => $ttl,
                    "form_desc" => "",
                    "submit_text" => $btn,
                    "submit_text_processing" => $txt,
                    "form_class" => "tt-form",
                    "submit_class" => "",
                    "dynamic_population" => "1",
                    "ajax_submit" => "1",
                    "notification_enable" => "0",
                    "notifications" => array(),
                    "confirmations" => array(
                        "1" => array(
                            "type" => "message",
                            "message" => "<p>Saved.</p>",
                            "message_scroll" => "0",
                            "page" => "2",
                            "redirect" => TT_HOME
                        )
                    ),
                    "antispam" => "0",
                    "form_tags" => array()
                ),
                "meta" => array(
                    "template" => "blank"
                )
            );
            return $frm;
        }


        /**
         * Form settings are saved with a field_id of max(fields) + 1
         * 
         */
        private static function update_field_count(&$frm) {
            if ($frm) {
                if (array_key_exists("fields", $frm)) {
                    if (is_array($frm["fields"])) {
                        $frm["field_id"] = count($frm["fields"]) + 1;
                    }
                }
            }
            return $frm;
        }
        
        
        /**
         * Create field for WPForms
         * 
         */
        private static function create_field_base($typ, $lbl, $desc, $sz, $plchldr, $dflt, $css) {
            $fld = array(
                "id" => "",
                "type" => $typ,
                "label" => $lbl,
                "description" => $desc,
                "size" => $sz,
                "placeholder" => $plchldr,
                "default_value" => $dflt,
                "css" => $css
            );
            return $fld;
        }


        /**
         * Create choice for WPForms select field
         * 
         */
        private static function create_select_choice($lbl, $val, $img, $icn, $icnstyl) {
            $chc = array(
                "label" => $lbl,
                "value" => $val,
                "image" => $img,
                "icon" => $icn,
                "icon_style" => $icnstyl
            );
            return $chc;
        }

        
        /**
         * Create number field for WPForms
         * 
         */
        private static function create_field_number($lbl, $desc, $sz, $plchldr, $dflt, $css) {
            $fld = self::create_field_base("number", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            return $fld;
        }

        
        /**
         * Create date field for WPForms
         * 
         */
        private static function create_field_date($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $css) {
            //WPForms date-time field only available in WPForms Lite
            $fld = self::create_field_base("text", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            $fld["input_mask"] = "[9]9/[9]9/[9][9]99";
            return $fld;
        }
        
        
        /**
         * Create date-time field for WPForms
         * 
         */
        private static function create_field_datetime($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $css) {
            //WPForms date-time field only available in WPForms Lite
            $fld = self::create_field_base("text", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            $fld["input_mask"] = "9[9]/9[9]/99[9][9] 9[9]:99 \(A|a|P|p)\(M|m)";
            return $fld;
        }
        
        
        /**
         * Create text field for WPForms
         * 
         */        
        private static function create_field_text($lbl, $desc, $reqd, $sz, $plchldr, $lmtd, $lmt, $lmtmd, $dflt, $inptmsk, $css) {
            $fld = self::create_field_base("text", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            $fld["required"] = $reqd;
            $fld["input_mask"] = $inptmsk;
            //if no limit set, do not add to field, this may default to on with limit_count set to 1 and limit_mode set to words
            if ($lmtd !== "") {
                $fld["limit_enabled"] = $lmtd;
            }
            $fld["limit_count"] = $lmt;
            $fld["limit_mode"] = $lmtmd;
            return $fld;
        }

        
        /**
         * Create text area (long text) field for WPForms
         * 
         */        
        private static function create_field_textarea($lbl, $desc, $reqd, $sz, $plchldr, $lmtd, $lmt, $lmtmd, $dflt, $css) {
            $fld = self::create_field_base("textarea", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            $fld["required"] = $reqd;
            //if no limit set, do not add to field, this may default to on with limit_count set to 1 and limit_mode set to words
            if ($lmtd !== "") {
                $fld["limit_enabled"] = $lmtd;
            }
            $fld["limit_count"] = $lmt;
            $fld["limit_mode"] = $lmtmd;
            return $fld;
        }

        
        /**
         * Create select (choice, dropdown) field for WPForms
         * 
         */
        private static function create_field_select($lbl, $desc, $reqd, $sz, $plchldr, $dflt, $inptmsk, $css) {
            $fld = self::create_field_base("select", $lbl, $desc, $sz, $plchldr, $dflt, $css);
            $fld["style"] = "classic";
            return $fld;
        }

               
        /**
         * Make WPForms select field dynamic by custom post type
         * 
         */
        private static function add_dynamic_select_choices(&$fld, $psttyp="", $tax="", $optns=null) {
            if ($psttyp != '') {
                $fld["dynamic_choices"] = "post_type";
                $fld["dynamic_post_type"] = $psttyp;
            } elseif ($tax != '') {
                $fld["dynamic_choies"] = 'taxonomy';
                $fld["dynamic_taxonomy"] = $tax;
            } elseif ($optns != null) {
                $choices = [];
                $i = 1;
                foreach ($optns as $optn) {
                    $choices[$i] = array(
                        'label' => esc_html($optn),
                        'value' => esc_html($optn)
                    );
                    $i = $i + 1;
                }
                $fld['choices'] = $choices;
            }
            return $fld;
        }

        
        /**
         * Add created field to WPForms
         * 
         */
        private static function add_field_to_form(&$frm, $fld) {
            if (array_key_exists("fields", $frm)) {
                if (is_array($frm["fields"]) && count($frm["fields"]) > 0) {
                    $i = max(array_keys($frm["fields"])) + 1;
                    $fld["id"] = strval($i);
                    $frm["fields"][strval($i)] = $fld;
                    return $frm;
                }
            }
            $fld["id"] = "1";
            $frm["fields"] = array(
                "1" => $fld
            );
            return $frm;
        }


    }  //close class

}