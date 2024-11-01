<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.zerobounce.net/
 * @since             1.0.10
 * @package           Zerobounce_Email_Validator
 *
 * @wordpress-plugin
 * Plugin Name:       ZeroBounce Email Validator
 * Plugin URI:        https://wordpress.org/plugins/zerobounce/
 * Description:       ZeroBounce Email Validation Plugin
 * Version:           1.1.2
 * Author:            ZeroBounce
 * Author URI:        https://www.zerobounce.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zerobounce-email-validator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('ZEROBOUNCE_BASENAME')) {
    define('ZEROBOUNCE_BASENAME', plugin_basename(__FILE__));
}

define('ZEROBOUNCE_EMAIL_VALIDATOR_VERSION', '1.1.2');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zerobounce-email-validator-activator.php
 */
function activate_zerobounce_email_validator($network_wide)
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-zerobounce-email-validator-activator.php';
    Zerobounce_Email_Validator_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zerobounce-email-validator-deactivator.php
 */
function deactivate_zerobounce_email_validator()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-zerobounce-email-validator-deactivator.php';
    Zerobounce_Email_Validator_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_zerobounce_email_validator');
register_deactivation_hook(__FILE__, 'deactivate_zerobounce_email_validator');


function zerobounce_setup_new_site_table($site)
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-zerobounce-email-validator-activator.php';
    Zerobounce_Email_Validator_Activator::setup_site_table($site->id);
}
add_action('wp_initialize_site', 'zerobounce_setup_new_site_table');


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-zerobounce-email-validator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zerobounce_email_validator()
{

    $plugin = new Zerobounce_Email_Validator();
    $plugin->run();
}

run_zerobounce_email_validator();
