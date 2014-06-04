<?php
die(Wunderground_Plugin::$dir_path);

include_once Wunderground_Plugin::$dir_path.'inc/Request.php';
include_once Wunderground_Plugin::$dir_path.'inc/Response.php';
include_once Wunderground_Plugin::$dir_path.'inc/Date.php';
include_once Wunderground_Plugin::$dir_path.'inc/ForecastDay.php';
include_once Wunderground_Plugin::$dir_path.'inc/Current_Observation.php';
include_once Wunderground_Plugin::$dir_path.'inc/Station.php';
include_once Wunderground_Plugin::$dir_path.'inc/Forecast.php';
include_once Wunderground_Plugin::$dir_path.'inc/Alerts.php';

class KWS_Wunderground {

	var $response;

	var $current_observation;

	var $forecast;

	function __construct( Wunderground_Request $request ) {

		$results = $request->get_results();

	#	echo '<pre>';
	#	print_r($results);
	#	echo '</pre>';

		if( !empty( $results->error ) ) {
			die(print_r($results->error));
			return NULL;
		}

		$this->response = new Wunderground_Response( $request );
		$this->forecast = new Wunderground_Forecast( $request );
		$this->current_observation = new Wunderground_Current_Observation( $request );
		$this->alerts = new Wunderground_Alerts( $request );

		Wunderground_Plugin::log_debug( 'Wunderground_Forecast', $this );
	}

	function getSimpleDay($number) {
		return $this->simple_forecast->days[$number];
	}

	function getTextDay($number) {
		return $this->text_forecast->days[$number];
	}

	function getCurrent() {
		return $this->current_observation;
	}

}
