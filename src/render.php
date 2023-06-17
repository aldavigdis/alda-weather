<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$alda_weather = new AldaWeather();
$weather      = $alda_weather->get_weather_by_station_name( $attributes['stationName'] );
$weather_icon = plugins_url( 'alda-weather/weather-icons/svg/' . $alda_weather->description_to_icon( $weather->description ) . '.svg' );
$temperature  = $alda_weather->map_temperature_unit( $attributes['temperatureUnit'], $weather->temperature );
$wind_speed   = $alda_weather->map_wind_speed_unit( $attributes['windSpeedUnit'], $weather->wind_speed );
?>

<div
	<?php echo get_block_wrapper_attributes( array( 'data-station' => $attributes['stationName'], 'data-description' => $weather->description ) ); ?>
>
	<figure class="icon">
		<img
			src="<?php echo esc_attr( $weather_icon ); ?>"
			alt="<?php echo esc_attr( $weather->description ); ?>"
		/>
	</figure>
	<div class="content">
		<p class="station-name"><?php echo esc_html( $attributes['stationName'] ); ?></p>
		<ul class="stats">
			<li class="stat">
				<span class="stat-label"><?php echo esc_html( __( 'Temperature:', 'alda-weather' ) ); ?></span>
				<?php echo esc_html( number_format_i18n( $temperature['value'], '1' ) . $temperature['symbol'] ); ?>
			</li>
			<li class="stat">
				<span class="stat-label"><?php echo esc_html( __( 'Wind Speed:', 'alda-weather' ) ); ?></span>
				<?php echo esc_html( round( $wind_speed['value'], 0 ) . ' ' . $wind_speed['symbol'] ); ?>
			</li>
			<?php if ( false === empty( $weather->description ) ) : ?>
			<li class="stat">
				<?php echo esc_html( $weather->description ); ?>
			</li>
			<?php endif ?>
		</ul>
	</div>
</div>
