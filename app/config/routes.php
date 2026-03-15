<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Framework routes
$route['default_controller'] = 'main';
$route['404_override'] = 'notify/error_404';
$route['translate_uri_dashes'] = TRUE;

// Auth routes
$route['login'] = 'main/login';
$route['logout'] = 'main/logout';
$route['profile'] = 'main/profile';
$route['dashboard'] = 'main/dashboard';
$route['register'] = 'main/register';
$route['login/(:any)'] = 'main/login/$1';
$route['logout/(:any)'] = 'main/logout/$1';
$route['profile/(:any)'] = 'main/profile/$1';
$route['forgot_password'] = 'main/forgot_password';
$route['activate/(:any)/(:any)'] = 'main/activate/$1/$2';
$route['reset_password/(:any)'] = 'main/reset_password/$1';
$route['language/(:any)'] = 'main/language/$1';
$route['language'] = 'main/language';

// Admin area routes
$route['admin'] = 'admin/welcome';
$route['admin/users'] = 'admin/auth/users';
$route['admin/users/create_user'] = 'admin/auth/create_user';
$route['admin/users/profile/(:num)'] = 'admin/auth/profile/$1';
$route['admin/gamma'] = 'admin/gamma';
$route['admin/sign-in'] = 'admin/auth/login';
$route['admin/sign-in/(:any)'] = 'admin/auth/login/$1';
$route['admin/login'] = 'admin/auth/login';
$route['admin/login/(:any)'] = 'admin/auth/login/$1';
$route['admin/sign-out'] = 'admin/auth/logout';
$route['admin/logout'] = 'admin/auth/logout';
$route['admin/logout/(:any)'] = 'admin/auth/logout/$1';
// $route['admin/register'] = 'admin/auth/register';
$route['admin/forgot-password'] = 'admin/auth/forgot_password';
$route['admin/forgot_password'] = 'admin/auth/forgot_password';
$route['admin/system_settings/service-points'] = 'admin/system_settings/warehouses';
$route['admin/system_settings/service-points/get'] = 'admin/system_settings/getWarehouses';
$route['admin/system_settings/service-points/add'] = 'admin/system_settings/add_warehouse';
$route['admin/system_settings/service-points/edit/(:num)'] = 'admin/system_settings/edit_warehouse/$1';
$route['admin/system_settings/service-points/delete/(:num)'] = 'admin/system_settings/delete_warehouse/$1';
$route['admin/system_settings/service-points/actions'] = 'admin/system_settings/warehouse_actions';
