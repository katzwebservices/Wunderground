<?php

class Wunderground_Display {

	function __construct() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'print_scripts' ) );
		add_shortcode( 'wunderground', array( &$this, 'shortcode') );

	}

	function print_scripts() {
		global $post;

		// Make sure it has has_shortcode()
		if(!function_exists('has_shortcode')) { return; }

		if( has_shortcode($post->post_content, 'wunderground') ) {
			wp_enqueue_style( 'wunderground', plugins_url( 'assets/css/wunderground.css', Wunderground_Plugin::$file ), array('dashicons') );
		}
	}

	function shortcode( $passed_atts = array() , $content = NULL ) {

		$defaults = array(
			'title' => __('Weather Forecast', 'wunderground'),
			'location'	=>	'Denver, Colorado',
			'iconset' 	=> 	'Incredible',
			'numdays'	=>	5,
			'class'		=>	'wp_wunderground',
			'cache'		=>	NULL,
			'layout'	=>	'table-vertical',
			'measurement' => 'english',
			'language' => wunderground_get_language(),
			'showdata' => array('alerts','pop','icon','text', 'conditions', 'date'),
		);

		$atts = shortcode_atts( $defaults, $passed_atts );

		$atts['showdata'] = is_string( $atts['showdata'] ) ? explode(',', $atts['showdata']) : $atts['showdata'];

		extract($atts);

		ob_start();

		$atts['wunderground'] = new KWS_Wunderground( new Wunderground_Request( $location, null, $language, $cache ) );

		do_action( 'wunderground_render_template', $layout, $atts );

		#Wunderground_Plugin::log_debug('Shortcode Atts passed to render_template', $atts);

		$content = ob_get_clean();

		return $content;
	}

}

new Wunderground_Display;
