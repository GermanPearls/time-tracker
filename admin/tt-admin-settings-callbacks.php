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
    $settings = get_option('time_tracker_categories');

    //display on menu page
    ?>
    <div class="tt-indent">Enter a default hourly billing rate.<br><br>
    <input type="text" id="tt-default-rate" name="time_tracker_categories[default_rate]" rows=1 cols=20 class="tt-options-form" form="tt-options"
    <?php $dr = trim(sanitize_textarea_field($settings['default_rate']));
    if (isset($dr)) {
        if ($dr != null) {
            echo " value=" . intval($dr);
        }
    }
    ?>><br></div>
    <hr>
    <?php
}



/**
 * Settings Field - Bill to Names
 * Callback Function
 */
function tt_categories_bill_to_names_callback() {
    //get the value if it's already been entered
    $setting = get_option('time_tracker_categories');

    //display on menu page
    ?>
    <div class="tt-indent">Do you always bill directly to the client? 
    Or do white-label services where you bill to a third party? 
    Do you perform services under different business names?<br>
    In the section below, add your different bill to names, one per line.<br>
    Examples: Self, Client, Third Party Business Name<br><br>
    <textarea id="tt-bill-to" name="time_tracker_categories[bill_to_names]" rows="5" cols="30" class="tt-options-form" form="tt-options"><?php
    $btn = trim(sanitize_textarea_field($setting['bill_to_names']));
    if (isset( $btn )) {
        echo esc_html($btn);
    } else {
        echo '';
    }
    ?></textarea><br></div>
    <hr>
    <?php
}


/**
 * Settings Field - Work Categories
 * Callback Function
 */
function tt_categories_work_categories_callback() {
    //get the value if it's already been entered
    $setting = get_option('time_tracker_categories');

    //display on menu page
    ?>
    <div class="tt-indent">Time Tracker can help you keep track of different types of work.<br>
    In the section below, add the options you'd like to see for this field when entering a new project or task.<br>
    Examples: Website Design, Social Media Management, Website Updates.<br>
    Enter one category on each line.<br><br>
    <textarea id="tt-categories" name="time_tracker_categories[work_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
    $wc = trim(sanitize_textarea_field($setting['work_categories']));
    if (isset( $wc )) {
        echo esc_html($wc);
    } else {
        echo '';
    }
    ?></textarea><br></div>
    <hr>
    <?php
}


/**
 * Settings Field - Client Categories
 * Callback Function
 */
function tt_categories_client_categories_callback() {
    //get the value if it's already been entered
    $setting = get_option('time_tracker_categories');

    //display on menu page
    ?>
    <div class="tt-indent">Time Tracker will maintain information on each of your clients. When adding a new client you'll be asked to enter how they found you.<br>
    In the section below, add the options you'd like to see for this field, one per line. <br>
    Examples: Paid Ad, Organic Search, Referral.<br><br>
    <textarea id="tt-csource" name="time_tracker_categories[client_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
    $cc = trim(sanitize_textarea_field($setting['client_categories']));
    if (isset( $cc )) {
        echo esc_html($cc);
    } else {
        echo '';
    }
    ?></textarea><br></div>
    <hr>
    <?php   
}


/**
 * Settings Field - Client Sub-Categories
 * Callback Function
 */
function tt_categories_client_sub_categories_callback() {
    //get the value if it's already been entered
    $setting = get_option('time_tracker_categories');

    //display on menu page
    ?>
    <div class="tt-indent">You can also add a second level of information on how the client found you.<br>
    In the section below, add the options you'd like to see for this field, one per line.<br>
    Examples: Google PPC, Facebook Ad, LinkedIn Ad, Name of Individual That Referred Client<br><br>
    <textarea id="tt-client-sub-categories" name="time_tracker_categories[client_sub_categories]" rows="10" cols="30" class="tt-options-form" form="tt-options"><?php
    $csc = sanitize_textarea_field($setting['client_sub_categories']);
    if (isset( $csc )) {
        echo esc_html($csc);
    } else {
        echo '';
    }
    ?></textarea><br></div>
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
    $styles = get_option('time_tracker_style');
    if (is_array($styles)) {
        if (array_key_exists('css', $styles)) {
            if (is_array($styles['css'])) {
                if (array_key_exists('buttons', $styles['css'])) {
                    if (is_array($styles['css']['buttons'])) {
                        if (array_key_exists('override', $styles['css']['buttons'])) {
                            if (trim(sanitize_textarea_field($styles['css']['buttons']['override'])) == "on") {
                                return true;
                            }
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
    $optn = '';
    $styles = get_option('time_tracker_style');
    if (is_array($styles)) {
        if (array_key_exists('css', $styles)) {
            $optn = $styles['css'];
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
