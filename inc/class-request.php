<?php

class Wunderground_Request {

	/**
	 * What features should be fetched by the request?
	 *
	 * Available features: 'alerts', 'almanac','conditions','currenthurricane','forecast','forecast10day','geolookup','history','hourly','planner','rawtide','tide','webcams','yesterday'
	 *
	 * @var array
	 */
	private $features = array('forecast10day', 'conditions', 'alerts' );

	/**
	 * The Wunderground API URL endpoint
	 * @var string
	 */
	private $apiUrl = "http://api.wunderground.com/api";

	/**
	 * Language for the request
	 * @var string
	 */
	private $language = 'EN';

	/**
	 * Unit of measurement ('english' or 'metric')
	 * @var string
	 */
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

	/**
	 * Publicly-accessible instance of the class
	 * @var Wunderground_Request
	 */
	static $instance;

	/**
	 * Create and fetch a request to Wunderground.com API
	 *
	 * Location:
	 *   - "City, State" combination, such as "AZ/Phoenix". Often, you can use commas in natural-language format, such as "Phoenix, AZ", but it doesn't always work.
	 *   - US ZIP Code, such as "60290"
	 *   - "Country/City" combination, such as "France/Paris" or "South Africa/Cape Town". Often, you can use commas in natural-language format, such as "Paris, France", but it doesn't always work.
	 *   - Wunderground.com "zmw" ID - Search for the location on wunderground.com and click on the results page. The URL should look something like `http://www.wunderground.com/q/zmw:00000.1.68816`. The `zmw:00000.1.68816` part can be used as the location.
	 *   - Wunderground.com location URL - Pass a Wunderground.com URL with a forecast (not a list of stations), such as `http://www.wunderground.com/weather-forecast/ZA/Cape_Town.html`
	 *   - Wunderground PWS ID - You can pass the ID of a Personal Weather Station. Wunderground assigns unique IDs, such as `KCASANFR70` or `pws:KCASANFR70`.
	 *   - Latitude, Longitude - Pass comma-separated latitude, longitude, such as `37.427, -108.527` or `37.8,-122.4`
	 *   - Airport Codes - Use standard airport codes, such as `DIA`, `JFK`, `SFO`
	 *
	 * @param string  $location
	 * @param array   $features [description]
	 * @param string  $language [description]
	 * @param string  $units    [description]
	 * @param boolean $cache    [description]
	 */
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

		try {

			$url = $this->build_url();

		} catch( Exception $e ) {

			Wunderground_Plugin::log_debug('Wunderground_Request had no location set. Returning because of exception.' );

			return;
		}


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

		$include_pws = false;

		// We've got a PWS!
		// Match both pws:KCASANFR70 and KCASANFR70 formats
		// I716, MBGLA2, M41101, MRNDA2, MBGLA2
		if( preg_match( '/(pws\:)?([A-Z]{1,11}[0-9]{1,11})/', $location, $matches ) ) {
			$location = isset( $matches[2] ) ? $matches[2] : $location;
			$location = '/q/pws:'.urlencode($location);
			$include_pws = true;
		}

		/**
		 * Include PWS stations in the results?
		 * @since TODO
		 * @param int `0` for no, `1` for yes. Default: `0`
		 */
		$pws = sprintf( 'pws:%d', intval( apply_filters( 'wunderground_include_pws', $include_pws ) ) );

		/*
		http://www.wunderground.com/weather-forecast/FR/Paris.html
		http://www.wunderground.com/q/FR/Paris.html
		http://www.wunderground.com/personal-weather-station/dashboard?ID=I75003PA1
		*/

		// If the location is a link, we don't need to turn it...wait for it...into a link.
		$location_path = preg_match( '/\/q\//ism', $location ) ? $location : '/q/'.rawurlencode($location);

		// Combine into one URL
		$url = sprintf('%s/%s/v:2.0/%s/%s/%s/%s%s.json', $this->apiUrl, Wunderground_Plugin::$api_key, $language, $units, $pws, $features, $location_path );

		return $url;
	}

	/**
	 * Fetch a URL and use/store cached result
	 *
	 * - Cached results are stored as transients starting with `wu_`
	 * - Results are stored for one hour by default, but that can be overridden by using the `wunderground_cache_time` filter.
	 * - The request array itself can be filtered by using the `wunderground_request_atts` filter
	 *
	 * @filter  wunderground_cache_time description
	 * @param  [type]  $url   [description]
	 * @param  boolean $cache [description]
	 * @return [type]         [description]
	 */
	static function request($url, $cache = true) {

		// Generate a cache key based on the result. Only get the first 44 characters because of
		// the transient key length limit.
		$cache_key = substr( 'wu_'.sha1($url) , 0, 44 );
		
		$response = get_transient( $cache_key );

		// If there's no cached result or caching is disabled
		if( empty( $cache ) || empty( $response ) ) {

			/**
			 * Modify the request array. By default, only sets timeout (10 seconds)
			 * @var array
			 */
			$atts = apply_filters( 'wunderground_request_atts', array(
				'timeout' => 10
			));

			$request = wp_remote_request( $url , $atts );

			if( is_wp_error( $request ) ) {
				$response = false;
				
			} else {

				$response = wp_remote_retrieve_body( $request );

				/**
				 * Modify the number of seconds to cache the request for.
				 *
				 * Default: cache the request for one hour, since we're dealing with changing conditions
				 *
				 * @var int
				 */
				$cache_time = apply_filters( 'wunderground_cache_time', HOUR_IN_SECONDS );

				// Backward compatible with 1.x
				$cache_time = apply_filters( 'wp_wunderground_forecast_cache', $cache_time );

				set_transient( $cache_key, $response, (int)$cache_time );
				
			}
		}

		return $response;
	}

	/**
	 * Set the language for the forecast
	 * @param string $language [description]
	 */
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
