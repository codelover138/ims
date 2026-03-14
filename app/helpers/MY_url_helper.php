<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('admin_normalize_uri')) {
	function admin_normalize_uri($uri = '') {
		$uri = ltrim((string) $uri, '/');
		$map = array(
			'' => '',
			'login' => 'sign-in',
			'login/' => 'sign-in',
			'auth/login' => 'sign-in',
			'logout' => 'sign-out',
			'logout/' => 'sign-out',
			'auth/logout' => 'sign-out',
			'forgot_password' => 'forgot-password',
			'auth/forgot_password' => 'forgot-password',
		);

		return isset($map[$uri]) ? $map[$uri] : $uri;
	}
}

// Add admin_url
if ( ! function_exists('admin_url')) {
	function admin_url($uri = '', $protocol = NULL) {
		$uri = admin_normalize_uri($uri);
		return get_instance()->config->site_url('admin/'.$uri, $protocol);
	}
}

// Add admin_redirect
if ( ! function_exists('admin_redirect')) {
	function admin_redirect($uri = '', $method = 'auto', $code = NULL) {
		if ( ! preg_match('#^(\w+:)?//#i', $uri)){
			$uri = site_url('admin/'.admin_normalize_uri($uri));
		}
		return redirect($uri, $method, $code);
	}
}

// Add shop_url
if ( ! function_exists('shop_url')) {
	function shop_url($uri = '', $protocol = NULL) {
		return get_instance()->config->site_url('shop/'.$uri, $protocol);
	}
}

// Add shop_redirect
if ( ! function_exists('shop_redirect')) {
	function shop_redirect($uri = '', $method = 'auto', $code = NULL) {
		if ( ! preg_match('#^(\w+:)?//#i', $uri)){
			$uri = site_url('shop/'.$uri);
		}
		return redirect($uri, $method, $code);
	}
}
