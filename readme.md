### Reporting issues ###

If you have issues with the plugin, please:

1. [Report the issue on Github](https://github.com/katzwebservices/Wunderground/issues/new) __(much preferred)__
2. Report the issue [using this form](https://widget.uservoice.com/omnibox/Csq4WQZTBPGtRqSZXIsNA?mode=contact&locale=en&forum_id=254985&contact_us=true&accent_color=007DBF&embed_type=popover&trigger_method=custom_trigger&menu=true&screenshot_enabled=false&contact_enabled=true&feedback_enabled=false&smartvote=false)
3. [Send an email](zack+wunderground@katz.co) *(not preferred)*

## Setting up the plugin

The Wunderground plugin displays forecasts in a widget or by using the `[wunderground]` shortcode.

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

* `location` - Define the location of the forecast. It can be in any format compatible with Wunderground, including `{city},{state}`, `{longitude}, {latitude}`, or Wunderground "zmw" `{zmw:00000.1.12345}`, or a 3-4 character airport code. Default: `Denver, Colorado`
* `iconset` -  	'Incredible',
* `numdays` - Default: `5`
* `class` - CSS class to be added to the `<div>` that wraps the output. Default: `wp_wunderground`
* `layout` - The layout template to be used. The value of this parameter is used to find the HTML template file in the `templates` directory. For example, `simple` loads the `templates/simple.html` template file. See Templates below for how the templates are loaded and can be overruled. Default: `table-vertical`
	- `table-vertical` - A vertical table with each day as a row
	- `table-horizontal` - A horizontal table with each day as a column
	- `simple` - A flexible day view
	- `current` - Current conditions only
* `measurement` - Whether to show items in Fahrenheit and Imperial (`english`) or Celsius and Metric (`metric`) Default: `english`
* `language` - A two-letter code representing the language. [See the complete list of language codes here](http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1). Default: `EN`
* `showdata` - The items to show in the forecast. Default: `alerts,pop,icon,text,date`
	- `alerts` - Weather alerts for the forecast area
	- `icon` - Weather forecast icon
	- `pop` - % chance of precipitation
	- `text` - Forecast text summary
	- `date` - Show the date in the output (table templates only)

## International

### Language
The Wunderground plugin attempts to use the language you defined in the WordPress admin by default. You can override the language in the plugin's widget and also in the shortcode using the `language` attribute. See Shortcode above for more information. You can override the default language using the `wunderground_default_language` filter.

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

### Screenshots

The new widget:  
![the Wunderground widget](https://raw.githubusercontent.com/katzwebservices/Wunderground/master/screenshot-1.jpg)

#### Copyright

*Weather Underground is a registered trademark of The Weather Channel, LLC. both in the United States and internationally. The Weather Underground Logo is a trademark of Weather Underground, LLC.*
