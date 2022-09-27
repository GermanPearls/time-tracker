<?php
/**
 * Time Tracker Handle Recaptcha on TT Forms
 *
 * Remove recaptcha for TT forms only
 * 
 * @since 2.4.0
 * 
 */


//add_action( 'wp_enqueue_scripts', function(){
function time_tracker_remove_cf7_recaptcha() {
    if(is_singular()) {
		global $post;
		$post_type = get_post_type( $post->template );
		if ($post_type = "Time Tracker Page") {
			remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20, 0 );
		}
	}
}
//}, 15);