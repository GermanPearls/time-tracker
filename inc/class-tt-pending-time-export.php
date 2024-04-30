<?php
/**
 * Time Tracker Export Pending Time (ie: hasn't been billed yet)
 *
 * Extends Class Pending_Time which pulls data from db and organizes it
 * 
 * @since 2.2.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Pending_Time_Export' ) ) {


    /**
     * Class
     * 
     * @since 2.2.0
     */
    class Pending_Time_Export extends Pending_Time
    {

        
        protected $original_data;   //data from queried database
        public $saved_files;        //array of files to return to ajax function and download, each file includes key-value array including fname, fpath, fcontent

        
        /**
         * Constructor
         * 
         * @since 2.2.0
         */
        public function __construct() {
            $this->original_data = parent::get_data_for_export();
            $this->saved_files = array();
        }


        /**
         * Export to CSV File(s)
         *
         * @since 2.2.0
         * 
         * @return array List of saved files.
         */
        public function export_each_billto() {
            $this->save_each_billto_as_a_file();         
            return $this->saved_files;
        }


        /**
         * Export to IIF File(s) for Import to Quickbooks Invoices
         *
         * @since 3.0.13
         * 
         * @return array List of saved files capable of being imported into QB.
         */
        public function export_each_billto_for_qb() {
            $this->save_each_billto_as_a_qb_import_file();         
            return $this->saved_files;
        }
        
        
        /**
         * Save Each Bill To In Separate IIF File Compatible with QB Import
         *
         * @since 3.0.13
         * 
         * @return null
         */
        private function save_each_billto_as_a_qb_import_file() {
            foreach ($this->original_data as $billtoname => $time_details) {
                if (!empty($time_details)) {   
                    $export_and_dl = New Time_Tracker_Export_To_File_And_Download($this->get_save_path(),
                        $this->get_filename($billtoname, "quickbooks_invoice_import"), ".iif", $this->convert_to_qb_import_format($time_details), "tab");
                    $fl = $export_and_dl->save_to_file();
                    array_push($this->saved_files, $fl);
                }
            }
            return;         
        } 
        
        /**
         * Process Each Bill To Separately
         *
         * @since 2.2.0
         * 
         * @return null
         */
        private function save_each_billto_as_a_file() {
            foreach ($this->original_data as $billtoname => $time_details) {
                if (!empty($time_details)) {   
                    $export_and_dl = New Time_Tracker_Export_To_File_And_Download($this->get_save_path(), $this->get_filename($billtoname), ".csv", $time_details);
                    $fl = $export_and_dl->save_to_file();
                    array_push($this->saved_files, $fl);
                }
            }
            return;         
        }        


        /**
         * Get File Save Path
         *
         * @since 2.2.0
         * 
         * @return string Path where files will be saved.
         */
        private function get_save_path() {
            $path = ABSPATH . "../tt_logs/exports";
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            return $path;            
        }


        /**
         * Get File Save Name
         *
         * @since 2.2.0
         * @since 3.0.13 Updated to allow more flexibility in name.
         * 
         * @param string $descriptor Description of file being saved.
         * 
         * @return string Name of file to save.
         */
        private function get_filename($descriptor, $nm="pending_time_export") {
            if (is_null($descriptor)) {
                $filename = $nm . "_" .  date('d-M-Y');
            } else {
                $filename = $nm . "_" . date('Y_M_d') . '_' . $descriptor;
            }
            if (!file_exists($filename)) {
                return $filename;
            }

            $i = 0;
            do {
                $i = $i + 1;
                $filename = $filename . '_' . $i;
            } while (file_exists($filename));
            return $filename;      
        }

        /**
         * Convert to Format for QB Import
         *
         * @since 3.0.13
         * 
         * @return Multi dimensional array ready for export to csv.
         */
        private function convert_to_qb_import_format($time_details) {
            $client = "";
            $ticket = "";
            $i = 1;
            $eom = \date_format(new \DateTime('last day of this month'), 'm/d/Y');
            $inv_header_1 = array("!TRNS", "TRNSID", "TRNSTYPE", "DATE", "ACCNT", "NAME", "CLASS", "AMOUNT", "DOCNUM", "MEMO", "CLEAR", "TOPRINT", "ADDR1", "ADDR2", "ADDR3", "ADDR4", "ADDR5", "DUEDATE", "TERMS", "PAID", "SHIPDATE");
            $inv_header_2 = array("!SPL", "SPLID", "TRNSTYPE", "DATE", "ACCNT", "NAME", "CLASS", "AMOUNT", "DOCNUM", "MEMO", "CLEAR", "QNTY", "PRICE", "INVITEM", "PAYMETH", "TAXABLE", "REIMBEXP", "EXTRA", "", "", "");
            $inv_header_3 = array("!ENDTRNS", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
            $inv_blank_line = array("SPL", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
            $inv_footer = array("ENDTRNS", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
            $qb_array = array($inv_header_1, $inv_header_2, $inv_header_3);
            foreach ($time_details as $time_detail) {
                //invoice header details
                if ($time_detail["Company"] != $client) {
                    if (count($qb_array) != 3) {
                        array_push($qb_array, $inv_footer);                        
                    }
                    $inv_header =  array("TRNS", strval($i), "INVOICE", $eom, "Accounts Receivable", $time_detail["Company"], "", "", "", "", "N", "N", "", "", "", "", "", "", "", "", "");
                    array_push($qb_array, $inv_header);
                    $i++;
                    $client = $time_detail["Company"];
                }

                //ticket header
                if ($time_detail["TaskID"] != $ticket) {
                    //if this is not the first ticket for this invoice, leave a blank line between tickets
                    if ($qb_array[count($qb_array)-1][0] != "TRNS") {
                        array_push($qb_array, $inv_blank_line);
                    }
                    $ticket_header = array("SPL", strval($i), "INVOICE", $eom, "", "", "", "", "", "TICKET " . $time_detail["TaskID"] . " - " . strtoupper($time_detail["TDescription"]), "", "", "", "", "", "N", "N", "", "", "", "");
                    array_push($qb_array, $ticket_header);
                    $ticket = $time_detail["TaskID"];
                }

                //invoice line items
                $inv_line = array();
                //note qty must be negative when importing into QB as invoice
                $inv_line =  array("SPL", strval($i), "INVOICE", $eom, "", "", "", "", "", str_replace("\r\n", "\\n", $time_detail["TNotes"]), "N", 
                    (0-\Logically_Tech\Time_Tracker\Inc\tt_convert_to_decimal_time($time_detail["LoggedHours"], $time_detail["LoggedMinutes"])),
                    $time_detail["BillingRate"], "Service - TBD", "", "N", "N", "", "", "", "");
                array_push($qb_array, $inv_line);
                $i++;
            }
            array_push($qb_array, $inv_footer);
            return $qb_array;         
        }

    } //class
} //if exists