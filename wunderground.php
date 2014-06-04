<?php
/*
* Plugin Name: Weather Underground
* Description: Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.
* Version: 2.0
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Author: Katz Web Services, Inc.
* Author URI: https://katz.co
* Text Domain: wunderground
* Domain Path: languages
*/

class Wunderground_Plugin {

	const version = '1.0';

	var $logger;
	var $is_debug = false;

	static $file;
	static $dir_path;

	function __construct() {

		self::$file = __FILE__;
		self::$dir_path = plugin_dir_path( __FILE__ );

		// Fire AJAX requests first.
		include_once self::$dir_path.'ajax.php';

		// Load once we're sure everything's been loaded.
		add_action('plugins_loaded', array( &$this, 'require_files' ) );

		add_action('wunderground_log_debug', array( 'Wunderground_Plugin', 'log_debug'), 10, 2);
	}

	function require_files() {

		// Twig template autoloader
		require_once self::$dir_path.'vendor/autoload.php';

		// Load the Wunderground requirements
		include_once self::$dir_path.'inc/Request.php';
		include_once self::$dir_path.'inc/Response.php';
		include_once self::$dir_path.'inc/Date.php';
		include_once self::$dir_path.'inc/ForecastDay.php';
		include_once self::$dir_path.'inc/Current_Observation.php';
		include_once self::$dir_path.'inc/Station.php';
		include_once self::$dir_path.'inc/Forecast.php';
		include_once self::$dir_path.'inc/Alerts.php';

		// Load the Wunderground wrapper class
		include_once self::$dir_path.'inc/KWS_Wunderground.php';

		include_once self::$dir_path.'functions.php';
		include_once self::$dir_path.'template.php';
		include_once self::$dir_path.'widget.php';
		include_once self::$dir_path.'display.php';
	}

	static function log_notice( $type = '', $message = '', $data = NULL ) {

		if(!isset($_GET['debug']) || !current_user_can( 'manage_options' )) { return; }

		if(is_string($message)) {
			echo '<h3>'.esc_attr($message).'</h3>';
		}

		if($data) {
			echo '<pre>'; print_r($data); echo '</pre>';
		}
	}

	static function log_debug( $message, $data = NULL) {
		self::log_notice( 'debug', $message, $data );
	}

	static function log_error( $message, $data = NULL) {
		self::log_notice( 'error', $message, $data );
	}
}

new Wunderground_Plugin;
