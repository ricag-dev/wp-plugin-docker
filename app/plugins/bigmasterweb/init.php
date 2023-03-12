<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package BigMasterWeb 
 */
/*
Plugin Name: BigMasterWeb
Plugin URI: https://bigmasterweb.com
Description: Actualiza fácilmente los plugins y temas adquiridos en BigMasterWeb.
Version: 1.0.2
Author: BigMasterWeb
Author URI: https://bigmasterweb.com
License: GPLv3
Text Domain: bigmasterweb
*/

// $ran_h  = random_int(8, 12);
// $ran_m  = random_int(1, 59);
// delete_option('BigMasterWebCHT_cron_verifique_version');
// $ran_h  = 0;
// $ran_m  = 5;
// $h      = 3600;
// $m      = 60;

// $time_p = time()+(($h*$ran_h)+($m*$ran_m));
// add_option('BigMasterWebCHT_cron_verifique_version', (string)$time_p);
// update_option('BigMasterWebCHT_updates_pending', 0);

// update_option('BigMasterWebCHT_cron_verifique_version', '1625087781');

// define('ALTERNATE_WP_CRON', true);
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! defined( 'BigMasterWebCHT_DIR' ) ) {
	define( 'BigMasterWebCHT_DIR', plugin_dir_path(__FILE__) );
}

if ( ! defined( 'BigMasterWebCHT_FILE' ) ) {
	define( 'BigMasterWebCHT_FILE', __FILE__);
}

require_once BigMasterWebCHT_DIR . 'settings/run.php';

function BigMasterWebCHT_event_register() {
	require_once(BigMasterWebCHT_DIR.'settings/myOptions.php');
	
    // if(!wp_next_scheduled('BigMasterWebCHT_update_complemento')){
	// 	// hourly
	// 	// twicedaily
	// 	wp_schedule_event( time(), 'hourly', 'BigMasterWebCHT_update_complemento');
    // }
}

function BigMasterWebCHT_deactivation() {
	// wp_clear_scheduled_hook( 'BigMasterWebCHT_update_complemento' );
}

function BigMasterWebCHT_event_uninstall(){
	delete_option('BigMasterWebCHT_updates_pending');
	delete_option('BigMasterWebCHT_cron_verifique_version');
	delete_option('BigMasterWebCHT_user_token');
}

register_activation_hook(__FILE__, 'BigMasterWebCHT_event_register');
register_deactivation_hook( __FILE__, 'BigMasterWebCHT_deactivation' );
register_uninstall_hook(__FILE__, 'BigMasterWebCHT_event_uninstall');