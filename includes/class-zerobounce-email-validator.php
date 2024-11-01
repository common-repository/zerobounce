<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/includes
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Zerobounce_Email_Validator_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->version = ZEROBOUNCE_EMAIL_VALIDATOR_VERSION;
        $this->plugin_name = 'zerobounce-email-validator';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-zerobounce-email-validator-loader.php';

        /**
         * The class responsible for defining ZeroBounce API functionality of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-zerobounce-email-validator-api.php';

        /**
         * The class responsible for defining internationalization functionality of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-zerobounce-email-validator-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-zerobounce-email-validator-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-zerobounce-email-validator-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-zerobounce-email-validator-form-public.php';

        $this->loader = new Zerobounce_Email_Validator_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Zerobounce_Email_Validator_i18n class in order to set the domain and to register the hook with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Zerobounce_Email_Validator_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Zerobounce_Email_Validator_Admin(new Zerobounce_Email_Validator_API($this->get_api_key(), $this->get_api_timeout()), $this->get_plugin_name(), $this->get_version());

        $prefix = is_network_admin() ? 'network_admin_' : '';
        $this->loader->add_filter("{$prefix}plugin_action_links_" . ZEROBOUNCE_BASENAME, $plugin_admin, 'settings_shortcut', 10, 2);
        $this->loader->add_filter('pre_update_option_zerobounce_settings_api_key', $plugin_admin, 'pre_update_option_zb_api_callback', 10, 2);

        if (isset($_GET['page']) && strpos($_GET['page'], 'zerobounce-email-validator') !== -1) {
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        }

        $this->loader->add_action('admin_menu', $plugin_admin, 'addPluginAdminMenu');
        $this->loader->add_action('admin_init', $plugin_admin, 'registerAndBuildFields');
        $this->loader->add_action('admin_notices', $plugin_admin, 'admin_notice');
        $this->loader->add_action('added_option', $plugin_admin, 'added_option_callback', 10, 2);
        $this->loader->add_action('wp_ajax_zerobounce_current_credits', $plugin_admin, 'current_credits');
        $this->loader->add_action('wp_ajax_zerobounce_validate_email_test', $plugin_admin, 'validate_email_test');
        $this->loader->add_action('wp_ajax_zerobounce_validate_bulk_test', $plugin_admin, 'validate_bulk_test');
        $this->loader->add_action('wp_ajax_zerobounce_validation_logs', $plugin_admin, 'validation_logs');
        $this->loader->add_action('wp_ajax_zerobounce_credit_usage_logs', $plugin_admin, 'credit_usage_logs');
        $this->loader->add_action('wp_ajax_zerobounce_validation_full_logs', $plugin_admin, 'validation_full_logs');
        $this->loader->add_action('wp_ajax_zerobounce_validation_single_log', $plugin_admin, 'validation_single_log');
        $this->loader->add_action('wp_ajax_zerobounce_batch_email_validation', $plugin_admin, 'validate_batch');
        $this->loader->add_action('wp_ajax_zerobounce_get_uploaded_file_data', $plugin_admin, 'get_uploaded_file_data');
        $this->loader->add_action('wp_ajax_zerobounce_validated_emails_download', $plugin_admin, 'validated_emails_download');
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Zerobounce_Email_Validator_Public(new Zerobounce_Email_Validator_API($this->get_api_key(), $this->get_api_timeout()), $this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $validation_forms = get_option('zerobounce_settings_validation_forms');

        // Contact Form7
        if (is_array($validation_forms) && in_array('validation_contact_form_7', $validation_forms)) {
            $this->loader->add_filter('wpcf7_validate_email', $plugin_public, 'contact_form_7_validator', 10, 2);
            $this->loader->add_filter('wpcf7_validate_email*', $plugin_public, 'contact_form_7_validator', 10, 2);
        }

        // BWS Forms
        if (is_array($validation_forms) && in_array('validation_bws_forms', $validation_forms)) {
            $this->loader->add_filter('cntctfrm_check_form', $plugin_public, 'contact_form_validator', 10, 1);
        }

        // WPForms
        if (is_array($validation_forms) && in_array('validation_wpforms', $validation_forms)) {
            $this->loader->add_filter('wpforms_process_after_filter', $plugin_public, 'wpforms_validator', 10, 3);
        }

        // NinjaForms
        if (is_array($validation_forms) && in_array('validation_ninjaforms', $validation_forms)) {
            $this->loader->add_filter('ninja_forms_submit_data', $plugin_public, 'ninjaforms_validator', 10, 1);
        }

        // Formidable Forms
        if (is_array($validation_forms) && in_array('validation_formidableforms', $validation_forms)) {
            $this->loader->add_filter('frm_validate_entry', $plugin_public, 'formidableforms_validator', 20, 2);
        }

        // WooCommerce
        if (is_array($validation_forms) && in_array('validation_woocommerce', $validation_forms)) {
            $this->loader->add_filter('woocommerce_after_checkout_validation', $plugin_public, 'woocommerce_validator', 10, 2);
        }

        // WordPress Post Comments
        if (is_array($validation_forms) && in_array('validation_wordpress_comments', $validation_forms)) {
            $this->loader->add_action('pre_comment_on_post', $plugin_public, 'apply_is_email_validator');
            $this->loader->add_action('comment_post', $plugin_public, 'remove_is_email_validator');
        }

        // WordPress Registration
        if (is_array($validation_forms) && in_array('validation_wordpress_registration', $validation_forms)) {
            $this->loader->add_filter('registration_errors', $plugin_public, 'wordpress_registration_validator', 10, 3);
            $this->loader->add_filter('wpmu_validate_user_signup', $plugin_public, 'wordpress_multisite_registration_validator', 10, 1);
        }

        // MC4WP: Mailchimp for WordPress
        if (is_array($validation_forms) && in_array('validation_mc4wp_mailchimp', $validation_forms)) {
            $this->loader->add_filter('mc4wp_form_messages', $plugin_public, 'mc4wp_mailchimp_error_message');
            $this->loader->add_filter('mc4wp_form_errors', $plugin_public, 'mc4wp_mailchimp_validator', 10, 2);
        }

        // Gravity Forms
        if (is_array($validation_forms) && in_array('validation_gravity_forms', $validation_forms)) {
            $this->loader->add_filter('gform_field_validation', $plugin_public, 'gravity_forms_validator', 10, 4);
        }

        // Fluent Forms
        if (is_array($validation_forms) && in_array('validation_fluent_forms', $validation_forms)) {
            $this->loader->add_filter('fluentform/validate_input_item_input_email', $plugin_public, 'fluent_forms_validator', 10, 6);
        }

        // WS Forms
        if (is_array($validation_forms) && in_array('validation_ws_forms', $validation_forms)) {
            $this->loader->add_filter('wsf_action_email_email_validate', $plugin_public, 'ws_forms_validator', 10, 4);
        }

        // Mailster Form
        if (is_array($validation_forms) && in_array('validation_mailster_forms', $validation_forms)) {
            $this->loader->add_filter('mailster_verify_subscriber', $plugin_public, 'mailster_email_validator', 1);
        }

        // Forminator Form
        if (is_array($validation_forms) && in_array('validation_forminator_forms', $validation_forms)) {
            $this->loader->add_filter('forminator_custom_form_submit_errors', $plugin_public, 'forminator_email_validator', 10, 3);
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Zerobounce_Email_Validator_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the API Key for ZeroBounce.
     *
     * @return    string    The api key defined or null.
     * @since     1.0.0
     */
    public function get_api_key()
    {
        $api_key = get_option('zerobounce_settings_api_key');

        return $api_key ? $api_key : "";
    }

    /**
     * Retrieve the API Timeout for ZeroBounce.
     *
     * @return    int    The api timeout defined or 5 seconds by default.
     * @since     1.0.0
     */
    public function get_api_timeout()
    {
        $api_timeout = get_option('zerobounce_settings_api_timeout');

        return $api_timeout ? $api_timeout : (int)50;
    }
}
