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
    
    /**
     *register new settings in the database
     *
     **/
    register_setting('time_tracker_categories_group',    //option group
        'time_tracker_categories'                  //option name (in db)
    );
    register_setting('time_tracker_style_group',  //option group
        'time_tracker_style'                //option name
    );
    add_option('time_tracker_sql_result', array('result'=>'success','updated'=>$now, 'error'=>'N/A', 'file'=>'N/A', 'function'=>'N/A'));   



    /**
     *register new sections on different pages
     *
     **/
    add_settings_section(
        'time_tracker_categories_section',             //section id
        'Category Options',         //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_section_callback',  //callable callback
        'time-tracker'              //page
    );

    add_settings_section(
        'time_tracker_style_section',       //section id
        'Style Options',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_style_section_callback',      //callback
        'time-tracker-style'        //page
    );



    /*
     *register a new field in the tt_categories_section on the main menu page
     *
     */
    add_settings_field(
        'time_tracker_categories[default_rate]',
        'Default Billing Rate',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_default_rate_callback',
        'time-tracker',
        'time_tracker_categories_section'
    );

    add_settings_field(
        'time_tracker_categories[default_currency]',
        'Default Currency',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_default_currency_callback',
        'time-tracker',
        'time_tracker_categories_section'
    );

    add_settings_field(
        'time_tracker_categories[task-status]',
        'Task Status Options',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_task_status_callback',
        'time-tracker',
        'time_tracker_categories_section'
    );

    add_settings_field(
        'time_tracker_categories[bill_to_names]',    //field id
        'Bill To Names',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_bill_to_names_callback',   //callable callback
        'time-tracker',               //page
        'time_tracker_categories_section'       //section
    );
    
    add_settings_field(
        'time_tracker_categories[work_categories]',    //id
        'Work Categories',            //title
        'Logically_Tech\Time_Tracker\Admin\tt_categories_work_categories_callback',   //callable callback
        'time-tracker',               //page
        'time_tracker_categories_section'       //section
    );

    add_settings_field(
        'time_tracker_categories[client_categories]',
        'Client Categories',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_client_categories_callback',
        'time-tracker',
        'time_tracker_categories_section'
    );

    add_settings_field(
        'time_tracker_categories[client_sub_categories]',
        'Client Sub-Categories',
        'Logically_Tech\Time_Tracker\Admin\tt_categories_client_sub_categories_callback',
        'time-tracker',
        'time_tracker_categories_section'
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
        'time_tracker_style_section'
    );
    
    add_settings_field(
        'time_tracker_style[css][buttons][background][normal]',
        'Button Background Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_background_callback',
        'time-tracker-style',
        'time_tracker_style_section'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][text][normal]',
        'Button Text Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_text_callback',
        'time-tracker-style',
        'time_tracker_style_section'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][background][hover]',
        'Button Hover Background Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_background_hover_callback',
        'time-tracker-style',
        'time_tracker_style_section'
    );

    add_settings_field(
        'time_tracker_style[css][buttons][text][hover]',
        'Button Hover Text Color',
        'Logically_Tech\Time_Tracker\Admin\tt_css_buttons_text_hover_callback',
        'time-tracker-style',
        'time_tracker_style_section'
    );
}
