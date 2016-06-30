<?php
/**
 * Wunderground Widget
 */

class Wunderground_Forecast_Widget extends WP_Widget {

	/**
	 * Store the instance in the class as for retrieving using included templates
	 * @var array
	 */
	var $instance;
	/**
	 * Store the args in the class as for retrieving using included templates
	 * @var array
	 */
	var $args;

	/**
	 * Store the properties to retrieve using included templates.
	 * @var array
	 */
	var $properties;

	/**
	 * Register the widget on widgets_init
	 * @return void
	 */
	static function register_widget() {
		register_widget( __CLASS__ );
	}

	/**
	 * Set up the widget
	 */
	function __construct() {

		// Load the scripts & styles. {@see display.php}
		do_action( 'wunderground_print_scripts', true );

		$widget_ops = array(
			'classname' => 'wunderground',
			'description' => __( 'Add a Wunderground.com forecast', 'wunderground')
		);

		$control_options = array( 'width' => 450 ); // Min-width of widgets config with expanded sidebar

		parent::__construct( false, __('Wunderground', 'wunderground'), $widget_ops, $control_options );
	}

	/**
	 * Update the widget
	 * @param  array $new_instance New widget instance
	 * @param  array $old_instance Old widget instance
	 * @return array               Return the new widget
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Generate and output the widget
	 *
	 * @see Wunderground_Template in class-template.php to generate data
	 *
	 * @param  array $args     Widget args
	 * @param  array $instance Widget settings
	 */
	function widget( $args , $instance ) {

		$this->args = $args;
		$instance['location_data'] = json_decode($instance['location_data'], true);
		$this->instance = $instance;

		$title = apply_filters('widget_title', !isset( $instance['title'] ) ? NULL : $instance['title'], $instance, $this->id_base);

		extract( $args );

		$location = $this->getLocation();
		$request = new Wunderground_Request( $location, null, $instance['language'], $instance['measurement'] );
		$forecast = new Wunderground_Forecast( $request );

		$data = $instance;
		$data['widget'] = $args;
		$data['class'] = isset( $data['class'] ) ? $data['class'] : 'wp_wunderground';

		$language_details = wunderground_get_language( $data['language'], true );
		if( !empty( $language_details['rtl'] ) ) {
			$data['class'] .= ' wu-rtl';
		}

		$data['forecast'] = $forecast;
		$data['location'] = $instance['city'];
		$data['location_title'] = empty( $instance['location_title'] ) ? $data['location'] : $instance['location_title'];
		$data['wunderground'] = new KWS_Wunderground( $request );

		$data['datelabel'] = isset( $data['datelabel'] ) ? $data['datelabel'] : wunderground_get_date_format();

		// PWS is offline or something.
		if( !empty( $data['wunderground']->response->error )) {

			$this->maybe_display_error( $data['wunderground']->response->error );

			do_action('wunderground_log_debug', 'There was an error in the Wunderground response:', $data['wunderground']->response->error );
			return;
		}

		echo $before_widget;

		if ( !empty($title) ) {
			echo $before_title . $title . $after_title;
		}

		/**
		 * @see Wunderground_Template in class-template.php
		 */
		do_action('wunderground_render_template', $instance['layout'], $data );

		echo $after_widget;
	}

	/**
	 * If the user is logged in, display the error message
	 * @param stdClass $error
	 */
	function maybe_display_error( $error ) {

		if( !current_user_can( 'manage_options') || !is_object( $error ) || empty( $error->type ) ) {
			return;
		}

		echo '<h4>' . esc_html( sprintf( __( 'There was an error fetching the forecast: %s', 'wunderground' ), $error->type ) ). '</h4>';

	}

	function getLocation() {

		$location_data = $this->instance['location_data'];

		// First, we want to see if the link has been given to us by Wunderground.
		if( !empty($location_data['l']) ) {
			return $location_data['l'];
		}

		// Then the official name provided by Wunderground
		if( !empty($location_data['name']) ) {
			return $location_data['name'];
		}

		// If for some horrible reason, AJAX from Wunderground didn't work, use city name
		return !empty($this->instance['city']) ? $this->instance['city'] : NULL;
	}

	/**
	 * The form for the widget.
	 * @param  array $instance Widget instance
	 */
	function form( $instance ) {

		$instance = wunderground_parse_atts( $instance );

		extract($instance);

		?>
	<div class="wunderground-settings">

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<h3><?php esc_html_e('Title', 'wunderground'); ?></h3>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to hide the widget title.', 'wunderground' ); ?>" />
			</label>
		</div>

		<div class="setting-wrapper autocomplete" id="<?php echo $this->get_field_id('autocomplete'); ?>">
			<label for="<?php echo $this->get_field_id('city'); ?>">
				<h3><?php esc_html_e('Location', 'wunderground'); ?></h3>
				<p class="description"><?php esc_html_e('Locations will autoload, but you may also define custom locations.', 'wunderground'); ?></p>
				<input type="text" class="wu-autocomplete widefat" autocomplete="false" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" value="<?php echo esc_attr( $city ); ?>" placeholder="<?php esc_attr_e( 'Enter the name of a location.', 'wunderground' ); ?>" />
			</label>
			<input type="hidden" class="wu-location-data" id="<?php echo $this->get_field_id('location_data'); ?>" name="<?php echo $this->get_field_name('location_data'); ?>" value="<?php esc_attr_e( $location_data ); ?>" />
		</div>

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('location_title'); ?>">
				<h3><?php esc_html_e('Location Title', 'wunderground'); ?></h3>
				<p class="description"><?php esc_attr_e( 'Change how the location is displayed in the widget search field.', 'wunderground'); ?></p>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('location_title'); ?>" name="<?php echo $this->get_field_name('location_title'); ?>" value="<?php esc_attr_e( $location_title ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to use the location name.', 'wunderground' ); ?>" />
				<span class="howto"><?php esc_attr_e( 'Example: if the Location is set to "Denver, Colorado", you may prefer to set the Location Title as "Denver", which is simpler.', 'wunderground' ); ?></span>
			</label>
		</div>

		<div class="setting-wrapper">
		<?php

			$days_select = wunderground_render_select($this->get_field_name('numdays'), $this->get_field_id('numdays'), array( 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10 ), $numdays);

			echo sprintf('<label for="%s"><h3>%s</h3> %s</label>', $this->get_field_id('numdays'), __('# of Days in Forecast', 'wunderground'), $days_select);

		?>
			<p>
				<label>
					<input type="checkbox" value="current" name="<?php echo $this->get_field_name('showdata'); ?>[current]" <?php checked( in_array( 'current' , (array)$showdata ), true ); ?> />
					<span class="title"><?php esc_html_e( 'Include Current Conditions', 'wunderground'); ?></span>
					<span class="howto"><?php esc_html_e( 'Add the current conditions to the forecast.', 'wunderground' ); ?></span>
				</label>
			</p>
			<p>
				<label>
					<input type="checkbox" value="night" name="<?php echo $this->get_field_name('showdata'); ?>[night]" <?php checked( in_array( 'night' , (array)$showdata ), true ); ?> />
					<span class="title"><?php esc_html_e( 'Include Night Forecasts', 'wunderground'); ?></span>
					<span class="howto"><?php esc_html_e( 'This will result in double the number of forecasts shown.', 'wunderground' ); ?></span>
				</label>
			</p>
		</div>

		<div class="setting-wrapper icons">
			<h3><?php esc_html_e('Icon Set', 'wunderground'); ?></h3>
			<p class="description"><?php esc_html_e('Choose the look and feel of the images that will represent the weather.', 'wunderground'); ?></p>

			<ul>
		<?php

			$icons = wunderground_get_icons( true );

			foreach ( $icons as $name => $key) {

				$checked = checked( $iconset, $name, false);

				echo sprintf('
				<li class="alignleft">
					<label>
						<input class="alignleft" type="radio" value="%s" name="%s" id="%s" %s />
						<span class="title">%s</span>
						<span class="alignleft icon">
							<img src="%s/clear.gif" alt="" />
						</span>
					</label>
				</li>', $name, $this->get_field_name('iconset'), $this->get_field_id('iconset'), $checked, esc_html( $name ), wunderground_get_icon( $name ) );
			}
		?>
			</ul>
			<div class="clear"></div>
		</div>

		<div class="setting-wrapper layout">
			<h3 class="layout-title"><?php esc_html_e('Widget Template', 'wunderground'); ?></h3>
			<h4><?php esc_html_e('Choose how you would like to display the forecast.', 'wunderground'); ?></h4>
		<?php echo $this->render_input_template($layout); ?>
		</div>

		<div class="setting-wrapper forecast">
			<h3><?php esc_html_e('Show in Forecast', 'wunderground'); ?></h3>
			<ul>
			<?php
				$boxes = array(
					'search' => array(
						'label' => __('Search Form', 'wunderground'),
						'description' => __('Allow searching weather forecasts.', 'wunderground')
					),
					'daynames' => array(
						'label' => __('Weekday Labels', 'wunderground'),
						'description' => __('Show the names of the days of the week.', 'wunderground')
					),
					'date' => array(
						'label' => __('Date', 'wunderground'),
						'description' => __('Display the date numerically ("09/14").', 'wunderground'),
					),
					'icon' => array(
						'label' => __('Weather Icon', 'wunderground'),
						'description' => __('Icon representing the forecast conditions.', 'wunderground'),
					),
					'pop' => array(
						'label' => __('Chance of Rain', 'wunderground'),
						'description' => __('Display the percent chance of rain.', 'wunderground'),
					),
					'highlow' => array(
						'label' => __('High & Low Temp', 'wunderground'),
						'description' => __('Show the high & low temperatures for forecast.', 'wunderground'),
					),
					'conditions' => array(
						'label' => __('Condition Title', 'wunderground'),
						'description' => __('Short summary of conditions ("Clear", "Partly Cloudy", etc.).', 'wunderground'),
					),
					'text' => array(
						'label' => __('Forecast Text', 'wunderground'),
						'description' => __('Display a description of the forecast, normally in sentence format.', 'wunderground'),
					),
					'alerts' => array(
						'label' => __('Weather Alerts &amp; Warnings', 'wunderground'),
						'description' => __('Display Severe Weather alerts and warnings.', 'wunderground'),
					),
				);
				foreach ($boxes as $value => $box) {

					$label = esc_html( $box['label'] );
					$description = esc_html( $box['description'] );

					printf('<li><label><input type="checkbox" value="%s" name="%s[%s]" %s /> <span class="title">%s</span>%s</label></li>', $value, $this->get_field_name('showdata'), $value, checked( in_array( $value , (array)$showdata ), true , false ), $label, '<span class="howto">'.$description.'</span>' );
				}
			?>
			</ul>
		</div>

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('language'); ?>">
			<h3><?php esc_html_e('Forecast Language', 'wunderground'); ?></h3>
			<?php

			$languages = wp_list_pluck( wunderground_get_languages(), 'label', 'key' );

			echo wunderground_render_select($this->get_field_name('language'), $this->get_field_id('language'), $languages, $language);
			?>
			</label>
		</div>

		<div class="setting-wrapper">
			<h3><?php esc_html_e('Measurements', 'wunderground'); ?></h3>
			<ul>
				<li>
					<label class="radio"><input type="radio" class="radio" id="<?php echo $this->get_field_id('measurement_f'); ?>" name="<?php echo $this->get_field_name('measurement'); ?>" value="english" <?php checked('english', $measurement); ?> /> <span class="title"><?php esc_html_e('Fahrenheit &amp; Inches', 'wunderground'); ?></span></label>
				</li>
				<li>
					<label class="radio"><input type="radio" class="radio" id="<?php echo $this->get_field_id('measurement_c'); ?>" name="<?php echo $this->get_field_name('measurement'); ?>" value="metric" <?php checked('metric', $measurement); ?> /> <span class="title"><?php esc_html_e('Celsius &amp; Meters', 'wunderground'); ?></span></label>
				</li>
			</ul>
		</div>

	</div>
<?php
	}

	/**
	 * Generate output for each of the output Templates
	 * @param  string      $current_layout Key of the current layout
	 * @return string                      Output HTML
	 */
	function render_input_template($current_layout) {

		$layouts = Wunderground_Template::get_layouts();

		$output = '';
		foreach ($layouts as $key => $layout) {
			$output .= sprintf('
			<div class="layout">
				<label>
					<h3><input type="radio" value="%s" name="%s" onchange="jQuery(\'body\').trigger(\'wu-change\')" %s /> <span class="title">%s</span>%s</h3>
					<p><span class="howto">%s</span></p>
				</label>
			</div>', $key, $this->get_field_name('layout'), checked( $current_layout, $key, false ),  $layout['label'], $layout['thumbnail'], $layout['desc'] );
		}

		return $output;
	}

}

add_action('widgets_init', array( 'Wunderground_Forecast_Widget', 'register_widget' ) );
