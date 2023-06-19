<?php
/**
 * Plugin Name:       Alda Weather
 * Description:       Display a local Icelandic weather report
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Alda Vigdís
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       alda-weather
 * Domain Path:       alda-weather
 *
 * @package           alda-weather
 */

class AldaWeather {
	const TRANSIENT_EXPIRY = HOUR_IN_SECONDS * 2;
	const CRON_RECURRENCE = 'hourly';
	const API_BASE_URL = 'https://apis.is/weather/observations/en';
	const STATION_IDS = array(1, 422, 6300, 571, 31674, 2481, 2642);

	const DESCRIPTION_ICON_MAPPINGS = array(
		'Clear sky' => 'wi-day-sunny',
		'Party cloudy' => 'wi-day-cloudy',
		'Cloudy' => 'wi-day-cloudy',
		'Overcast' => 'wi-day-sunny-overcast',
		'Light rain' => 'wi-day-showers',
		'Rain' => 'wi-day-rain',
		'Light sleet' => 'wi-day-sleet',
		'Sleet' => 'wi-day-sleet',
		'Light snow' => 'wi-day-snow',
		'Snow' => 'wi-day-snow',
		'Rain showers' => 'wi-day-rain-mix',
		'Sleet showers' => 'wi-day-sleet',
		'Snow showers' => 'wi-day-snow',
		'Dust devil' => 'wi-tornado',
		'Dust storm' => 'wi-dust',
		'Blowing snow' => 'wi-strong-wind',
		'Fog' => 'wi-day-fog',
		'Light drizzle' => 'wi-day-sprinkle',
		'Drizzle', 'wi-day-sprinkle',
		'Freezing rain' => 'wi-snowflake-cold',
		'Hail' => 'wi-day-hail',
		'Light thunder' => 'wi-day-thunderstorm',
		'Thunder' => 'wi-lightning'
	);

	function __construct() {
		add_action( 'init', array( $this, 'block_init' ) );
		add_action( 'alda_weather_cron', array( $this, 'cron_sync' ) );

		register_activation_hook(
			__FILE__,
			array( $this, 'schedule_cron_sync' )
		);

		register_deactivation_hook(
			__FILE__,
			array( $this, 'unschedule_cron_sync' )
		);
	}

	/**
	 * Schedule retreival of our API data
	 */
	function schedule_cron_sync() {
		if ( ! wp_next_scheduled( 'alda_weather_cron' ) ) {
			wp_schedule_event(
				time(),
				self::CRON_RECURRENCE,
				'alda_weather_cron'
			);
		}
	}

	/**
	 * Cancell our wp-cron event
	 */
	function unschedule_cron_sync() {
		wp_unschedule_event(
			wp_next_scheduled( 'alda_weather_cron' ),
			'alda_weather_cron'
		);
	}

	/**
	 * Fetch the weather data from apis.is if the transient has expired
	 */
	function cron_sync() {
		$current_weather = get_transient( 'alda-weather' );

		if ( $current_weather ) {
			return false;
		}

		return $this->save_weather_as_transient();
	}

	/**
	 * Register the Alda Weather block
	 */
	function block_init() {
		register_block_type( __DIR__ . '/build' );
	}

	/**
	 * Assemble an API url to fetch our weather reports
	 */
	private function api_url() {
		$query = http_build_query(
			array(
				'stations' => implode( ',', self::STATION_IDS ),
				'time'     => '1h',
				'anytime'  => '1'
			)
		);

		return self::API_BASE_URL . '?' . $query;
	}

	/**
	 * Fetch the weather data from apis.is
	 */
	function fetch_weather() {
		$api_url = $this->api_url();
		$weather_request = wp_remote_get( $api_url );
		$weather_results = json_decode( $weather_request['body'] )->results;

		return $weather_results;
	}

	/**
	 * Set a transient with the current weather reports
	 */
	function save_weather_as_transient() {
		$weathers = array();
		$weathers_from_met_office = $this->fetch_weather();
		foreach ($weathers_from_met_office as $w) {
			$weathers[$w->name] = (object) array(
				'description' => $w->W,
				'wind_speed' => $w->F,
				'temperature' => $w->T
			);
		}

		return set_transient(
			'alda-weather', $weathers, self::TRANSIENT_EXPIRY
		);
	}

	/**
	 * Get the weather for every station
	 */
	function get_weather() {
		$current_weather = get_transient( 'alda-weather' );

		if ( $current_weather ) {
			return $current_weather;
		}

		if ( $this->save_weather_as_transient() ) {
			return get_transient( 'alda-weather' );
		}

		return false;
	}

	/**
	 * Read the weather for a specific station, by name
	 */
	function get_weather_by_station_name( $station_name ) {
		return $this->get_weather()[$station_name];
	}

	/**
	 * Convert a weather description to an icon file name
	 *
	 * The met office has a different list of descriptions than our icon
	 * collection, so they get mapped to the associated file name.
	 *
	 * @link https://www.vedur.is/media/vedurstofan/XMLthjonusta.pdf
	 */
	function description_to_icon( $description ) {
		if ( true === array_key_exists(
			$description, self::DESCRIPTION_ICON_MAPPINGS
		)) {
			return self::DESCRIPTION_ICON_MAPPINGS[$description];
		}
		return 'wi-day-sunny';
	}

	/**
	 * Convert temperature units from degrees celcius to a different unit
	 */
	function map_temperature_unit( $unit, $celcius_value ) {
		switch ($unit) {
			case 'farenheit':
				return array(
					'symbol' => '°F',
					'value' => round( 1.8 * $celcius_value, 1 )
				);
			case 'kelvin':
				return array(
					'symbol' => ' K',
					'value' => round( 273.15 + $celcius_value, 1 )
				);
		}
		return array(
			'symbol' => '°C',
			'value' => round( $celcius_value )
		);
	}

	/**
	 * Convert wind speed from meters per second to a different unit
	 */
	function map_wind_speed_unit( $unit, $ms_value ) {
		switch ($unit) {
			case 'kmh':
				return array(
					'symbol' => 'km/h',
					'value' => round( 3.6 * $ms_value )
				);
			case 'mph':
				return array(
					'symbol' => 'mph',
					'value' => round( 2.23694 * $ms_value )
				);
			case 'kts':
				return array(
					'symbol' => 'kts.',
					'value' => round( 1.94384 * $ms_value )
				);
		}
		return array(
			'symbol' => 'm/s',
			'value' => round( $ms_value )
		);
	}
}

$alda_weather = new AldaWeather();
