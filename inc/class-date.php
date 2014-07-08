<?php

class Wunderground_Date {

	/**
	 * Unix timestamp
	 * @var string
	 */
	var $epoch;

	/**
	 * "Pretty" display of date; "7:00 PM CDT on May 21, 2014"
	 * @var string
	 */
	var $pretty;

	/**
	 * RFC #822 spec date format {@link http://www.ietf.org/rfc/rfc0822.txt}
	 * @var string
	 */
	var $rfc822;

	/**
	 * ISO 8601 spec date format {@link http://en.wikipedia.org/wiki/ISO_8601}
	 * @var string
	 */
	var $iso8601;

	/**
	 * Current year
	 * @var int
	 */
	var $year;

	/**
	 * Current month
	 * @var int
	 */
	var $month;

	/**
	 * Current day of month
	 * @var int
	 */
	var $day;

	/**
	 * The number of the day of the year `140` (out of 365.25)
	 * @var int
	 */
	var $yday;

	/**
	 * Current hour of 24-hour clock
	 * @var int
	 */
	var $hour;

	/**
	 * Current minute
	 * @var int
	 */
	var $min;

	/**
	 * Current second
	 * @var int
	 */
	var $sec;

	/**
	 * Current month full name
	 * @var string
	 */
	var $monthname;

	/**
	 * Current month short name "Oct" instead of "October". Not set for short months like "May".
	 * @var string
	 */
	var $monthname_short;

	/**
	 * Current day name; "Wednesday"
	 * @var string
	 */
	var $weekday;

	/**
	 * Current day short name "Wed" instead of "Wednesday"
	 * @var string
	 */
	var $weekday_short;

	/**
	 * AM or PM
	 * @var string
	 */
	var $ampm;

	/**
	 * Current timezone short string "CDT" for Central Daylight Time
	 * @var string
	 */
	var $tz_short;

	/**
	 * Current timezone long string "America/Chicago"
	 * @var string
	 */
	var $tz_long;

	/**
	 * Time zone offset text. Not sure how this is used.
	 * @var string
	 */
	var $tz_offset_text;

	/**
	 * Time zone GMT offset.
	 * @var float
	 */
	var $tz_offset_hours;

	function __construct( $date ) {

		if( !is_object( $date ) ) { return NULL; }

		foreach ($date as $key => $value) {
			$this->{$key} = $value;
		}

	}

}

