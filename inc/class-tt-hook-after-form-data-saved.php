<?php
/**
 * Class Time_Tracker_After_Form_Data_Saved
 *
 * Action to happen after form data saved to database (if necessary)
 * Either go back to TT homepage or stay on same page and filter data if it's a filter form
 * 
 * @since 1.0
 * 
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

/**
 * If class doesn't already exist
 * 
 */
if ( ! class_exists('Time_Tracker_After_Form_Data_Saved') ) {
    
    
    /**
     * Main Plugin Class
     * 
     */
    class Time_Tracker_After_Form_Data_Saved {

    
        /**
         * Constructor
         * 
        */
        public function _construct() {

        }


        /**
         * Decide what to do after data saved to db (if necessary)
         * 
        */
        public function after_save() {
            ?>
            <script type='text/javascript'>
    
                document.addEventListener('DOMContentLoaded', function () {  //make sure doc is done loading before looking for element
                
                    document.addEventListener( 'wpcf7mailsent', function (event) {
                    
                        var str = window.location.pathname;
                        var tthome = document.location.origin + '/time-tracker';
                        var formtype = "";

                        for (var i=0; i < event.detail.inputs.length; i++) {
                            if (event.detail.inputs[i].name == 'form-type') {
                                var formtype = event.detail.inputs[i].value;
                            }
                        }

                        if (formtype == 'filter') {
                            tt_filter_time_log(event);
                        
                        //if it's a time tracker form submission, go back to tt homepage after submit
                        } else if ( str.includes('time-tracker') ) {
                            location = tthome;
                        }
                    
                    }, false );  //end wpcf7submit event listener
                
                });  //end domcontentloaded event listener
            </script>
            <?php
        }

    }  //close class
}

$aftersave = new Time_Tracker_After_Form_Data_Saved();

add_action( 'wp_footer', [$aftersave, 'after_save'] );