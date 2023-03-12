<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https:// dmonioazul.com
 * @since      1.0.0
 *
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Calcula_Finiquito
 * @subpackage Calcula_Finiquito/includes
 * @author     Ric Ag <kharman123@gmail.com>
 */
class Calcula_Finiquito_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'calcula-finiquito',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
