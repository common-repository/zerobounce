<?php

/**
 * Define the API functionality.
 *
 * @since      1.0.0
 * @package    Zerobounce_Email_Validator
 * @subpackage Zerobounce_Email_Validator/includes
 * @author     ZeroBounce (https://zerobounce.net/)
 */
class Zerobounce_Email_Validator_API
{
    /**
     * The ZeroBounce API Key
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api_key The ZeroBounce API Key
     */
    private $api_key;

    /**
     * The ZeroBounce API Timeout
     *
     * @since    1.0.0
     * @access   private
     * @var      int $api_timeout The ZeroBounce API Timeout in seconds
     */
    private $api_timeout;

    /**
     * Save the API key for future usage.
     *
     * @since    1.0.0
     */
    public function __construct($api_key = "", $api_timeout = 50)
    {
        $this->api_key = $api_key;
        $this->api_timeout = $api_timeout;
    }

    private function getApi(): string
    {
        $apiZone = get_option('zerobounce_settings_api_zone');
        if (is_array($apiZone) && in_array('api_usa', $apiZone)) {
            return 'https://api-us.zerobounce.net/v2';
        } else {
            return 'https://api.zerobounce.net/v2';
        }
    }

    public function get_credits_info()
    {
        try {
            if (!$this->is_api_key()) {
                return -1;
            }
            $api = $this->getApi();
            $response = wp_remote_get($api . '/getcredits?api_key=' . $this->api_key, [
                'method' => 'GET',
                'data_format' => 'body',
                'timeout' => $this->api_timeout,
                'user-agent' => 'ZeroBounce Email Validator (WordPress Plugin)',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
                $body = wp_remote_retrieve_body($response);

                $body_json = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($body_json['Credits'] !== '-1') {
                        return number_format($body_json['Credits']);
                    }
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return -1;
    }

    public function validate_key($key)
    {
        try {
            $response = wp_remote_get('https://members-api.zerobounce.net/api/keys/validate/?api_key=' . $key, [
                'method' => 'GET',
                'data_format' => 'json',
                'timeout' => $this->api_timeout,
                'user-agent' => 'ZeroBounce Email Validator (WordPress Plugin)',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
                $body = wp_remote_retrieve_body($response);

                $body_json = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {

                    if (array_key_exists("valid", $body_json) && $body_json['valid']) {
                        return true;
                    }

                    return false;
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return false;
    }

    public function validate_email($email)
    {
        try {
            if (!$this->is_api_key()) {
                return null;
            }
            $api = $this->getApi();
            $response = wp_remote_get($api . '/validate?api_key=' . $this->api_key . '&email=' . urlencode($email), [
                'method' => 'GET',
                'data_format' => 'json',
                'timeout' => $this->api_timeout,
                'user-agent' => 'ZeroBounce Email Validator (WordPress Plugin)',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            if ((!is_wp_error($response)) && (200 === wp_remote_retrieve_response_code($response))) {
                $body = wp_remote_retrieve_body($response);

                $body_json = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {

                    if (array_key_exists("error", $body_json)) {
                        return null;
                    }

                    return $body_json;
                }
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }

        return null;
    }

    public function is_api_key()
    {
        if (!strlen($this->api_key) || empty($this->api_key) || $this->api_key === "") {
            return false;
        }

        return true;
    }

    // Validate bulk
    public function batch_email_validation($emails)
    {
        if (!$this->is_api_key()) {
            return null;
        }

        $url = 'https://bulkapi.zerobounce.net/v2/validatebatch';
        $emailArray = array_map('trim', preg_split('/[\s,]+/', $emails));
        $nr_of_emails = count($emailArray);

        if ($nr_of_emails > 50) {
            wp_send_json_error(['error' => "You can only validate up to 50 emails at a time.<br/>The number of emails you try to validate is $nr_of_emails."]);
            wp_die();
        }

        $emailBatch = [];
        foreach ($emailArray as $email) {
            if (!empty($email)) {
                $emailBatch[] = ["email_address" => $email, "ip_address" => null];
            }
        }

        $body = wp_json_encode([
            'api_key' => $this->api_key,
            'email_batch' => $emailBatch,
        ]);

        $args = [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'method' => 'POST',
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return ['error' => 'Request failed: ' . $response->get_error_message()];
        }

        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);

        return $decoded_response ?: ['error' => 'Invalid response from the API'];
    }

    public function batch_file_validation($file)
    {
        if (!$this->is_api_key()) {
            wp_send_json_error('Check your API Key.');
        }

        $url = 'https://bulkapi.zerobounce.net/v2/sendfile';

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload error.'];
        }

        $boundary = wp_generate_password(24, false);

        $body = '';

        $body .= "--{$boundary}\r\n";
        $body .= 'Content-Disposition: form-data; name="email_address_column"' . "\r\n\r\n";
        $body .= '1' . "\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= 'Content-Disposition: form-data; name="has_header_row"' . "\r\n\r\n";
        $body .= 'true' . "\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= 'Content-Disposition: form-data; name="remove_duplicate"' . "\r\n\r\n";
        $body .= 'true' . "\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= 'Content-Disposition: form-data; name="file"; filename="' . $file['name'] . '"' . "\r\n";
        $body .= 'Content-Type: ' . $file['type'] . "\r\n\r\n";
        $body .= file_get_contents($file['tmp_name']) . "\r\n";
        $body .= "--{$boundary}--\r\n";

        $headers = array(
            'Content-Type' => "multipart/form-data; boundary={$boundary}",
            'Authorization' => "Bearer {$this->api_key}"
        );

        $response = wp_remote_post($url, [
            'body'    => $body,
            'headers' => $headers,
        ]);

        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        }

        $response_body = wp_remote_retrieve_body($response);
        return json_decode($response_body) ?: ['error' => 'Invalid response from the API'];
    }

    public function batch_file_status($file_id)
    {
        if (!$this->is_api_key()) {
            wp_send_json_error('Check your API Key.');
        }

        $api_url = 'https://bulkapi.zerobounce.net/v2/filestatus?api_key=' . urlencode($this->api_key). '&file_id=' . urlencode($file_id);


        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return 'API Error: ' . $data['error'];
        }

        $response_body = wp_remote_retrieve_body($response);
        return json_decode($response_body) ?: ['error' => 'Invalid response from the API'];
    }

    public function file_results_download($file_id)
    {
        if (!$this->is_api_key()) {
            wp_send_json_error('Check your API Key.');
        }

        $download_url = 'https://bulkapi.zerobounce.net/v2/getfile?api_key=' . $this->api_key . '&file_id=' . $file_id;

        $response = wp_remote_get($download_url);

        if (is_wp_error($response)) {
            wp_die('Failed to fetch the file.');
        }

        $file_data = wp_remote_retrieve_body($response);

        if (empty($file_data)) {
            wp_die('No file data returned.');
        }

        return $file_data;
    }
}
