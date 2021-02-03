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
         * Get client list from db
         * 
         */
        private function get_clients_from_db() {
            //$tt_db = new wpdb(DB_USER, DB_PASSWORD, TT_DB_NAME, DB_HOST);
            global $wpdb;
            $sql_string = "SELECT tt_client.*
                FROM tt_client
                ORDER BY tt_client.ClientID ASC";
            $sql_result = $wpdb->get_results($sql_string);
            catch_sql_errors(__FILE__, __FUNCTION__, $wpdb->last_query, $wpdb->last_error);
            $this->all_clients = $sql_result;
        }


        /**
         * Create HTML table for front end display
         * 
         */
        public function create_table() {
            
            $clients = $this->all_clients;

            //Begin creating table and headers
            $table = "<strong>Note: Gray shaded cells can't be changed.</strong><br/><br/>";
            
            $table .= "<table class=\"tt-table client-list-table\">";
            $table .= "<thead><tr>";
            $table .= "<th>ID</th>";
            $table .= "<th>Client</th>";
            $table .= "<th>Contact</th>";
            $table .= "<th>Email</th>";
            $table .= "<th>Phone</th>";
            $table .= "<th>Bill To</th>";                        
            $table .= "<th>Source</th>";
            $table .= "<th>Source Details</th>";
            $table .= "<th>Comments</th>";
            $table .= "<th>Date Added</th>";
            $table .= "</tr></thead>";
            
            
            //Create body
            foreach ($clients as $item) {          

                if ($item->DateAdded == "0000-00-00 00:00:00") {
                    $date_added_formatted = "";
                } else {
                    $date_added_formatted = date_format(\DateTimeImmutable::createFromFormat("Y-m-d G:i:s", sanitize_text_field($item->DateAdded)), "n/j/y");
                }
                        
                //create row
                $table .= "<tr>";
                
                $table .= "<td id=\"client-id\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->ClientID));
                $table .= "<button onclick='open_time_entries_for_client(\"" . esc_attr($item->Company) . "\")' id=\"" . esc_attr($item->ClientID)  . "\" class=\"open-time-entry-detail chart-button\">View Time</button>";
                $table .= "</td>";
                
                //$table .= "<td id=\"client-name\" class=\"not-editable\">" . nl2br(stripslashes($item->Company)) . "</td>";
                $table .= "<td id=\"client-name\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->Company)) . "</td>";
                $table .= "<td id=\"contact\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->Contact)) . "</td>";
                $table .= "<td id=\"email\" class=\"not-editable\">" . esc_html(sanitize_email($item->Email)) . "</td>";
                $table .= "<td id=\"phone\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->Phone)) . "</td>";
                $table .= "<td id=\"bill-to\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->BillTo)) . "</td>";
                $table .= "<td id=\"source\" class=\"not-editable\">" . esc_html(sanitize_text_field($item->Source)) . "</td>";
                $table .= "<td id=\"source-details\" class=\"not-editable\">" . wp_kses_post(nl2br($item->SourceDetails)) . "</td>";
                //$table .= "<td id=\"comments\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_client', 'ClientID', 'CComments'," . $item->CComments . ")\">" . nl2br(stripslashes($item->CComments)) . "</td>";
                $table .= "<td id=\"comments\" contenteditable=\"true\" onBlur=\"updateDatabase(this, 'tt_client', 'ClientID', 'CComments'," . wp_kses_post(nl2br($item->CComments)) . ")\">" . wp_kses_post(nl2br($item->CComments)) . "</td>";
                $table .= "<td id=\"date-added\" class=\"not-editable\">" . esc_textarea(sanitize_text_field($date_added_formatted)) . "</td>";
                $table .="</tr>";

            } // foreach client loop

            //close out table
            $table .= "</table>";

            return $table;
        }

    } //close class

} //close if class exists