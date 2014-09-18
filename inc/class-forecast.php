<?php


class Wunderground_Forecast {

	/**
	 * Source of forecast (Best Forecast/NWS)
	 * @var string
	 */
	var $source;

	/**
	 * Array of day objects
	 * @var array
	 */
	var $days = NULL;

	function __construct( Wunderground_Request $request ) {

		$results = $request->get_results();

		if( empty( $results->forecast ) ) {

			Wunderground_Plugin::log_error('Response is empty.');

			return NULL;
		}


		if( !empty( $results->forecast ) && !empty( $results->forecast->days ) ) {

			$this->days = array();

			foreach ($results->forecast->days as $key => $day ) {
				$this->days[] = new Wunderground_Forecast_Summary( $day );
			}
		}

		Wunderground_Plugin::log_debug('Wunderground_Forecast', $this);
	}
}
