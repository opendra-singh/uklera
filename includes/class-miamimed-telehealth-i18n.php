<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.smartdatainc.com
 * @since      1.0.0
 *
 * @package    Miamimed_Telehealth
 * @subpackage Miamimed_Telehealth/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Miamimed_Telehealth
 * @subpackage Miamimed_Telehealth/includes
 * @author     smartdatainc <opendrakumar.ss@smartdatainc.net>
 */
class Miamimed_Telehealth_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'miamimed-telehealth',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
