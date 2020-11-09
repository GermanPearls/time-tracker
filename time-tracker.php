<?php
 /**
 * Time Tracker
 *
 * @package           
 * @author            Amy McGarity
 * @copyright         2020 Amy McGarity
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Time Tracker
 * Plugin URI:        https://www.logicallytech.com/services/wordpress-plugins/
 * Description:       A project, task and time tracking program for freelancers.
 * Version:           1.0.0
 * Requires at least: 5.3
 * Requires PHP:      7.0
 * Author:            Amy McGarity
 * Author URI:        https://www.logicallytech.com/
 * Text Domain:       time-tracker
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined( 'ABSPATH' ) ) { 
  die( 'Nope, not accessing this' );
}


/**
 * Current plugin version.
 * Use SemVer - https://semver.org
 */
define( 'TIME_TRACKER_VERSION', '1.0.0' );
define('PLUGIN_BASENAME', plugin_basename(__FILE__));


/**
 * ACTIVATE PLUGIN
 * Define the plugin activation class
 */
function activate_time_tracker() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-activator.php';
	Time_Tracker_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_time_tracker' );


/**
 * DEACTIVATE PLUGIN
 * Define the plugin deactivation class
 */
function deactivate_time_tracker() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-deactivator.php';
	Time_Tracker_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_time_tracker' );


/**
 * DELETE PLUGIN
 * Define the plugin uninstall/delete class
 */
function uninstall_time_tracker() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker-delete.php';
	Time_Tracker_Deletor::delete_all();
}
register_uninstall_hook(__FILE__, 'uninstall_time_tracker');


/**
 * START PLUGIN
 * This is the function that creates the main plugin class
 */
function time_tracker_load() {
  require plugin_dir_path( __FILE__ ) . 'inc/class-time-tracker.php';
  return Time_Tracker::instance();
}


/**
 * START PLUGIN
 * Start it up!
 */
time_tracker_load();