<?php

class Wunderground_Request {

	/**
	 * @var string $apiKey Your Wunderground.com API key
	 */
	private $apiKey = '3ffab52910ec1a0e';

	/**
	 * What features should be fetched by the request?
	 * @var array
	 */
	private $features = array('forecast10day', 'conditions', 'alerts' );

#	private $features = array( 'alerts', 'almanac','conditions','currenthurricane','forecast','forecast10day','geolookup','history','hourly','planner','rawtide','tide','webcams','yesterday');

	/**
	 * @var string $weatherUrl The basic api url to fetch weather data from.
	 */
	private $apiUrl = "http://api.wunderground.com/api";

	/**
	 * Language for the request
	 * @var string
	 */
	private $language = 'EN';


	private $units = 'english';

	/**
	 * Should the request be cached?
	 * @var boolean
	 */
	private $cache = true;

	/**
	 * JSON array object
	 * @var array
	 */
	private $results = array();

	static $instance;

	function __construct( $location = '', $features = array(), $language = 'EN', $units = 'english', $cache = true ) {

		$this->location = $location;

		$features = wp_parse_args( $features, $this->features );

		$this->set_features( $features );
		$this->set_language( $language );
		$this->set_units( $units );
		$this->set_cache( $cache );
		$this->set_results();

	}

	private function set_cache( $cache ) {

		if( isset($_GET['cache']) && current_user_can( 'manage_options' ) ) {
			$this->cache = false;
			return;
		}

		if( empty( $cache ) || $cache === 'false' ) {
			$this->cache = false;
		} else {
			$this->cache = true;
		}

	}

	private function set_units( $units ) {

		switch ( strtolower( $units ) ) {
			case 'c':
			case 'celsius':
			case 'metric':
				$this->units = 'metric';
				break;
			default:
				$this->units = 'english';
				break;
		}
	}

	private function set_results() {
		$url = $this->build_url();
		$response = self::request( $url, $this->cache );

		$response = $this->_v2_json_fix( $response );

		$this->results = json_decode( $response );

		Wunderground_Plugin::log_debug('Wunderground_Request results for URL '. $url, $this->results);
	}

	/**
	 * V2 of the API had broken response JSON that ended with commas. This fixes that.
	 * @param  [type] $response [description]
	 * @return [type]           [description]
	 */
	private function _v2_json_fix( $response ) {

		$response = preg_replace( '/\}\s+,\s+?\}$/ism', "\t}\n}", $response );

		$response = preg_replace('/\}(\s+,\s+?)+/ism', "},", $response);


		return $response;
	}

	public function get_results() {
		return $this->results;
	}

	private function build_url() {

		if( empty($this->location) ) {
			throw new Exception('You must supply a location when constructing a Wunderground_Request.');
		}

		// Combine features into /feature1/feature2/
		$features = implode('/', $this->features);

		// Add the language to the URL
		$language = sprintf( 'lang:%s', strtoupper($this->language) );

		// Add the units measurement (F° or C°, inches vs mm)
		$units = sprintf( 'units:%s', $this->units );

		$location = $this->location;

		// We've got a PWS!
		if( preg_match( '/[K][A-Z]{5,10}[0-9]{1,10}/', $location ) ) {
			$location = '/q/pws:'.urlencode($location);
		}

		// If the location is a link, we don't need to turn it...wait for it...into a link.
		$location_path = preg_match( '/\/q\//ism', $location ) ? $location : '/q/'.urlencode($location);

		// Combine into one URL
		$url = sprintf('%s/%s/v:2.0/%s/%s/%s%s.json', $this->apiUrl, $this->apiKey, $language, $units, $features, $location_path );

		return $url;
	}

	static function request($url, $cache = true) {

		// Generate a cache key based on the result. Only get the first 44 characters because of
		// the transient key length limit.
		$cache_key = substr( 'wu_'.sha1($url) , 0, 44 );

		$response = get_transient( $cache_key );

		// If there's no cached result or caching is disabled
		if( empty( $cache ) || empty( $response ) ) {

			$atts = apply_filters( 'wunderground_request_atts', array(
				'timeout' => 10
			));

			$request = wp_remote_request( $url , $atts );

			$response = wp_remote_retrieve_body( $request );

			// Cache the request for a week
			set_transient( $cache_key, $response, apply_filters( 'wunderground_cache_time', HOUR_IN_SECONDS ) );
		}

		return $response;
	}

	private function set_language( $language = 'EN' ) {

		// If the helper function doesn't exist for some reason, don't use it
		if(function_exists('wunderground_get_language')) {
			$this->language = wunderground_get_language( $language );
		} else {
			$this->language = $language;
		}
	}

	private function set_features( $features = array() ) {


		$available_features = array(
			'alerts',
			'almanac',
			'astronomy',
			'conditions',
			'currenthurricane',
			'forecast',
			'forecast10day',
			'geolookup',
			'history',
			'hourly',
			'hourly10day',
			'planner',
			'rawtide',
			'tide',
			'webcams',
			'yesterday',
		);

		foreach ($features as $feature) {

			// The feature exists and is valid
			if( !empty($feature) && in_array( $feature, $available_features ) ) {
				// If it's not already set as a feature
				if( !in_array($feature, $this->features) ) {
					$this->features[] = $feature;
				}
			} else {
				_doing_it_wrong( 'Wunderground_Request::set_features()', sprintf('You have tried to set a feature that does not exist: %s.', '<code>'.$feature.'</code>'), Wunderground_Plugin::version );
			}

		}
	}

	public function getForecast() {

	}

}
