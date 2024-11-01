<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/admin
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator_Admin
{
    /**
     * The ZeroBounce API class used for validation
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api Zerobounce_Email_Validator_API
     */
    private $api;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $api New instance for Zerobounce_Email_Validator_API class
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($api, $plugin_name, $version)
    {

        $this->api = $api;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the shortcut for the settings area.
     *
     * @since    1.0.0
     */
    public function settings_shortcut($links, $file)
    {
        if (is_network_admin()) {
            $settings_url = network_admin_url('admin.php?page=zerobounce-email-validator');
        } else {
            $settings_url = admin_url('admin.php?page=zerobounce-email-validator');
        }

        $settings_link = '<a href="' . esc_url($settings_url) . '">' . __('Settings', 'zerobounce-email-validator') . '</a>';
        array_unshift($links, $settings_link);

        $credits_link = '<a style="font-weight: bold;" href="https://www.zerobounce.net/members/pricing" target="_blank">' . __('Buy Credits', 'zerobounce-email-validator') . '</a>';
        array_unshift($links, $credits_link);

        return $links;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook)
    {
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'zerobounce-email-validator':
                case 'zerobounce-email-validator-settings':
                case 'zerobounce-email-validator-tools':
                case 'zerobounce-email-validator-logs':
                    {
                        // On all zerobounce-email-validator* pages
                        wp_enqueue_style('zerobounce-email-validator-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), '5.0.2', 'all');

                        // On dashboard page
                        if ($_GET['page'] === 'zerobounce-email-validator') {
                            wp_enqueue_style('zerobounce-email-validator-apexcharts', plugin_dir_url(__FILE__) . 'css/apexcharts.min.css', array(), '3.37.0', 'all');
                        }

                        // On logs page
                        if ($_GET['page'] === 'zerobounce-email-validator-logs') {
                            wp_enqueue_style('zerobounce-email-validator-datatables', plugin_dir_url(__FILE__) . 'css/datatables.min.css', array(), '1.13.2', 'all');
                        }

                        // On all zerobounce-email-validator* pages
                        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zerobounce-email-validator-admin.css', array(), $this->version, 'all');
                    }
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'zerobounce-email-validator':
                case 'zerobounce-email-validator-settings':
                case 'zerobounce-email-validator-tools':
                case 'zerobounce-email-validator-logs':
                    {
                        // On all zerobounce-email-validator* pages
                        wp_enqueue_script('zerobounce-email-validator-bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', array(), '5.0.2', false);

                        // On dashboard page
                        if ($_GET['page'] === 'zerobounce-email-validator') {
                            wp_enqueue_script('zerobounce-email-validator-apexcharts', plugin_dir_url(__FILE__) . 'js/apexcharts.min.js', array(), '3.37.0', false);
                        }

                        // On logs page
                        if ($_GET['page'] === 'zerobounce-email-validator-logs') {
                            wp_enqueue_script('zerobounce-email-validator-datatables', plugin_dir_url(__FILE__) . 'js/datatables.min.js', array(), '1.13.2', false);
                        }

                        // On all zerobounce-email-validator* pages
                        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zerobounce-email-validator-admin.js', array('jquery'), $this->version, false);

                        wp_localize_script($this->plugin_name, 'params', [
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'ajax_current_credits_nonce' => wp_create_nonce('zerobounce-credits-nonce'),
                            'ajax_validation_nonce' => wp_create_nonce('zerobounce-validation-nonce'),
                            'ajax_validation_charts_nonce' => wp_create_nonce('zerobounce-validation-charts-nonce'),
                            'ajax_credit_usage_charts_nonce' => wp_create_nonce('zerobounce-credits-charts-nonce'),
                            'ajax_validation_full_logs_nonce' => wp_create_nonce('zerobounce-full-logs-nonce'),
                            'ajax_validation_single_log_nonce' => wp_create_nonce('zerobounce-single-log-nonce'),
                            'ajax_batch_validation_nonce' => wp_create_nonce('zerobounce-batch-validation-nonce'),
                            'ajax_get_files_info_nonce' => wp_create_nonce('zerobounce-get-files-info-nonce'),
                            'ajax_download_validated_file_nonce' => wp_create_nonce('zerobounce-download-validated-file-nonce'),
                        ]);
                    }
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * addPluginAdminMenu
     *
     * @return void
     */
    public function addPluginAdminMenu()
    {
        add_menu_page($this->plugin_name, __('ZeroBounce Email', 'zerobounce-email-validator'), 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'), 'dashicons-email-alt', 65);

        add_submenu_page($this->plugin_name, __('ZeroBounce Email Dashboard', 'zerobounce-email-validator'), __('Dashboard', 'zerobounce-email-validator'), 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'));

        add_submenu_page($this->plugin_name, __('ZeroBounce Email Settings', 'zerobounce-email-validator'), __('Settings', 'zerobounce-email-validator'), 'administrator', $this->plugin_name . '-settings', array($this, 'displayPluginAdminSettings'));

        add_submenu_page($this->plugin_name, __('ZeroBounce Email Tools', 'zerobounce-email-validator'), __('Tools', 'zerobounce-email-validator'), 'administrator', $this->plugin_name . '-tools', array($this, 'displayPluginAdminTools'));

        add_submenu_page($this->plugin_name, __('ZeroBounce Email Logs', 'zerobounce-email-validator'), __('Logs', 'zerobounce-email-validator'), 'administrator', $this->plugin_name . '-logs', array($this, 'displayPluginAdminLogs'));
    }

    /**
     * displayPluginAdminDashboard
     *
     * @return void
     */
    public function displayPluginAdminDashboard()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-display.php';
    }

    /**
     * displayPluginAdminSettings
     *
     * @return void
     */
    public function displayPluginAdminSettings()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
    }

    public function displayPluginAdminTools()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-tools-display.php';
    }

    public function displayPluginAdminLogs()
    {
        require_once 'partials/' . $this->plugin_name . '-admin-logs-display.php';
    }

    /**
     * admin_notice
     *
     * @return void
     */
    public function admin_notice()
    {
        if (isset($_GET['page']) && $_GET['page'] !== 'zerobounce-email-validator-settings' || isset($_GET['plugin_status'])) {
            if (!$this->api->is_api_key()) {
                echo '<div class="notice notice-warning">
                    <p>' . sprintf(__('Please get your ZeroBounce API Key from %shere%s and save it inside the %ssettings page%s.', 'zerobounce-email-validator'),
                        '<a href="https://www.zerobounce.net/members/API" target="_blank">', '</a>', '<a href="' . get_admin_url() . 'admin.php?page=zerobounce-email-validator-settings">', '</a>') . '
                    </p>
        		</div>';
                return;
            }
        }

        $credits = $this->api->get_credits_info();

        if ($credits && $credits !== -1 && $credits < 1) {
            echo '<div class="notice notice-error">
                    <p>' . sprintf(__('You have run out of ZeroBounce Credits! Please %sbuy more credits%s to continue validating emails.', 'zerobounce-email-validator'), '<a href="https://www.zerobounce.net/members/pricing" target="_blank">', '</a>') . '</p>
        		</div>';
            return;
        }
    }

    public function registerAndBuildFields()
    {
        add_settings_section(
            'zerobounce_email_validator_settings_general_section',
            __('General', 'zerobounce-email-validator'),
            [$this, 'settings_render_sections'],
            'zerobounce_email_validator_settings'
        );

        add_settings_section(
            'zerobounce_email_validator_settings_validation_section',
            __('Validation', 'zerobounce-email-validator'),
            [$this, 'settings_render_sections'],
            'zerobounce_email_validator_settings'
        );


        $fields = [
            [
                'uid' => 'zerobounce_settings_api_key',
                'label' => __('API Key', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'password',
                'placeholder' => '123456789....',
                'helper' => '<a href="https://www.zerobounce.net/members/API" target="_blank">' . __('Get API Key', 'zerobounce-email-validator') . '</a>',
                'supplimental' => __('Please input your ZeroBounce API Key.', 'zerobounce-email-validator'),
                'default' => ''
            ],
            [
                'uid' => 'zerobounce_settings_api_zone',
                'label' => __('API Location', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'checkbox',
                'placeholder' => '',
                'options' => ['api_usa' => 'U.S.A API'],
                'supplimental' => __('* Only the authentication will be handled by our default servers', 'zerobounce-email-validator'),
                'helper' => '',
                'default' => [''],
            ],
            [
                'uid' => 'zerobounce_settings_api_timeout',
                'label' => __('API Timeout', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'number',
                'helper' => '',
                'supplimental' => __('Set the maximum API timeout in seconds. The plugin will wait for this limit after which it will abort validation.', 'zerobounce-email-validator'),
                'default' => '50',
                'placeholder' => '50',
            ],
            [
                'uid' => 'zerobounce_settings_error_message',
                'label' => __('Custom Invalid Error Message', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'text',
                'helper' => '',
                'supplimental' => __('* Note that the MailChimp integration does not support custom error message', 'zerobounce-email-validator'),
                'default' => '',
                'placeholder' => __('Enter custom invalid error message.', 'zerobounce-email-validator'),
            ],
            [
                'uid' => 'zerobounce_settings_did_you_mean',
                'label' => __('Typo validation', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'checkbox',
                'options' => ['did_you_mean' => __('Validate typos', 'zerobounce-email-validator')],
                'helper' => '',
                'supplimental' => '',
                'default' => ['did_you_mean'],
            ],
            [
                'uid' => 'zerobounce_settings_did_you_mean_error',
                'label' => '',
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'text',
                'options' => ['did_you_mean' => __('Validate typos', 'zerobounce-email-validator')],
                'helper' => '',
                'supplimental' => __('* The custom error message you provide will be added before the suggested correction', 'zerobounce-email-validator'),
                'default' => '',
                'placeholder' => __('Enter custom typo error message.', 'zerobounce-email-validator'),
            ],
            [
                'uid' => 'zerobounce_settings_validation_forms',
                'label' => __('Hook and validate forms', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_general_section',
                'type' => 'checkbox',
                'placeholder' => '',
                'options' => [
                    'validation_contact_form_7' => __('Contact Form 7', 'zerobounce-email-validator'),
                    'validation_wpforms' => __('WPForms', 'zerobounce-email-validator'),
                    'validation_ninjaforms' => __('Ninja Forms', 'zerobounce-email-validator'),
                    'validation_formidableforms' => __('Formidable Forms', 'zerobounce-email-validator'),
                    'validation_woocommerce' => __('WooCommerce', 'zerobounce-email-validator'),
                    'validation_wordpress_comments' => __('WordPress Post Comments', 'zerobounce-email-validator'),
                    'validation_wordpress_registration' => __('WordPress Registration', 'zerobounce-email-validator'),
                    'validation_mc4wp_mailchimp' => __('MC4WP: Mailchimp for WordPress', 'zerobounce-email-validator'),
                    'validation_gravity_forms' => __('Gravity Forms', 'zerobounce-email-validator'),
                    'validation_fluent_forms' => __('Fluent Forms', 'zerobounce-email-validator'),
                    'validation_ws_forms' => __('WS Forms', 'zerobounce-email-validator'),
                    'validation_mailster_forms' => __('Mailster Forms', 'zerobounce-email-validator'),
                    'validation_forminator_forms' => __('Forminator Forms', 'zerobounce-email-validator'),
                    'validation_bws_forms' => __('BWS Forms', 'zerobounce-email-validator')
                ],
                'helper' => '',
                'supplimental' => __('Select on which forms to validate email addresses. We recommend that you only enable the ones you actually have installed and are using. For example if you are using only WPForms, then select only WPForms.', 'zerobounce-email-validator'),
                'default' => [
                    'validation_contact_form_7',
                    'validation_wpforms',
                    'validation_ninjaforms',
                ],
            ],
            [
                'uid' => 'zerobounce_settings_validation_pass',
                'label' => __('Email will pass on status', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_validation_section',
                'type' => 'checkbox',
                'placeholder' => '',
                'options' => [
                    'valid' => __('Valid', 'zerobounce-email-validator'),
                    'invalid' => __('Invalid', 'zerobounce-email-validator'),
                    'catch-all' => __('Catch-All', 'zerobounce-email-validator'),
                    'unknown' => __('Unknown', 'zerobounce-email-validator'),
                    'spamtrap' => __('Spamtrap', 'zerobounce-email-validator'),
                    'abuse' => __('Abuse', 'zerobounce-email-validator'),
                    'do_not_mail' => __('Do Not Mail', 'zerobounce-email-validator'),
                ],
                'helper' => '',
                'supplimental' => __('Select on which status an Email passes validation on forms. We recommend "Valid", "Catch-All" and "Unknown" only.', 'zerobounce-email-validator'),
                'default' => ['']
            ],
            [
                'uid' => 'zerobounce_settings_block_free_email',
                'label' => __('Free email services', 'zerobounce-email-validator'),
                'section' => 'zerobounce_email_validator_settings_validation_section',
                'type' => 'checkbox',
                'placeholder' => '',
                'options' => [
                    'valid' => __('Block free email services', 'zerobounce-email-validator'),
                ],
                'helper' => '',
                'supplimental' => __('Check to block email addresses from free mail services (e.g.: Yahoo, Gmail, Outlook, etc).', 'zerobounce-email-validator'),
                'default' => ['']
            ],
        ];

        foreach ($fields as $field) {
            add_settings_field($field['uid'], $field['label'], [$this, 'settings_render_fields'], 'zerobounce_email_validator_settings', $field['section'], $field);
            register_setting('zerobounce_email_validator_settings', $field['uid']);
        }
    }

    public function settings_render_sections($arguments)
    {
        switch ($arguments['id']) {
            case 'zerobounce_email_validator_settings_general_section':
                echo __('These are the general settings that you must configure, like your ZeroBounce API key.', 'zerobounce-email-validator');
                break;
            case 'zerobounce_email_validator_settings_validation_section':
                echo __('These are your email validation rules, where you can selectively allow or disallow emails based on status.', 'zerobounce-email-validator');
                break;
        }
    }

    public function settings_render_fields($arguments)
    {
        $value = get_option($arguments['uid']);

        if (!$value) {
            $value = isset($arguments['default']) ? $arguments['default'] : "";
        }

        switch ($arguments['type']) {
            case 'text':
            case 'password':
            case 'number':
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
                break;
            case 'textarea':
                printf('<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value);
                break;
            case 'select':
            case 'multiselect':
                if (!empty($arguments['options']) && is_array($arguments['options'])) {
                    $attributes = '';
                    $options_markup = '';
                    foreach ($arguments['options'] as $key => $label) {
                        $options_markup .= sprintf('<option value="%s" %s>%s</option>', $key, selected($value[array_search($key, $value, true)], $key, false), $label);
                    }
                    if ($arguments['type'] === 'multiselect') {
                        $attributes = ' multiple="multiple" ';
                    }
                    printf('<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup);
                }
                break;
            case 'radio':
            case 'checkbox':
                if (!empty($arguments['options']) && is_array($arguments['options'])) {
                    $options_markup = '';
                    $iterator = 0;
                    foreach ($arguments['options'] as $key => $label) {
                        $iterator++;
                        $options_markup .= sprintf(
                            '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
                            $arguments['uid'],
                            $arguments['type'],
                            $key,
                            checked($value[@array_search($key, $value, true)] ?? false, $key, false),
                            $label,
                            $iterator
                        );
                    }
                    printf('<fieldset>%s</fieldset>', $options_markup);
                }
                break;
        }

        if ($helper = $arguments['helper']) {
            printf('<span class="helper"> %s</span>', $helper);
        }

        if ($supplimental = $arguments['supplimental']) {
            printf('<p class="description">%s</p>', $supplimental);
        }
    }

    public function pre_update_option_zb_api_callback($option_new_value, $option_old_value)
    {
        $value = $this->sanitize_and_escape($option_new_value);

        if ($option_old_value === $value) {
            return $value;
        }


        if ($this->api->validate_key($value) === false) {
            add_settings_error('zerobounce_settings_api_key', 'error', __('Error Code 1: Sorry, your API Key seems invalid! Please double check.', 'zerobounce-email-validator'), 'error');
            return null;
        }

        return $value;
    }

    public function added_option_callback($option_name, $option_value)
    {
        if ($option_name === 'zerobounce_settings_api_key') {
            if ($this->api->validate_key($this->sanitize_and_escape($value)) === false) {
                add_settings_error('zerobounce_settings_api_key', 'error', __('Error Code 2: Sorry, your API Key seems invalid! Please double check.', 'zerobounce-email-validator'), 'error');
            }
        }
    }

    public function updated_option_callback($option, $old_value, $value)
    {
        if ($option === 'zerobounce_settings_api_key') {
            if ($this->api->validate_key($this->sanitize_and_escape($value)) === false) {
                add_settings_error('zerobounce_settings_api_key', 'error', __('Error Code 3: Sorry, your API Key seems invalid! Please double check.', 'zerobounce-email-validator'), 'error');
            }
        }
    }

    public function current_credits()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-credits-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        $credits = $this->api->get_credits_info();

        if ($credits) {
            wp_send_json_success($credits, 200, 0);
        }

        wp_send_json_success(0, 200, 0);
    }

    /**
     * validate_email_test
     *
     * @return void
     */
    public function validate_email_test()
    {
        global $wpdb;

        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-validation-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        $email = sanitize_email($_POST['email']);

        if (empty($email)) {
            wp_send_json_error(['reason' => esc_html__('You have entered an invalid email address.')], 400, 0);
            exit();
        }

        $validation = $this->api->validate_email($email);

        if (!isset($validation) || $validation === null || empty($validation)) {
            wp_send_json_error(['reason' => esc_html__('Could not validate this email. Internal error occured.')], 400, 0);
            exit();
        }

        $wpdb->insert(
            $wpdb->prefix . 'zerobounce_validation_logs',
            [
                'source' => 'Administrator Test',
                'email' => $email,
                'status' => $validation['status'],
                'sub_status' => $validation['sub_status'],
                'ip_address' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']),
                'result' => serialize($validation),
                'date_time' => current_time('mysql')
            ]
        );

        wp_send_json_success($validation, 200, 0);
    }


    /**
     * @return void
     */
    public function validate_bulk_test()
    {
        global $wpdb;

        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-bulk-test-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        wp_send_json_success($validation, 200, 0);

    }

    public function validation_logs()
    {
        global $wpdb;

        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-validation-charts-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        $data = [];

        $firstDay = new \DateTime('first day of this month 00:00:00');
        $lastDay = new \DateTime('last day of this month 00:00:00');

        $dates = new \DatePeriod(
            $firstDay,
            new \DateInterval('P1D'),
            $lastDay
        );

        foreach ($dates as $date) {
            $logs = $wpdb->get_results("SELECT DATE_FORMAT(date_time, '%Y-%m-%d') as date,
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'valid' then 1 else 0 end) AS valid,
                SUM(CASE WHEN status = 'invalid' then 1 else 0 end) AS invalid,
                SUM(CASE WHEN status = 'catch-all' then 1 else 0 end) AS catchall,
                SUM(CASE WHEN status = 'unknown' then 1 else 0 end) AS unknown,
                SUM(CASE WHEN status = 'spamtrap' then 1 else 0 end) AS spamtrap,
                SUM(CASE WHEN status = 'abuse' then 1 else 0 end) AS abuse,
                SUM(CASE WHEN status = 'do_not_mail' then 1 else 0 end) AS do_not_mail,
                SUM(CASE WHEN status = 'no-free-service' then 1 else 0 end) AS no_free_service
            FROM " . $wpdb->prefix . "zerobounce_validation_logs
            WHERE date_time BETWEEN '" . $date->format("Y-m-d") . " 00:00:00' AND '" . $date->format("Y-m-d") . " 23:59:59'
            GROUP BY date");

            $data['count'][] = $logs ? ['date' => $date->format("Y-m-d"), 'total' => (int)$logs[0]->total, 'abuse' => (int)$logs[0]->abuse, 'catchall' => (int)$logs[0]->catchall, 'do_not_mail' => (int)$logs[0]->do_not_mail, 'invalid' => (int)$logs[0]->invalid, 'spamtrap' => (int)$logs[0]->spamtrap, 'unknown' => (int)$logs[0]->unknown, 'valid' => (int)$logs[0]->valid, 'no_free_service' => (int)$logs[0]->no_free_service] : ['date' => $date->format("Y-m-d"), 'total' => 0, 'abuse' => 0, 'catchall' => 0, 'do_not_mail' => 0, 'invalid' => 0, 'spamtrap' => 0, 'unknown' => 0, 'valid' => 0, 'no_free_service' => 0];
        }

        wp_send_json_success($data, 200, 0);
    }

//    public function credit_usage_logs()
//    {
//        global $wpdb;
//
//        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-credits-charts-nonce')) {
//            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
//            exit();
//        }
//
//        $data = [];
//
//        $firstDay = new \DateTime('first day of this month 00:00:00');
//        $lastDay = new \DateTime('last day of this month 00:00:00');
//
//        $dates = new \DatePeriod(
//            $firstDay,
//            new \DateInterval('P1D'),
//            $lastDay
//        );
//
//        foreach ($dates as $date) {
//            $logs = $wpdb->get_results("SELECT credits_used FROM " . $wpdb->prefix . "zerobounce_credit_usage_logs WHERE date = '" . $date->format("Y-m-d") . "'");
//
//            $data['count'][] = $logs ? ['date' => $date->format("Y-m-d"), 'credits_used' => (int)$logs[0]->credits_used] : ['date' => $date->format("Y-m-d"), 'credits_used' => 0];
//        }
//
//        wp_send_json_success($data, 200, 0);
//    }

    public function validation_full_logs()
    {
        global $wpdb;

        if (!wp_verify_nonce($_GET['nonce'], 'zerobounce-full-logs-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        $logs = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "zerobounce_validation_logs");

        if ($logs) {
            $final_logs = [];

            foreach ($logs as $k => $v) {
                $final_logs[$k]['id'] = $v->id;

                switch ($v->source) {
                    case 'wpforms':
                        $wpforms_preview = get_site_url(null, '/?wpforms_form_preview=' . $v->form_id, null);

                        $final_logs[$k]['source'] = "<a href=\"" . $wpforms_preview . "\" target=\"_blank\">WP Form</a>";
                        break;

                    case 'ninjaforms':
                        $ninjaforms_preview = get_site_url(null, '/?nf_preview_form=' . $v->form_id, null);

                        $final_logs[$k]['source'] = "<a href=\"" . $ninjaforms_preview . "\" target=\"_blank\">Ninja Form</a>";
                        break;

                    case 'cf7forms':
                        $cf7forms_preview = get_site_url(null, '/wp-admin/admin.php?page=wpcf7&post=' . $v->form_id . '&action=edit', null);

                        $final_logs[$k]['source'] = "<a href=\"" . $cf7forms_preview . "\" target=\"_blank\">Contact Form 7</a>";
                        break;

                    case 'formidableforms':
                        $formidableforms_preview = get_site_url(null, '/wp-admin/admin.php?page=formidable&frm_action=edit&id=' . $v->form_id, null);

                        $final_logs[$k]['source'] = "<a href=\"" . $formidableforms_preview . "\" target=\"_blank\">Formidable Form</a>";
                        break;

                    case 'woocommerceforms':
                        $final_logs[$k]['source'] = "WooCommerce";
                        break;

                    case 'wordpressisemail':
                        $final_logs[$k]['source'] = "WordPress Comment";
                        break;

                    case 'wordpressregister':
                        $final_logs[$k]['source'] = "WordPress Register";
                        break;

                    case 'mc4wp_mailchimp':
                        $mc4wp_preview = get_site_url(null, '/wp-admin/admin.php?page=mailchimp-for-wp-forms&view=edit-form&form_id=' . $v->form_id, null);

                        $final_logs[$k]['source'] = "<a href=\"" . $mc4wp_preview . "\" target=\"_blank\">MC4WP: Mailchimp for WordPress</a>";
                        break;

                    case 'gravity_forms':
                        $gravity_forms_preview = get_site_url(null, '/wp-admin/admin.php?page=gf_edit_forms&id=' . $v->form_id, null);

                        $final_logs[$k]['source'] = "<a href=\"" . $gravity_forms_preview . "\" target=\"_blank\">Gravity Forms</a>";
                        break;

                    default:
                        $final_logs[$k]['source'] = $v->source ? $v->source : "Unknown";
                        break;
                }

                $final_logs[$k]['email'] = $v->email;

                switch ($v->status) {
                    case 'valid':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #3ecf8f !important;\">" . __('Valid', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'invalid':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #e65849 !important;\">" . __('Invalid', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'no-free-service':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #ff5f15 !important;\">" . __('Block Free Services', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'catch-all':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #ff978a !important;\">" . __('Catch-All', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'unknown':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #ffbe43 !important;\">" . __('Unknown', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'spamtrap':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #dcdcdc !important;\">" . __('Spamtrap', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'abuse':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #014b70 !important;\">" . __('Abuse', 'zerobounce-email-validator') . "</span>";
                        break;
                    case 'do_not_mail':
                        $final_logs[$k]['status'] = "<span class=\"badge\" style=\"color: #fff!important;background-color: #1e8bc2 !important;\">" . __('Do Not Mail', 'zerobounce-email-validator') . "</span>";
                        break;
                    default:
                        $final_logs[$k]['status'] = $v->status ? $v->status : "-";
                        break;
                }

                switch ($v->sub_status) {
                    case 'antispam_system':
                        $final_logs[$k]['sub_status'] = __("Antispam", 'zerobounce-email-validator');
                        break;
                    case 'greylisted':
                        $final_logs[$k]['sub_status'] = __("Greylisted", 'zerobounce-email-validator');
                        break;
                    case 'mail_server_temporary_error':
                        $final_logs[$k]['sub_status'] = __("Server Temporary Error", 'zerobounce-email-validator');
                        break;
                    case 'forcible_disconnect':
                        $final_logs[$k]['sub_status'] = __("Forcible Disconnect", 'zerobounce-email-validator');
                        break;
                    case 'mail_server_did_not_respond':
                        $final_logs[$k]['sub_status'] = __("Server Non-Responsive", 'zerobounce-email-validator');
                        break;
                    case 'timeout_exceeded':
                        $final_logs[$k]['sub_status'] = __("Timeout Exceeded", 'zerobounce-email-validator');
                        break;
                    case 'failed_smtp_connection':
                        $final_logs[$k]['sub_status'] = __("SMPT Failed", 'zerobounce-email-validator');
                        break;
                    case 'mailbox_quota_exceeded':
                        $final_logs[$k]['sub_status'] = __("Quota Exceeded", 'zerobounce-email-validator');
                        break;
                    case 'exception_occurred':
                        $final_logs[$k]['sub_status'] = __("Exception Occured", 'zerobounce-email-validator');
                        break;
                    case 'possible_trap':
                        $final_logs[$k]['sub_status'] = __("Possible Trap", 'zerobounce-email-validator');
                        break;
                    case 'role_based':
                        $final_logs[$k]['sub_status'] = __("Role Based", 'zerobounce-email-validator');
                        break;
                    case 'global_suppression':
                        $final_logs[$k]['sub_status'] = __("Global Suppression", 'zerobounce-email-validator');
                        break;
                    case 'mailbox_not_found':
                        $final_logs[$k]['sub_status'] = __("Mailbox Not Found", 'zerobounce-email-validator');
                        break;
                    case 'no_dns_entries':
                        $final_logs[$k]['sub_status'] = __("No DNS Entries", 'zerobounce-email-validator');
                        break;
                    case 'failed_syntax_check':
                        $final_logs[$k]['sub_status'] = __("Failed Syntax", 'zerobounce-email-validator');
                        break;
                    case 'possible_typo':
                        $final_logs[$k]['sub_status'] = __("Possible Typo", 'zerobounce-email-validator');
                        break;
                    case 'unroutable_ip_address':
                        $final_logs[$k]['sub_status'] = __("IP Non-Routable", 'zerobounce-email-validator');
                        break;
                    case 'leading_period_removed':
                        $final_logs[$k]['sub_status'] = __("Leading Period", 'zerobounce-email-validator');
                        break;
                    case 'does_not_accept_mail':
                        $final_logs[$k]['sub_status'] = __("Not Accepting Mail", 'zerobounce-email-validator');
                        break;
                    case 'alias_address':
                        $final_logs[$k]['sub_status'] = __("Alias Address", 'zerobounce-email-validator');
                        break;
                    case 'role_based_catch_all':
                        $final_logs[$k]['sub_status'] = __("Role Based Catch-All", 'zerobounce-email-validator');
                        break;
                    case 'disposable':
                        $final_logs[$k]['sub_status'] = __("Disposable", 'zerobounce-email-validator');
                        break;
                    case 'toxic':
                        $final_logs[$k]['sub_status'] = __("Toxic", 'zerobounce-email-validator');
                        break;

                    default:
                        $final_logs[$k]['sub_status'] = $v->sub_status ? $v->sub_status : "-";
                        break;
                }

                $final_logs[$k]['ip_address'] = $v->ip_address;
                $final_logs[$k]['date_time'] = $v->date_time;
            }

            wp_send_json_success($final_logs, 200, 0);
        }


        wp_send_json_success([], 200, 0);
    }

    public function validation_single_log()
    {
        global $wpdb;

        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-single-log-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            exit();
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $log = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "zerobounce_validation_logs WHERE id=" . $id . "");

        if ($log) {
            $result = unserialize($log[0]->result);

            wp_send_json_success($result, 200, 0);
        }

        wp_send_json_success([], 200, 0);
    }

    private function sanitize_and_escape($input)
    {
        $sanitized = wp_strip_all_tags($input, true);

        $escaped = esc_html($sanitized);

        return $escaped;
    }

    /**
     * @param $table_name
     * @return void
     */
    private function create_file_bulk_validation_table($table_name)
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            file_name varchar(255) NOT NULL,
            file_id varchar(255) NOT NULL,
            validation_status varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * @param $fileInfo
     * @return void
     * @throws Exception
     */
    private function set_bulk_file_validation_info($fileInfo): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . '_file_bulk_validation';
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));

        if ($table_exists !== $table_name) {
            $this->create_file_bulk_validation_table($table_name);
        }

        $file_id = $fileInfo->file_id;
        $file_name = $fileInfo->file_name;
        $validation_status = $fileInfo->complete_percentage ?? '0%';

        $existing_file = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE file_id = %s", $file_id));

        if ($existing_file) {
            $result = $wpdb->update(
                $table_name,
                [
                    'validation_status' => $validation_status,
                    'created_at' => current_time('mysql'),
                ],
                ['file_id' => $file_id],
                ['%s', '%s'],
                ['%s']
            );

            if ($result === false) {
                throw new Exception('Error updating file validation info in the database.');
            } elseif ($result === 0) {
                throw new Exception('No rows updated. It may indicate the same data was submitted.');
            }

        } else {
            $result = $wpdb->insert(
                $table_name,
                [
                    'file_id' => $file_id,
                    'file_name' => $file_name,
                    'validation_status' => $validation_status,
                    'created_at' => current_time('mysql'),
                ],
                ['%s', '%s', '%s', '%s']
            );

            if ($result === false) {
                throw new Exception('Error inserting file validation info into the database.');
            }
        }
    }

    /**
     * @throws Exception
     */
    public function validate_batch()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-batch-validation-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400, 0);
            wp_die();
        }

        if (isset($_FILES["csvUpload"]["name"]) && !empty($_FILES["csvUpload"]["name"])) {

            $response = $this->api->batch_file_validation($_FILES["csvUpload"]);
            if (!$response->success) {
                wp_send_json(["success" => $response->success, "error" => $response->error_message, "type" => "file"]);
                wp_die();
            }

            $this->set_bulk_file_validation_info($response);
            wp_send_json(["success" => $response->success, "type" => "file"]);
            wp_die();
        } else if (!empty($_POST["manual-upload"])) {
            $response = $this->api->batch_email_validation($_POST["manual-upload"]);
            $this->add_results_to_logs($response);
            $response['type'] = "manual";
            wp_send_json($response);
            wp_die();
        }
        wp_send_json_error(['error' => 'No input provided!']);
        wp_die();
    }

    public function get_uploaded_file_data()
    {
        global $wpdb;

        if (!wp_verify_nonce($_POST['nonce'], 'zerobounce-get-files-info-nonce')) {
            wp_send_json_error(['reason' => esc_html__('Request is invalid. Please refresh the page and try again.')], 400);
            exit();
        }

        $table_name = $wpdb->prefix . '_file_bulk_validation';
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));


        if ($table_exists !== $table_name) {
            wp_send_json_error(['message' => 'Table does not exist.']);
            wp_die();
        }

        $page = $_POST['page'] ?: 1;
        $recordsPerPage = 10;
        $offset = ($page - 1) * $recordsPerPage;

        try {
            $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $total_pages = ceil($total_records / $recordsPerPage);

            $query = $wpdb->prepare("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d", $recordsPerPage, $offset);
            $results = $wpdb->get_results($query, ARRAY_A);

            if (empty($results)) {
                wp_send_json_error(['message' => 'No data found.']);
                wp_die();
            }

            foreach ($results as &$file) {
                $file_id = $file["file_id"];

                if ($file["validation_status"] === '100%') {
                    continue;
                }

                $api_response = $this->api->batch_file_status($file_id);

                if (is_wp_error($api_response)) {
                    continue;
                }

                if (isset($api_response->complete_percentage)) {
                    if ($file['validation_status'] !== $api_response->complete_percentage) {
                        $wpdb->update(
                            $table_name,
                            ['validation_status' => $api_response->complete_percentage],
                            ['file_id' => $file_id],
                            ['%s'],
                            ['%s']
                        );
                        $file['validation_status'] = $api_response->complete_percentage;
                    }
                }
            }

            wp_send_json([
                'data' => $results,
                'pagination' => [
                    'total_records' => $total_records,
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'records_per_page' => $recordsPerPage,
                ],
                'success' => true
            ]);
            wp_die();
        } catch (Exception $e) {
            wp_send_json([
                'success' => false,
                'message' => 'An error occurred while retrieving or updating the data.',
                'error' => $e->getMessage(),
            ]);
            wp_die();
        }
    }

    public function validated_emails_download()
    {

        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'zerobounce-download-validated-file-nonce')) {
            wp_die('Invalid nonce.');
        }

        if (!isset($_GET['file_id'])) {
            wp_die('Invalid file ID.');
        }

        $file_data = $this->api->file_results_download($_GET['file_id']);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="downloaded_file.csv"');
        header('Content-Length: ' . strlen($file_data));

        echo $file_data;

        wp_die();
    }

    private function add_results_to_logs($response)
    {
        global $wpdb;

        foreach ($response['email_batch'] as $validation) {
            $wpdb->insert(
                $wpdb->prefix . 'zerobounce_validation_logs',
                [
                    'source' => 'Administrator Test',
                    'email' => $validation['address'],
                    'status' => $validation['status'],
                    'sub_status' => $validation['sub_status'],
                    'ip_address' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']),
                    'result' => serialize($validation),
                    'date_time' => current_time('mysql')
                ]
            );
        }
    }
}
