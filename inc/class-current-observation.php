<?php

class Wunderground_Current_Observation {

	var $source;

	var $station;

	var $estimated;

	var $date;

	var $metar;

	/**
	 * Short summary of conditions ("clear, partly cloudy, etc.")
	 * @var string
	 */
	var $condition;

	var $temperature;

	var $humidity;

	var $wind_speed;

	var $wind_gust_speed;

	var $wind_dir;

	var $wind_dir_degrees;

	var $wind_dir_variable;

	var $pressure;

	var $pressure_trend;

	var $dewpoint;

	var $heatindex;

	var $windchill;

	var $feelslike;

	var $visibility;

	var $cloud_description;

	var $solarradiation;
	var $uv_index;
	var $precip_1hr;
	var $precip_today;
	var $soil_temp;
	var $soil_moisture;
	var $leaf_wetness;

    var $icon;

    var $icon_url;

    var $forecast_url;

    var $history_url;

    var $ob_url;

    var $pollen;

    var $flu;

    var $ozone_index;

    var $ozone_text;

    var $pm_index;

    var $pm_text;

	function __construct( $request ) {

		$results = $request->get_results();

		if( empty( $results->forecast ) ) {
			Wunderground_Plugin::log_error('Response is empty.');
			return NULL;
		}

		foreach( $results->current_observation as $key => $value ) {
			$this->{$key} = $value;
		}

		$this->station = new Wunderground_Station( $this->station );
		$this->date = new Wunderground_Date( $this->date );
	}

}
