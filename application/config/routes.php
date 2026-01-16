<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['auth/callback'] = 'auth/callback';
$route['logout'] = 'auth/logout';

// Login routes
$route['login'] = 'auth/login_form';
$route['login/password'] = 'auth/login_password';
$route['login/google'] = 'auth/login';

// Dashboard routes
$route['dashboard'] = 'dashboard/index';
$route['dashboard/profile'] = 'dashboard/profile';
$route['dashboard/profile/update'] = 'dashboard/update_profile';
$route['dashboard/transactions'] = 'dashboard/transactions';
$route['dashboard/settings'] = 'dashboard/settings';
$route['dashboard/settings/update-business'] = 'dashboard/update_business';
$route['dashboard/set_password'] = 'dashboard/set_password';
$route['dashboard/remove_telegram/(:num)'] = 'dashboard/remove_telegram/$1';

// API v1 routes - User
$route['api/v1/user/verify'] = 'api/v1/user/verify';
$route['api/v1/user/link-telegram'] = 'api/v1/user/link_telegram';
$route['api/v1/user/unlink-telegram'] = 'api/v1/user/unlink_telegram';
$route['api/v1/user/profile'] = 'api/v1/user/profile';

// API v1 routes - Transaction
$route['api/v1/transaction'] = 'api/v1/transaction/index';
$route['api/v1/transaction/create'] = 'api/v1/transaction/create';
$route['api/v1/transaction/summary'] = 'api/v1/transaction/summary';
$route['api/v1/transaction/(:num)'] = 'api/v1/transaction/show/$1';
$route['api/v1/transaction/delete/(:num)'] = 'api/v1/transaction/delete/$1';

// API v1 routes - Masterdata
$route['api/v1/masterdata/categories'] = 'api/v1/masterdata/categories';
$route['api/v1/masterdata/categories/create'] = 'api/v1/masterdata/create_category';

// Legacy API routes (backward compatible)
$route['api/v1/verify-user'] = 'api/v1/user/verify';
$route['api/v1/link-telegram'] = 'api/v1/user/link_telegram';
$route['api/v1/transactions'] = 'api/v1/transaction/index';
$route['api/v1/transactions/create'] = 'api/v1/transaction/create';
$route['api/v1/summary'] = 'api/v1/transaction/summary';
$route['api/v1/categories'] = 'api/v1/masterdata/categories';

// API v1 routes - AI Usage
$route['api/v1/ai/check-limit'] = 'api/v1/aiusage/check_limit';
$route['api/v1/ai/counter'] = 'api/v1/aiusage/counter';
$route['api/v1/ai/status'] = 'api/v1/aiusage/status';
$route['api/v1/ai/history'] = 'api/v1/aiusage/history';
