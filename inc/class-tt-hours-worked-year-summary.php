<?php
/**
 * Time Tracker Class_Hours_Worked_Year_Summary 
 *
 * Takes the data from the hours worked class (query) and summarizes it by year for display
 * 
 * @since 1.0
 * 
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Class_Hours_Worked_Year_Summary' ) ) {


    /**
     * Class
     * 
     */ 
    class Class_Hours_Worked_Year_Summary extends Class_Hours_Worked_Detail
    {

 
        /**
         * Constructor
         * 
         */         
        public function __construct() {
            parent::__construct();
            $hours_worked = $this->hours_worked;
        }

        
        /**
         * Reorganize data, Group by Month, then Bill To
         * 
         */ 
        private function groupDataByMonthAndBillTo() {
            $grouped_time = array();
            if (!empty($this->hours_worked)) {
                foreach ($this->hours_worked as $item) {
                    //only summarize current year
                    if ($item['WorkYear'] == date('Y')) {
                        //get month of current item
                        $workmonth = $item['WorkMonth'];
                        
                        //get bill to of current item
                        if ($item['BillTo'] == "") {
                            $billto = "Unknown";
                        } else {
                            $billto = $item['BillTo'];
                        }

                        $grouped_time[$workmonth][$billto][] = $item;
                    } //if work is current year                
                } //for each piece of data from database
            } //array is empty
            return $grouped_time;
        }


        /**
         * Calculate totals by Month and Bill To
         * 
         */ 
        private function totalDataByMonthAndBillTo() {
            $grouped_time = $this->groupDataByMonthAndBillTo();
            $totaled_time = array();
            
            $billto = "";
            $lastbillto = "not started";
            if (!empty($grouped_time)) {
                foreach ($grouped_time as $monthkey => $month_array) {
                    $monthhoursworked = 0;
                    $monthhoursinvoiced = 0;
                    foreach ($month_array as $billto => $billto_array) {                    
                        $totalhours = 0;
                        $totalminutes = 0;
                        $billedtime = 0;
                        foreach ($billto_array as $item) {
                            $totalminutes = $totalminutes + $item['MinutesWorked'];
                            $totalhours = $totalhours + $item['HoursWorked'];
                            $billedtime = $billedtime + $item['BilledTime'];                        
                        } //total hours from each detailed record inside billto name array
                        //save the total from the last bill to in a new array
                        $decimal_time_worked = tt_convert_to_decimal_time($totalhours, $totalminutes);
                        $totaled_time[$item['WorkMonth']][$item['BillTo']]['TimeWorked'] = round($decimal_time_worked,1);
                        $totaled_time[$item['WorkMonth']][$item['BillTo']]['TimeInvoiced'] = round($billedtime,1);
                        if ($decimal_time_worked == 0) {
                            $totaled_time[$item['WorkMonth']][$item['BillTo']]['PercentTimeInvoiced'] = 0;
                        } else {
                            $totaled_time[$item['WorkMonth']][$item['BillTo']]['PercentTimeInvoiced'] = round($billedtime/$decimal_time_worked*100,0);
                        }
                        //cumulative total for month (of all bill tos)
                        $monthhoursworked = $monthhoursworked + $decimal_time_worked;
                        $monthhoursinvoiced = $monthhoursinvoiced + $billedtime;
                        if ($monthhoursworked == 0) {
                            $monthpercenthoursinvoiced = 0;
                        } else {
                            $monthpercenthoursinvoiced = round($monthhoursinvoiced/$monthhoursworked*100,0);
                        }
                    } //loop bill to name inside this month
                    $totaled_time[$item['WorkMonth']]['Total']['TimeWorked'] = $monthhoursworked;
                    $totaled_time[$item['WorkMonth']]['Total']['TimeInvoiced'] = $monthhoursinvoiced;
                    $totaled_time[$item['WorkMonth']]['Total']['PercentTimeInvoiced'] = $monthpercenthoursinvoiced;
                } //loop through each month
            } //if there's data
            return $totaled_time;
        }


        /**
         * Get all Bill To Names that will need to be displayed in the table
         * 
         */ 
        private function listBillToNames($monthBillToArray) {
            $bill_to_names = array();
            if (!empty($monthBillToArray)) {
                foreach ($monthBillToArray as $billToArray) {
                    foreach ($billToArray as $billToName => $detail) {
                        if ( ($billToName != 'Total') && (! (in_array($billToName, $bill_to_names))) ) {
                            $bill_to_names[] = $billToName;
                        }
                    } //for each billto group
                } //for each month array
            } //if not empty
            //make sure Total appears last in the array
            $bill_to_names[] = 'Total';
            return $bill_to_names;
        }


        /**
         * Create HTML table for front end display
         * 
         */ 
        public function createHTMLTable() {
            $time_summary = $this->totalDataByMonthAndBillTo();
            $bill_to_names = $this->listBillToNames($time_summary);

            //table begin
            $table = "<table class=\"tt-table yearly-summary-table\">";
            
            //header row
            $table .= "<tr class=\"tt-header-row\">";
            $table .= "<th class=\"tt-bold-font tt-align-center\">Month</th>";
            foreach ($bill_to_names as $bill_to_name) {
                $table .= "<th class=\"tt-bold-font tt-align-center\">" . $bill_to_name . "</th>";
            }            
            $table .= "</tr>";

            //populate data
            $dateNow = new DateTime();
            $monthNumber = $dateNow->format('n');
            for ($i = 1; $i <= $monthNumber; $i++) {
                //start row for month
                $monthName = get_month_name_from_number($i);
                $table .= "<tr>";                
                $table .= "<td class=\"tt-align-center\">" . $monthName . "</td>";

                foreach ($bill_to_names as $bill_to_name) {                    
                    //no data at all
                    if (empty($time_summary)) {
                        $table .= "<td class=\"tt-align-right\">N/A</td>";
                    //no data for this month
                    } elseif (!array_key_exists($i, $time_summary)) {
                        $table .= "<td class=\"tt-align-right\">N/A</td>";
                    //no data for this billto for this month
                    } elseif (array_key_exists($bill_to_name,$time_summary[$i])) {
                        $table .= "<td class=\"tt-align-right\">";
                        $table .= $time_summary[$i][$bill_to_name]['TimeWorked'] . " Worked<br/>";
                        $table .= $time_summary[$i][$bill_to_name]['TimeInvoiced'] . " Billed<br/>";
                        $table .= $time_summary[$i][$bill_to_name]['PercentTimeInvoiced'] . "% Billed<br/>";
                        $table .= "</td>";
                    //catch other
                    } else {
                        $table .= "<td class=\"tt-align-right\">N/A</td>";
                    }                 
                } //cycle through each bill to name for this month                
                
                //close row for this month
                $table .= "</td>";
            } //for each month until current month

            //close table
            $table .= "</table>";
            return $table;
        }

    }  //close class
} //close if class exists