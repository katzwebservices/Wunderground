<?php

class Wunderground_Alerts {

	var $alerts = array();

	function __construct( Wunderground_Request $request ) {

		$results = $request->get_results();

		if( !empty( $results->alerts ) ) {
			foreach( $results->alerts as $key => $alert ) {
				$this->alerts[] = new Wunderground_Alert( $alert );
			}
		}

	}

}

class Wunderground_Alert {

	var $type;
    var $description;
    var $date;
    var $date_epoch;
    var $expires;
    var $expires_epoch;
    var $tz_short;
    var $tz_long;
    var $message;
    var $phenomena;
    var $significance;

    var $ZONES;
    var $StormBased;

	function __construct( $alert ) {

		foreach ($alert as $key => $value) {
			$this->{$key} = $value;
		}
	}

}
