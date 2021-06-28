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
         * Get table column order and details
         * 
         */
        private function get_table_fields() {
            $cols = [
                "ID" => [
                    "fieldname" => "ClientID",
                    "id" => "client-id",
                    "editable" => false,
                    "columnwidth" => "five",
                    "type" => "text",
                    "class" => ""
                ],
                "Client" => [
                    "fieldname" => "Company",
                    "id" => "company-name",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text",
                    "class" => ""
                ],
                "Contact" => [
                    "fieldname" => "Contact",
                    "id" => "contact-name",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "text",
                    "class" => ""
                ],
                "Email" => [
                    "fieldname" => "Email",
                    "id" => "contact-email",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "email",
                    "class" => ""
                ],
                "Phone" => [
                    "fieldname" => "Phone",
                    "id" => "contact-phone",
                    "editable" => true,
                    "columnwidth" => "ten",
                    "type" => "text",
                    "class" => ""
                ],
                "Bill To" => [
                    "fieldname" => "BillTo",
                    "id" => "bill-to",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text",
                    "class" => ""
                ],
                "Source" => [
                    "fieldname" => "Source",
                    "id" => "source",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "text",
                    "class" => ""
                ],
                "Source Details" => [
                    "fieldname" => "SourceDetails",
                    "id" => "source-details",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "long text",
                    "class" => ""
                ],
                "Comments" => [
                    "fieldname" => "CComments",
                    "id" => "client-comments",
                    "editable" => true,
                    "columnwidth" => "fifteen",
                    "type" => "long text",
                    "class" => ""
                ],
                "Date Added" => [
                    "fieldname" => "DateAdded",
                    "id" => "date-added",
                    "editable" => false,
                    "columnwidth" => "ten",
                    "type" => "date",
                    "class" => "tt-align-right"
                ]
            ];
            return $cols;
        }


        /**
         * Iterate through data and add additional information for table
         * 
        **/
        private function get_all_data_for_display() {
            $clients = $this->all_clients;
            foreach ($clients as $item) {
                $client_details_button = "<button onclick='open_time_entries_for_client(\"" . esc_attr(sanitize_textarea_field($item->Company)) . "\")' id=\"client-" . esc_attr(sanitize_text_field($item->ClientID))  . "\" class=\"open-client-detail chart-button\">View Time</button>";
                $item->ClientID = [
                    "value" => $item->ClientID,
                    "button" => $client_details_button
                ];
            }
            return $clients;
        }


        /**
         * Create HTML table for front end display
         * 
         */
        public function get_html() {
            $fields = $this->get_table_fields();
            $clients = $this->get_all_data_for_display();
            $args["class"] = ["tt-table", "client-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $clients, $args, "tt_client", "ClientID");
            return $table;
        }

    } //close class

} //close if class exists