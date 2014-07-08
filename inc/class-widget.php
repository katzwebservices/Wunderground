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
			'description' => __( 'Add a forecast.')
		);

		$control_options = array( 'width'=> 400 ); // Min-width of widgets config with expanded sidebar

		parent::WP_Widget( false, __('Wunderground'), $widget_ops, $control_options );
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
	 * Uses template file `widget-related-listings.php` to generate the output
	 * @version 2.0.44
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
		$data['forecast'] = $forecast;
		$data['location'] = $instance['city'];
		$data['location_title'] = empty( $instance['location_title'] ) ? $data['location'] : $instance['location_title'];
		$data['wunderground'] = new KWS_Wunderground( $request );

		// PWS is offline or something.
		if( !empty( $data['wunderground']->response->error )) {
			do_action('wunderground_log_debug', 'There was an error in the Wunderground response:', $data['wunderground']->response->error );
			return;
		}

		echo $before_widget;

		if ( !empty($title) ) {
			echo $before_title . $title . $after_title;
		}

		do_action('wunderground_render_template', $instance['layout'], $data );

		echo $after_widget;
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

		//Defaults
		$instance = wp_parse_args( (array) $instance, array(
			'title' => __('Weather Forecast', 'wunderground'),
			'city' => '',
			'location_title' => NULL,
			'location_data' => '',
			'layout' => 'table-vertical',
			'iconset' => 'Incredible',
			'measurement' => 'english',
			'language' => wunderground_get_language(),
			'numdays' => '5',
			'showdata' => array('alerts', 'pop', 'night', 'date'),
		));

		extract($instance);

		?>
	<div class="wunderground-settings">

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<h3><?php _e('Title', 'wunderground'); ?></h3>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to hide the widget title.', 'wunderground' ); ?>" />
			</label>
		</div>

		<div class="setting-wrapper autocomplete" id="<?php echo $this->get_field_id('autocomplete'); ?>">
			<label for="<?php echo $this->get_field_id('city'); ?>">
				<h3><?php _e('Location', 'wunderground'); ?></h3>
				<p class="description"><?php _e('Locations will autoload, but you may also define custom locations.', 'wunderground'); ?></p>
				<input type="text" class="wu-autocomplete widefat" autocomplete="false" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" value="<?php echo esc_attr( $city ); ?>" placeholder="<?php esc_attr_e( 'Enter the name of a location.', 'wunderground' ); ?>" />
			</label>
			<input type="hidden" class="wu-location-data" id="<?php echo $this->get_field_id('location_data'); ?>" name="<?php echo $this->get_field_name('location_data'); ?>" value="<?php echo $location_data; ?>" />
		</div>

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('location_title'); ?>">
				<h3><?php _e('Location Title', 'wunderground'); ?></h3>
				<p class="description"><?php esc_attr_e( 'Change how the location is displayed in the widget search field.', 'wunderground'); ?></p>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('location_title'); ?>" name="<?php echo $this->get_field_name('location_title'); ?>" value="<?php echo esc_attr( $location_title ); ?>" placeholder="<?php esc_attr_e( 'Leave empty to use the location name.', 'wunderground' ); ?>" />
				<span class="howto"><?php esc_attr_e( 'Example: if the Location is set to "Denver, Colorado", you may prefer to set the Location Title as "Denver", which is simpler.', 'wunderground' ); ?></span>
			</label>
		</div>

		<div class="setting-wrapper">
		<?php

			$days_select = wunderground_render_select($this->get_field_name('numdays'), $this->get_field_id('numdays'), array( '1' => 1, '3' => 3, '5' => 5, '10' => 10 ), $numdays);

			echo sprintf('<label for="%s"><h3>%s</h3> %s</label>', $this->get_field_id('numdays'), __('# of Days in Forecast'), $days_select);
		?>
		</div>

		<div class="setting-wrapper icons">
			<h3><?php _e('Icon Set', 'wunderground'); ?></h3>
			<p class="description"><?php _e('Choose the look and feel of the images that will represent the weather.', 'wunderground'); ?></p>

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
							<img src="%s/clear.gif" />
						</span>
					</label>
				</li>', $name, $this->get_field_name('iconset'), $this->get_field_id('iconset'), $checked, $name, wunderground_get_icon( $name ) );
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
			<h3><?php _e('Show in Forecast', 'wunderground'); ?></h3>
			<ul>
			<?php
				$boxes = array(
					'icon' => __('Weather Icon', 'wunderground'),
					'night' => __('Night Forecasts', 'wunderground'),
					'conditions' => __('Condition Title', 'wunderground'),
					'text' => __('Forecast Text', 'wunderground'),
					'pop' => __('Chance of Rain', 'wunderground'),
					'summary' => __('Today\'s Weather Summary', 'wunderground'),
					'alerts' => __('Weather Alerts &amp; Warnings', 'wunderground'),
					'date' => __('Date', 'wunderground'),
				);
				foreach ($boxes as $value => $label) {
					printf('<li><label><input type="checkbox" value="%s" name="%s[%s]" %s /> <span class="title">%s</span></label></li>', $value, $this->get_field_name('showdata'), $value, checked( in_array( $value , (array)$showdata ), true , false ), $label );
				}
			?>
			</ul>
		</div>

		<div class="setting-wrapper">
			<label for="<?php echo $this->get_field_id('language'); ?>">
			<h3><?php _e('Forecast Language', 'wunderground'); ?></h3>
			<?php

			echo wunderground_render_select($this->get_field_name('language'), $this->get_field_id('language'), wunderground_get_languages(), $language);
			?>
			</label>
		</div>

		<div class="setting-wrapper">
			<h3><?php _e('Measurements', 'wunderground'); ?></h3>
			<ul>
				<li>
					<label class="radio"><input type="radio" class="radio" id="<?php echo $this->get_field_id('measurement_f'); ?>" name="<?php echo $this->get_field_name('measurement'); ?>" value="english" <?php checked('english', $measurement); ?> /> <span class="title"><?php _e('Fahrenheit &amp; Inches', 'wunderground'); ?></span></label>
				</li>
				<li>
					<label class="radio"><input type="radio" class="radio" id="<?php echo $this->get_field_id('measurement_c'); ?>" name="<?php echo $this->get_field_name('measurement'); ?>" value="metric" <?php checked('metric', $measurement); ?> /> <span class="title"><?php _e('Celsius &amp; Meters', 'wunderground'); ?></span></label>
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
