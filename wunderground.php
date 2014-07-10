<?php
/*
* Plugin Name: Weather Underground
* Description: Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.
* Version: 2.0.2
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Author: Katz Web Services, Inc.
* Author URI: https://katz.co
* Text Domain: wunderground
* Domain Path: languages
*/

class Wunderground_Plugin {

	const version = '2.0.2';

	var $logger;
	var $is_debug = false;

	static $file;
	static $dir_path;

	function __construct() {

		self::$file = __FILE__;
		self::$dir_path = plugin_dir_path( __FILE__ );

		// Fire AJAX requests immediately
		include_once self::$dir_path.'inc/class-ajax.php';

		// Load once we're sure everything's been loaded.
		add_action( 'plugins_loaded', array( &$this, 'require_files' ) );

		add_action( 'init', array(&$this, 'init') );

		// Use the `wunderground_log_debug` action for logging
		add_action( 'wunderground_log_debug', array( 'Wunderground_Plugin', 'log_debug'), 10, 2 );
	}

	function init() {

		// Add translation support
		load_plugin_textdomain( 'wunderground', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Add the shortcode
		add_shortcode( 'wunderground', 'wunderground_shortcode' );
		add_shortcode( 'forecast', 'wunderground_shortcode' );

	}

	function require_files() {

		// Load the functions needed
		include_once self::$dir_path.'inc/functions.php';

		// Twig template autoloader
		require_once self::$dir_path.'vendor/twig/twig/lib/Twig/Autoloader.php';

		// Twig template setup
		include_once self::$dir_path.'inc/class-template.php';

		// Load the Wunderground requirements
		include_once self::$dir_path.'inc/class-request.php';
		include_once self::$dir_path.'inc/class-response.php';
		include_once self::$dir_path.'inc/class-date.php';
		include_once self::$dir_path.'inc/class-forecastday.php';
		include_once self::$dir_path.'inc/class-current-observation.php';
		include_once self::$dir_path.'inc/class-station.php';
		include_once self::$dir_path.'inc/class-forecast.php';
		include_once self::$dir_path.'inc/class-alerts.php';

		// Load the Wunderground wrapper class
		include_once self::$dir_path.'inc/class-KWS-wunderground.php';

		// WordPress widget
		include_once self::$dir_path.'inc/class-widget.php';

		// Scripts and styles
		include_once self::$dir_path.'inc/class-display.php';
	}

	/**
	 * Output debugging information when $_GET['debug'] is set and the user is an administrator
	 * @param  string      $type    Type of notice: debug or error
	 * @param  string      $message Title of notice
	 * @param  [type]      $data    Data for the notice that will be printed
	 * @return void
	 */
	static function log_notice( $type = '', $message = '', $data = NULL ) {

		if( empty( $_GET['debug'] ) || $_GET['debug'] !== 'wunderground' || !current_user_can( 'manage_options' )) { return; }

		if(is_string($message)) {
			echo '<h3>'.esc_attr($message).'</h3>';
		}

		if($data) {
			echo '<pre>'; print_r($data); echo '</pre>';
		}
	}

	/**
	 * Log a debug message
	 * @see Wunderground_Plugin::log_notice()
	 */
	static function log_debug( $message, $data = NULL) {
		self::log_notice( 'debug', $message, $data );
	}

	/**
	 * Log an error message
	 * @see Wunderground_Plugin::log_notice()
	 */
	static function log_error( $message, $data = NULL) {
		self::log_notice( 'error', $message, $data );
	}
}

new Wunderground_Plugin;
