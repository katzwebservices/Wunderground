<?php

class Wunderground_Response {

	/**
	 * API version
	 * @var float
	 */
	var $version;

	/**
	 * Unit type; metric or english
	 * @var string
	 */
	var $units;

	/**
	 * URL of the ToS
	 * @var string
	 */
	var $termsofService;

	/**
	 * Array with `image`, `title`, `link` keys.
	 *
	 * `image` - URL to Wunderground icon
	 * `title` - Text to display
	 * `link` - URL to link to
	 *
	 * @var object
	 */
	var $attribution;

	/**
	 * API features enabled in the request
	 * @var object
	 */
	var $features;

	/**
	 * Location details
	 *
	 * name
	 * neighborhood
	 * city
	 * state
	 * state_name
	 * country
	 * country_iso3166
	 * country_name
	 * zip
	 * magic
	 * wmo
	 * latitude
	 * longitude
	 * elevation
	 * l - location URL path
	 *
	 * @var object
	 */
	var $location;

	/**
	 * Date information.
	 * @see Wunderground_Date() class
	 * @var object
	 */
	var $date = NULL;

	function __construct(  Wunderground_Request $request ) {

		$results = $request->get_results();

		if( empty( $results->response ) ) {

			Wunderground_Plugin::log_error('Response is empty.');

			return NULL;
		}

		foreach ($results->response as $key => $value) {
			$this->{$key} = $value;
		}

		$this->date = empty( $this->date ) ? NULL : new Wunderground_Date( $this->date );
	}
}
