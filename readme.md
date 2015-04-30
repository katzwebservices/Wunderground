### Reporting issues ###

If you have issues with the plugin, please:

1. [Report the issue on Github](https://github.com/katzwebservices/Wunderground/issues/new) __(much preferred)__
2. Report the issue [using this form](https://widget.uservoice.com/omnibox/Csq4WQZTBPGtRqSZXIsNA?mode=contact&locale=en&forum_id=254985&contact_us=true&accent_color=007DBF&embed_type=popover&trigger_method=custom_trigger&menu=true&screenshot_enabled=false&contact_enabled=true&feedback_enabled=false&smartvote=false)

## Setting up the plugin

The Wunderground plugin displays forecasts in the Wunderground.com widget or by using the `[wunderground]` shortcode.

## Widget
To configure the widget, go to your WordPress Dashboard, then navigate to Appearance, then click on the Widgets submenu.

Once there, you will see a Wunderground widget. Add it to one of your sidebars and follow the on-screen instructions.

## Shortcode

You can use either the `[wunderground]` or `[forecast]` shortcode to embed a forecast in a post or page.

### Shortcode Example

This code:
`[wunderground location="Philadelphia, PA" numdays="3" layout="simple"]`

Will output the forecast using the `simple` template, show three days of forecasts for Philadelphia, PA.

### Shortcode Parameters

* `location` - Define the location of the forecast. Default: `Denver, Colorado`. It can be in any format compatible with Wunderground, including:
	*  City / State (example: `{city},{state}`)
	*  Country / City (example: `Paris, France`)
	*  Coordinates (example: `{longitude}, {latitude}`),
	*  Wunderground "zmw" (example: `zmw:00000.1.12345`)
	*  Wunderground Personal Weather Station (PWS) ID (example: `KCODOLOR2`)
	*  3-4 character airport code (example: `DEN`)
* `location_title` - If your shortcode uses an ugly location title like `zmw:00000.1.12345`, you might want to override that! Use this parameter to define how the location appears in the shortcode output.
* `iconset` - Choose from Wunderground icon sets. Default: `Incredible`. Options are: `Wunderground`, `Elemental`, `Incredible`, `Helen`, `Default`, `Smiley`,`Generic`, `Old School`, `Cartoon`, `Mobile`, `Simple`, `Contemporary`
* `numdays` - Number of days to show in a forecast. Default: `5`
* `class` - CSS class to be added to the `<div>` that wraps the output. Default: `wp_wunderground`
* `datelabel` - Change the format of the date using PHP date formatting. See [Formatting Date and Time](http://codex.wordpress.org/Formatting_Date_and_Time) for more information. Default: `m/d` (Example for October 21: `10/21`)
* `layout` - The layout template to be used. The value of this parameter is used to find the HTML template file in the `templates` directory. For example, `simple` loads the `templates/simple.html` template file. See Templates below for how the templates are loaded and can be overruled. Default: `table-vertical`
	- `table-vertical` - A vertical table with each day as a row
	- `table-horizontal` - A horizontal table with each day as a column
	- `simple` - A flexible day view
	- `current` - Current conditions only
* `measurement` - Whether to show items in Fahrenheit and Imperial (`english` or `f`) or Celsius and Metric (`metric` or `c`). Default: `english`
* `language` - A two-letter code representing the language. [See the complete list of supported language codes here](http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1). Default: `EN`
* `showdata` - The items to show in the forecast. Default: `search,alert,daynames,pop,icon,text,conditions,date`
	- `search` - Location search bar
	- `daynames` - Display the day of the week (Example: "Thursday")
	- `icon` - Weather forecast icon
	- `pop` - % chance of precipitation
	- `text` - Forecast text summary
	- `date` - Show the date in the output
	- `conditions` - Short summary of conditions ("Clear", "Partly Cloudy", etc.)
	- `highlow` - Show the high & low temperatures for forecast.
	- `text` - Display a description of the forecast, normally in sentence format.
	- `alerts` - Weather alerts for the forecast area.
* `hidedata` - The items to hide in the forecast. Instead of setting what to show, use the defaults and set what to hide. Accepts the same parameters as `showdata`. Default: `(Empty)` Example: `hidedata="search"` will hide the search bar, but will show the rest of the default data.

## Finding your location

If you're using the shortcode, and your location can't be found:

* Go to Wunderground.com
* In the "Search Locations" box, type in your location
* Click on the location when it appears in the auto-complete box
* When the page loads, copy the URL. It will likely look like this: `http://www.wunderground.com/q/zmw:00000.4.17340`
* Copy the part of the URL after the `/q/`. In this example, it would be `zmw:00000.4.17340`
* Use that as your location in the shortcode, like this: `[wunderground location="zmw:00000.4.17340" /]`
* That should work!

## International

### Language
The Wunderground plugin attempts to use the language you defined in the WordPress admin by default. You can override the language in the plugin's widget and also in the shortcode using the `language` attribute. See Shortcode above for more information. You can override the default language using the `wunderground_default_language` filter. See the "Filters" section below.

### Wunderground subdomains
The plugin will read your defined language and try to determine the Wunderground.com subdomain to use. For example, the Simplified Chinese (code: CN) Wunderground site is `simplifiedchinese.wunderground.com`.

If you want to override the setting, use the `wunderground_redirect_subdomain` filter, which passes two parameters:  `$subdomain` for the current subdomain, and `$language_key` for the defined language.

### Help translate the plugin!

We'd love your assistance in helping to translate the plugin. __[Submit your translations here](https://www.transifex.com/projects/p/wunderground/)__.

## Templates

### Using your own templates
If you want to use your own template:

* Create a directory in your theme named `wunderground` - the path will look like: `wp-content/themes/your-theme/wunderground/`
* Go to the Wunderground plugin directory (`wp-content/wunderground/templates/`)
* Copy the files you want to change into the `wunderground` directory you created
* Modify the files

The template uses the [Twig template engine](http://twig.sensiolabs.org) to access the data. This helps keep the plugin secure.

If you want to have the files located in another place, you can use the `wunderground_template_paths` filter to modify the paths checked. [See template.php](https://github.com/katzwebservices/Wunderground/blob/master/template.php#L30) for the code.

## Filters

### Language & Formatting Filters

* `wunderground_enable_forecast_shortcode` (boolean) Disable the old `[forecast]` shortcode and only use `[wunderground]`. Default: `true`
* `wunderground_widget_text_do_shortcode` (boolean) Prevent the plugin from enabling `do_shortcode` filter on widget output. This is to provide backward compatibility for Version 1.x. Added in 2.0.8. Default: `true`
* `wp_wunderground_forecast_icon` (string) URL path to icon. Passes two arguments: `$output` (default path), `$icon` Name of icon to be fetched
* `wunderground_autocomplete_country_code` (string) Set the locale of the Wunderground autocomplete results. If you only want US locations, for example, return `US`. Default: `NULL`
* `wunderground_date_format` (string) Filter the date format sitewide. Return a PHP date format string. [Learn more about formatting dates](http://codex.wordpress.org/Formatting_Date_and_Time). Default: `m/d`
* `wunderground_default_language` (string) Override the language used by Wunderground if the WordPress language isn't set. Use the two-letter abbreviation of the [Wunderground list of languages](http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1) Default: `NULL`
* `wunderground_redirect_subdomain` (string) Change the subdomain used for the autocomplete results, based on the language key (see `wunderground_default_language`). Default: `www`

### Forecast Request Filters

* `wunderground_request_atts` (array) Modify forecast request settings passed to the `wp_remote_request()` function when calling Wunderground. By default, only sets timeout (10 seconds)
* `wunderground_cache_time` (int) Modify the number of seconds to cache the request for. Default: `3600` (cache the request for one hour, since we're dealing with changing conditions)

### Template Filters

* `wunderground_template_paths` (array) Paths to check for template files. Default: `/wunderground/` sub-directory of the current stylesheet directory
* `wunderground_twig_debug` (boolean) Enable Twig template debugging. Default: if user is logged in and `?debug` is set in the URL.
* `wunderground_template_data` (array) Data passed to the template, available in Twig template rendering engine
* `wp_wunderground_forecast` (string) Filter the output of the forecast HTML.


### Screenshots

The new widget:
![the Wunderground widget](https://raw.githubusercontent.com/katzwebservices/Wunderground/master/screenshot-1.jpg)

#### Copyright

*Weather Underground is a registered trademark of The Weather Channel, LLC. both in the United States and internationally. The Weather Underground Logo is a trademark of Weather Underground, LLC.*
