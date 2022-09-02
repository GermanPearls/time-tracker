<?php
/**
 * @since 2.3.0
 * load dynamic stylesheet within WP so we can use get_option WP function
 *
 **/

function tt_load_dynamic_stylesheets(){
    include_once( dirname(__FILE__) . '/css/tt-css-buttons.php' );
}

add_action('init', 'tt_load_dynamic_stylesheets');
?>