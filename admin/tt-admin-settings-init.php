<?php
/**
 * Time Tracker Plugin Settings
 *
 * Initialize settings for Time Tracker Plugin
 * Ref: https://developer.wordpress.org/plugins/settings/using-settings-api/
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Admin;


function tt_admin_settings_init() {


    $now = date('m-d-Y g:i A');


    /*
     *register new settings in the database
     *
     */
    register_setting('time-tracker',    //option group
        'time-tracker'                  //option name
    );
    register_setting('time_tracker_style',  //option group
        'time_tracker_style'                //option name
    );
    add_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now, 'error'=>'N/A', 'file'=>'N/A', 'function'=>'N/A'));



    /*
     *register new sections on different pages
     *
     */
    add_settings_section(
        'time-tracker',             //id
        'Category Options',         //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_section_callback',  //callable callback
        'time-tracker'              //page
    );

    add_settings_section(
        'time-tracker-style',       //section id
        'Style Options',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_style_section_callback',      //callback
        'time-tracker-style'        //page
    );



    /*
     *register a new field in the tt_categories_section on the main menu page
     *
     */
    add_settings_field(
        'time-tracker[bill-to-names]',    //id
        'Bill To Names',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_bill_to_names_callback',   //callable callback
        'time-tracker',               //page
        'time-tracker'       //section
    );
    
    add_settings_field(
        'time-tracker[work-categories]',    //id
        'Work Categories',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_work_categories_callback',   //callable callback
        'time-tracker',               //page
        'time-tracker'       //section
    );

    add_settings_field(
        'time-tracker[client-categories]',
        'Client Categories',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_client_categories_callback',
        'time-tracker',
        'time-tracker'
    );

    add_settings_field(
        'time-tracker[client-sub-categories]',
        'Client Sub-Categories',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_client_sub_categories_callback',
        'time-tracker',
        'time-tracker'
    );



    /*
     *register a new section in the tt_categories_section on the main menu page
     *
     */    
    add_settings_field(
        'time_tracker_style[css][buttons][override]',
        'Override Theme Button Colors',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_override_callback',
        'time-tracker-style',
        'time-tracker-style'
    );
    
    add_settings_field(
        'time_tracker_style[css][buttons][background][normal]',
        'Button Background Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_background_callback',
        'time-tracker-style',
        'time-tracker-style'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][text][normal]',
        'Button Text Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_text_callback',
        'time-tracker-style',
        'time-tracker-style'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][background][hover]',
        'Button Hover Background Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_background_hover_callback',
        'time-tracker-style',
        'time-tracker-style'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][text][hover]',
        'Button Hover Text Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_text_hover_callback',
        'time-tracker-style',
        'time-tracker-style'
    );
}