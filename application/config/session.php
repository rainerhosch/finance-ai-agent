<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Session Configuration
| -------------------------------------------------------------------------
*/

// Session driver - use 'files' for simplicity or 'database' for production
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'finance_ai_session';
$config['sess_expiration'] = 7200; // 2 hours
$config['sess_save_path'] = sys_get_temp_dir();
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

// Cookie settings
$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = FALSE; // Set to TRUE in production with HTTPS
$config['cookie_httponly'] = TRUE;
