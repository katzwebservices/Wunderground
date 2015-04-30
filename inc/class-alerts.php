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
