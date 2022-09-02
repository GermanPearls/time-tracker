<?php
//header("Content-type: text/css;");

/** 
 * Button Colors (and Table, Accordion Headers) 
 * 
 * Check for selected colors - use defaults if missing
 * 
 **/

/** defaults **/
$button_background_color = "#01375d";    //dark blue
$button_text_color = "#fff";
$button_hover_background_color = "#809bae";   //light gray blue
$button_hover_text_color = "#01375d";     //dark blue
    
$settings = get_option('time_tracker_style');
if ($settings) {
    if (trim(sanitize_textarea_field($settings['css']['buttons']['override'])) == "on") {
        //override with user settings
        if (array_key_exists('css', $settings)) {
            if (array_key_exists('buttons', $settings['css'])) {
                if (array_key_exists('background', $settings['css']['buttons'])) {
                    if (array_key_exists('normal', $settings['css']['buttons']['background'])) {
                        $button_background_color = sanitize_text_field($settings['css']['buttons']['background']['normal']);
                    }
                    if (array_key_exists('hover', $settings['css']['buttons']['background'])) {
                        $button_hover_background_color = sanitize_text_field($settings['css']['buttons']['background']['hover']);
                    }
                }
                if (array_key_exists('text', $settings['css']['buttons'])) {
                    if (array_key_exists('normal', $settings['css']['buttons']['text'])) {
                        $button_text_color = sanitize_text_field($settings['css']['buttons']['text']['normal']);
                    }
                    if (array_key_exists('hover', $settings['css']['buttons']['text'])) {
                        $button_hover_text_color = sanitize_text_field($settings['css']['buttons']['text']['hover']);
                    }            
                }  
            }
        }
    }
}
?>

<style>
    .tt-table-button,
    .tt-mobile-menu-button,
    a.tt-sidebar-button,
    a.tt-header-button,
    .tt-midpage-button,
    button.end-work-timer,
    .start-work-timer, 
    .end-work-timer,
    .open-task-detail,
    .tt-delete-confirmation-button,
    button.clear-error,
    .tt-pagination-nav a,
    .tt-form button,
    .tt-form input[type="submit"],
    input[type="submit"].tt-form-button,
    .tt-table th,
    .project-list-table td#status-header-row,
    .tt-accordion {
        background-color: <?php echo $button_background_color; ?>;
        color: <?php echo $button_text_color; ?>;
    }

    a.tt-sidebar-button:visited, a.tt-header-button:visited, input[type="submit"].tt-form-button:visited {
        background-color: <?php echo $button_background_color; ?>;
        color: <?php echo $button_text_color; ?>;
    }


    /** Hover **/
    .tt-table-button:hover,
    .tt-mobile-menu-button:hover,
    a.tt-sidebar-button:hover,
    a.tt-header-button:hover,
    .tt-midpage-button:hover,
    button.end-work-timer:hover,
    .start-work-timer:hover, 
    .end-work-timer:hover,
    .open-task-detail:hover,
    .tt-delete-confirmation-button:hover,
    button.clear-error:hover,
    .tt-pagination-nav a:hover,
    .tt-form button:hover,
    .tt-form input[type="submit"]:hover,
    input[type="submit"].tt-form-button:hover,
    .tt-table th:hover,
    .project-list-table td#status-header-row:hover,
    .tt-accordion:hover {
        background-color: <?php echo $button_hover_background_color; ?>;
        color: <?php echo $button_hover_text_color; ?>;
    }

    .tt-accordion.active, .tt-accordion:focus {
        background-color: <?php echo $button_hover_background_color; ?>;
        color: <?php echo $button_hover_text_color; ?>;
    }

    .tt-table-button:focus {
        background-color: <?php echo $button_hover_background_color; ?>;
        color: <?php echo $button_hover_text_color; ?>;
    }

    .tt-pagination-nav .current {
        background-color: <?php echo $button_hover_background_color; ?>;
        color: <?php echo $button_hover_text_color; ?>;
    }
</style>