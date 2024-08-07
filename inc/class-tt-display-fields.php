<?php
/**
 * Class Time_Tracker_Display_Fields
 *
 * Define how to display fields on front end
 * 
 * @since 3.0.13
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( !class_exists( 'Time_Tracker_Display_Fields' ) ) {


    /**
     * Class
     * 
     */
    class Time_Tracker_Display_Fields
    {

        public $taskid;
        public $task;
        public $project_select;
        public $client_select;
        public $work_type;
        public $due_date;
        public $status;
        public $date_added;
        public $time_logged_v_estimate;
        public $notes;
        public $timeid;
        public $start_time;
        public $end_time;
        public $invoice_details;
        public $follow_up;
        public $recurring_taskid;
        public $recurring_frequency;
        public $recurring_last_created;
        public $recurring_end_repeat;
        public $recurring_task_description;
        public $recurring_task_name;
        public $recurring_task_category;
        public $projectid;
        public $project_name;
        public $project_category;
        public $project_status;
        public $project_last_worked;
        public $project_due_date;
        public $project_date_started;
        public $project_details;
        public $client_email;
        public $client_phone;
        public $clientid;
        public $client_name;
        public $contact;
        public $client_bill_to;
        public $client_billing_rate;
        public $client_source;
        public $client_source_details;
        public $client_comments;
        public $client_date_added;


        /**
         * Constructor
         * 
         * @since 3.0.13
         */
        public function __construct() {
            $this->set_taskid();
            $this->set_task_description();
            $this->set_project_select();
            $this->set_client_select();
            $this->set_work_type();
            $this->set_due_date();
            $this->set_task_status();
            $this->set_date_added();
            $this->set_time_logged_v_estimate();
            $this->set_notes();
            $this->set_timeid();
            $this->set_start_time();
            $this->set_end_time();
            $this->set_invoice_details();
            $this->set_follow_up();
            $this->set_recurring_taskid();
            $this->set_recurring_frequency();
            $this->set_recurring_last_created();
            $this->set_recurring_end_repeat();
            $this->set_recurring_task_description();
            $this->set_recurring_task_name();
            $this->set_recurring_task_category();
            $this->set_projectid();
            $this->set_project_name();
            $this->set_project_category();
            $this->set_project_status();
            $this->set_project_last_worked();
            $this->set_project_due_date();
            $this->set_project_date_started();
            $this->set_project_details();
            $this->set_client_email();
            $this->set_client_phone();
            $this->set_clientid();
            $this->set_client_name();
            $this->set_contact();
            $this->set_client_bill_to();
            $this->set_client_billing_rate();
            $this->set_client_source();
            $this->set_client_source_details();
            $this->set_client_comments();
            $this->set_client_date_added();
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_taskid() {
            $this->taskid = [
                "fieldname" => "TaskID",
                "id" => "task-id",
                "editable" => false,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_task_description() {
            $this->task = [
                "fieldname" =>"TDescription",
                "id" => "task-description",
                "editable" => true,
                "edit-details" => [
                    "table" => "tt_task",
                    "ref-field" => "TaskID"
                ],
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_select() {
            $this->project_select = [
                "fieldname" => "ProjectID",
                "id" => "project-id",
                "editable" => true,
                "columnwidth" => "",
                "type" => "select",
                "select_options" => [
                    "title" => "project-with-id",
                    "data_type" => "text",
                    "options" => $this->get_project_select_options(),
                    "nullable" => true
                ],
                "class" => ""
            ];
        }

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_select() {
            $this->client_select = [
                "fieldname" => "ClientID",
                "id" => "client",
                "editable" => true,
                "columnwidth" => "",
                "type" => "select",
                "select_options" => [
                    "title" => "client-with-id",
                    "data_type" => "text",
                    "options" => $this->get_client_select_options()
                ],
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_work_type() {
            $this->work_type = [
                "fieldname" => "TCategory",
                "id" => "task-type",
                "editable" => false,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_due_date() {
            $this->due_date = [
                "fieldname" => "TDueDate",
                "id" => "due-date",
                "editable" => true,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_task_status() {
            $this->status =[
                "fieldname" => "TStatus",
                "id" => "task-status",
                "editable" => true,
                "edit-details" => [
                    "table" => "tt_task",
                    "ref-field" => "TaskID"
                ],
                "columnwidth" => "",
                "type" => "select",
                "select_options" => [
                    "title" => "task-status",
                    "data_type" => "text",
                    "options" => $this->get_task_status_options()
                ],
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_date_added() {
            $this->date_added = [
                "fieldname" => "TDateAdded",
                "id" => "date-added",
                "editable" => false,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_time_logged_v_estimate() {
            $this->time_logged_v_estimate = [
                "fieldname" => "TimeLoggedVsEstimate",
                "id" => "time-worked",
                "editable" => false,
                "columnwidth" => "",
                "type" => "long text",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_notes() {
            $this->notes = [
                "fieldname" => "TNotes",
                "id" => "task-notes",
                "editable" => true,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }



        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_timeid() {
            $this->timeid = [
                "fieldname" => "TimeID",
                "id" => "time-id",
                "editable" => false,
                "columnwidth" => "five",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_start_time() {
            $this->start_time = [
                "fieldname" => "StartTime",
                "id" => "start-time",
                "editable" => true,
                "columnwidth" => "",
                "type" => "date and time",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_end_time() {
            $this->end_time = [
                "fieldname" => "EndTime",
                "id" => "end-time",
                "editable" => true,
                "columnwidth" => "",
                "type" => "date and time",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_invoice_details() {
            $this->invoice_details = [
                "fieldname" => "",
                "id" => "invoice-details",
                "editable" => true,
                "columnwidth" => "",
                "type" => "widget-invoice",
                "class" => "tt-table tt-widget-table",
                "widget-data" => [
                    "Invoiced?" => [
                        "fieldname" => "Invoiced",
                        "id" => "invoiced",
                        "editable" => true,
                        "columnwidth" => "",
                        "type" => "text",
                        "class" => "tt-align-left"
                    ],
                    "Invoice Number" => [
                        "fieldname" => "InvoiceNumber",
                        "id" => "invoice-number",
                        "editable" => true,
                        "columnwidth" => "",
                        "type" => "text",
                        "class" => "tt-align-left"
                    ],
                    "Invoiced Time" => [
                        "fieldname" => "InvoicedTime",
                        "id" => "invoice-time",
                        "editable" => true,
                        "columnwidth" => "",
                        "type" => "text",
                        "class" => "tt-align-left"
                    ],
                    "Invoice Comments" => [
                        "fieldname" => "InvoiceComments",
                        "id" => "invoice-comments",
                        "editable" => true,
                        "columnwidth" => "",
                        "type" => "long text",
                        "class" => "tt-align-left"
                    ]
                ]
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_follow_up() {
            $this->follow_up = [
                "fieldname" => "FollowUp",
                "id" => "follow-up",
                "editable" => true,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_taskid() {
            $this->recurring_taskid = [
                "fieldname" => "RecurringTaskID",
                "id" => "recurring-task-id",
                "editable" => false,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_frequency() {
            $this->recurring_frequency = [
                "fieldname" => "Frequency",
                "id" => "frequency",
                "editable" => true,
                "columnwidth" => "",
                "type" => "select",
                "select_options" => [
                    "title" => "recurring-frequency",
                    "data_type" => "text",
                    "options" => [
                        "Weekly",
                        "Monthly",
                        "Yearly"
                    ]
                ],
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_last_created() {
            $this->recurring_last_created = [
                "fieldname" => "LastCreated",
                "id" => "last-created",
                "editable" => false,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_end_repeat() {
            $this->recurring_end_repeat = [
                "fieldname" => "EndRepeat",
                "id" => "end-repeat",
                "editable" => true,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_task_description() {
            $this->recurring_task_description = [
                "fieldname" => "RTDescription",
                "id" => "recurring-task-description",
                "editable" => true,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_task_name() {
            $this->recurring_task_name = [
                "fieldname" =>"RTName",
                "id" => "recurring-task-name",
                "editable" => true,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_recurring_task_category() {
            $this->recurring_task_category = [
                "fieldname" => "RTCategory",
                "id" => "task-category",
                "editable" => false,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_projectid() {
            $this->projectid = [
                "fieldname" => "ProjectID",
                "id" => "project-id",
                "editable" => false,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_name() {
            $this->project_name = [
                "fieldname" => "PName",
                "id" => "project-name",
                "editable" => true,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_category() {
            $this->project_category = [
                "fieldname" => "PCategory",
                "id" => "project-category",
                "editable" => false,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_status() {
            $this->project_status = [
                "fieldname" => "PStatus",
                "id" => "project-status",
                "editable" => true,
                "columnwidth" => "",
                "type" => "text",
                "class" => ""
            ];
        }

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_last_worked() {
            $this->project_last_worked = [
                "fieldname" => "LastEntry",
                "id" => "project-last-worked",
                "editable" => false,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_due_date() {
            $this->project_due_date = [
                "fieldname" => "PDueDate",
                "id" => "project-due-date",
                "editable" => false,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_date_started() {
            $this->project_date_started = [
                "fieldname" =>"PDateStarted",
                "id" => "project-date-started",
                "editable" => false,
                "columnwidth" => "",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }
        

        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_project_details() {
            $this->project_details = [
                "fieldname" => "PDetails",
                "id" => "end-repeat",
                "editable" => true,
                "columnwidth" => "",
                "type" => "long text",
                "class" => ""
            ];
        }

        
        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_email() {
            $this->client_email = [
                "fieldname" => "Email",
                "id" => "contact-email",
                "editable" => true,
                "columnwidth" => "ten",
                "type" => "email",
                "class" => ""
            ];
        }

        
        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_phone() {
            $this->client_phone = [
                "fieldname" => "Phone",
                "id" => "contact-phone",
                "editable" => true,
                "columnwidth" => "ten",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_clientid() {
            $this->clientid = [
                "fieldname" => "ClientID",
                "id" => "client-id",
                "editable" => false,
                "columnwidth" => "five",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_name() {
            $this->client_name = [
                "fieldname" => "Company",
                "id" => "company-name",
                "editable" => false,
                "columnwidth" => "ten",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_contact() {
            $this->contact = [
                "fieldname" => "Contact",
                "id" => "contact-name",
                "editable" => true,
                "columnwidth" => "ten",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_bill_to() {
            $this->client_bill_to = [
                "fieldname" => "BillTo",
                "id" => "bill-to",
                "editable" => false,
                "columnwidth" => "ten",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_billing_rate() {
            $this->client_billing_rate = [
                "fieldname" => "BillingRate",
                "id" => "billing-rate",
                "editable" => true,
                "columnwidth" => "five",
                "type" => "integer",
                "class" => "tt-align-right"
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_source() {
            $this->client_source = [
                "fieldname" => "Source",
                "id" => "source",
                "editable" => false,
                "columnwidth" => "ten",
                "type" => "text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_source_details() {
            $this->client_source_details = [
                "fieldname" => "SourceDetails",
                "id" => "source-details",
                "editable" => false,
                "columnwidth" => "ten",
                "type" => "long text",
                "class" => ""
            ];
        }


        /**
         * Define field display
         * 
         * @since 3.0.13
         */
        private function set_client_comments() {
            $this->client_comments = [
                "fieldname" => "CComments",
                "id" => "client-comments",
                "editable" => true,
                "columnwidth" => "fifteen",
                "type" => "long text",
                "class" => ""
            ];
        }

        
        /**
         * Define field display
         * 
         * @since 3.0.13
         */
         private function set_client_date_added() {
            $this->client_date_added = [
                "fieldname" => "DateAdded",
                "id" => "date-added",
                "editable" => false,
                "columnwidth" => "five",
                "type" => "date",
                "class" => "tt-align-right"
            ];
        }




        /****HELPER FUNCITONS ******/

        /**
         * Get client options for a dropdown (includes client name and ID)
         * 
         * @since 3.0.13
         * 
         * @return array List of clients, with IDs as strings.
         */
        private function get_client_select_options() {
            $clients = tt_get_clients();
            $arr = [];
            if ($clients) {
                foreach ($clients as $client) {
                    array_push($arr, ["id" => $client->ClientID, "display" => $client->Company]);
                }
            }
            return $arr;
        }


        /**
         * Get project options for a dropdown (includes project name and ID)
         * 
         * @since 3.0.13
         * 
         * @return array List of projects, with IDs as strings.
         */
        private function get_project_select_options() {
            $projects = tt_get_projects();
            $arr = [];
            if ($projects) {
                foreach ($projects as $project) {
                    array_push($arr, ["id" => $project->ProjectID, "display" => $project->PName]);
                }
            }
            return $arr;
        }

        /**
         * Get task status options - user defined
         * 
         * @since 3.0.13
         * 
         * @return array List of task statuses as strings.
         */
        private function get_task_status_options() {
            $task_status = tt_get_user_options("time_tracker_categories", "task_status");
            if ($task_status != "" && $task_status != null) {
                return explode(chr(13), $task_status);
            } else {
                return [
                    "New",
                    "In Process",
                    "Waiting Client",
                    "Complete",
                    "Canceled"
                ];
            }
        }

    } //close class

} //close if class exists