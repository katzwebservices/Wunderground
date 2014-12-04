=== Weather Underground ===
Tags: weather, weather.com, wunderground, weather underground, weatherbug, forecast, Yahoo! Weather, wp-weather, wp weather, local weather, weather man, weather widget, cool weather, accuweather, get weather, wordpress weather
Requires at least: 3.6
Tested up to: 4.0
Stable tag: trunk
Contributors: katzwebdesign, katzwebservices
Donate link: https://katz.co

Get accurate and beautiful weather forecasts powered by Wunderground.com

== Description ==

### Wunderground is the best WordPress weather site.

Wunderground.com has the most accurate and in-depth weather information. They're also not evil corporate giants, and are weather geeks, which is nice. This plugin uses the Wunderground API for its accurate forecasts.

### If you want a great-looking weather forecast, use this plugin.

__This is the best-looking weather forecast plugin for WordPress.__ It looks great on many different templates out of the box, including the default WP theme.

### For more information, visit the [plugin information page](https://github.com/katzwebservices/Wunderground#setting-up-the-plugin)

Learn about setting up the plugin, how to configure the shortcode, template overrides and more on the [plugin info page](https://github.com/katzwebservices/Wunderground#setting-up-the-plugin).

-------------------

#### About Weather Underground

> The beating heart of our brand is the generous and passionate community of weather enthusiasts that share weather data and content across our products. With over 34,000 of our members sending real-time data from their own personal weather stations, they provide us with the extensive data that makes our forecasts and products so unique.

*Weather Underground is a registered trademark of The Weather Channel, LLC. both in the United States and internationally. The Weather Underground Logo is a trademark of Weather Underground, LLC.*

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. To add a forecast to your sidebar, go to Appearance, then Widgets. Click on the Wunderground widget and add it to a  sidebar.
1. To embed a forecast in a post or page, use the `[wunderground]` "shortcode" as described on the plugin page's [Shortcode Parameter](https://github.com/katzwebservices/Wunderground#shortcode-parameters) guide.

== Screenshots ==

1. Widget configuration
1. Vertical Table layout
1. Horizontal Table layout

== Frequently Asked Questions ==

= [Upgrading from 1.x] Where is the settings page? =

Version 2.0 got rid of the default settings page; now shortcodes and widgets are configured individually.

= [Upgrading from 1.x] My forecast looks different =

Version 2.0 made lots of changes as to how the forecast is displayed. You can download the last update of the "1.x" version of the plugin here: **[Version 1.2.5.1](https://downloads.wordpress.org/plugin/wunderground.1.2.5.1.zip)**

= [Upgrading from 1.x] My location can no longer be found =
If your location isn't working any more, follow the steps below:

* Go to Wunderground.com
* In the "Search Locations" box, type in your location
* Click on the location when it appears in the auto-complete box
* When the page loads, copy the URL. It will likely look like this: `http://www.wunderground.com/q/zmw:00000.4.17340`
* Copy the part of the URL after the `/q/`. In this example, it would be `zmw:00000.4.17340`
* Use that as your location in the shortcode, like this: `[wunderground location="zmw:00000.4.17340" /]`
* That should work!

= How do I use my own icons? =

If you want to use your own icons, you would add a filter to the bottom of your theme's <code>functions.php</code> file. See a [list of icons you should have available](http://www.wunderground.com/weather/api/d/docs?d=resources/icon). Here's sample code:

<pre>
add_filter('wp_wunderground_forecast_icon', 'use_custom_wunderground_icons', 10, 2 );

function use_custom_wunderground_icons( $url_base = '', $icon_name = '' ) {
	return 'http://www.example.com/path-to-full-icon-set/';
}
</pre>

= I want to modify the forecast output. How do I do that? =

Please see the "Using your own template" section on the [Plugin Github page](https://github.com/katzwebservices/Wunderground)

= What is the plugin license? =

This plugin is released under a GPL license. *Weather Underground is a registered trademark of The Weather Channel, LLC. both in the United States and internationally. The Weather Underground Logo is a trademark of Weather Underground, LLC.*

= Do I need a Wunderground account? =
Weather Underground has been very gracious and has provided the plugin with free data - you don't need your own account. If you want to use Wunderground data in your own application, [register for a Weather Underground API account](http://www.wunderground.com/?apiref=5f97d1e033236c26).

== Changelog ==

= Version 2.0 is a major update! =

If you are upgrading the plugin, your forecast will look different. Version 2.x made lots of changes as to how the forecast is displayed. If you want to go back after upgrading, you can [download the previous version here](https://downloads.wordpress.org/plugin/wunderground.1.2.5.1.zip).

* Fixed: Fatal error when no location is set in shortcode
* Fixed: Link to Wunderground now goes to forecast URL, not the weather station URL
* Fixed: Duplicate forecasts in Horizontal Table layout ([reported here](https://github.com/katzwebservices/Wunderground/issues/12))
* Fix: Remove erroneous PHP code in `<table>` tag
* Tweak: Replace `<table cellspacing="0">` with CSS
= 2.0.9 on October 22 =
* Added: Caching of forecast output HTML. This makes the plugin use fewer server resources when displaying the forecast.
* Fixed: Widget forecasts use the correct date formatting (set by `wunderground_get_date_format()`)
* Modified: Locally storing font, which fixes issues with older IE versions causing site load time issues
* Added: Hebrew translation - thanks, [@nirbentzy](https://www.transifex.com/accounts/profile/nirbentzy/)!

= 2.0.8 on October 7 =
* Fixed: PHP warning [reported here](https://wordpress.org/support/topic/error-with-new-version-1)
* Fixed: Date wasn't respecting current timezone ([as reported here](https://github.com/katzwebservices/Wunderground/issues/9))
* Added: Support for `[forecast]` and `[wunderground]` shortcodes in Text widgets by adding a `do_shortcode` filter on widgets. This had been enabled in Version 1.x.
* Added: `wp_wunderground_forecast` filter to be backward-compatible with Version 1.x

= 2.0.7 on October 3 =
* Fixed: `hidedata` shortcode parameter wasn't working properly
* Modified: Removed support for `%%day%%` `%%month%%` and `%%year%%` placeholder tags

= 2.0.6 on October 3 =
* Added: Support for using using settings from Version 1.x as the defaults. This fixes the loss of your configuration if you use the `[forecast]` shortcode with no parameters.
* Added: Lithuanian translation

= 2.0.5 on October 2 =
* Added: `datelabel` parameter for backward compatibility
* Added: `wunderground_get_date_format()` function to process date format
* Added: German and Romanian translations
* Fixed: Escaped translation strings
* Tweak: Improved readme

= 2.0.4 =
* Fixed: Widget location data saving is fixed
* Fixed: Number of days display properly for the Simple forecast
* Added: Support for RTL languages
* Added: Show the Horizontal Table layout in widget options
* Tweak: Move widget settings around
* Added: Night forecast support for picker
* Added: Locally host icons
* Fixed: Fixed: Hide chance of precipitation when not set
* Fixed: Show current forecast in horizontal table mode
* Fixed: Show temperature in lieu of high/low for current conditions
* Modified: Converted template files to include snippets for forecast elements
* Tweak: Update language files
* Tweak: Added descriptions to widget checkboxes
* Tweak: Add search box as an optional widget checkbox
* Tweak: Widget now uses `wunderground_parse_atts()`

= 2.0.3 =
* Fixed: Cached results weren't being used!
* Fixed: Autocomplete not working in admin Widgets page
* Fixed: Handle queries with quotes in them
* Modified: Switched to showing global results by default. You can still limit results by using the `wunderground_autocomplete_country_code` filter and returning a country code.

= 2.0.2 =
* Fixed: `table-horizontal` layout now works
* Fixed: `simple` template updated to use Chance of Precipitation translation string
* Fixed: Added checks to make sure `numdays` is always less than 10
* Added: `hidedata` attribute that works like `showdata`, but in reverse. By default, all items are shown when embedding the shortcode (conditions, precipitation %, icon, etc). If you want to hide the icon, for example, you would add `hidedata="icon"` to the shortcode.
* Added: `wunderground_twig_debug` filter. You can force Twig debug mode by adding `add_filter( 'wunderground_twig_debug' '__return_true' );` to your theme's `functions.php` file. You can also enable debug mode if you're logged in as an Administrator by adding `?debug` to your current page's URL.

= 2.0.1 =
* Added: `location_title` parameter. Setting `location_title` overwrites that location that is displayed in the search bar, so that if your location is "ugly", like longitude/latitude, you can overwrite that.
* Fixed: Forecast icon display in themes that add borders, etc. to images
* Fixed: Units (C/F) now working in shortcode
* Fixed: Added support for Personal Weather Station codes in the shortcode

= 2.0 =
* __Major re-write__ You may need to re-configure your widget.
* Images are now stored locally; this will allow use on SSL-secured websites.

= 1.2.5.1 =
* Quick fix for icon issues: the icon images were broken.

= 1.2.5 =
* Fixed issue where checkbox state wasn't being reflected in settings (as mentioned <a href="http://wordpress.org/support/topic/plugin-weather-forecast-wp-wunderground-not-saving-checkbox-settings" rel="nofollow">in this support thread</a>)

= 1.2.4 =
* Removed error generation when XML file cannot be read (Error on line 427, <a href="http://wordpress.org/support/topic/506565" rel="nofollow">as reported</a>). Now, it just outputs an HTML comment error message.

= 1.2.3 =
* Fixed bug where Degree Measurement select drop-down would not show saved state as Celsius, even though it was working properly. (thanks <a href="http://www.OwlConcept.com">Robson</a>)
* Added proper HTML escaping for High/Low formatting and temperature output

= 1.2.2 =
* Added GoDaddy compatibility by switching from `simplexml_load_file` to `wp_remote_fopen` and `simplexml_load_string` (<a href="http://wordpress.org/support/topic/490946">thanks, rjune</a>)

= 1.2.1 =
* Fixed issue with "Give thanks" link

= 1.2 =
* Improved data storage, fixing issues users were having with Celsius / Fahrenheit settings and setting the number of columns in a table
* A new `cache` option has been added to the shortcode. Add `cache=0` or `cache=` to the shortcode to disable storing forecasts. <strong>Not recommended;</strong> will dramatically slow down site.
	* If you want to refresh the results, you can add `?cache=false` to your URL and the forecast will be updated.
* Added "width" option to shortcode to define table width. `100%` is the default. Use `width=0` or `width=` to disable hard-coding width in table.
* Changed the default high/low setting to add the degree symbol.
* Removed code whitespace when storing table for added speed
* Added CSS classes to forecast columns based on weather conditions. This will allow you to make "Partly Cloudy" columns gray, "Sunny" blue, etc.
* Added three new filters:
	* `wp_wunderground_forecast_cache` - How long results are cached for. Default: 6 hours.
	* `wp_wunderground_forecast_icon`
	* `wp_wunderground_forecast_conditions`
	* `wp_wunderground_forecast_temp`
* Rounded column width to two digits. Instead of `16.66666667%`, it's now `16.67%`

= 1.1 =
* Added data storage - the plugin will now store forecast tables for 6 hours. This should speed up the time it takes to load the forecasts.
* Added a check for PHP5 and `simplexml_load_file`, which are required for the plugin.

= 1.0 =
* Initial launch

== Upgrade Notice ==

= Version 2.0 is a major update! =

If you are upgrading the plugin, your forecast will look different. Version 2.x made lots of changes as to how the forecast is displayed. If you want to go back after upgrading, you can [download the previous version here](https://downloads.wordpress.org/plugin/wunderground.1.2.5.1.zip).

* Fixed: Link to Wunderground now goes to forecast URL, not the weather station URL
* Fix: Remove erroneous PHP code in `<table>` tag
* Tweak: Replace `<table cellspacing="0">` with CSS
= 2.0.9 on October 22 =
* Added: Caching of forecast output HTML. This makes the plugin use fewer server resources when displaying the forecast.
* Fixed: Widget forecasts use the correct date formatting (set by `wunderground_get_date_format()`)
* Modified: Locally storing font, which fixes issues with older IE versions causing site load time issues
* Added: Hebrew translation - thanks, [@nirbentzy](https://www.transifex.com/accounts/profile/nirbentzy/)!

= 2.0.8 on October 7 =
* Fixed: PHP warning [reported here](https://wordpress.org/support/topic/error-with-new-version-1)
* Fixed: Date wasn't respecting current timezone ([as reported here](https://github.com/katzwebservices/Wunderground/issues/9))
* Added: Support for `[forecast]` and `[wunderground]` shortcodes in Text widgets by adding a `do_shortcode` filter on widgets. This had been enabled in Version 1.x.
* Added: `wp_wunderground_forecast` filter to be backward-compatible with Version 1.x

= 2.0.7 on October 3 =
* Fixed: `hidedata` shortcode parameter wasn't working properly
* Modified: Removed support for `%%day%%` `%%month%%` and `%%year%%` placeholder tags

= 2.0.6 on October 3 =
* Added: Support for using using settings from Version 1.x as the defaults. This fixes the loss of your configuration if you use the `[forecast]` shortcode with no parameters.

= 2.0.5 on October 2 =
* Added: `datelabel` parameter for backward compatibility
* Added: `wunderground_get_date_format()` function to process date format
* Added: German and Romanian translations
* Fixed: Escaped translation strings
* Tweak: Improved readme

= 2.0.4 =
* Fixed: Widget location data saving is fixed
* Fixed: Number of days display properly for the Simple forecast
* Added: Support for RTL languages
* Added: Show the Horizontal Table layout in widget options
* Tweak: Move widget settings around
* Added: Night forecast support for picker
* Added: Locally host icons
* Fixed: Fixed: Hide chance of precipitation when not set
* Fixed: Show current forecast in horizontal table mode
* Fixed: Show temperature in lieu of high/low for current conditions
* Modified: Converted template files to include snippets for forecast elements
* Tweak: Update language files
* Tweak: Added descriptions to widget checkboxes
* Tweak: Add search box as an optional widget checkbox
* Tweak: Widget now uses `wunderground_parse_atts()`

= 2.0.3 =
* Fixed: Cached results weren't being used!
* Fixed: Autocomplete not working in admin Widgets page
* Fixed: Handle queries with quotes in them
* Modified: Switched to showing global results by default. You can still limit results by using the `wunderground_autocomplete_country_code` filter and returning a country code.

= 2.0.2 =
* Fixed: `table-horizontal` layout now works
* Fixed: `simple` template updated to use Chance of Precipitation translation string
* Fixed: Added checks to make sure `numdays` is always less than 10
* Added: `hidedata` attribute that works like `showdata`, but in reverse. By default, all items are shown when embedding the shortcode (conditions, precipitation %, icon, etc). If you want to hide the icon, for example, you would add `hidedata="icon"` to the shortcode.
* Added: `wunderground_twig_debug` filter. You can force Twig debug mode by adding `add_filter( 'wunderground_twig_debug' '__return_true' );` to your theme's `functions.php` file. You can also enable debug mode if you're logged in as an Administrator by adding `?debug` to your current page's URL.

= 2.0.1 =
* Added: `location_title` parameter. Setting `location_title` overwrites that location that is displayed in the search bar, so that if your location is "ugly", like longitude/latitude, you can overwrite that.
* Improved Widget design to make the template screenshots clearer.
* Fixed: Forecast icon display in themes that add borders, etc. to images
* Fixed: Units (C/F) now working in shortcode
* Fixed: Added support for Personal Weather Station codes in the shortcode
* Fixed: Fatal error caused by date being empty instead of StdClass.

= 2.0 =
* __Major re-write__ You may need to re-configure your widget.
* Images are now stored locally; this will allow use on SSL-secured websites.

= 1.2.5.1 =
* Quick fix for icon issues: the icon images were broken.

= 1.2.5 =
* Fixed issue where checkbox state wasn't being reflected in settings (as mentioned <a href="http://wordpress.org/support/topic/plugin-weather-forecast-wp-wunderground-not-saving-checkbox-settings" rel="nofollow">in this support thread</a>)

= 1.2.4 =
* Removed error generation when XML file cannot be read (Error on line 427, <a href="http://wordpress.org/support/topic/506565" rel="nofollow">as reported</a>). Now, it just outputs an HTML comment error message.

= 1.2.3 =
* Fixed bug where Degree Measurement select drop-down would not show saved state as Celsius, even though it was working properly. (thanks <a href="http://www.OwlConcept.com" rel="nofollow">Robson</a>)
* Added proper HTML escaping for High/Low formatting and temperature output

= 1.2.2 =
* Added GoDaddy compatibility by switching from `simplexml_load_file` to `wp_remote_fopen` and `simplexml_load_string` (<a href="http://wordpress.org/support/topic/490946">thanks, rjune</a>)

= 1.2.1 =
* Fixed issue with "Give thanks" link

= 1.2 =
* Improved data storage, fixing issues users were having with Celsius / Fahrenheit settings and setting the number of columns in a table
* Many other updates & improvements - cheek the changelog.

= 1.1 =
* Added data storage - the plugin will now store forecast tables for 6 hours. This should speed up the time it takes to load the forecasts.
* Added a check for PHP5 and `simplexml_load_file`, whichare required for the plugin. Users will no longer get `Parse error: syntax error, unexpected '{' in {your-site}/wunderground.php on line 412` error

= 1.0 =
* Blastoff!
