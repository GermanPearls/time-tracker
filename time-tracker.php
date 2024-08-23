<?php
 /**
 * Time Tracker
 *
 * @package           
 * @author            Amy McGarity
 * @copyright         2020-2024 Amy McGarity
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Time Tracker
 * Plugin URI:        https://www.logicallytech.com/services/wordpress-plugins/time-tracker/
 * Description:       A task and time tracking program. Perfect for freelancers or indivdiuals keeping track of to do lists and time worked and billed to clients.
 * Version:           3.0.13
 * Requires at least: 5.3
 * Requires PHP:      7.0
 * Author:            Amy McGarity
 * Author URI:        https://www.logicallytech.com/
 * Text Domain:       time-tracker
 * License:           GPL v3 or later
 * 
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Logically_Tech\Time_Tracker;

use Logically_Tech\Time_Tracker\Inc\Time_Tracker;
use Logically_Tech\Time_Tracker\Inc\Time_Tracker_Activator;
use Logically_Tech\Time_Tracker\Inc\Time_Tracker_Deactivator;
use Logically_Tech\Time_Tracker\Inc\Time_Tracker_Deletor;


if ( !defined( 'ABSPATH' ) ) { 
  die( 'Nope, not accessing this' );
}


/**
 * Current plugin version.
 * Use SemVer - https://semver.org
 */
define('TIME_TRACKER_VERSION', '3.0.13');
define('TIME_TRACKER_PLUGIN_BASENAME', plugin_basename(__FILE__));


/**
 * ACTIVATE PLUGIN
 * Define the plugin activation class
 */
function activate_time_tracker() {
	//establish dependent form plugin and load tt files for activation
	tt_form_dependency();
	time_tracker_load();
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-activator.php';
	Inc\Time_Tracker_Activator::activate();
}
register_activation_hook( __FILE__, 'Logically_Tech\Time_Tracker\activate_time_tracker' );


/**
 * DEACTIVATE PLUGIN
 * Define the plugin deactivation class
 */
function deactivate_time_tracker() {
	//establish dependent form plugin and load tt files for deactivation
	tt_form_dependency();
	time_tracker_load();
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-deactivator.php';
	Inc\Time_Tracker_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'Logically_Tech\Time_Tracker\deactivate_time_tracker' );


/**
 * DELETE PLUGIN
 * Define the plugin uninstall/delete class
 */
function uninstall_time_tracker() {
	//establish dependent form plugin and load tt files for deletion
	tt_form_dependency();
	time_tracker_load();
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-delete.php';
	Inc\Time_Tracker_Deletor::delete_all();
}
register_uninstall_hook(__FILE__, 'Logically_Tech\Time_Tracker\uninstall_time_tracker');


/**
 * START PLUGIN
 * This is the function that creates the main plugin class
 */
function time_tracker_load() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker.php';
  	return Inc\Time_Tracker::instance();
}


/**
 * CHECK FORM PLUGIN DEPENDENCY
 * 
 */
function tt_form_dependency() {
	if (!defined('TT_PLUGIN_FORM_TYPE')) {
		if (class_exists( 'WPCF7' )) {
			define('TT_PLUGIN_FORM_TYPE', 'CF7');
		}
		elseif (class_exists( 'WPForms' )) {
			define('TT_PLUGIN_FORM_TYPE', 'WPF');
		}
		else {
			define('TT_PLUGIN_FORM_TYPE', '');
		}
	}
}

/**
 * QUEUE UP PLUGIN LOAD
 * 
 */
function tt_queue_load() {
	if (class_exists( 'WPCF7' )) {
		add_action( 'plugins_loaded', 'Logically_Tech\Time_Tracker\time_tracker_load', 11);
	} 		
	elseif (class_exists( 'WPForms' )) {
		//add_action( 'wp_loaded', 'Logically_Tech\Time_Tracker\time_tracker_load' );
		add_action( 'plugins_loaded', 'Logically_Tech\Time_Tracker\time_tracker_load', 11 );
	}
}


/**
 * START PLUGIN
 * Start it up!
 */
add_action( 'plugins_loaded', 'Logically_Tech\Time_Tracker\tt_form_dependency', 10 );
add_action( 'plugins_loaded', 'Logically_Tech\Time_Tracker\tt_queue_load', 10 );