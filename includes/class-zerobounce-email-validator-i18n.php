<?php

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/includes
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'zerobounce-email-validator',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
