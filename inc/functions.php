<?php
/**
 * Helper functions
 * @package WPWunderground
 */

/**
 * The Wunderground shortcode
 * @filter default text
 * @action default text
 * @param  array       $passed_atts [description]
 * @param  [type]      $content     [description]
 * @return [type]                   [description]
 */
function wunderground_shortcode( $passed_atts = array() , $content = NULL ) {

	$defaults = array(
		'location_title' => NULL,
		'location'	=>	'Denver, Colorado',
		'iconset' 	=> 	'Incredible',
		'numdays'	=>	5,
		'class'		=>	'wp_wunderground',
		'layout'	=>	'table-vertical',
		'measurement' => 'english',
		'language' => wunderground_get_language(),
		'showdata' => array('alerts','pop','icon','text', 'conditions', 'date'),
	);

	$atts = shortcode_atts( $defaults, $passed_atts );

	// Convert comma-separated value to array
	$atts['showdata'] = is_string( $atts['showdata'] ) ? explode(',', $atts['showdata']) : $atts['showdata'];

	extract($atts);

	// What to show in the search bar
	if( empty( $atts['location_title'] ) ) {
		$atts['location_title'] = $atts['location'];
	}

	ob_start();

	$atts['wunderground'] = new KWS_Wunderground( new Wunderground_Request( $location, null, $language, $measurement) );

	do_action( 'wunderground_render_template', $layout, $atts );

	Wunderground_Plugin::log_debug('Shortcode Atts passed to render_template', $atts);

	$content = ob_get_clean();

	return $content;
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

	if(isset($icons[$icon])) {
		$output = plugins_url( sprintf('assets/img/icons/%s', $icons[$icon]), Wunderground_Plugin::$file );
	}

	// Maintain backward compatibility with 1.*
	return apply_filters( 'wp_wunderground_forecast_icon', $output, $icon );
}

/**
 * Get the logo to be used in the template
 * @return string      URL of logo
 */
function wunderground_get_logo() {
	return plugins_url( 'assets/img/logos/wundergroundLogo_4c_horz.gif', Wunderground_Plugin::$file );
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


function wunderground_render_select( $name, $id, $options, $selected ) {

	$output = sprintf( '<select name="%s" id="%s">', $name, $id );

	foreach ($options as $key => $value) {
		$output .= '<option value="'.$key.'"'.selected( $selected, $key, false ).'>'.esc_html( $value ).'</option>';
	}
	$output .= '</select>';

	return $output;
}

function wunderground_render_radios( $name, $id, $options, $selected ) {

	$output = sprintf( '<select name="%s" id="%s">', $name, $id );

	foreach ($options as $key => $value) {
		$output .= '<option value="'.$key.'"'.selected( $selected, $key, false ).'>'.esc_html( $value ).'</option>';
	}
	$output .= '</select>';

	return $output;
}

function wunderground_get_autocomplete_country_code() {
	return apply_filters( 'wunderground_autocomplete_country_code', (get_locale() === 'en_US' ? 'US' : NULL) );
}

function wunderground_get_language( $language = NULL ) {

	$wunderground_languages = wunderground_get_languages();

	// If the language exists in Wunderground supported languages, use it.
	if( !empty( $language ) && array_key_exists( $language, $wunderground_languages ) ) {
		return $language;
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
		return $language;
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

	if(empty($language_key)) {
		$language_key = wunderground_get_language();
	}

	switch ($language_key) {
		case 'EN':
			$subdomain = 'www';
			break;
		case 'DL':
			$subdomain = 'deutsch';
			break;
		case 'HT':
			$subdomain = 'haitian';
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

			if( isset( $languages[$language_key] ) ) {

				// Get the name of the language
				$subdomain = $languages[$language_key];

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
 * @link http://www.wunderground.com/weather/api/d/docs?d=language-support&MR=1
 * @return array      Associative array
 */
function wunderground_get_languages() {
	return array(
		'AF'	=>	__('Afrikaans', 'wunderground'),
		'AL'	=>	__('Albanian', 'wunderground'),
		'AR'	=>	__('Arabic', 'wunderground'),
		'HY'	=>	__('Armenian', 'wunderground'),
		'AZ'	=>	__('Azerbaijani', 'wunderground'),
		'EU'	=>	__('Basque', 'wunderground'),
		'BY'	=>	__('Belarusian', 'wunderground'),
		'BU'	=>	__('Bulgarian', 'wunderground'),
		'LI'	=>	__('British English', 'wunderground'),
		'MY'	=>	__('Burmese', 'wunderground'),
		'CA'	=>	__('Catalan', 'wunderground'),
		'CN'	=>	__('Chinese - Simplified', 'wunderground'),
		'TW'	=>	__('Chinese - Traditional', 'wunderground'),
		'CR'	=>	__('Croatian', 'wunderground'),
		'CZ'	=>	__('Czech', 'wunderground'),
		'DK'	=>	__('Danish', 'wunderground'),
		'DV'	=>	__('Dhivehi', 'wunderground'),
		'NL'	=>	__('Dutch', 'wunderground'),
		'EN'	=>	__('English', 'wunderground'),
		'EO'	=>	__('Esperanto', 'wunderground'),
		'ET'	=>	__('Estonian', 'wunderground'),
		'FA'	=>	__('Farsi', 'wunderground'),
		'FI'	=>	__('Finnish', 'wunderground'),
		'FR'	=>	__('French', 'wunderground'),
		'FC'	=>	__('French Canadian', 'wunderground'),
		'GZ'	=>	__('Galician', 'wunderground'),
		'DL'	=>	__('German', 'wunderground'),
		'KA'	=>	__('Georgian', 'wunderground'),
		'GR'	=>	__('Greek', 'wunderground'),
		'GU'	=>	__('Gujarati', 'wunderground'),
		'HT'	=>	__('Haitian Creole', 'wunderground'),
		'IL'	=>	__('Hebrew', 'wunderground'),
		'HI'	=>	__('Hindi', 'wunderground'),
		'HU'	=>	__('Hungarian', 'wunderground'),
		'IS'	=>	__('Icelandic', 'wunderground'),
		'IO'	=>	__('Ido', 'wunderground'),
		'ID'	=>	__('Indonesian', 'wunderground'),
		'IR'	=>	__('Irish Gaelic', 'wunderground'),
		'IT'	=>	__('Italian', 'wunderground'),
		'JP'	=>	__('Japanese', 'wunderground'),
		'JW'	=>	__('Javanese', 'wunderground'),
		'KM'	=>	__('Khmer', 'wunderground'),
		'KR'	=>	__('Korean', 'wunderground'),
		'KU'	=>	__('Kurdish', 'wunderground'),
		'LA'	=>	__('Latin', 'wunderground'),
		'LV'	=>	__('Latvian', 'wunderground'),
		'LT'	=>	__('Lithuanian', 'wunderground'),
		'ND'	=>	__('Low German', 'wunderground'),
		'MK'	=>	__('Macedonian', 'wunderground'),
		'MT'	=>	__('Maltese', 'wunderground'),
		'GM'	=>	__('Mandinka', 'wunderground'),
		'MI'	=>	__('Maori', 'wunderground'),
		'MR'	=>	__('Marathi', 'wunderground'),
		'MN'	=>	__('Mongolian', 'wunderground'),
		'NO'	=>	__('Norwegian', 'wunderground'),
		'OC'	=>	__('Occitan', 'wunderground'),
		'PS'	=>	__('Pashto', 'wunderground'),
		'GN'	=>	__('Plautdietsch', 'wunderground'),
		'PL'	=>	__('Polish', 'wunderground'),
		'BR'	=>	__('Portuguese', 'wunderground'),
		'PA'	=>	__('Punjabi', 'wunderground'),
		'RO'	=>	__('Romanian', 'wunderground'),
		'RU'	=>	__('Russian', 'wunderground'),
		'SR'	=>	__('Serbian', 'wunderground'),
		'SK'	=>	__('Slovak', 'wunderground'),
		'SL'	=>	__('Slovenian', 'wunderground'),
		'SP'	=>	__('Spanish', 'wunderground'),
		'SI'	=>	__('Swahili', 'wunderground'),
		'SW'	=>	__('Swedish', 'wunderground'),
		'CH'	=>	__('Swiss', 'wunderground'),
		'TL'	=>	__('Tagalog', 'wunderground'),
		'TT'	=>	__('Tatarish', 'wunderground'),
		'TH'	=>	__('Thai', 'wunderground'),
		'TR'	=>	__('Turkish', 'wunderground'),
		'TK'	=>	__('Turkmen', 'wunderground'),
		'UA'	=>	__('Ukrainian', 'wunderground'),
		'UZ'	=>	__('Uzbek', 'wunderground'),
		'VU'	=>	__('Vietnamese', 'wunderground'),
		'CY'	=>	__('Welsh', 'wunderground'),
		'SN'	=>	__('Wolof', 'wunderground'),
		'JI'	=>	__('Yiddish - transliterated', 'wunderground'),
		'YI'	=>	__('Yiddish - unicode', 'wunderground'),
	);
}
