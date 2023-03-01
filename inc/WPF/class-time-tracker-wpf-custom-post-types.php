<?php
/**
 * Class Time_Tracker_WPF_Custom_Post_Types
 *
 * Select options can be generated in WPForms using custom post types
 * Specific to WPF installations
 * 
 * @since 3.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Inc\WPF;


/**
 * Check if class exists
 * 
 */
if ( ! class_exists('Time_Tracker_WPF_Custom_Post_Types') ) {

    class Time_Tracker_WPF_Custom_Post_Types {

        /**
         * Create custom post types required for Time Tracker Select Fields
         * 
         */
        public function tt_create_custom_post_types() {
            $this->create_custom_post_type("bill-to", "Bill To Names", "Bill To");
            $this->create_custom_post_type("work-categories", "Work Categories", "Work Category");
            $this->create_custom_post_type("client-categories", "Client Categories", "Client Category");
            $this->create_custom_post_type("client-sub-categories", "Client Sub-Categories", "Client Sub-Category");

            $this->create_custom_post_type("clients", "Clients", "Client");
            $this->create_custom_post_type("projects", "Projects", "Project");
            $this->create_custom_post_type("tasks", "Tasks", "Task");
        }


        /**
         * WP Forms dynamic dropdown options are set from custom post types
         * This function creates the custom post types needed for the dynamic dropdowns
         * Needs to run on the init hook!!
         * 
         */
        private function create_custom_post_type($nm, $nm_public, $nm_singular) {
            if (!post_type_exists($nm)) {
                register_post_type($nm,
                //options
                    array(
                        'labels' => array(
                            'name' => __( $nm_public ),
                            'singular_name' => __( $nm_singular )
                        ),
                        'public' => false
                    )    
                );
            }
        }





    }   //end class

}   //class exists
