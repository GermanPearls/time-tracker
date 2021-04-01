<?php
/**
 * Class Client_List
 *
 * Get and display client list in table on front end
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Client_List' ) ) {


    /**
     * Class
     * 
     */
    class Client_List
    {


        //private $status_order = ["New", "Ongoing", "In Process", "Waiting Client", "Complete", "Canceled"];

        /**
         * Constructor
         * 
         */
        public function __construct() {
            $this->get_clients_from_db();
        }


        /**
         * Get html result
         * 
         */
        public function create_table() {
            return $this->get_html();
        }


        /**
         * Get client list from db
         * 
         */
        private function get_clients_from_db() {
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $sql_string = "SELECT tt_client.*
                FROM tt_client
                ORDER BY tt_client.Company ASC";
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            $this->all_clients = $sql_result;
        }


        /**
         * Get table column order
         * 
         */
        private function get_column_order() {
            $cols = [
                "ID" => [
                    "fieldname" => "ClientID",
                    "id" => "client-id",
                    "editable" => false,
                    "columnwidth" => "five",
                    "type" => "text"
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "company-name",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text"
                ],
                "Contact" => [
                    "fieldname" => "Contact",
                    "id" => "contact-name",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "text"
                ],
                "Email" => [
                    "fieldname" => "Email",
                    "id" => "contact-email",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "email"
                ],
                "Phone" => [
                    "fieldname" => "Phone",
                    "id" => "contact-phone",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "text"
                ],
                "Bill To" => [
                    "fieldname" => "BillTo",
                    "id" => "bill-to",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text"
                ],
                "Source" => [
                    "fieldname" => "Source",
                    "id" => "source",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text"
                ],
                "Source Details" => [
                    "fieldname" => "SourceDetails",
                    "id" => "source-details",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "long text"
                ],
                "Comments" => [
                    "fieldname" => "CComments",
                    "id" => "client-comments",
                    "editable" => true,
                    "columnwidth" => "fifteen",
                    "type" => "long text"
                ],
                "Date Added" => [
                    "fieldname" => "DateAdded",
                    "id" => "date-added",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "date"
                ]
            ];
            return $cols;
        }


        /**
         * Create HTML table for front end display
         * 
         */
        public function get_html() {
            
            $clients = $this->all_clients;
            $args = [];

            //notice above table
            $table = "<div style='font-weight:bold; text-align:center;'>Note: Gray shaded cells can't be changed.</div>";
            //$table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            
            //start table
            $args['class'] = ["tt-table", "client-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table .= $tbl->start_table($args);

            //header row
            $table .= $tbl->start_row();
            $columns = $this->get_column_order();
            foreach ($columns as $name=>$details) {
                $table .= $tbl->start_header_data() . $name . $tbl->close_header_data();                
            }
            $table .= $tbl->close_row();

            //data
            foreach ($clients as $item) {
                $end_repeat_args = [];
                $end_repeat_class = "";

                if ($item->DateAdded == "0000-00-00 00:00:00") {
                    $date_added_formatted = "";
                } else {
                    $date_added_formatted = date_format(\DateTimeImmutable::createFromFormat("Y-m-d G:i:s", sanitize_text_field($item->DateAdded)), "n/j/y");
                }

                $row = $tbl->start_row();

                foreach ($columns as $header=>$details) {
                    $sql_fieldname = $details["fieldname"];
                    $args = [];
                    $args["id"] = $details["id"];
                    if ($details["editable"]) {
                        $args["class"] = ["editable"];
                        $args["contenteditable"] = "true";
                        $args["onBlur"] = "updateDatabase(this, 'tt_client', 'ClientID', '" . $sql_fieldname . "', '" . $item->ClientID . "')";
                    } else {
                        $args["class"] = ["not-editable"];
                    }
                    if ( strlen($details["columnwidth"]) > 0 ) {
                        array_push($args["class"], ".tt-col-width-" . $details["columnwidth"] . "-pct");
                    }

                    $cell = $tbl->start_data($args);

                    //sanitize and escape based on field type
                    if ($details["type"] == "text") {
                        $cell .= esc_html(sanitize_text_field($item->$sql_fieldname));
                    } elseif ($details["type"] == "long text") {
                        $cell .= stripslashes(wp_kses_post(nl2br($item->$sql_fieldname)));
                    } elseif ($details["type"] == "date") {
                        $formatted_date = tt_format_date_for_display(sanitize_text_field($item->$sql_fieldname), "date_only");
                        $cell .= esc_html($formatted_date);
                    } elseif ($details["type"] == "date and time") {
                        $formatted_date = tt_format_date_for_display(sanitize_text_field($item->$sql_fieldname), "date_and_time");
                        $cell .= esc_html($formatted_date);
                    }  elseif ($details["type"] == "email") {
                        $cell .= esc_html(sanitize_email($item->$sql_fieldname));
                    } else {
                        $cell .= esc_html(sanitize_text_field($item->$sql_fieldname));
                    }
                    $cell .= $tbl->close_data();
                    $row .= $cell;
                }

                $row .= $tbl->close_row();
                $table .= $row;
                
            } // foreach row loop

            $table .= $tbl->close_table();

            return $table;
        }

    } //close class

} //close if class exists