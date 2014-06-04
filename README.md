OpenWeatherMap-PHP-Api
======================
A php api to parse weather data from [wunderground.com](http://www.wunderground.com). This api tries to normalise and abstract the data and remove inconsistencies.

If you are looking for an implementation for the [CMS Zikula](http://www.zikula.org), you may want to take a look at [katzwebservices/Weather](https://github.com/katzwebservices/Weather).

For example code and how to use this api, please take a look into `Examples_*.php` files and run them in your browser.
- `Examples_Current.php` Shows how to receive the current weather.
- `Examples_Forecast.php` Shows how to receive weather forecasts.
- `Examples_Cache.php` Shows how to implement a cache.

**Notice:** This api is not made by OpenWeatherMap, nor their offical php api.

Example call
============
```php
<?php
use katzwebservices\Wunderground;
use katzwebservices\Wunderground\Exception as WunderException;

// Remove this line if you are using composer.
require('katzwebservices/Wunderground.php');

// Language of data (try your own language here!):
$lang = 'de';

// Units (can be 'metric' or 'imperial' [default]):
$units = 'metric';

// Get OpenWeatherMap object. Don't use caching (take a look into Example_Cache.php to see how it works).
$owm = new Wunderground();

try {
    $weather = $owm->getWeather('Berlin', $units, $lang);
} catch(WunderException $e) {
    echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
    echo "<br />\n";
} catch(\Exception $e) {
    echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
    echo "<br />\n";
}

echo $weather->temperature;
```

License
=======
MIT — Please see the [LICENSE file](https://github.com/katzwebservices/OpenWeatherMap-PHP-Api/blob/master/LICENSE) distributed with this source code for further information regarding copyright and licensing.

**Please visit the following links to read about the usage policies and the license of OpenWeatherMap before using this class.**
- [wunderground.com](http://www.wunderground.com)
- [wunderground.com/about](http://www.wunderground.com/about)
- [wunderground.com/copyright](http://www.wunderground.com/copyright)
- [wunderground.com/appid](http://www.wunderground.com/appid)

Contribute || Support me
========================
I'm very happy if you open **pull requests** or **issues** to help making this API **more awesome**.

However if you like this and want to **support** _me_, you may want to **flattr** a few coins?

[![Flattr this git repo](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=katzwebservices&url=https://github.com/katzwebservices/OpenWeatherMap-PHP-Api&title=OpenWeatherMap-PHP-Api&language=&tags=github&category=software)

Roadmap
=======
- [x] Add forecast functionality
- [x] Tell the guys of [wunderground.com](http://www.wunderground.com) that you made such an api.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/katzwebservices/openweathermap-php-api/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

