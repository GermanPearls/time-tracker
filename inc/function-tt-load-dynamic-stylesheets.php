<?php
/**
 * Load dynamic stylesheet within WP so we can use get_option WP function
 * 
 * @since 2.3.0
 *
 **/

function tt_load_dynamic_stylesheets(){
    include_once( dirname(__FILE__) . '/css/tt-css-buttons.php' );
}

add_action('wp_head', 'tt_load_dynamic_stylesheets');
?>