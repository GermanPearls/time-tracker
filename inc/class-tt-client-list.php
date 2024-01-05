<?php
/**
 * Class Client_List
 *
 * Get and display client list in table on front end
 * 
 * @since 1.0.0
 * @since 3.0.13 clarify column header
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
     * @since 1.0.0
     */
    class Client_List
    {


        private $clientid; 
        private $all_clients;

        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            if (isset($_GET['client'])) {
                if ($_GET['client'] <> null) {
                    $this->clientid = get_client_id_from_name(sanitize_text_field($_GET['client']));
                }
            } elseif (isset($_GET['client-id'])) {
                $this->clientid = intval($_GET['client-id']);
            } else {
                $this->clientid  = null;
            };
            $this->get_clients_from_db();
        }


        /**
         * Get html result
         * 
         * @since 1.0.0
         * 
         * @return string Html output string.
         */
        public function create_table() {
            return $this->get_html();
        }


        /**
         * Get client list from db
         * 
         * @since 1.0.0
         */
        private function get_clients_from_db() {
            $sql_string = "SELECT tt_client.*
                FROM tt_client";
            $sql_string .= $this->get_where_clauses();
            $sql_string .= " ORDER BY tt_client.Company ASC";
            $sql_result = tt_query_db($sql_string);
            $this->all_clients = $sql_result;
        }


        /**
         * Get where clauses depending on input
         * 
         * @since x.x.x
         * 
         * @return string Where parameters for end of sql string.
         */
        private function get_where_clauses() {
            $where_clauses = array();
            $where_clause = "";
            if ($this->clientid <> null) {
                array_push($where_clauses, "tt_client.ClientID = " . $this->clientid);
            }
            if ( (count($where_clauses) > 1) or ((count($where_clauses) == 1) and ($where_clauses[0] <> "")) ) {
                $where_clause = " WHERE ";
                $where_clause .= implode(" AND ", $where_clauses);
            }
            return $where_clause;
        }


        /**
         * Get table column order and details
         * 
         * @since x.x.x
         * @since 3.0.13 clarify column header
         * 
         * @return array Multi-dimensional array including list of columns, with key-value pairs of column parameters.
         */
        private function get_table_fields() {
            $cols = [
                "Client ID" => [
                    "fieldname" => "ClientID",
                    "id" => "client-id",
                    "editable" => false,
                    "columnwidth" => "five",
                    "type" => "text",
                    "class" => ""
                ],
                "Client Name" => [
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
                "Billing Rate " . tt_get_currency_type() => [
                    "fieldname" => "BillingRate",
                    "id" => "billing-rate",
                    "editable" => true,
                    "columnwidth" => "five",
                    "type" => "integer",
                    "class" => "tt-align-right"
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
                    "columnwidth" => "five",
                    "type" => "date",
                    "class" => "tt-align-right"
                ]
            ];
            return $cols;
        }


        /**
         * Iterate through data and add additional information for table
         * 
         * @since x.x.x
         * 
         * @return array Array of clients with information for forming html table.
        **/
        private function get_all_data_for_display() {
            $clients = $this->all_clients;
            foreach ($clients as $item) {
                $client_details_button = "<button onclick='open_time_entries_for_client(\"" . esc_attr(sanitize_textarea_field($item->Company)) . "\")' id=\"client-" . esc_attr(sanitize_text_field($item->ClientID))  . "\" class=\"open-client-detail tt-table-button\">View Time</button>";
                $delete_client_button = "<button onclick='location.href = \"" . TT_HOME . "delete-item/?client-id=" . esc_attr($item->ClientID) . "\"' id=\"delete-client-" . esc_attr($item->ClientID)  . "'\" class=\"open-delete-page tt-button tt-table-button\">Delete</button>";
                $item->ClientID = [
                    "value" => $item->ClientID,
                    "button" => [
                        $client_details_button,
                        $delete_client_button
                    ]
                ];
            }
            return $clients;
        }


        /**
         * Create HTML table for front end display
         * 
         * @since x.x.x
         * 
         * return string Html output consisting of table of clients.
         */
        public function get_html() {
            $fields = $this->get_table_fields();
            $clients = $this->get_all_data_for_display();
            $args["class"] = ["tt-table", "client-list-table"];
            $tbl = new Time_Tracker_Display_Table();
            $table = $tbl->create_html_table($fields, $clients, $args, "tt_client", "ClientID");
            return $table;
        }

    }
}