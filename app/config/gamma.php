<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['gamma_enabled'] = true;
$config['gamma_base_path'] = 'Gamma/Users/';
$config['gamma_user_folders'] = array(
    '1 UserInterfaceFiles',
    '2 PrecedentClauses',
    '3 DocumentGeneration',
    '4 OutputFiles',
);
$config['gamma_repeat_standard_folders_in_form_root'] = false;
$config['gamma_test_username'] = 'TestUser';
$config['gamma_default_output_folder'] = '4 OutputFiles';

// Google Places scaffold. Add a real API key to enable address autocomplete.
$config['gamma_google_places_api_key'] = 'AIzaSyBtLRNS5N--2IFbr7c1ytDc6zJTh_DYAbE';
$config['gamma_google_places_country'] = '';

// Mail sender override. Use an address on the same domain as your SMTP account.
$config['gamma_mail_from_email'] = 'info@oneclicksolutionbd.com';
$config['gamma_mail_from_name'] = 'One Click Solution BD';
$config['gamma_mail_reply_to_email'] = 'info@oneclicksolutionbd.com';
$config['gamma_mail_reply_to_name'] = 'One Click Solution BD';
