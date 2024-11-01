<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/public
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator_Form_Public
{
    /**
     * The ZeroBounce API class used for validation
     *
     * @since    1.0.0
     * @access   private
     * @var      Zerobounce_Email_Validator_API
     */
    private Zerobounce_Email_Validator_API $api;

    /**
     * @var string
     */
    private string $source;

    /**
     * @var string
     */
    private string $formId;

    /**
     * @var array
     */
    private array $validationPass;

    /**
     * @param $source
     * @param $formId
     * @return void
     */
    public function __construct($source, $formId)
    {
        $this->source = $source;
        $this->formId = $formId;
        $this->validationPass = get_option('zerobounce_settings_validation_pass');
        $this->api = new Zerobounce_Email_Validator_API(get_option('zerobounce_settings_api_key') ?? '', get_option('zerobounce_settings_api_timeout') ?? 50);
    }

    /**
     * @param string $raw_email
     */
    private function validate(string $raw_email)
    {
        $email = sanitize_email($raw_email);
        return $this->api->validate_email($email);
    }

    /**
     * @return string
     */
    public function set_error_message(string $didYouMean = null): string
    {
        $custom_error = get_option('zerobounce_settings_error_message');
        $typoSettings = get_option('zerobounce_settings_did_you_mean');

        $error_message = __('Sorry, upon checking we cannot accept this email address.', 'zerobounce-email-validator');
        if (isset($custom_error) && $custom_error) {
            $error_message = $custom_error;
        }

        if (!is_null($didYouMean) && $typoSettings) {
            $typo_error_message = get_option('zerobounce_settings_did_you_mean_error');
            $error_message = isset($typo_error_message) && $typo_error_message ? "$typo_error_message $didYouMean" : "Did you mean: $didYouMean";
        }

        return $error_message;
    }

    /**
     * @param $email
     * @return array|null
     */
    public function prep_validation_info($email): ?array
    {
        global $wpdb;

        $validationInfo = $this->validate($email);

        $blockedFreeEmail = get_option('zerobounce_settings_block_free_email');

        if ($validationInfo != null) {
            $wpdb->insert(
                $wpdb->prefix . 'zerobounce_validation_logs',
                [
                    'source' => $this->source,
                    'form_id' => $this->formId,
                    'email' => $email,
                    'status' => $validationInfo['status'] === 'valid' && $validationInfo['free_email'] && $blockedFreeEmail ? 'no-free-service' : $validationInfo['status'],
                    'sub_status' => $validationInfo['status'] === 'valid' && $validationInfo['free_email'] && $blockedFreeEmail ? 'Block Free Email Providers' : $validationInfo['sub_status'],
                    'ip_address' => ($_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR']),
                    'result' => serialize($validationInfo),
                    'date_time' => current_time('mysql')
                ]
            );
        }

        return $validationInfo;
    }

    /**
     * @param ?array $validationInfo
     * @param callable $callback
     * @param $args
     * @return array|null
     */
    public function setup_form_validation(?array $validationInfo, callable $callback, $args): ?array
    {
        if (!is_null($validationInfo)) {
            $blockedFreeEmail = get_option('zerobounce_settings_block_free_email');

            if (!in_array($validationInfo['status'], $this->validationPass) || ($blockedFreeEmail && $validationInfo['free_email'])) {
                call_user_func($callback, $args);
                return $args;
            }
        }
        return null;
    }
}