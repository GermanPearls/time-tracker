<?php
/**
 * Time Tracker Handle Recaptcha on TT Forms
 *
 * Remove recaptcha for TT forms only
 * 
 * @since 2.4.0
 * 
 */

function time_tracker_remove_cf7_recaptcha() {
    if(is_singular()) {
        $post_type = get_post_meta( get_post()->ID, '_wp_page_template', true );
        if ($post_type == "tt-page-template.php") {
            remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20, 0 );
            //remove_action( 'wpcf7_init', 'wpcf7_recaptcha_add_form_tag_recaptcha', 10, 0);
        }
    }
}
add_action( 'wp', 'time_tracker_remove_cf7_recaptcha', 10);


function time_tracker_skip_cf7_spam_check() {
    if(is_singular()) {
        $post_type = get_post_meta( get_post()->ID, '_wp_page_template', true );
        echo $post_type;
        if ($post_type == "tt-page-template.php") {
            echo 'should be off';
            return true;
        }
    }
}
//add_filter( 'wpcf7_skip_spam_check', 'time_tracker_skip_cf7_spam_check');
add_filter( 'wpcf7_skip_spam_check', '__return_true');


/** If user has Advanced Google Recaptcha Plugin installed, disable it on TT forms */
if ( defined('ADVANCED_GOOGLE_RECAPTCHA_VERSION') ) {
    function time_tracker_remove_advanced_google_recaptcha() {
        if(is_singular()) {
            $post_type = get_post_meta( get_post()->ID, '_wp_page_template', true );
            if ($post_type == "tt-page-template.php") {
                remove_action ( 'init', 'advanced_google_recaptcha_init', 10, 0 );
                remove_action ( 'wp_enqueue_scripts', 'advanced_google_recaptcha_load_frontend_scripts', 10, 0);
            }
        }    
    }
    add_action( 'wp', 'time_tracker_remove_advanced_google_recaptcha', 10);
}
