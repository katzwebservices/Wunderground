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

	function __construct( $debug = false ) {

		// This is how to call the template engine:
		// do_action('wunderground_render_template', $file_name, $data_array );
		add_action('wunderground_render_template', array( &$this, 'render' ), 10, 2 );


		// This path should always be the last
		$base_path = trailingslashit( plugin_dir_path( __FILE__ ) ).'templates';

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

		$this->twig = new Twig_Environment($this->loader, array(
			'debug' => !empty($debug),
			'auto_reload' => true,
		));

		if(!empty($debug)) {
			$this->twig->addExtension(new Twig_Extension_Debug());
		}

	}

	function strings() {
		return array(
			'alert_statement_as_of' => __('Statement as of %s', 'wunderground'),
			'chance_of_precipitation' => __('%s%%', 'wunderground'),
			'chance_of_precipitation_title' => __('%s%% Chance of Precipitation', 'wunderground'),
			'date_format' => __('m/d', 'wunderground'),
			'currently' => __('Currently', 'wunderground'),
			'view_forecast' => __('View the %s forecast on Wunderground.com', 'wunderground'),
		);
	}

	function render( $template = NULL, $data = array() ) {
		$data['strings'] = $this->strings();
		$data['user_icon_url'] = wunderground_get_icon($data['iconset']);
		$data['logo'] = plugins_url( 'assets/img/logos/wundergroundLogo_4c_horz.gif', __FILE__ );

		// Map the keys better
		$showdata = array();
		foreach ( (array)$data['showdata'] as $key => $value) {
			$data['showdata'][$value] = $value;
		}

		echo $this->twig->render("{$template}.html", $data);

	}

	static function get_layouts() {

		$layouts = array(
			'current' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/current.png', __FILE__ ).'" />',
				'path' => '',
				'label' => __('Current Conditions', 'wunderground'),
				'desc' => __('Simple display of the current conditions.', 'wunderground'),
			),
			'simple' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/simple.png', __FILE__ ).'" />',
				'path' => '',
				'label' => 'Grid Forecast',
				'desc' => __('Scales to any screen size.', 'wunderground'),
			),
			'table-vertical' => array(
				'thumbnail' => '<img src="'.plugins_url( 'assets/img/thumbnail/table-vertical.png', __FILE__ ).'" />',
				'path' => '',
				'label' => __('Details Table', 'wunderground'),
				'desc' => __('Display the forecast in a table. Great for in-depth forecast display.', 'wunderground'),
			),
		);

		return $layouts;
	}

}

new Wunderground_Template;
