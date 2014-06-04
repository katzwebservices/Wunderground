<?php

class Wunderground_Forecast_Summary {

	/**
	 * Date object
	 * @var Wunderground_Date
	 */
	var $date;

	var $high;

	var $low;

	var $condition;

	/**
	 * Key of the icon name (partlycloudly)
	 * @var string
	 */
	var $icon;

	/**
	 * Full path the icon url
	 * @var string
	 */
	var $icon_url;

	/**
	 * [$skyicon description]
	 * @var [type]
	 */
	var $skyicon;

	/**
	 * Percentage chance of precipitation
	 * @var int
	 */
	var $pop;

	var $liquid_precip;

	var $snow;

	var $wind_max_speed;

	var $wind_max_dir;
    var $wind_max_dir_degrees;
    var $wind_avg_speed;
    var $wind_avg_dir;
    var $wind_avg_dir_degrees;
    var $humidity_avg;
    var $humidity_min;
    var $humidity_max;

    /**
     * Comparitive summary of weather ("warmer than yesterday")
     * @var string
     */
    var $weather_quickie;

    var $day;
    var $night;

	/**
	 * Unit of temperature. Options: `fahrenheit` or `celsius`
	 * @var string
	 */
	var $unit;

	/**
	 * Unit of temperature abbreviation. Options: `F°` or `°C`
	 * @var string
	 */
	var $unit_abbr;

	function __construct( $day ) {

		foreach ($day as $key => $value) {

			switch($key) {
				default:
					$this->{$key} = $value;
					break;
			}
		}

	}


}
