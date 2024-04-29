<?php
/**
 * Class Time_Tracker_After_Form_Data_Saved
 *
 * Action to happen after form data saved to database (if necessary)
 * Either:
 *  - go back to TT homepage
 *  - stay on same page and filter data (if it's a filter form)
 *  - go to time entry form to start tracking time (if 'start working' selected) 
 * 
 * @since 1.0.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc;

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 *
 * @since 1.0.0
 */
if ( ! class_exists('Time_Tracker_After_Form_Data_Saved') ) {
    
    
    /**
     * Main Plugin Class
     * 
     * @since 1.0.0
     */
    class Time_Tracker_After_Form_Data_Saved {

    
        /**
         * Constructor
         * 
         * @since 1.0.0
        */
        public function _construct() {
        }


        /**
         * Decide what to do after data saved to db (if necessary)
         * 
         * @since 1.0.0
        */
        public function after_save() {
            if (TT_PLUGIN_FORM_TYPE == "WPF") {
                ?>
                <script type='text/javascript'>
                    document.addEventListener('DOMContentLoaded', function () {  
                        jQuery(document).find("form[id^='wpforms-form']").each(function(i, form) {
                            if (window.location.pathname.includes('time-tracker')) {

                                var redirect = function() { location = <?php $home = "'" . TT_HOME . "'"; echo $home; ?>; };
                                var submitandwork = function() { tt_start_timer_for_new_task(); };
                                var filtertime = function(e) { tt_filter_time_log(e); };

                                jQuery(form).on('wpformsAjaxSubmitSuccess', redirect);
                                jQuery(form).find('button').each(function(i, btn) {
                                    if (btn.name == 'wpforms[submit]' && btn.innerHTML == 'Search') {
                                        jQuery(form).off('wpformsAjaxSubmitSuccess', redirect);
                                        jQuery(form).on('wpformsAjaxSubmitSuccess', filtertime);
                                    } else {
                                        if (btn.name == 'wpforms[submit]' && btn.innerHTML == 'Start Working') {
                                            jQuery(btn).on('click', function() {
                                                jQuery(form).off('wpformsAjaxSubmitSuccess', redirect);
                                                jQuery(form).on('wpformsAjaxSubmitSuccess', submitandwork);
                                            })
                                        }
                                    }
                                });
                                
                            };
                        });
                    }, false);
                
                </script>
                <?php

            } elseif (TT_PLUGIN_FORM_TYPE == "CF7") {
                ?>
                <script type='text/javascript'>
        
                    document.addEventListener('DOMContentLoaded', function () {  
                    
                        if (window.location.pathname.includes('time-tracker')) {

                            document.addEventListener( 'wpcf7mailsent', function (event) {

                                var formtype = "";
                                var startworking = false;
                                //var client = "";
                                //var taskdesc = "";

                                for (var i=0; i < event.detail.inputs.length; i++) {
                                    if (event.detail.inputs[i].name == 'form-type') {
                                        formtype = event.detail.inputs[i].value;
                                    } else {
                                        if ( (event.detail.inputs[i].name == 'what-next') && (event.detail.inputs[i].value == 'StartWorking') ) {
                                            startworking = true;
                                        //} else {
                                            //if (event.detail.inputs[i].name == 'client-name') {
                                            //    client = event.detail.inputs[i].value;
                                            //} else {
                                            //    if (event.detail.inputs[i].name == 'task-description') {
                                            //        taskdesc = event.detail.inputs[i].value;
                                            //    }
                                            //}
                                        }
                                    }
                                }

                                if (formtype == 'filter') {
                                    tt_filter_time_log(event);
                                
                                } else {
                                    if (startworking) {
                                        tt_start_timer_for_new_task();
                                    } else {
                                        location = <?php $home = "'" . TT_HOME . "'"; echo $home; ?>;
                                    }
                                }
                        
                            }, false );  //end wpcf7submit event listener
                        
                        }; //if on tt page
                    
                    }, false );  //end domcontentloaded event listener
                </script>
                <?php
                }   //if wpcf7
            }   //after save function
    }  //close class
}   //if class does not exist

$aftersave = new Time_Tracker_After_Form_Data_Saved();
add_action( 'wp_footer', array($aftersave, 'after_save') );