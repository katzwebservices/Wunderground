<?php
/**
 * Helper functions
 * @package WPWunderground
 */

/**
 * The Wunderground shortcode
 *
 * @action wunderground_render_template Render the template with the settings passed to the shortcode
 * @param  string|array       $passed_atts  String or array of settings for the shortcode
 * @param  string      $content     Content inside shortcode tags. Should be empty.
 * @return [type]                   [description]
 */
function wunderground_shortcode( $passed_atts = array() , $content = NULL, $shortcode = 'wunderground' ) {

	$atts = wunderground_parse_atts( $passed_atts, $shortcode );

	ob_start();

	$atts['wunderground'] = new KWS_Wunderground( new Wunderground_Request( $atts['location'], null, $atts['language'], $atts['measurement'] ) );

	do_action( 'wunderground_render_template', $atts['layout'], $atts );

	Wunderground_Plugin::log_debug('Shortcode Atts passed to render_template', $atts);

	$content = ob_get_clean();

	return $content;
}


/**
 * Handle edgecases and validation for shortcode attributes.
 *
 * @link http://codex.wordpress.org/Formatting_Date_and_Time Date & Time formatting
 * @param  array      $passed_atts   Array of values to parse
 * @param  array      $shortcode Name of shortcode being used (`wunderground`)
 * @return array                [description]
 */
function wunderground_parse_atts( $passed_atts, $shortcode = 'wunderground' ) {

	$defaults = array(
		'title' => __('Weather Forecast', 'wunderground'),
		'location_title' => NULL,
		'location_data' => '',
		'city' 		=> '',
		'location'	=>	'',
		'iconset' 	=> 	'Incredible',
		'numdays'	=>	5,
		'class'		=>	'wp_wunderground',
		'layout'	=>	'table-vertical',
		'measurement' => 'english',
		'datelabel'	=> 'm/d',
		'language' => wunderground_get_language(),
		'showdata' => array('search', 'alerts', 'daynames','pop','icon','text', 'conditions', 'date'),
		'hidedata' => array(),
	);

	// Use previous settings as defaults to better support backward compatibility
	$defaults = wp_parse_args( get_option('wp_wunderground', array() ), $defaults );

	if( !empty( $shortcode ) ) {
		$atts = shortcode_atts( $defaults, $passed_atts, $shortcode );
	} else {
		$atts = wp_parse_args( (array)$passed_atts, $defaults );
	}

	$atts['datelabel'] = wunderground_get_date_format( $atts['datelabel'] );

	// If there was no numdays passed,
	// 4 is a better default for table-horizontal layout
	if( empty( $passed_atts['numdays'] ) ) {
		switch ($atts['layout']) {
			case 'table-horizontal':
				$atts['numdays'] = 4;
				break;
		}
	}

	// Convert comma-separated value to array
	$atts['showdata'] = is_string( $atts['showdata'] ) ? explode(',', $atts['showdata']) : $atts['showdata'];

	if( !is_numeric( $atts['numdays'] ) ) {
		Wunderground_Plugin::log_error( sprintf( '"numdays" was set not a number: %s. Changed to the default: %d. wunderground_shortcode', $atts['numdays'], $defaults['numdays'] ) );

		$atts['numdays'] = $defaults['numdays'];

	} else if( absint( $atts['numdays'] ) > 10 ) {
		Wunderground_Plugin::log_error( sprintf( '"numdays" set too high in shortcode: %s. It was changed to the max: 10. wunderground_shortcode', $atts['numdays'] ) );
		$atts['numdays'] = 10;
	}

	// What to show in the search bar
	if( empty( $atts['location_title'] ) ) {
		$atts['location_title'] = $atts['location'];
	}

	// Process hidedata/showdata
	if( !empty( $atts['hidedata'] ) ) {

		$hidedata = is_array( $atts['hidedata'] ) ? $hidedata : explode(',', $atts['hidedata']);

		// For each hidedata, unset showdata.
		foreach ($hidedata as $value) {
			foreach ($atts['showdata'] as $k => $v) {
				if( $v === $value ) {
					unset( $atts['showdata'][ $k ] );
				}
			}
		}
	}

	return $atts;

}


/**
 * Get the URL for a specific icon name
 *
 * @filter wp_wunderground_forecast_icon Modify icon path output. Maintains backward compat. with version 1.*
 * @link  http://www.wunderground.com/weather/api/d/docs?d=resources/icon-sets read more about Wunderground icon use
 * @return string|boolean      If found, returns the URL base of the icon. Otherwise, returns false.
 */
function wunderground_get_icon( $icon = 'Incredible' ) {

	$icons = wunderground_get_icons();
	$output = false;

	$icon_formatted = ucwords( strtolower( $icon ) );

	if( isset( $icons[ $icon_formatted ] ) ) {
		$output = plugins_url( sprintf('assets/img/icons/%s', $icons[ $icon_formatted ]), Wunderground_Plugin::$file );
	}

	// Maintain backward compatibility with 1.*
	return apply_filters( 'wp_wunderground_forecast_icon', $output, $icon );
}

/**
 * Get the logo to be used in the template
 * @return string      URL of logo
 */
function wunderground_get_logo() {
	return plugins_url( 'assets/img/logos/wundergroundLogo_4c_horz.png', Wunderground_Plugin::$file );
}

/**
 * Get an array of all icon sets
 *
 * Uses the Wunderground names shown here: {@link http://www.wunderground.com/member/membersettings.html?page=icons}
 *
 * @filter wp_wunderground_forecast_icons Modify the icons array
 * @return array      Associative array with key as icon set name, value as icon set base URL
 */
function wunderground_get_icons( $value_is_path = true ) {

	if($value_is_path) {
		return array(
			__('Incredible', 'wunderground') => 'k',
			__('Elemental', 'wunderground') => 'z',
			__('Helen', 'wunderground') => 'i',
			__('Default', 'wunderground') => 'a',
			__('Smiley', 'wunderground') => 'b',
			__('Generic', 'wunderground') => 'c',
			__('Old School', 'wunderground') => 'd',
			__('Cartoon', 'wunderground') => 'e',
			__('Mobile', 'wunderground') => 'f',
			__('Simple', 'wunderground') => 'g',
			__('Contemporary', 'wunderground') => 'h',
		);
	}

	return array(
		'Wunderground' => __('Wunderground', 'wunderground'),
		'Elemental' => __('Elemental', 'wunderground'),
		'Incredible' => __('Incredible', 'wunderground'),
		'Helen' => __('Helen', 'wunderground'),

		'Default' => __('Default', 'wunderground'),
		'Smiley' => __('Smiley', 'wunderground'),
		'Generic' => __('Generic', 'wunderground'),
		'Old School' => __('Old School', 'wunderground'),
		'Cartoon' => __('Cartoon', 'wunderground'),
		'Mobile' => __('Mobile', 'wunderground'),
		'Simple' => __('Simple', 'wunderground'),
		'Contemporary' => __('Contemporary', 'wunderground'),
	);
}


/**
 * Render HTML <select> dropdown menu
 * @param  string $name     Name attribute
 * @param  string $id       ID attribute
 * @param  array $options  Array of options with key as the value and value as the label
 * @param  string $selected The value that's selected, if any
 * @return string           HTML output
 */
function wunderground_render_select( $name, $id, $options, $selected = NULL ) {

	$output = sprintf( '<select name="%s" id="%s">', $name, $id );

	foreach ($options as $key => $value) {
		$output .= '<option value="'.$key.'"'.selected( $selected, $key, false ).'>'.esc_html( $value ).'</option>';
	}
	$output .= '</select>';

	return $output;
}


function wunderground_get_autocomplete_country_code() {
	// In WP 4.0+, this will work better, when installations include language setups out of the box.
	// (get_locale() === 'en_US' ? 'US' : NULL)

	return apply_filters( 'wunderground_autocomplete_country_code', NULL );
}

/**
 * Get the date format for the output.
 *
 * Backward compatible with 1.x by converting %%weekday%%, %%day%%, %%month%% and %%year%% into PHP date formats. Also supports converting `date('d/m/Y')` to `d/m/Y`
 *
 * @link http://codex.wordpress.org/Formatting_Date_and_Time Learn more about formatting datetime
 * @filter wunderground_date_format Filter the date format sitewide.
 * @param  string $format PHP date format
 * @return string         PHP date format
 */
function wunderground_get_date_format( $format = 'm/d' ) {

	$default_format = 'm/d';

	$format = empty( $format ) ? $default_format : $format;

// Backward compatibility

	// Remove placeholder tags from v1
	$format = str_replace( array('%%weekday%%', '%%day%%', '%%month%%', '%%year%%' ), '', $format );

	// Then we look for the php date() function by matching:
	// date('[stuff in here]') or date("[stuff in here]")
	if( preg_match('/date\([\'"]{0,1}(.*?)[\'"]{0,1}\)/xism', $format, $matches) ) {
		$format = $matches[1];
	}

// End backward compatibility

	if( empty( $format ) ) {
		$format = $default_format;
	}

	return apply_filters( 'wunderground_date_format', $format );
}

function wunderground_get_language( $passed_language = NULL, $language_details = false ) {

	$language = strtoupper( $passed_language );

	$wunderground_languages = wunderground_get_languages();

	// If the language exists in Wunderground supported languages, use it.
	if( !empty( $language ) && array_key_exists( $language, $wunderground_languages ) ) {
		return $language_details ? $wunderground_languages[ $language ] : $language;
	}

	// First, we want to fetch the current WordPress locale
	// @link http://codex.wordpress.org/Function_Reference/get_locale
	$locale = get_locale();

	// Then we want the core language (en_US and en_GB both are en for Wunderground)
	// So we want the first part of the string, before _
	// If it doesn't work, the whole string will be returned, which we'll handle in the next step.
	$pieces = explode('_', $locale);

	// The langs are all uppercase
	$language = strtoupper($pieces[0]);

	// If the language exists in Wunderground supported languages, use it.
	if(array_key_exists($language, $wunderground_languages)) {
		return $language_details ? $wunderground_languages[ $language ] : $language;
	}

	// Otherwise, use the default Wunderground language.
	return apply_filters( 'wunderground_default_language', NULL);
}

/**
 * Get the subdomain for Wunderground on a per-language basis.
 *
 * The names of the languages passed by wunderground_get_languages() are
 * the subdomains, with a few exceptions. The subdomains have spaces and
 * hyphens removed.
 *
 * @filter wunderground_redirect_subdomain Change the subdomain used for a language key.
 * @param  string      $language_key Language key from wunderground_get_languages()
 * @return string                    Wunderground subdomain string
 */
function wunderground_get_subdomain( $language_key = NULL ) {

	if( empty( $language_key ) ) {
		$language_key = wunderground_get_language();
	}

	$language_key = strtoupper( $language_key );

	switch ( $language_key ) {
		case 'EN':
			$subdomain = 'www';
			break;
		case 'DL':
			$subdomain = 'deutsch';
			break;
		case 'HT':
			$subdomain = 'haitian';
			break;
		case 'JP':
			$subdomain = 'nihongo';
			break;
		case 'CN':
			$subdomain = 'simplifiedchinese';
			break;
		case 'TW':
			$subdomain = 'traditionalchinese';
			break;
		default:

			// Convert "French Canadian" to "frenchcanadian" for subdomain
			$languages = wunderground_get_languages();

			if( isset( $languages[$language_key] ) && isset( $languages[$language_key]['value'] ) ) {

				// Get the name of the language
				$subdomain = $languages[$language_key]['value'];

				// Replace "-" and " " with nothing
				$subdomain = str_replace(array(' ', '-'), '', $subdomain);

				// Then lower-case it.
				$subdomain = strtolower($subdomain);

			} else {
				$subdomain = 'www';
			}
			break;
	}

	return apply_filters( 'wunderground_redirect_subdomain', $subdomain, $language_key );
}

/**
 * List of languages supported by Wunderground
 *
 * These values are also used to
 *
 * @link http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1
 * @return array      Associative array
 */
function wunderground_get_languages() {
	return array(
		'AF' => array(
			'label' => __('Afrikaans', 'wunderground'),
			'value' => 'Afrikaans',
			'key'	=> 'AF',
		),
		'AL' => array(
			'label' => __('Albanian', 'wunderground'),
			'value' => 'Albanian',
			'key'	=> 'AL',
		),
		'AR' => array(
			'label' => __('Arabic', 'wunderground'),
			'value' => 'Arabic',
			'key'	=> 'AR',
			'rtl'	=> true,
		),
		'HY' => array(
			'label' => __('Armenian', 'wunderground'),
			'value' => 'Armenian',
			'key'	=> 'HY',
		),
		'AZ' => array(
			'label' => __('Azerbaijani', 'wunderground'),
			'value' => 'Azerbaijani',
			'key'	=> 'AZ',
		),
		'EU' => array(
			'label' => __('Basque', 'wunderground'),
			'value' => 'Basque',
			'key'	=> 'EU',
		),
		'BY' => array(
			'label' => __('Belarusian', 'wunderground'),
			'value' => 'Belarusian',
			'key'	=> 'BY',
		),
		'BU' => array(
			'label' => __('Bulgarian', 'wunderground'),
			'value' => 'Bulgarian',
			'key'	=> 'BU',
		),
		'LI' => array(
			'label' => __('British English', 'wunderground'),
			'value' => 'British English',
			'key'	=> 'LI',
		),
		'MY' => array(
			'label' => __('Burmese', 'wunderground'),
			'value' => 'Burmese',
			'key'	=> 'MY',
		),
		'CA' => array(
			'label' => __('Catalan', 'wunderground'),
			'value' => 'Catalan',
			'key'	=> 'CA',
		),
		'CN' => array(
			'label' => __('Chinese - Simplified', 'wunderground'),
			'value' => 'Chinese - Simplified',
			'key'	=> 'CN',
		),
		'TW' => array(
			'label' => __('Chinese - Traditional', 'wunderground'),
			'value' => 'Chinese - Traditional',
			'key'	=> 'TW',
		),
		'CR' => array(
			'label' => __('Croatian', 'wunderground'),
			'value' => 'Croatian',
			'key'	=> 'CR',
		),
		'CZ' => array(
			'label' => __('Czech', 'wunderground'),
			'value' => 'Czech',
			'key'	=> 'CZ',
		),
		'DK' => array(
			'label' => __('Danish', 'wunderground'),
			'value' => 'Danish',
			'key'	=> 'DK',
		),
		'DV' => array(
			'label' => __('Dhivehi', 'wunderground'),
			'value' => 'Dhivehi',
			'key'	=> 'DV',
			'rtl'	=> true,
		),
		'NL' => array(
			'label' => __('Dutch', 'wunderground'),
			'value' => 'Dutch',
			'key'	=> 'NL',
		),
		'EN' => array(
			'label' => __('English', 'wunderground'),
			'value' => 'English',
			'key'	=> 'EN',
		),
		'EO' => array(
			'label' => __('Esperanto', 'wunderground'),
			'value' => 'Esperanto',
			'key'	=> 'EO',
		),
		'ET' => array(
			'label' => __('Estonian', 'wunderground'),
			'value' => 'Estonian',
			'key'	=> 'ET',
		),
		'FA' => array(
			'label' => __('Farsi', 'wunderground'),
			'value' => 'Farsi',
			'key'	=> 'FA',
		),
		'FI' => array(
			'label' => __('Finnish', 'wunderground'),
			'value' => 'Finnish',
			'key'	=> 'FI',
		),
		'FR' => array(
			'label' => __('French', 'wunderground'),
			'value' => 'French',
			'key'	=> 'FR',
		),
		'FC' => array(
			'label' => __('French Canadian', 'wunderground'),
			'value' => 'French Canadian',
			'key'	=> 'FC',
		),
		'GZ' => array(
			'label' => __('Galician', 'wunderground'),
			'value' => 'Galician',
			'key'	=> 'GZ',
		),
		'DL' => array(
			'label' => __('German', 'wunderground'),
			'value' => 'German',
			'key'	=> 'DL',
		),
		'KA' => array(
			'label' => __('Georgian', 'wunderground'),
			'value' => 'Georgian',
			'key'	=> 'KA',
		),
		'GR' => array(
			'label' => __('Greek', 'wunderground'),
			'value' => 'Greek',
			'key'	=> 'GR',
		),
		'GU' => array(
			'label' => __('Gujarati', 'wunderground'),
			'value' => 'Gujarati',
			'key'	=> 'GU',
		),
		'HT' => array(
			'label' => __('Haitian Creole', 'wunderground'),
			'value' => 'Haitian Creole',
			'key'	=> 'HT',
		),
		'IL' => array(
			'label' => __('Hebrew', 'wunderground'),
			'value' => 'Hebrew',
			'key'	=> 'IL',
			'rtl'	=> true,
		),
		'HI' => array(
			'label' => __('Hindi', 'wunderground'),
			'value' => 'Hindi',
			'key'	=> 'HI',
		),
		'HU' => array(
			'label' => __('Hungarian', 'wunderground'),
			'value' => 'Hungarian',
			'key'	=> 'HU',
		),
		'IS' => array(
			'label' => __('Icelandic', 'wunderground'),
			'value' => 'Icelandic',
			'key'	=> 'IS',
		),
		'IO' => array(
			'label' => __('Ido', 'wunderground'),
			'value' => 'Ido',
			'key'	=> 'IO',
		),
		'ID' => array(
			'label' => __('Indonesian', 'wunderground'),
			'value' => 'Indonesian',
			'key'	=> 'ID',
		),
		'IR' => array(
			'label' => __('Irish Gaelic', 'wunderground'),
			'value' => 'Irish Gaelic',
			'key'	=> 'IR',
		),
		'IT' => array(
			'label' => __('Italian', 'wunderground'),
			'value' => 'Italian',
			'key'	=> 'IT',
		),
		'JP' => array(
			'label' => __('Japanese', 'wunderground'),
			'value' => 'Japanese',
			'key'	=> 'JP',
		),
		'JW' => array(
			'label' => __('Javanese', 'wunderground'),
			'value' => 'Javanese',
			'key'	=> 'JW',
		),
		'KM' => array(
			'label' => __('Khmer', 'wunderground'),
			'value' => 'Khmer',
			'key'	=> 'KM',
		),
		'KR' => array(
			'label' => __('Korean', 'wunderground'),
			'value' => 'Korean',
			'key'	=> 'KR',
		),
		'KU' => array(
			'label' => __('Kurdish', 'wunderground'),
			'value' => 'Kurdish',
			'key'	=> 'KU',
			'rtl'	=> true,
		),
		'LA' => array(
			'label' => __('Latin', 'wunderground'),
			'value' => 'Latin',
			'key'	=> 'LA',
		),
		'LV' => array(
			'label' => __('Latvian', 'wunderground'),
			'value' => 'Latvian',
			'key'	=> 'LV',
		),
		'LT' => array(
			'label' => __('Lithuanian', 'wunderground'),
			'value' => 'Lithuanian',
			'key'	=> 'LT',
		),
		'ND' => array(
			'label' => __('Low German', 'wunderground'),
			'value' => 'Low German',
			'key'	=> 'ND',
		),
		'MK' => array(
			'label' => __('Macedonian', 'wunderground'),
			'value' => 'Macedonian',
			'key'	=> 'MK',
		),
		'MT' => array(
			'label' => __('Maltese', 'wunderground'),
			'value' => 'Maltese',
			'key'	=> 'MT',
		),
		'GM' => array(
			'label' => __('Mandinka', 'wunderground'),
			'value' => 'Mandinka',
			'key'	=> 'GM',
		),
		'MI' => array(
			'label' => __('Maori', 'wunderground'),
			'value' => 'Maori',
			'key'	=> 'MI',
		),
		'MR' => array(
			'label' => __('Marathi', 'wunderground'),
			'value' => 'Marathi',
			'key'	=> 'MR',
		),
		'MN' => array(
			'label' => __('Mongolian', 'wunderground'),
			'value' => 'Mongolian',
			'key'	=> 'MN',
		),
		'NO' => array(
			'label' => __('Norwegian', 'wunderground'),
			'value' => 'Norwegian',
			'key'	=> 'NO',
		),
		'OC' => array(
			'label' => __('Occitan', 'wunderground'),
			'value' => 'Occitan',
			'key'	=> 'OC',
		),
		'PS' => array(
			'label' => __('Pashto', 'wunderground'),
			'value' => 'Pashto',
			'key'	=> 'PS',
			'rtl'	=> true,
		),
		'GN' => array(
			'label' => __('Plautdietsch', 'wunderground'),
			'value' => 'Plautdietsch',
			'key'	=> 'GN',
		),
		'PL' => array(
			'label' => __('Polish', 'wunderground'),
			'value' => 'Polish',
			'key'	=> 'PL',
		),
		'BR' => array(
			'label' => __('Portuguese', 'wunderground'),
			'value' => 'Portuguese',
			'key'	=> 'BR',
		),
		'PA' => array(
			'label' => __('Punjabi', 'wunderground'),
			'value' => 'Punjabi',
			'key'	=> 'PA',
			'rtl'	=> true,
		),
		'RO' => array(
			'label' => __('Romanian', 'wunderground'),
			'value' => 'Romanian',
			'key'	=> 'RO',
		),
		'RU' => array(
			'label' => __('Russian', 'wunderground'),
			'value' => 'Russian',
			'key'	=> 'RU',
		),
		'SR' => array(
			'label' => __('Serbian', 'wunderground'),
			'value' => 'Serbian',
			'key'	=> 'SR',
		),
		'SK' => array(
			'label' => __('Slovak', 'wunderground'),
			'value' => 'Slovak',
			'key'	=> 'SK',
		),
		'SL' => array(
			'label' => __('Slovenian', 'wunderground'),
			'value' => 'Slovenian',
			'key'	=> 'SL',
		),
		'SP' => array(
			'label' => __('Spanish', 'wunderground'),
			'value' => 'Spanish',
			'key'	=> 'SP',
		),
		'SI' => array(
			'label' => __('Swahili', 'wunderground'),
			'value' => 'Swahili',
			'key'	=> 'SI',
		),
		'SW' => array(
			'label' => __('Swedish', 'wunderground'),
			'value' => 'Swedish',
			'key'	=> 'SW',
		),
		'CH' => array(
			'label' => __('Swiss', 'wunderground'),
			'value' => 'Swiss',
			'key'	=> 'CH',
		),
		'TL' => array(
			'label' => __('Tagalog', 'wunderground'),
			'value' => 'Tagalog',
			'key'	=> 'TL',
		),
		'TT' => array(
			'label' => __('Tatarish', 'wunderground'),
			'value' => 'Tatarish',
			'key'	=> 'TT',
		),
		'TH' => array(
			'label' => __('Thai', 'wunderground'),
			'value' => 'Thai',
			'key'	=> 'TH',
		),
		'TR' => array(
			'label' => __('Turkish', 'wunderground'),
			'value' => 'Turkish',
			'key'	=> 'TR',
		),
		'TK' => array(
			'label' => __('Turkmen', 'wunderground'),
			'value' => 'Turkmen',
			'key'	=> 'TK',
		),
		'UA' => array(
			'label' => __('Ukrainian', 'wunderground'),
			'value' => 'Ukrainian',
			'key'	=> 'UA',
		),
		'UZ' => array(
			'label' => __('Uzbek', 'wunderground'),
			'value' => 'Uzbek',
			'key'	=> 'UZ',
		),
		'VU' => array(
			'label' => __('Vietnamese', 'wunderground'),
			'value' => 'Vietnamese',
			'key'	=> 'VU',
		),
		'CY' => array(
			'label' => __('Welsh', 'wunderground'),
			'value' => 'Welsh',
			'key'	=> 'CY',
		),
		'SN' => array(
			'label' => __('Wolof', 'wunderground'),
			'value' => 'Wolof',
			'key'	=> 'SN',
		),
		'JI' => array(
			'label' => __('Yiddish - transliterated', 'wunderground'),
			'value' => 'Yiddish - transliterated',
			'key'	=> 'JI',
		),
		'YI' => array(
			'label' => __('Yiddish - unicode', 'wunderground'),
			'value' => 'Yiddish - unicode',
			'key'	=> 'YI',
			'rtl'	=> true,
		),
	);
}
