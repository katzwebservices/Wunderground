<?php

final class Wunderground_Ajax {

	function __construct() {
		add_action( 'wp_ajax_wunderground_aq', array( &$this, 'autocomplete' ) );
		add_action( 'wp_ajax_nopriv_wunderground_aq', array( &$this, 'autocomplete' ) );

		add_action( 'wp_ajax_wunderground_update', array( &$this, 'update_forecast' ) );
		add_action( 'wp_ajax_nopriv_wunderground_update', array( &$this, 'update_forecast' ) );
	}

	function autocomplete() {

		if(!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wunderground-aq')) {
			exit(0);
		}

		$country = wunderground_get_autocomplete_country_code();

		$url = add_query_arg( array(
			'query' => urlencode( stripslashes_deep( $_REQUEST['query'] ) ),
			'h' => 0, // No hurricanes, please.
			'c' => $country,
			'type' => 'city',
		), 'https://autocomplete.wunderground.com/aq' );

		$response = Wunderground_Request::request( $url );

		exit($response);
	}

}

new Wunderground_Ajax;
