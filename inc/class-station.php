<?php

class Wunderground_Station {

	var $id;
	var $name;
	var $neighborhood;
	var $city;
	var $state;
	var $state_name;
	var $country;
	var $country_name;
	var $country_iso3166;
	var $latitude;
	var $longitude;
	var $elevation;

	function __construct( stdClass $Station ) {

		foreach ($Station  as $key => $value) {
			$this->{$key} = $value;
		}

	}

}
