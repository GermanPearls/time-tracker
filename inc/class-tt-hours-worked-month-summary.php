<?php
/**
 * Time Tracker Class_Hours_Worked_Month_Summary 
 *
 * Takes the data from the hours worked class (query) and summarizes the current month's data for display
 * 
 * @since 1.0.0
 * @since 3.0.12 added group by today and value estimates for today and current work week
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );


/**
 * If class doesn't already exist
 * 
 * @since 1.0.0
 */
if ( !class_exists( 'Class_Hours_Worked_Month_Summary' ) ) {


    /**
     * If class doesn't already exist
     * 
     * @since 1.0.0
     */
    class Class_Hours_Worked_Month_Summary extends Class_Hours_Worked_Detail
    {

        
        /**
         * Constructor
         * 
         * @since 1.0.0
         */
        public function __construct() {
            parent::__construct();
            $hours_worked = $this->hours_worked;
        }


        /**
         * Reorganize data - Group by Month, then Week, then Bill To
         * 
         * @since 1.0.0
         * @since 3.0.12 add group by today
         * 
         * @return array Array of hours worked, grouped by month-week-bill to.
         */        
        private function groupDataByMonthWeekAndBillTo() {
            $grouped_time = array();
            if (!empty($this->hours_worked)) {
                foreach ($this->hours_worked as $item) {
                    //only summarize current year and this month or week
                    $workyear = sanitize_text_field($item['WorkYear']);
                    $workmonth = sanitize_text_field($item['WorkMonth']);
                    $workweek = sanitize_text_field($item['WorkWeek']);
                    $thisweek = sanitize_text_field($item['ThisWeek']);
                    $workday = new \DateTime(sanitize_text_field($item['StartTime']));
                    $billto = sanitize_text_field($item['BillTo']);

                    if ( ($workyear == date('Y')) && ( ($workmonth == date('n')) || ($workweek == $thisweek) ) ) {
                        //get month and week of current item
                        $workmonth = $workmonth;
                        $workweek = $workweek;
                        
                        //get bill to of current item
                        if ($billto == "") {
                            $billto = "Unknown";
                        } else {
                            $billto = $billto;
                        }

                        if (date_format($workday, "m/d/y") == date("m/d/y")) {
                            $grouped_time['Today'][$billto][] = $item;
                        }
                        if ($workweek == $thisweek) {
                            $grouped_time['This Week'][$billto][] = $item;
                        }
                        if ($workmonth == date('n')) {
                            $grouped_time['This Month'][$billto][] = $item;
                        }
                        
                    } //if work is current year                
                } //for each piece of data from database
            }  //if no data
            return $grouped_time;
        }


        /**
         * Calculate running totals by Month, then Week, then Bill To
         * 
         * @since 1.0.0
         * 
         * @return array Time totaled by month-week-bill to.
         */ 
        private function totalDataByMonthWeekAndBillTo() {
            $grouped_time = $this->groupDataByMonthWeekAndBillTo();
            $totaled_time = array();
            if (!empty($grouped_time)) {
                foreach ($grouped_time as $timeunit => $time_array) {
                    $timeunithoursworked = 0.0;
                    $timeunithoursinvoiced = 0.0;
                    $timeunitpending = 0.0;
                    $timeunitvalueinvoiced = 0.0;
                    $timeunitpendingvalue = 0;
                    foreach ($time_array as $billto => $billto_array) {                    
                        $totalhours = 0.0;
                        $totalminutes = 0.0;
                        $billedtime = 0.0;
                        $pendinghours = 0.0;
                        $pendingminutes = 0.0;
                        $valueinvoiced = 0.0;
                        $pendingvalue = 0.0;
                        foreach ($billto_array as $item) {
                            $totalminutes = $totalminutes + $item['MinutesWorked'];
                            $totalhours = $totalhours + $item['HoursWorked'];
                            if ( ($item['Invoiced']=="") || ($item['Invoiced']==null) )  {
                                if (is_null($item['BilledTime'])) {
                                    $pendinghours = $pendinghours + $item['HoursWorked'];
                                    $pendingminutes = $pendingminutes + $item['MinutesWorked'];
                                    $pendingvalue = $pendingvalue + ($item['HoursWorked'] * $item['BillingRate']) + ($item['MinutesWorked'] / 60 * $item['BillingRate']);
                                }
                                elseif ($item['BilledTime'] >= 0) {
                                    $billedtime = $billedtime + $item['BilledTime'];
                                    $valueinvoiced = $valueinvoiced + ($item['BilledTime'] * $item['BillingRate']);
                                } else {
                                    $pendinghours = $pendinghours + $item['HoursWorked'];
                                    $pendingminutes = $pendingminutes + $item['MinutesWorked'];
                                    $pendingvalue = $pendingvalue + ($item['HoursWorked'] * $item['BillingRate']) + ($item['MinutesWorked'] / 60 * $item['BillingRate']);
                                }
                            } else {
                                $billedtime = $billedtime + $item['BilledTime'];
                                $valueinvoiced = $valueinvoiced + round(($item['BilledTime'] * $item['BillingRate']),2);
                            }
                        } //total hours from each detailed record inside billto name array
                        //save the total from the last bill to in a new array
                        $decimal_time_worked = tt_convert_to_decimal_time($totalhours, $totalminutes);
                        $totaled_time[$timeunit][$billto]['TimeWorked'] = round($decimal_time_worked,1);
                        $totaled_time[$timeunit][$billto]['TimeInvoiced'] = round($billedtime,1);
                        $totaled_time[$timeunit][$billto]['ValueInvoiced'] = round($valueinvoiced, 0);
                        if ($decimal_time_worked == 0) {
                            $totaled_time[$timeunit][$billto]['PercentTimeInvoiced'] = 0;
                        } else {
                            $totaled_time[$timeunit][$billto]['PercentTimeInvoiced'] = round($billedtime/$decimal_time_worked*100,0);
                        }
                        $decimal_time_pending = tt_convert_to_decimal_time($pendinghours, $pendingminutes);
                        $totaled_time[$timeunit][$billto]['PendingTime'] = round($decimal_time_pending,1);
                        $totaled_time[$timeunit][$billto]['PendingValue'] = round($pendingvalue, 0);
                        $totaled_time[$timeunit][$billto]['Billable'] = $item['Billable'];
                        //cumulative total for month (of all bill tos)
                        $timeunithoursworked = $timeunithoursworked + $decimal_time_worked;
                        $timeunithoursinvoiced = $timeunithoursinvoiced + $billedtime;
                        $timeunitvalueinvoiced = $timeunitvalueinvoiced + $valueinvoiced;
                        $timeunitpendingvalue = $timeunitpendingvalue + $pendingvalue;
                        if ($timeunithoursworked == 0) {
                            $timeunitpercenthoursinvoiced = 0;
                        } else {
                            $timeunitpercenthoursinvoiced = round($timeunithoursinvoiced/$timeunithoursworked*100,0);
                        }
                        //only include billable clients in pending time
                        if ( $item['Billable'] == 1) {
                            $timeunitpending = $timeunitpending + $decimal_time_pending;    
                        }
                    } //loop bill to name inside this month
                    $totaled_time[$timeunit]['Total']['TimeWorked'] = $timeunithoursworked;
                    $totaled_time[$timeunit]['Total']['TimeInvoiced'] = $timeunithoursinvoiced;
                    $totaled_time[$timeunit]['Total']['PercentTimeInvoiced'] = $timeunitpercenthoursinvoiced;
                    $totaled_time[$timeunit]['Total']['PendingTime'] = $timeunitpending;
                    $totaled_time[$timeunit]['Total']['PendingValue'] = $timeunitpendingvalue;
                    $totaled_time[$timeunit]['Total']['Billable'] = 1;
                    $totaled_time[$timeunit]['Total']['ValueInvoiced'] = $timeunitvalueinvoiced;
                } //loop through each month
            } //if not empty
            return $totaled_time;
        }


        /**
         * Summarize all Bill To Names included
         * 
         * @since 1.0.0
         * 
         * @return array List of bill-to names.
         */ 
        private function listBillToNames($dataArray) {
            $bill_to_names = array();
            if (!empty($dataArray)) {
                foreach ($dataArray as $timeunit => $billToArray) {
                    foreach ($billToArray as $billToName => $detail) {
                        if ( ($billToName != 'Total') && (! (in_array($billToName, $bill_to_names))) ) {
                            $bill_to_names[] = $billToName;
                        }
                    } //for each billto group
                } //for each month array
            } //if array isn't empty
            //put in alphabetical order
            sort($bill_to_names);
            //make sure Total appears last in the array
            $bill_to_names[] = 'Total';
            return $bill_to_names;
        }


        /**
         * Create HTML display for front end display
         * 
         * @since 1.0.0
         * @since 3.0.12 include today and current week value estimates in current month dashboard table
         * 
         * @return string Html table summarizing data.
         */ 
        public function createHTMLTable() {
            $time_summary = $this->totalDataByMonthWeekAndBillTo();
            $bill_to_names = $this->listBillToNames($time_summary);
            $columncount = count($bill_to_names) + 1;
            $table = "<h2>" . date('F') . " " . date('Y') . " Hours Worked</h2>";

            //open table
            $table .= "<table class=\"tt-table monthly-summary-table tt-even-columns-" . esc_attr($columncount) . "\">";

            //header row
            $table .= "<tr class=\"tt-header-row\">";
            $table .= "<th class=\"tt-bold-font tt-align-center\"></th>";
            foreach ($bill_to_names as $bill_to_name) {
                $table .= "<th class=\"tt-bold-font tt-align-center\">" . esc_textarea($bill_to_name) . "</th>";
            }            
            $table .= "</tr>";

            //add data to table                
            
            //row - current week hours worked
            $table .= "<tr><td class=\"tt-align-center\">Current Week Hours Worked</td>";
            foreach ($bill_to_names as $bill_to_name) {        
                //no data for at all
                if (empty($time_summary)) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                //no data for this week
                } elseif ( ! ( array_key_exists("This Week", $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                //no data for this bill to for this week
                } elseif ( array_key_exists($bill_to_name, $time_summary['This Week']) ) {
                    $table .= "<td class=\"tt-align-right\">" . esc_textarea($time_summary['This Week'][$bill_to_name]['TimeWorked']) . "</td>";
                //cath other
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }
            }
            $table .= "</tr>";

            //row - current month hours worked
            $table .= "<tr><td class=\"tt-align-center\">" . date('F') . " " . date('Y') . " Hours Worked</td>";
            foreach ($bill_to_names as $bill_to_name) {      
                //no data at all
                if (empty($time_summary)) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                //no data for this month
                } elseif ( ! ( array_key_exists("This Month", $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                //no data for this bill to
                } elseif (array_key_exists($bill_to_name, $time_summary['This Month'])) {
                    $table .= "<td class=\"tt-align-right\">" . esc_textarea($time_summary['This Month'][$bill_to_name]['TimeWorked']) . "</td>";
                //catch other
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }
            }
            $table .= "</tr>";

            //row - pending time
            $table .= "<tr><td class=\"tt-align-center\">" . date('F') . " " . date('Y') . " Hours Pending</td>";
            foreach ($bill_to_names as $bill_to_name) {        
                if ( (empty($time_summary)) or (!array_key_exists('This Month', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['This Month']) && ($time_summary['This Month'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right\">" . esc_textarea($time_summary['This Month'][$bill_to_name]['PendingTime']) . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }            
            }
            $table .= "</tr>";

            //row - invoiced time
            $table .= "<tr><td class=\"tt-align-center\">" . date('F') . " " . date('Y') . " Hours Invoiced</td>";
            foreach ($bill_to_names as $bill_to_name) {        
                if ( (empty($time_summary)) or (!array_key_exists('This Month', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['This Month']) && ($time_summary['This Month'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right\">" . esc_textarea($time_summary['This Month'][$bill_to_name]['TimeInvoiced']) . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }
            }
            $table .= "</tr>"; 
            
            //row - today value estimate
            $curr_sign = tt_get_currency_type();
            $table .= "<tr><td class=\"tt-align-center tt-border-top-divider\">" . "Today's  " . $curr_sign . " Estimate</td>";
            foreach ($bill_to_names as $bill_to_name) {
                if ( (empty($time_summary)) or (!array_key_exists('Today', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['Today']) && ($time_summary['Today'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">" . $curr_sign . " " . number_format($time_summary['Today'][$bill_to_name]['PendingValue'] + $time_summary['Today'][$bill_to_name]['ValueInvoiced'], 0, '.', ',') . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">N/A</td>";
                }
            }
            $table .= "</tr>";
            
            
            //row - current week value estimate
            $curr_sign = tt_get_currency_type();
            $table .= "<tr><td class=\"tt-align-center\">" . "Current Week's  " . $curr_sign . " Estimate</td>";
            foreach ($bill_to_names as $bill_to_name) {
                if ( (empty($time_summary)) or (!array_key_exists('This Week', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['This Week']) && ($time_summary['This Week'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right\">" . $curr_sign . " " . number_format($time_summary['This Week'][$bill_to_name]['PendingValue'] + $time_summary['This Week'][$bill_to_name]['ValueInvoiced'], 0, '.', ',') . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }
            }
            $table .= "</tr>";


            //row - pending value estimate
            $curr_sign = tt_get_currency_type();
            $table .= "<tr><td class=\"tt-align-center tt-border-top-divider\">" . date('F') . " " . date('Y') . " " . $curr_sign . " Pending (Estimate)</td>";
            foreach ($bill_to_names as $bill_to_name) {
                if ( (empty($time_summary)) or (!array_key_exists('This Month', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['This Month']) && ($time_summary['This Month'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">" . $curr_sign . " " . number_format($time_summary['This Month'][$bill_to_name]['PendingValue'], 0, '.', ',') . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right tt-border-top-divider\">N/A</td>";
                }
            }
            $table .= "</tr>";

            //row - billed estimate
            $table .= "<tr><td class=\"tt-align-center\">" . date('F') . " " . date('Y') . " " . $curr_sign . " Invoiced (Estimate)</td>";
            foreach ($bill_to_names as $bill_to_name) {
                if ( (empty($time_summary)) or (!array_key_exists('This Month', $time_summary)) ) {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                } elseif (array_key_exists($bill_to_name, $time_summary['This Month']) && ($time_summary['This Month'][$bill_to_name]['Billable'] == 1)) {
                    $table .= "<td class=\"tt-align-right\">" . $curr_sign . " " . number_format($time_summary['This Month'][$bill_to_name]['ValueInvoiced'], 0, '.', ',') . "</td>";
                } else {
                    $table .= "<td class=\"tt-align-right\">N/A</td>";
                }
            }
            $table .= "</tr>";

            //close table
            $table .= "</table>";
            return $table;
        }

    } //close out class

}  //close out if class exists