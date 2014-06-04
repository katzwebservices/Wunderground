<?php
/**
 * Wunderground-PHP-API — An php api to parse weather data from http://www.wunderground.com .
 *
 * @license MIT
 *
 * Please see the LICENSE file distributed with this source code for further
 * information regarding copyright and licensing.
 *
 * Please visit the following links to read about the usage policies and the license of Wunderground before using this class.
 * @see http://www.wunderground.com
 * @see http://www.wunderground.com/about
 * @see http://www.wunderground.com/copyright
 * @see http://openweathermap.org/appid
 */

/**
 * The city class representing a city object.
 */
class Wunderground_City
{
    /**
     * @var int The city id.
     */
    public $id;

    /**
     * @var string The name of the city.
     */
    public $name;

    /**
     * @var float The longitude of the city.
     */
    public $lon;

    /**
     * @var float The latitude of the city.
     */
    public $lat;

    /**
     * @var string The abbreviation of the country the city is located in.
     */
    public $country;

    /**
     * Create a new city object.
     *
     * @param int $id The city id.
     * @param string $name The name of the city.
     * @param float $lon The longitude of the city.
     * @param float $lat The latitude of the city.
     * @param string $country The abbreviation of the country the city is located in.
     *
     * @internal
     */
    public function __construct($id, $name, $lon, $lat, $country)
    {
        $this->id = (int)$id;
        $this->name = (string)$name;
        $this->lon = (float)$lon;
        $this->lat = (float)$lat;
        $this->country = (string)$country;
    }
}
