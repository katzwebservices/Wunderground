<?php

class Wunderground_Template {

	/**
	 * Loader class
	 * @var Twig_Loader_Filesystem
	 */
	var $loader;

	/**
	 * Twig class
	 * @var Twig_Environment
	 */
	var $twig;

	function __construct() {

		// This is how to call the template engine:
		// do_action('wunderground_render_template', $file_name, $data_array );
		add_action('wunderground_render_template', array( &$this, 'render' ), 10, 2 );

		// Set up Twig
		Twig_Autoloader::register();

		// This path should always be the last
		$base_path = trailingslashit( plugin_dir_path( Wunderground_Plugin::$file ) ).'templates';

		$this->loader = new Twig_Loader_Filesystem( $base_path );

		// Tap in here to add additional template paths
		$additional_paths = apply_filters('wunderground_template_paths', array(
			trailingslashit(  get_stylesheet_directory() ).'wunderground',
		));

		foreach($additional_paths as $path) {

			// If the directory exists
			if(is_dir($path)) {
				// Tell Twig to use it first
				$this->loader->prependPath($path);
			}
		}

		// You can force debug mode by adding `add_filter( 'wunderground_twig_debug' '__return_true' );`
		$debug = apply_filters( 'wunderground_twig_debug', current_user_can( 'manage_options' ) && isset( $_GET['debug'] ) );

		$this->twig = new Twig_Environment($this->loader, array(
			'debug' => !empty($debug),
			'auto_reload' => true,
		));

		if(!empty($debug)) {
			$this->twig->addExtension(new Twig_Extension_Debug());
		}

	}

	/**
	 * Strings that are used inside the template are defined here to allow for localization.
	 *
	 * @return [type]      [description]
	 */
	function strings() {

		return array(
			'alert_statement_as_of' => __('Statement as of %s', 'wunderground'),
			'no_results' => __('The location could not be found.', 'wunderground'),
			'chance_of_precipitation' => __('%s%%', 'wunderground'),
			'chance_of_precipitation_title' => __('%s%% Chance of Precipitation', 'wunderground'),
			'currently' => __('Currently', 'wunderground'),
			'high' => __('High %d&deg;', 'wunderground'),
			'low' => __('Low %d&deg;', 'wunderground'),
			'current' => __('%d&deg;', 'wunderground'),
			'view_forecast' => __('View the %s forecast on Wunderground.com', 'wunderground'),
			'alert_issued' => _x('Issued:', 'Weather alert issued date/time', 'wunderground'),
			'alert_expires' => _x('Expires:', 'Weather alert expires date/time', 'wunderground'),
		);

	}

	function render( $template = NULL, $data = array() ) {

		// The translation text
		$data['strings'] = $this->strings();

		// The base URL for the weather icons
		$data['user_icon_url'] = wunderground_get_icon($data['iconset']);

		// The required logo
		$data['logo'] = wunderground_get_logo();

		// Map the keys so that they are consistent instead of having some
		// using key => key and others using index => key
		$showdata = array();
		foreach ( (array)$data['showdata'] as $key => $value ) {
			$data['showdata'][$value] = $value;
		}

		// Enqueue the scripts
		do_action('wunderground_print_scripts', true);

		/**
		 * Filter the data passed to the template
		 * @var array
		 */
		$data = apply_filters( 'wunderground_template_data', apply_filters( 'wunderground_template_data_'.$template, $data ) );

		$output = $this->twig->render("{$template}.html", $data);

		$output = apply_filters( 'wp_wunderground_forecast', $output, $template, $data );

		echo $output;

	}

	static function get_layouts() {

		$layouts = array(
			'current' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/current.png', Wunderground_Plugin::$file ).'" />',
				'path' => '',
				'label' => __('Current Conditions', 'wunderground'),
				'desc' => __('Simple display of the current conditions.', 'wunderground'),
			),
			'simple' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/simple.png', Wunderground_Plugin::$file ).'" />',
				'path' => '',
				'label' => __('Grid Forecast', 'wunderground'),
				'desc' => __('Scales to any screen size.', 'wunderground'),
			),
			'table-vertical' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/table-vertical.png', Wunderground_Plugin::$file ).'" />',
				'path' => '',
				'label' => __('Details Table', 'wunderground'),
				'desc' => __('Display the forecast in a table with rows. Great for in-depth forecast display.', 'wunderground'),
			),
			'table-horizontal' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/table-horizontal.png', Wunderground_Plugin::$file ).'" />',
				'path' => '',
				'label' => __('Horizontal Table', 'wunderground'),
				'desc' => __('Display the forecast in a table with columns. Great for forecast summaries.', 'wunderground'),
			),
		);

		return $layouts;
	}

}

new Wunderground_Template;
