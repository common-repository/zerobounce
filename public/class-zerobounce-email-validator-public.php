<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/public
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator_Public
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
     * @param class $api New instance for Zerobounce_Email_Validator_API class
     * @param string $plugin_name The name of the plugin.
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
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zerobounce-email-validator-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zerobounce-email-validator-public.js', array('jquery'), $this->version, false);
    }


    private function save_validation_info_to_db($email, $id, $source)
    {
        global $wpdb;
        $validation = $this->api->validate_email($email);

        if ($validation != null) {
            $wpdb->insert(
                $wpdb->prefix . 'zerobounce_validation_logs',
                [
                    'source' => $source,
                    'form_id' => $id,
                    'email' => $email,
                    'status' => $validation['status'],
                    'sub_status' => $validation['sub_status'],
                    'ip_address' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']),
                    'result' => serialize($validation),
                    'date_time' => current_time('mysql')
                ]
            );

            $validation_pass = get_option('zerobounce_settings_validation_pass');

            $custom_error = get_option('zerobounce_settings_error_message');
            $error_message = __('Sorry, upon checking we cannot accept this email address.', 'zerobounce-email-validator');
            if (isset($custom_error) && $custom_error) {
                $error_message = $custom_error;
            }
            return ['message' => $error_message, 'validation_pass' => $validation_pass, 'validation' => $validation];
        }

        return null;
    }

    /**
     * WordPress Form Validator Hook
     * @param $fields
     * @param $entry
     * @param $form_data
     * @return mixed
     */
    public function wpforms_validator($fields, $entry, $form_data)
    {
        foreach ($fields as $field_id => $field) {
            if (isset($field['type']) && $field['type'] === 'email' && !empty($field['value'])) {
                $wpForm = new Zerobounce_Email_Validator_Form_Public('wpforms', '');
                $validationInfo = $wpForm->prep_validation_info($field['value']);
                $message = $wpForm->set_error_message($validationInfo['did_you_mean']);
                $wpForm->setup_form_validation($validationInfo, function () {
                    $args = func_get_args();
                    $form_data = $args[0]['form_data'];
                    $message = $args[0]['message'];
                    $field_id = $args[0]['field_id'];
                    wpforms()->process->errors[$form_data['id']][$field_id] = esc_html__($message);
                }, ['form_data' => $form_data, 'field_id' => $field_id, 'message' => $message]);
            }
        }
        return $fields;
    }

    /**
     * Ninja Forms Validator Hook
     * @param $form_data
     * @return mixed
     */
    public function ninjaforms_validator($form_data)
    {
        foreach ($form_data['fields'] as $key => $field) {
            $value = $field['value'];
            if (!empty($value) && is_string($value) && preg_match('/@.+\./', $value) && !str_contains($value, "\n") && !str_contains($value, '\n')) {
                $ninjaForm = new Zerobounce_Email_Validator_Form_Public('ninjaforms', $form_data['id']);
                $validationInfo = $ninjaForm->prep_validation_info($value);
                $message = $ninjaForm->set_error_message($validationInfo['did_you_mean']);
                $ninjaForm->setup_form_validation($validationInfo, function () {
                    $args = func_get_args();
                    $form_data = &$args[0]['form_data'];
                    $field = $args[0]['field'];
                    $message = $args[0]['message'];
                    $form_data['errors']['fields'][$field['id']] = esc_html__($message);
                }, ['form_data' => &$form_data, 'field' => &$field, 'message' => &$message]);
            }
        }

        return $form_data;
    }

    /**
     * Contact Forms 7 Validator Hook
     * @param $result
     * @param $tag
     * @return mixed
     */
    public function contact_form_7_validator($result, $tag)
    {
        $tag = new WPCF7_FormTag($tag);
        if ('email' == $tag->type || 'email*' == $tag->type) {
            $wpcf7Form = new Zerobounce_Email_Validator_Form_Public('cf7forms', '');
            $validationInfo = $wpcf7Form->prep_validation_info($_POST[$tag->name]);
            $message = $wpcf7Form->set_error_message($validationInfo['did_you_mean']);
            $wpcf7Form->setup_form_validation($validationInfo, function () {
                $args = func_get_args();
                extract($args[0]);
                $result->invalidate($tag, esc_html__($message));
            }, ['message' => $message, 'tag' => $tag, 'result' => &$result]);
        }
        return $result;
    }


    public function contact_form_validator($valid) {
        global $cntctfrm_error_message;
        if (!$valid) {
            return;
        }

        if (!(empty($_POST['cntctfrm_contact_email'])) && ($_POST['cntctfrm_contact_email'] != '')) {
            $email = sanitize_email($_POST['cntctfrm_contact_email']);
            $cntctForm = new Zerobounce_Email_Validator_Form_Public('cntctfrm_contact_email', '');
            $validationInfo = $cntctForm->prep_validation_info($email);
            $message = $cntctForm->set_error_message($validationInfo['did_you_mean']);
            $cntctForm->setup_form_validation($validationInfo, function () {
                $args = func_get_args();
                $message = $args[0]['message'];
                $cnctFrmErrorMsg = &$args[0]['cntctfrm_error_message'];
                $cnctFrmErrorMsg['error_email'] = esc_html__($message);
            }, ['message' => $message, 'cntctfrm_error_message' => &$cntctfrm_error_message]);
        }
    }



    /**
     * Formidable Forms Validator Hook
     * @param $errors
     * @param $posted_values
     * @return mixed
     */
    public function formidableforms_validator($errors, $posted_values)
    {
        foreach ($posted_values['item_meta'] as $key => $value) {
            if (!empty($value) && is_string($value) && preg_match("/^\S+@\S+\.\S+$/", $value)) {
                $formidableForm = new Zerobounce_Email_Validator_Form_Public('formidableforms', $posted_values['form_id']);
                $validationInfo = $formidableForm->prep_validation_info($value);
                $message = $formidableForm->set_error_message($validationInfo['did_you_mean']);
                $formidableForm->setup_form_validation($validationInfo, function () {
                    $args = func_get_args();
                    $message = $args[0]['message'];
                    $errors = &$args[0]['errors'];
                    $errors['ct_error'] = esc_html__($message);
                }, ['message' => $message, 'errors' => &$errors]);
            }
        }
        return $errors;
    }

    /**
     * Woocommerce Checkout Form Validator Hook - shortcode blocks
     * @param $fields
     * @param $errors
     * @return void
     */
    public function woocommerce_validator($fields, $errors)
    {
        $woocommerceForm = new Zerobounce_Email_Validator_Form_Public('woocommerceforms', '');
        $validationInfo = null;

        if (!empty($fields['billing_email'])) {
            $validationInfo = $woocommerceForm->prep_validation_info($fields['billing_email']);
        }

        if (!empty($fields['shipping_email'])) {
            $validationInfo = $woocommerceForm->prep_validation_info($fields['shipping_email']);
        }

        $message = $woocommerceForm->set_error_message($validationInfo['did_you_mean']);
        $woocommerceForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $message = $args[0]['message'];
            $errors = &$args[0]['errors'];
            $errors->add('validation', esc_html__($message));
        }, ['message' => $message, 'errors' => &$errors]);
    }

    /**
     * WordPress Comment Filter - add hook
     * @return void
     */
    public function apply_is_email_validator()
    {
        add_filter('is_email', [$this, 'wordpress_is_email_validator'], 10, 3);
    }

    /**
     * WordPress Comment Filter - remove hook
     * @return void
     */
    public function remove_is_email_validator()
    {
        remove_filter('is_email', [$this, 'wordpress_is_email_validator'], 10, 3);
    }

    /**
     * WordPress Comments Form Validator Hook
     * @param $is_email
     * @param $email
     * @param $context
     * @return false|mixed
     */
    public function wordpress_is_email_validator($is_email, $email, $context)
    {
        if (!strlen($email) || strlen($email) < 3) {
            return false;
        }

        $wpForm = new Zerobounce_Email_Validator_Form_Public('wordpressisemail', get_the_ID());
        $validationInfo = $wpForm->prep_validation_info($email);
        $wpForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $is_email = &$args[0]['is_email'];
            $is_email = false;
        }, ['is_email' => &$is_email]);

        return $is_email;
    }

    /**
     * WordPress Registration Form Validator Hook
     * @param $errors
     * @param $sanitized_user_login
     * @param $email
     * @return mixed
     */
    public function wordpress_registration_validator($errors, $sanitized_user_login, $email)
    {
        if (email_exists($email)) {
            return $errors;
        }

        $wprForm = new Zerobounce_Email_Validator_Form_Public('wordpressregister', '');
        $validationInfo = $wprForm->prep_validation_info($email);
        $message = $wprForm->set_error_message($validationInfo['did_you_mean']);
        $wprForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $message = $args[0]['message'];
            $errors = &$args[0]['errors'];
            $errors->add('invalid_email', esc_html__($message));
        }, ['message' => $message, 'errors' => &$errors]);

        return $errors;
    }

    /**
     * WordPress Multisite Registration Form Validator Hook
     * @param $result
     * @return mixed
     */
    public function wordpress_multisite_registration_validator($result)
    {
        $email = $result['user_email'];
        if (!strlen($email) || strlen($email) < 3) {
            return $result;
        }

        $wprForm = new Zerobounce_Email_Validator_Form_Public('wordpressmultisiteregister', '');
        $validationInfo = $wprForm->prep_validation_info($email);
        $message = $wprForm->set_error_message($validationInfo['did_you_mean']);
        $wprForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $message = $args[0]['message'];
            $result = &$args[0]['result'];
            $result['errors']->add('user_email', esc_html__($message));
        }, ['message' => $message, 'result' => &$result]);

        return $result;
    }


    /**
     * Mailchimp Form Validator Hook
     * @param $errors
     * @param MC4WP_Form $form
     * @return mixed
     */
    public function mc4wp_mailchimp_validator($errors, MC4WP_Form $form)
    {
        $data = $form->get_data();
        $email = strtolower($data['EMAIL']);

        $mc4Form = new Zerobounce_Email_Validator_Form_Public('mc4wp_mailchimp', $form->ID);
        $validationInfo = $mc4Form->prep_validation_info($email);
        $mc4Form->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $errors = &$args[0]['errors'];
            $errors[] = 'invalid_email';
        }, ['errors' => &$errors]);
        return $errors;
    }

    /**
     * Mailchimp add custom message to email validation - hook
     * @param $messages
     * @return mixed
     */
    public function mc4wp_mailchimp_error_message($messages)
    {
        $mc4Form = new Zerobounce_Email_Validator_Form_Public('mc4wp_mailchimp', '');
        $messages['invalid_email'] = $mc4Form->set_error_message();
        return $messages;
    }

    private function gravity_form_validation($id, $email, &$result)
    {
        $gravityForm = new Zerobounce_Email_Validator_Form_Public('gravity_forms', $id);
        $validationInfo = $gravityForm->prep_validation_info($email);
        $message = $gravityForm->set_error_message($validationInfo['did_you_mean']);
        $gravityForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $message = $args[0]['message'];
            $result = &$args[0]['result'];
            $result['is_valid'] = false;
            $result['message'] = esc_html__($message);
        }, ['message' => $message, 'result' => &$result]);
    }

    /**
     * Gravity Forms Validator Hook
     * @param $result
     * @param $value
     * @param $form
     * @param $field
     * @return mixed
     */
    public function gravity_forms_validator($result, $value, $form, $field)
    {
        if ($field->type == 'email' && $field->isRequired == 1) {
            if (is_array($value) && count($value) !== 0) {
                foreach ($value as $k => $v) {
                    $this->gravity_form_validation($form['id'], $v, $result);
                }
            } else {
                $this->gravity_form_validation($form['id'], $value, $result);
            }
        }

        return $result;
    }

    /**
     * Fluent Forms Validator Hook
     * @param $errorMessage
     * @param $field
     * @param $formData
     * @param $fields
     * @param $form
     * @return array|mixed
     */
    public function fluent_forms_validator($errorMessage, $field, $formData, $fields, $form)
    {
        $fieldName = $field['name'];
        if (empty($formData[$fieldName])) {
            return $errorMessage;
        }

        $fluentForm = new Zerobounce_Email_Validator_Form_Public('fluent_forms', $form->id);
        $validationInfo = $fluentForm->prep_validation_info($formData[$fieldName]);
        $message = $fluentForm->set_error_message($validationInfo['did_you_mean']);
        $fluentForm->setup_form_validation($validationInfo, function () {
            $args = func_get_args();
            $message = $args[0]['message'];
            $errorMessage = &$args[0]['errorMessage'];
            $errorMessage = [$message];
        }, ['message' => $message, 'errorMessage' => &$errorMessage]);

        return $errorMessage;
    }

    /**
     * @param $valid
     * @param $email
     * @param $form_id
     * @param $field_id
     * @return mixed
     */
    public function ws_forms_validator($valid, $email, $form_id, $field_id)
    {
        if (!empty($email)) {
            $wsform = new Zerobounce_Email_Validator_Form_Public('wsf_forms', $form_id);
            $validationInfo = $wsform->prep_validation_info($email);
            $message = $wsform->set_error_message($validationInfo['did_you_mean']);
            $wsform->setup_form_validation($validationInfo, function () {
                $args = func_get_args();
                $message = $args[0]['message'];
                $valid = &$args[0]['valid'];
                $valid = esc_html($message);
            }, ['message' => $message, 'valid' => &$valid]);
        }
        return $valid;
    }

    /**
     * @param $result
     * @return mixed
     */
    public function mailster_email_validator($result)
    {
        if (isset($result['email'])) {
            $mailsterForm = new Zerobounce_Email_Validator_Form_Public('mailster', '');
            $validationInfo = $mailsterForm->prep_validation_info($result['email']);
            $message = $mailsterForm->set_error_message($validationInfo['did_you_mean']);
            $mailsterForm->setup_form_validation($validationInfo, function () {
                $args = func_get_args();
                $message = $args[0]['message'];
                $result = &$args[0]['result'];
                $result = new WP_Error('email', $message);
            }, ['message' => $message, 'result' => &$result]);
        }

        return $result;
    }

    /**
     * @param $submit_errors
     * @param $form_id
     * @param $field_data_array
     * @return mixed
     */
    public function forminator_email_validator($submit_errors, $form_id, $field_data_array)
    {
        $email = null;

        foreach ($field_data_array as $field) {
            if ($field['name'] === 'email-1') {
                $email = $field['value'];
            }
        }

        if (!is_null($email)) {
            $forminatorForm = new Zerobounce_Email_Validator_Form_Public('formninator', $form_id);
            $validationInfo = $forminatorForm->prep_validation_info($email);
            $message = $forminatorForm->set_error_message($validationInfo['did_you_mean']);
            $forminatorForm->setup_form_validation($validationInfo, function () {
                $args = func_get_args();
                $message = $args[0]['message'];
                $submit_errors = &$args[0]['submit_errors'];
                $submit_errors[]['email-1'] = $message;
            }, ['message' => $message, 'submit_errors' => &$submit_errors]);
        }

        return $submit_errors;
    }
}
