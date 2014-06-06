## Setting up the plugin

The plugin has been completely rewritten!

* Auto-complete location support
* Multi-language support
* Current conditions report
* New templates
  * Ability to override the templates with your own
* New icon set

### Reporting issues ###

If you have issues with the plugin, please:

1. [Report the issue on Github](https://github.com/katzwebservices/Wunderground/issues/new) __(much preferred)__
2. Report the issue [using this form](https://widget.uservoice.com/omnibox/Csq4WQZTBPGtRqSZXIsNA?mode=contact&locale=en&forum_id=254985&contact_us=true&accent_color=007DBF&embed_type=popover&trigger_method=custom_trigger&menu=true&screenshot_enabled=false&contact_enabled=true&feedback_enabled=false&smartvote=false)
3. [Send an email](zack+wunderground@katz.co) *(not preferred)*

## Shortcode

* `language` - A two-letter code representing the language; `EN` is English. `TR` is Turkish. [See the complete list of language codes here](http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1).

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

*Weather Underground is a registered trademark of The Weather Channel, LLC. both in the United States and internationally. The Weather Underground Logo is a trademark of Weather Underground, LLC.*
