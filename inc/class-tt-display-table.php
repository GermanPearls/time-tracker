<?php
/**
 * Class Time_Tracker_Display_Table
 *
 * Takes data, fields, arguments, etc and creates html table output for front end
 * 
 * @param $fields       Array of fields with parameters
 * @param $data         Array of data, each item in array can be an OBJECT (result of sql) or an ARRAY - code should handle either
 * @param $table_args   Arguments for entire table (ie: classes)
 * @param $table_name   Name for entire table (id: id)
 * @param $table_key    If data inside table is editable, this is the primary key fieldname for the table
 * 
 * @return string       Html output including data in html table
 * 
 * @since 1.1.1
 * @since 3.0.11 Fix null entries, display as empty text.
 * @since 3.0.12 Fix revision number. Change data sanitization for text to remove slashes.
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_Display_Table') ) {
    
    /**
     * Class
     * 
     * @since 1.1.1
     */
    class Time_Tracker_Display_Table {

        public function __construct() {
        }


		/**
         * Create Entire HTML Table
         * 
         * @since 2.2.0
         * 
         * @param xxx $fields xxx
         * @param xxx $data xxx
         * @param xxx $table_args xxx
         * @param xxx $table_name xxx
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return string Html string.
         */
        public function create_html_table($fields, $data, $table_args, $table_name, $table_key) {
            if ($data) {
                $html_out = "<div style='text-align:center; padding-bottom: 0; margin-bottom: 0;'>Note: Gray shaded cells can't be changed.</div>";
				$html_out .= $this->start_table($table_args);
				$html_out .= $this->create_header_row($fields);
				$html_out .= $this->create_data_rows($fields, $data, $table_name, $table_key);
				$html_out .= $this->close_table();
			} else {
                $html_out = "<p style='font-weight:bold;padding-left:20px;'>Nothing to Display</p>";
            }
            return $html_out;
        }	 
        
        
        /**
         * Create arguments for including within html tab
         * 
         * @since 1.2.1
         * @since 3.0.13 Remove button and icon from showing up as td attributes on front end.
         * 
         * @param array $arguments Array of arguments to add within a tag, stored in key-value pairs of attribute name-value.
         * 
         * @return string String of attributes to add within an html tag.
         */
        private function add_arguments($arguments) {
            $args = "";
            foreach ($arguments as $type=>$value) {
                if ($type != "button" && $type != "icon") {
                    $args .= $type . "='";
                    
                    if (is_array($value)) {
                        foreach ($value as $ind_value) {
                            $args .= esc_attr($ind_value) . " ";
                        }
                    } else {
                        $args .= esc_attr($value) . " ";
                    }
                    
                    $args = trim($args) . "' ";
                }
            }
            return " " . trim($args);
        }


        /**
         * Start Table Tag
         * 
         * @since 1.2.1
         * 
         * @return string Html opening table tag, with attributes, if applicable.
         */
        public function start_table($head_arguments = null) {
            $tbl = "<table";
            if ($head_arguments) {
                $tbl .= $this->add_arguments($head_arguments);
            }
            $tbl .=">";
            return $tbl;
        }
        
        
        /**
         * Close Table Tag
         * 
         * @since 1.2.1
         * 
         * @return string Html closing table tag.
         */
        public function close_table() {
            return "</table>";
        }


        /**
         * Start Row
         * 
         * @since 1.2.1
         * 
         * @return string Html opening table row tag, tr, with attributes, if applicable.
         */
        public function start_row($row_arguments = null) {
            $row = "<tr";
            if ($row_arguments) {
                $row .= $this->add_arguments($row_arguments);
            }
            $row .=">";
            return $row;
        }


        /**
         * Close Row
         * 
         * @since 1.2.1
         * 
         * @return string Html closing table row tag.
         */
        public function close_row() {
            return "</tr>";
        }


        /**
         * Start Header Data
         * 
         * @since 1.2.1
         * 
         * @return string Html opening table header tag, with attributes if applicable.
         */
        public function start_header_data($header_data_arguments = null) {
            $th = "<th";
            if ($header_data_arguments) {
                $th .= $this->add_arguments($header_data_arguments);
            }
            $th .= ">";
            return $th;
        }


        /**
         * Close Header Data
         * 
         * @since 1.2.1
         * 
         * @return string Html closing table header tag.
         */
        public function close_header_data() {
            return "</th>";
        }


        /**
         * Start Data
         * 
         * @since 1.2.1
         * 
         * @return string Html opening table cell tag, td, with attributes, if applicable.
         */
        public function start_data($data_arguments = null) {
            $td = "<td";
            if ($data_arguments) {
                $td .= $this->add_arguments($data_arguments);
            }
            $td .= ">";
            return $td;
        }


        /**
         * Close Data
         * 
         * @since 1.2.1
         * 
         * @return string Html closing table cell tag.
         */
        public function close_data() {
            return "</td>";
        }


        /**
         * Create Header Row
         * 
         * @since 1.2.1
         * 
         * @return string Html of header row, including opening tag, cells, closing header tag.
         */
        private function create_header_row($fields) {
            $header_row = $this->start_row();
            $columns = $fields;
            foreach ($columns as $name=>$details) {
                $header_row .= $this->start_header_data() . $name . $this->close_header_data();                
            }
            $header_row .= $this->close_row();
            return $header_row;
        }


        /**
         * Sanitize, Escape, and Display Data
         * 
         * @since 3.0.5
         * @since 3.0.11 Null display values returned as empty string.
         * @since 3.0.13 Change data sanitization setting for text. Modified to allow for creation of widgets.
         * 
         * @param string $data_type Type of data to return.
         * @param string $display_value Data which needs to be displayed in cell.
         * @param array $args Additional details needed display data in cell, ex: used with select dropdown creation. 
         * 
         * @return varies Output to display in cell, can be string, long text, integer, more.
         */
        public function display_data_in_cell($data_type, $display_value, $args=[], $itm=[]) {
            if ($display_value == null && $data_type != "select") {
                return "";
            }
            if ($data_type == "text") {
                //return esc_html(sanitize_text_field($display_value));
                return stripslashes(wp_kses_post(nl2br($display_value)));
            } elseif ($data_type == "long text") {
                return stripslashes(wp_kses_post(nl2br($display_value)));
            } elseif ($data_type == "date") {
                $formatted_date = tt_format_date_for_display(sanitize_text_field($display_value), "date_only");
                return esc_html($formatted_date);
            } elseif ($data_type == "date and time") {
                $formatted_date = tt_format_date_for_display(sanitize_text_field($display_value), "date_and_time");
                return esc_html($formatted_date);
            } elseif ($data_type == "email") {
                return esc_html(sanitize_email($display_value));
            } elseif ($data_type == "integer") {
                return intval($display_value);
            } elseif ($data_type == "select") {
                return $this->create_select_dropdown($args, $display_value);
            } elseif ($data_type == "widget-invoice") {
                return $this->create_widget_invoice($display_value, $itm);
            }
            return esc_html(sanitize_text_field($display_value));
        }

        /**
         * Create Widget for Invoice Details
         * 
         * @since 3.0.13
         * 
         */
        private function create_widget_invoice($dtls, $itm) {
            $widget = new Time_Tracker_Widget_Invoice_Details($dtls, $itm);
            return $widget->get_widget_invoice_html();
        }


        /**
         * Get Arguments for Data Cell
         * 
         * @since 1.4.0
         * @since 3.0.13 Adjusted order to skip some logic if no sql fieldname provided.
         * 
         * @param array $details Information about this cell and its structure (ie: html tags).
         * @param array|object $item Data that will populate this entire row.
         * @param string $sql_fieldname	This sql field name where this cell's data originated.
         * @param string $table_name Main database table this table data originates from.
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return array Array of data and arguments for creating cell.
         */
        public function get_cell_args($details, $item, $sql_fieldname, $table_name, $table_key) {
            $args = [];
            $args["id"] = $details["id"];
            $args["class"] = [];

            if (array_key_exists("class", $details)) {
                if (is_array($details["class"])) {
                    foreach ($details["class"] as $ind_class) {
                        array_push($args["class"], $ind_class);
                    }
                }
                if ($details["class"] <> "") {
                    array_push($args["class"], $details["class"]);                 
                }
            }
            
            if ( strlen($details["columnwidth"]) > 0 ) {
                array_push($args["class"], "tt-col-width-" . $details["columnwidth"] . "-pct");
            }
            
            if ($sql_fieldname <> "") {
                if ($details["editable"]) {
                    array_push($args["class"], "editable");
                    $args["contenteditable"] = "true";
                    $args["onBlur"] = $this->build_edit_action($details, $item, $table_name, $table_key, $sql_fieldname);
                } else {
                    array_push($args["class"], "not-editable");
                }

                $item_sql_fieldname_details = is_object($item) ? $item->$sql_fieldname : (array_key_exists($sql_fieldname, $item) ? $item[$sql_fieldname] : "");
                if ( is_array($item_sql_fieldname_details) ) {
                    if ( array_key_exists("class", $item_sql_fieldname_details) ) {
                        array_push($args["class"], $item_sql_fieldname_details["class"]);
                    }
                    if ( array_key_exists("button", $item_sql_fieldname_details) ) {
                        if (is_array($item_sql_fieldname_details["button"])) {
                            $args["button"] = [];
                            foreach ($item_sql_fieldname_details["button"] as $ind_button) {
                                array_push($args["button"], $ind_button);
                            }
                        } else {
                            $args["button"] = $item_sql_fieldname_details["button"];
                        }
                    }
                    if ( array_key_exists("icon", $item_sql_fieldname_details) ) {
                        if (is_array($item_sql_fieldname_details["icon"])) {
                            $args["icon"] = [];
                            foreach ($item->$item_sql_fieldname_details["icon"] as $ind_icon) {
                                array_push($args["icon"], $ind_icon);
                            }
                        } else {
                            $args["icon"] = $item_sql_fieldname_details["icon"];
                        }
                    }
                }
            } else {
                array_push($args["class"], "not-editable");
            }
            return $args;
        }


        /**
         * Build edit details
         * 
         * @since 3.0.13
         * 
         * @param array $details Information about this cell and its structure (ie: html tags).
         * @param array|object $item Data that will populate this entire row.
         * @param string $sql_fieldname	This sql field name where this cell's data originated.
         * @param string $table_name Main database table this table data originates from.
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return string Function with parameters to paste into onBlur action of data cell.
         */
        private function build_edit_action($details, $item, $table_name, $table_key_name, $sql_fieldname) {
            //allow for manually set table name, ref field, and ref value (vs default for entire table)
            if (array_key_exists("edit-details", $details)) {
                $table_name = $details["edit-details"]["table"];
                $table_key_name = $details["edit-details"]["ref-field"];
            }
            if ( is_object($item) ) {
                $table_key_value = is_array($item->$table_key_name) ? $item->$table_key_name["value"] : $item->$table_key_name;
            } elseif ( is_array($item) ) {
                $table_key_value = is_array($item[$table_key_name]) ? $item[$table_key_name]["value"] : $item[$table_key_name];
            }
            return "updateDatabase(this, '" . $table_name . "', '" . $table_key_name . "', '" . $sql_fieldname . "', '" . $table_key_value. "')";
        }


        /**
         * Create All Data Rows
         * 
         * @since 1.4.0
         * 
         * @param array $fields Array of fields to be displayed across row
         * @param xxx $data xxx
         * @param string $table_name Name of table we are creating.
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return string Html output of all data rows.
         */
        private function create_data_rows($fields, $data, $table_name, $table_key) {
            $data_rows = "";
            foreach ($data as $item) {
                $row = $this->create_data_row($fields, $item, $table_name, $table_key);
                $data_rows .= $row;
            }
            return $data_rows;
        }


        /**
         * Create Single Data Row
         * 
         * @since 1.4.0
         * 
         * @param array $fields Array of fields to be displayed across row
         * @param array|object $item Data to be filled in across row
         * @param string $table_name Name of table we are creating.
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return string Html output of one data row including opening row tag, data cells, and closing row tag.
         */
        private function create_data_row($fields, $item, $table_name, $table_key) {
            $row = $this->start_row($this->get_row_args($item));
            foreach ($fields as $header=>$field_details) {
                $row .= $this->create_data_cell($field_details, $item, $table_name, $table_key);
            }
            $row .= $this->close_row();
            return $row;
        }

        /**
         * Get any row classes from cell data and apply to row element above.
         * 
         * @since 3.0.13
         * 
         * @param array|object $itm Data for row.
         * 
         * @return string Any classes to apply to row element.
         */
        private function get_row_args($itm) {
            $rw_args = [];
            foreach ($itm as $cell_data) {
                if (is_array($cell_data)) {
                    if (array_key_exists("class", $cell_data)) {
                        if (str_contains($cell_data["class"], "row")) {
                            $rw_args["class"] = $cell_data["class"];
                        }
                    }

                    if (array_key_exists("widget-data", $cell_data)) {
                        foreach ($cell_data["widget-data"] as $sub_cell) {
                            if (is_array($sub_cell)) {
                                if (array_key_exists("class", $sub_cell)) {
                                    if (str_contains($sub_cell["class"], "row")) {
                                        $rw_args["class"] = $sub_cell["class"];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $rw_args;
        }



        /**
         * Create Single Data Cell
         * 
         * @since 1.4.0
         * @since 3.0.13 Modified to allow for creating widgets instead of just data cells.
         * 
         * @param array $field_details Details on how the field should be displayed.
         * @param array|object $item Data for this table section, which includes data for this cell.
         * @param string $table_name Name of table we are creating.
         * @param string $table_key Name of main ID column in database table to reference this record in the table.
         * 
         * @return string Html of one cell including opening cell tag, data, and closing cell tag.
         */
        private function create_data_cell($field_details, $item, $table_name, $table_key) {
            $sql_fieldname = $field_details["fieldname"];
            $args = $this->get_cell_args($field_details, $item, $sql_fieldname, $table_name, $table_key);
            
            if ($field_details["type"] == "widget-invoice") {
                $display_value = $field_details;
            } elseif ( is_object($item) ) {
                $display_value = is_array($item->$sql_fieldname) ? $item->$sql_fieldname["value"] : $item->$sql_fieldname;
            } elseif ( is_array($item) ) {
                $display_value = is_array($item[$sql_fieldname]) ? $item[$sql_fieldname]["value"] : $item[$sql_fieldname];
            }

            $cell = $this->start_data($args);
            $cell .= $this->display_data_in_cell($field_details["type"], $display_value, array_key_exists("select_options", $field_details) ? $field_details["select_options"] : [], $item);
            $cell .= $this->add_button_to_cell($args);
            $cell .= $this->add_icon_to_cell($args);
            $cell .= $this->close_data();
            return $cell;
        }


        /**
         * Create Select Dropdown
         * 
         * @since 3.0.5
         * @since 3.0.13 resolve deprecated - cannot pass null to trim function
         * 
         * @param array $args Array of arguments in key-value pairs.
         * @param string $val Value to show as selected in dropdown.
         * 
         * @return string Html for a select dropdown.
         */
        private function create_select_dropdown($args, $val) {
            $typ = array_key_exists("data_type", $args) ? $args["data_type"] : "text";
            $optns = array_key_exists("options", $args) ? $args["options"] : [];
            $ttl = array_key_exists("title", $args) ? $args["title"] : "";
            $dropdown = "<select title=\"" . $ttl . "\" onblur=\"trigger_table_cell_blur_event(this);\">";
            if (array_key_exists("nullable", $args)) {
                if ($args["nullable"]) {
                    $dropdown .= "<option value=\"\"";
                    if (is_null($val)) {
                        $dropdown .= " selected=\"selected\"";
                    }
                    $dropdown .= ">";
                }
            }
            foreach ($optns as $optn) {
                //if option display value different than ID options may be passed as a k-v array
                if (gettype($optn) === gettype([])) {
                    $id = array_key_exists("id", $optn) ? $optn["id"] : "";
                    $display = array_key_exists("display", $optn) ? $optn["display"] : "";
                } else {
                    $id = $optn;
                    $display = $optn;
                }
                $dropdown .= "<option value=\"" . trim($id) . "\"";
                if (! is_null($val)) {
                    if (trim($id) == trim($val)) {
                        $dropdown .= " selected=\"selected\"";
                    }
                }
                $dropdown .= ">";
                $dropdown .= $this->display_data_in_cell($typ, trim($display));
                $dropdown .= "</option>";
            }
            $dropdown .= "</select>";
            return $dropdown;
        }


        /**
         * Add Button to Cell
         * 
         * @since 1.4.0
         * 
         * @param array $args Array of arguments used to create cell.
         * 
         * @return string Html of button.
         */
        private function add_button_to_cell($args) {
            $button = "<br/>";
            if (array_key_exists("button", $args)) {
                if (is_array($args["button"])) {
                    foreach ($args["button"] as $ind_button) {
                        $button .= $ind_button;
                    }
                } else {
                    $button .= $args["button"];
                }
            }
            return $button;            
        }


        /**
         * Add Icon to Cell
         * 
         * @since 1.4.0
         * 
         * @param array $args Array of arguments used to create cell.
         * 
         * @return string Html of icon.
         */
        private function add_icon_to_cell($args) {
            $icon = "";
            if (array_key_exists("icon", $args)) {
                $icon .= "<br/>";
                if (is_array($args["icon"])) {
                    foreach ($args["icon"] as $ind_icon) {
                        $icon .= $ind_icon;
                    }
                } else {
                    $icon .= $args["icon"];
                }
            }
            return $icon;            
        }

    }  //close class
 }  //close if class exists
