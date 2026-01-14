<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Google OAuth Configuration
| -------------------------------------------------------------------------
| Configure your Google OAuth 2.0 credentials here.
| Get your credentials from: https://console.cloud.google.com/apis/credentials
|
*/

$config['google_client_id'] = env('GOOGLE_CLIENT_ID', '');
$config['google_client_secret'] = env('GOOGLE_CLIENT_SECRET', '');
$config['google_redirect_uri'] = base_url('auth/callback');

/*
| -------------------------------------------------------------------------
| Session Configuration
| -------------------------------------------------------------------------
*/
$config['auth_session_key'] = 'user_logged_in';
$config['auth_user_key'] = 'user_data';

/*
| -------------------------------------------------------------------------
| API Configuration
| -------------------------------------------------------------------------
*/
$config['api_token_length'] = 64;
