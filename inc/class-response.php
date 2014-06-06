<?php

class Wunderground_Response {
	var $version;

	var $units;

	var $termsofService;

	var $attribution;

	var $features;

	var $location;

	var $date;

	function __construct(  Wunderground_Request $request ) {

		$results = $request->get_results();

		if( empty( $results->response ) ) {

			Wunderground_Plugin::log_error('Response is empty.');

			return NULL;
		}

		foreach ($results->response as $key => $value) {
			$this->{$key} = $value;
		}

		$this->date = new Wunderground_Date( $this->date );
	}
}
