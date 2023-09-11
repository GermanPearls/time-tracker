<?php
/**
 * Time Tracker Plugin Settings Callback Functions
 *
 * Define callback functions for settings for Time Tracker Plugin
 * Ref: https://developer.wordpress.org/plugins/settings/using-settings-api/
 * 
 * 
 */

namespace Logically_Tech\Time_Tracker\Admin;

 
/**
 * Settings Section - Categories
 * Callback Function
 */
function tt_categories_section_callback() {
}


/**
 * Settings Section - Style
 * Callback Function
 */
function tt_style_section_callback() {
}


/**
 * Settings Field - Default Rate
 * Callback Function
 */
function tt_categories_default_rate_callback() {
    //get the value if it's already been saved
    $dr = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "default_rate")));

    //display on menu page
    ?>
    <div class="tt-indent">
    <input type="text" id="tt-default-rate" name="time_tracker_categories[default_rate]" rows=1 cols=20 class="tt-options-form" form="tt-options"
    <?php
    if (isset($dr)) {
        if ($dr != null && $dr != "") {
            echo " value=" . intval($dr);
        }
    }
    ?>><span class="tt-options-form">Enter a default hourly billing rate. (Enter whole number only.)</span></div>
    <hr>
    <?php
}

/**
 * Settings Field - Default Currency
 * Callback function
 */
function tt_categories_default_currency_callback() {
    //get the value if it's already been saved
    $cs = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "currency_sign")));

    //display on menu page
    ?>
    <div class="tt-indent">
    <input type="text" id="tt-currency-sign" name="time_tracker_categories[currency_sign]" rows=1 cols=20 class="tt-options-form" form="tt-options"
    <?php
    if (isset($cs)) {
        if ($cs != null && $cs != "") {
            echo " value=" . esc_html($cs);
        }
    }
    ?>><span class="tt-options-form">Enter currency sign.</span></div>
    <hr>
    <?php
}


/**
 * Settings Field - Task Status Options
 * Callback function
 */
function tt_categories_task_status_callback() {
    //default values
    $str = 'New
In Process
Waiting Client
Complete
Canceled';
    //get the value if it's already been saved to db
    $ts = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "task_status")));
    if (isset( $ts )) {
        if ($ts != "" && $ts != null) {
            $str = esc_html($ts);                
        }
    }

    //display on admin page
    ?>
    <div class="tt-indent">
        <textarea id="tt-task-status" name="time_tracker_categories[task_status]" rows="7" cols="30" class="tt-options-form" form="tt-options"><?php 
        echo $str;
        ?></textarea><span class="tt-options-form">List the statuses you'd like to use to keep track of your tasks.<br>
        Examples: New, In Process, Waiting Client, Complete, Canceled<br>
        Enter one status on each line.</span></div>
        <hr>
    <?php
}



/**
 * Settings Field - Bill to Names
 * Callback Function
 */
function tt_categories_bill_to_names_callback() {
    //get the value if it's already been entered
    $btn = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "bill_to_names")));
    $str = "";
    if (isset( $btn )) {
        $str = esc_html($btn);
    }

    //display on menu page
    ?>
    <div class="tt-indent">
    <textarea id="tt-bill-to" name="time_tracker_categories[bill_to_names]" rows="5" cols="30" class="tt-options-form" form="tt-options"><?php
    echo $str;
    ?></textarea><span class="tt-options-form">Do you always bill directly to the client? Or do white-label services where you bill to a third party?<br>
    Do you perform services under different business names?<br> 
    In this section, add your different bill to names, one per line.
    Examples: Self, Client, Third Party Business Name</span></div>
    <hr>
    <?php
}


/**
 * Settings Field - Work Categories
 * Callback Function
 */
function tt_categories_work_categories_callback() {
    //get the value if it's already been entered
    $wc = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "work_categories")));
    $str = "";
    if (isset( $wc )) {
        $str = esc_html($wc);
    }

    //display on menu page
    ?>
    <div class="tt-indent">
        <textarea id="tt-categories" name="time_tracker_categories[work_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
        echo $str;
        ?></textarea><span class="tt-options-form">Time Tracker can help you keep track of different types of work.<br>
        In this section, add the options you'd like to see for this field when entering a new project or task.<br>
        Examples: Website Design, Social Media Management, Website Updates.<br>
        Enter one category on each line.</span></div>
    <hr>
    <?php
}


/**
 * Settings Field - Client Categories
 * Callback Function
 */
function tt_categories_client_categories_callback() {
    //get the value if it's already been entered
    $cc = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "client_categories")));
    $str = "";
    if (isset( $cc )) {
        $str = esc_html($cc);
    }

    //display on menu page
    ?>
    <div class="tt-indent">
        <textarea id="tt-csource" name="time_tracker_categories[client_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
        echo $str;
        ?></textarea><span class="tt-options-form">Time Tracker will maintain information on each of your clients.<br>
        When adding a new client you'll be asked to enter how they found you.<br>
        In this section, add the options you'd like to see for this field, one per line.<br>
        Examples: Paid Ad, Organic Search, Referral.</span></div>
    <hr>
    <?php   
}


/**
 * Settings Field - Client Sub-Categories
 * Callback Function
 */
function tt_categories_client_sub_categories_callback() {
    //get the value if it's already been entered
    $csc = trim(sanitize_textarea_field(\Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_categories", "client_categories")));
    $str = "";
    if (isset( $csc )) {
        $str = esc_html($csc);
    }

    //display on menu page
    ?>
    <div class="tt-indent">
    <textarea id="tt-client-sub-categories" name="time_tracker_categories[client_sub_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
    echo $str;
    ?></textarea><span class="tt-options-form">You can also add a second level of information on how the client found you.<br>
    In this section, add the options you'd like to see for this field, one per line.<br>
    Examples: Google PPC, Facebook Ad, LinkedIn Ad, Name of Individual That Referred Client</span></div>
    <br>
    <?php
}


/**
 * Settings Field - CSS - Buttons - Override colors
 * Callback Function
 */
function tt_css_buttons_override_callback() {
    //display on menu page
    ?>
    <div class="tt-indent">
    <input type="checkbox" id="tt-css-button-override" name="time_tracker_style[css][buttons][override]" class="tt-options-form" form="tt-options" <?php
    if (tt_css_button_override_value()) {
        echo 'checked';
    }
    ?>
    >   </text>    <br>    </div>
    <?php
}


/** Get Value of CSS Button Style Override
 * 
 * 
 */
function tt_css_button_override_value() {
    //get the value if it's already been entered
    $css = \Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_style", "css");
    if ($css != null) {
        if (is_array($css)) {
            if (array_key_exists('buttons', $css)) {
                if (is_array($css['buttons'])) {
                    if (array_key_exists('override', $css['buttons'])) {
                        if (trim(sanitize_textarea_field($css['buttons']['override'])) == "on") {
                            return true;
                        }
                    }
                }
            }
        }
    }
    return false;
}


function tt_css_option_callback($args) {
    //get the value if it's already been entered
    $optn = "";
    $css = \Logically_Tech\Time_Tracker\Inc\tt_get_user_options("time_tracker_style", "css");
    if ($css != null) {
        $optn = $css;
        foreach ($args as $arg) {
            if (is_array($optn)) {
                if (array_key_exists($arg, $optn)) {
                    $optn = $optn[$arg];
                }
            } else {
                $optn = trim(sanitize_text_field($optn));
            }
        }
    }    

    //display on menu page
    ?><div class="tt-indent">
    <input type="text" id="tt-css-<?php echo implode("-", $args); ?>" name="time_tracker_style[css][<?php echo implode("][", $args); ?>]" rows="1" cols="20" form="tt-options" class="tt-options-form <?php
    if (tt_css_button_override_value() == false) {
        echo 'tt-disabled" disabled';
    } else {
        echo '"';
    }
    ?> value="<?php echo $optn; ?>"><div id="tt-color-display-<?php echo implode("-", $args); ?>"" style="display:inline-block; margin-left: 30px; min-width: 30px; min-height: 30px; background-color: <?php echo $optn; ?>;"><p></p></div>   <br>    </div>
    <?php
}


/**
 * Settings Field - CSS - Buttons - Background Color
 * Callback Function
 */
function tt_css_buttons_background_callback() {
    tt_css_option_callback(array('buttons', 'background', 'normal'));
}

/**
 * Settings Field - CSS - Buttons - Text Color
 * Callback Function
 */
function tt_css_buttons_text_callback() {
    tt_css_option_callback(array('buttons', 'text', 'normal'));
}

/**
 * Settings Field - CSS - Buttons - Background Color - Hover
 * Callback Function
 */
function tt_css_buttons_background_hover_callback() {
    tt_css_option_callback(array('buttons', 'background', 'hover'));
}

/**
 * Settings Field - CSS - Buttons - Text Color - Hover
 * Callback Function
 */
function tt_css_buttons_text_hover_callback() {
    tt_css_option_callback(array('buttons', 'text', 'hover'));
}
