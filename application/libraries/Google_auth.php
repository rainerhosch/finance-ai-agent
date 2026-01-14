<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Google Auth Library
 * 
 * Wrapper for Google OAuth 2.0 authentication.
 * Uses Google's OAuth endpoint directly without requiring the full SDK.
 */
class Google_auth
{
    private $CI;
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    private $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $token_url = 'https://oauth2.googleapis.com/token';
    private $userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('auth');

        $this->client_id = $this->CI->config->item('google_client_id');
        $this->client_secret = $this->CI->config->item('google_client_secret');
        $this->redirect_uri = $this->CI->config->item('google_redirect_uri');
    }

    /**
     * Get the Google OAuth authorization URL
     */
    public function get_auth_url()
    {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        );

        return $this->auth_url . '?' . http_build_query($params);
    }

    /**
     * Authenticate with authorization code and get user info
     */
    public function authenticate($code)
    {
        // Exchange authorization code for access token
        $token_data = $this->get_access_token($code);

        if (!$token_data || !isset($token_data['access_token'])) {
            log_message('error', 'Failed to get access token from Google');
            return false;
        }

        // Get user info using access token
        $user_info = $this->get_user_info($token_data['access_token']);

        return $user_info;
    }

    /**
     * Exchange authorization code for access token
     */
    private function get_access_token($code)
    {
        $post_data = array(
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            log_message('error', 'cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($http_code !== 200) {
            log_message('error', 'Google Token API returned status ' . $http_code . ': ' . $response);
            return false;
        }

        return json_decode($response, true);
    }

    /**
     * Get user info using access token
     */
    private function get_user_info($access_token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $access_token
        ));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            log_message('error', 'cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        if ($http_code !== 200) {
            log_message('error', 'Google UserInfo API returned status ' . $http_code . ': ' . $response);
            return false;
        }

        return json_decode($response, true);
    }
}
