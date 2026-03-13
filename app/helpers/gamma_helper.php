<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('gamma_pad_form_id')) {
    function gamma_pad_form_id($form_id)
    {
        return str_pad((string) ((int) $form_id), 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('gamma_safe_segment')) {
    function gamma_safe_segment($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/[\\\\\\/:\*\?"<>\|]+/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = trim($value);

        return $value === '' ? 'Untitled' : $value;
    }
}
