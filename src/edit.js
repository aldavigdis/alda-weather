/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { SelectControl, RadioControl, PanelBody } from '@wordpress/components';

import apiFetch from '@wordpress/api-fetch';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const mapWindSpeedUnit = ( windSpeedUnit ) => {
		switch (windSpeedUnit) {
			case 'kmh':
				return __( 'km/h', 'alda-weather' )
			case 'mph':
				return __( 'mi/h', 'alda-weather' )
			case 'kts':
				return __( 'kts.', 'alda-weather' )
		}
		return __( 'm/s', 'alda-weather' )
	}

	const convertWindSpeedUnit = ( windSpeedUnit, msValue ) => {
		switch (windSpeedUnit) {
			case 'kmh':
				return Math.round( 3.6 * msValue )
			case 'mph':
				return Math.round( 2.23694 * msValue )
			case 'kts':
				return Math.round( 1.94384 * msValue )
		}
		return msValue
	}

	const mapTemperatureUnit = ( temperatureUnit ) => {
		switch (temperatureUnit) {
			case 'farenheit':
				return '°F'
			case 'kelvin':
				return ' K'
		}
		return '°C'
	}

	const convertTempratureUnit = ( temperatureUnit, celciusValue ) => {
		switch (temperatureUnit) {
			case 'farenheit':
				return ( 1.8 * celciusValue ).toFixed(1)
			case 'kelvin':
				return ( 273.15 + celciusValue ).toFixed(1)
		}
		return celciusValue.toFixed(1)
	}

	const onChangeStationName = ( newStationName ) => {
		setAttributes( { stationName: newStationName } );
	};

	const onChangeTemperatureUnit = ( newTemperatureUnit ) => {
		setAttributes( { temperatureUnit: newTemperatureUnit } )
	};

	const onChangeWindSpeedUnit = ( newWindSpeedUnit ) => {
		setAttributes( { windSpeedUnit: newWindSpeedUnit } )
	};

	return (
		<div { ...useBlockProps() } >
			<InspectorControls key = "setting" >
				<PanelBody >
					<SelectControl
						label={ __('Weather Station', 'alda-weather') }
						value={ attributes.stationName }
						options={
							[
								{
									label: __('Reykjavik', 'alda-weather'),
									value: 'Reykjavík'
								},
								{
									label: __('Hafnarfjall', 'alda-weather'),
									value: 'Hafnarfjall'
								},
								{
									label: __('Isafjordur', 'alda-weather'),
									value: 'Ísafjörður'
								},
								{
									label: __('Holmavik', 'alda-weather'),
									value: 'Hólmavík'
								},
								{
									label: __('Akureyri', 'alda-weather'),
									value: 'Akureyri'
								},
								{
									label: __('Egilsstaðir Airport', 'alda-weather'),
									value: 'Egilsstaðaflugvöllur'
								},
								{
									label: __('Selfoss', 'alda-weather'),
									value: 'Selfoss'
								}
							]
						}
						onChange={ onChangeStationName }
					/>
					<RadioControl
						label={ __('Temperature Unit', 'alda-weather') }
						selected={ attributes.temperatureUnit }
						options={
							[
								{
									label: __('Celcius', 'alda-weather'),
									value: 'celcius'
								},
								{
									label: __('Farenheit', 'alda-weather'),
									value: 'farenheit'
								},
								{
									label: __('Kelvin', 'alda-weather'),
									value: 'kelvin'
								}
							]
						}
						onChange={ onChangeTemperatureUnit }
					/>
					<RadioControl
						label={ __('Wind Speed Unit', 'alda-weather' ) }
						selected={ attributes.windSpeedUnit }
						options={
							[
								{
									label: __('m/s', 'alda-weather'),
									value: 'ms'
								},
								{
									label: __('km/h', 'alda-weather'),
									value: 'kmh'
								},
								{
									label: __('mph', 'alda-weather'),
									value: 'mph'
								},
								{
									label: __('knots', 'alda-weather'),
									value: 'kts'
								}
							]
						}
						onChange={ onChangeWindSpeedUnit }
					/>
					<p>
						{
							__(
								'Plugin by Alda Vigdís. Weather data from the Icelandic Meterological Office, via apis.is.',
								'alda-weather'
							)
						}
					</p>
					<p>
						{
							__(
								'Weather icons by Erik Flowers and Lukas Bischoff, licenced under the SIL Open Font License (OFL).',
								'alda-weather'
							)
						}
					</p>
				</PanelBody >
			</InspectorControls >
			<figure className = 'icon' >
				<img
					src='/wp-content/plugins/alda-weather/weather-icons/svg/wi-day-cloudy.svg'
				/>
			</figure >
			<div className='content' >
				<p className='station-name'>
					{ attributes.stationName }
				</p>
				<ul className='stats' >
					<li className='stat'>
						<span className='stat-label'>{ __( 'Temperature:', 'alda-weather' ) } </span>
						{ convertTempratureUnit( attributes.temperatureUnit, 17.41 ) }{ mapTemperatureUnit( attributes.temperatureUnit ) }
					</li>
					<li className='stat'>
						<span className='stat-label'>{ __( 'Wind Speed:', 'alda-weather' ) } </span>
						{ convertWindSpeedUnit( attributes.windSpeedUnit, 5 ) } { mapWindSpeedUnit( attributes.windSpeedUnit ) }
					</li>
					<li className='stat'>
						{ __('Overcast') }
					</li>
				</ul>
			</div>
		</div>
	);
}
