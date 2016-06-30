<?php

class Wunderground_Display {

	function __construct() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'print_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( &$this, 'print_scripts') );
		add_action( 'wunderground_print_scripts', array( &$this, 'print_scripts' ) );

	}

	/**
	 * Output the styles and scripts necessary
	 *
	 * @param  boolean     $force Will be empty string when passed by `wp_enqueue_scripts`, but will be `true` when passed by `wunderground_print_scripts`
	 * @return [type]             [description]
	 */
	function print_scripts( $force = false ) {
		global $post, $pagenow;

		// Is the widget active?
		$widget = is_active_widget( false, false, 'wunderground_forecast_widget' ) ? true : false;

		// Check if the content has the shortcode
		$content = false;
		if( !empty( $post->post_content ) && function_exists('has_shortcode') && has_shortcode($post->post_content, 'wunderground') ) {
			$content = true;
		}

		$admin = (is_admin() && in_array( $pagenow, array('widgets.php', 'customize.php') ) );

		if( $admin || $widget || $content || $force === true ) {

			// Only show the front-end display on the front-end
			if( !$admin ) {
				wp_enqueue_style( 'wunderground', plugins_url( 'assets/css/wunderground.css', Wunderground_Plugin::$file ), array('dashicons'), Wunderground_Plugin::version );
			} else {
				// And the backend on the backend
				wp_enqueue_style( 'wunderground-admin', plugins_url( 'assets/css/admin.css', Wunderground_Plugin::$file ) );
			}

			// If using SCRIPT_DEBUG, don't use the minified version.
			$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'wunderground-widget', plugins_url( 'assets/js/widget'.$min.'.js', Wunderground_Plugin::$file ), array('jquery-ui-autocomplete'), Wunderground_Plugin::version );

			wp_localize_script( 'wunderground-widget', 'WuWidget', array(
				'apiKey' => esc_attr( Wunderground_Plugin::$api_key ),
				'_wpnonce' => wp_create_nonce('wunderground-aq'),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'is_admin' => is_admin(),
				'subdomain' => wunderground_get_subdomain()
			));
		}

	}

}

new Wunderground_Display;
