<?php
/**
 * Class Time_Tracker_Widget_Invoice_Details
 *
 * Compact display of invoice details for a time entry.
 * 
 * @param $fields       Array of fields with parameters
 * @param $data         Array of data, each item in array can be an OBJECT (result of sql) or an ARRAY - code should handle either
 * @param $table_args   Arguments for entire table (ie: classes)
 * @param $table_name   Name for entire table (id: id)
 * @param $table_key    If data inside table is editable, this is the primary key fieldname for the table
 * 
 * @return string       Html output displaying invoice details.
 * 
 * @since 3.0.13
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

/**
 * Check if class exists
 * 
 * @since 3.0.13
 */
if ( ! class_exists('Time_Tracker_Widget_Invoice_Details') ) {
    
    /**
     * Class
     * 
     * @since 3.0.13
     */
    class Time_Tracker_Widget_Invoice_Details {


        public $tbl;
        private $structure_widget;
        private $structure_detail;
        private $itm;


        /**
         * Construct
         * 
         * @since 3.0.13
         * 
         * @param array $stucture Array defining structure of overall widget table, including sub-array with internal definitions.
         * @param array|object $itm Item details (data).
         **/
         public function __construct($structure, $itm) {
            $this->tbl = new Time_Tracker_Display_Table();
            $this->structure_widget = $structure;
            $this->structure_detail = $structure["widget-data"];
            $this->itm = $itm;
        }


        /**
         * Get widget html
         * 
         * @since 3.0.13
         *
         * @return string Html of widget to display
         **/
        public function get_widget_invoice_html() {
            return $this->create_widget_invoice_details();
        }



        /**
         * Create Widget
         * 
         * @since 3.0.13
         *
         * @return string Html of widget to display
         **/
        private function create_widget_invoice_details() {
            $args = $this->tbl->get_cell_args($this->structure_widget, "", "", "", "");
            
            $html_out = $this->tbl->start_table($args);
            foreach ($this->structure_detail as $rw_name => $rw_structure) {
                $html_out .= $this->create_row($rw_name, $rw_structure);
            }
            $html_out .= $this->tbl->close_table();
            return $html_out;
        }


        /**
         * Create one row in widget table.
         * 
         * @since 3.0.13
         * 
         * @param string $nm Name of field.
         * @param array $rw Array defining row structure.
         * 
         * @return string Html of one row in widget table.
         **/
        private function create_row($nm, $rw_details) {
            $rw = $this->tbl->start_row();
            
            $rw .= $this->tbl->start_data(["class"=>"not-editable"]);
            $rw .= $this->tbl->display_data_in_cell("text", $nm, []);
            $rw .= $this->tbl->close_data();

            $args = $this->tbl->get_cell_args($rw_details, $this->itm, $rw_details["fieldname"], "tt_time", "TimeID");
            
            $rw .= $this->tbl->start_data($args);
            $rw .= $this->tbl->display_data_in_cell($rw_details["type"], $this->get_value($rw_details), $args);
            $rw .= $this->tbl->close_data();

            $rw .=$this->tbl->close_row();      
            return $rw;      
        }


        /**
         * Get value for this field (cell)
         * 
         * @since 3.0.13
         * 
         * @param array $field_details Details of cell we're looking to populate.
         *
         * @return var Value to display in cell
         **/
        public function get_value($field_details) {
            $sql_fieldname = $field_details["fieldname"];
            if ( is_object($this->itm) ) {
                $display_value = is_array($this->itm->$sql_fieldname) ? $this->itm->$sql_fieldname["value"] : $this->itm->$sql_fieldname;
            } elseif ( is_array($this->itm) ) {
                $display_value = is_array($this->itm[$sql_fieldname]) ? $this->itm[$sql_fieldname]["value"] : $this->itm[$sql_fieldname];
            }
            return $display_value;
        }

    }  //close class
 }  //close if class exists
