<?php
/**
 * Class to bring together
 */
class KWS_Wunderground {

	var $response;

	var $current_observation;

	var $forecast;

	var $alerts;

	function __construct( Wunderground_Request $request ) {

		$results = $request->get_results();

		if( !empty( $results->error ) ) {
			// TODO: Handle properly
			Wunderground_Plugin::log_error( 'Error loading results KWS_Wunderground', $results->error );
			return NULL;
		}

		$this->response = new Wunderground_Response( $request );
		$this->forecast = new Wunderground_Forecast( $request );
		$this->current_observation = new Wunderground_Current_Observation( $request );
		$this->alerts = new Wunderground_Alerts( $request );

		Wunderground_Plugin::log_debug( 'Wunderground_Forecast', $this );
	}

}
