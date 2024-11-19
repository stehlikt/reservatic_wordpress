<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function get_reservatic_api() {
    $resUrl = get_option('reservatic_res_url');
    $apiToken = get_option('reservatic_api_token');
    $certificate = get_option('reservatic_certificate');
    $certificatePassword = get_option('reservatic_certificate_password');

    if (!$resUrl || !$apiToken || !$certificate || !$certificatePassword) {
        return null;
    }

    return \Reservatic\Api::reservatic($resUrl, $apiToken, $certificate, $certificatePassword);
}