<?php
/*
* Plugin Name: Weather Underground
* Plugin URI: https://github.com/katzwebservices/Wunderground#setting-up-the-plugin
* Description: Get accurate and beautiful weather forecasts powered by Wunderground.com for your content or your sidebar.
* Version: 2.1.3
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Author: Katz Web Services, Inc.
* Author URI: https://katz.co
* Text Domain: wunderground
* Domain Path: languages
*/

class Wunderground_Plugin {

	/**
	 * Version used to prime style and script caches
	 * @var string
	 */
	const version = '2.1.3';

	/**
	 * @var string The full path and filename of the main plugin file
	 */
	static $file;

	/**
	 * @var string Filesystem path to main plugin file, with trailing slash
	 */
	static $dir_path;

	/**
	 * Filter the Weather Underground API key used by the plugin
	 * Modify using the `wunderground_api_key` filter
	 * @since 2.1.2
	 */
	static $api_key = '3ffab52910ec1a0e';

	function __construct() {

		self::$file = __FILE__;

		self::$dir_path = plugin_dir_path( __FILE__ );

		/**
		 * Filter the Weather Underground API key used by the plugin
		 * @since 2.1.2
		 */
		self::$api_key = $this->get_api_key();

		// Fire AJAX requests immediately
		include_once self::$dir_path.'inc/class-ajax.php';

		// Load once we're sure everything's been loaded.
		add_action( 'plugins_loaded', array( &$this, 'require_files' ) );

		add_action( 'init', array(&$this, 'init') );

		// Use the `wunderground_log_debug` action for logging
		add_action( 'wunderground_log_debug', array( 'Wunderground_Plugin', 'log_debug'), 10, 2 );
	}

	/**
	 * Get the Weather Underground API key used by the plugin
	 *
	 * You can define your own Weather Underground API key using the `WUNDERGROUND_API_KEY` constant in wp-config.php,
	 * or you can filter the key using the `wunderground_api_key` filter.
	 *
	 * @since 2.1.2
	 *
	 * @return string The Weather Underground API key. Default: the plugin key ("3ffab52910ec1a0e")
	 */
	private function get_api_key() {

		$api_key = self::$api_key;

		if( defined( 'WUNDERGROUND_API_KEY' ) ) {
			$api_key = WUNDERGROUND_API_KEY;
		}

		$api_key = apply_filters( 'wunderground_api_key', $api_key );

		return $api_key;
	}

	/**
	 * Load the textdomain, add the [wunderground] and [forecast] shortcodes, add do_shortcode() to widgets
	 */
	function init() {

		// Add translation support
		load_plugin_textdomain( 'wunderground', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Add the shortcode
		add_shortcode( 'wunderground', 'wunderground_shortcode' );

		/**
		 * Enable or disable the forecast shortcode if you want to, since it's not namespaced
		 */
		if( apply_filters('wunderground_enable_forecast_shortcode', true ) ) {
			add_shortcode( 'forecast', 'wunderground_shortcode' );
		}

		/**
		 * Process shortcodes in widgets previous shortcodes in widgets
		 * @since 2.0.8
		 */
		if( apply_filters('wunderground_widget_text_do_shortcode', true ) ) {
			add_filter( 'widget_text', 'do_shortcode' );
		}

	}

	function require_files() {

		// Load the functions needed
		include_once self::$dir_path.'inc/functions.php';

		// Twig template autoloader
		if( !class_exists('Twig_Autoloader') ) {
			require_once self::$dir_path . 'vendor/twig/twig/lib/Twig/Autoloader.php';
		}

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
		include_once self::$dir_path.'inc/class-alert.php';
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
