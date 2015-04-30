<?php

class Wunderground_Alert {

	var $type;

	/**
	 * Alert title or phrase
	 * @var string
	 */
    var $description;

    /**
     * Date and time in GMT
     * @var string
     */
    var $date;

    /**
     * Date and time in GMT
     * @var [type]
     */
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

    // European cities only
    var $wtype_meteoalarm;
    /**
     * Title
     * @var string
     */
    var $wtype_meteoalarm_name;

    var $level_meteoalarm;
    var $level_meteoalarm_name;

    /**
     * Full description
     * @var string
     */
    var $level_meteoalarm_description;

	function __construct( $alert ) {
		foreach ($alert as $key => $value) {
			$this->{$key} = $value;
		}
	}

}
