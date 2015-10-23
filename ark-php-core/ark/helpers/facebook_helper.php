<?php

/**
 * facebook_helper.php
 *
 * @package     ark-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

function facebook_xmlns() {
    return 'xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/"';
}

function facebook_app_id() {
    $ci = & get_instance();

    return $ci->config->item('facebook_app_id');
}

function facebook_picture($who = 'me') {
    $ci = & get_instance();

    return $ci->facebook->append_token($ci->config->item('facebook_api_url') . $who . '/picture');
}

function facebook_opengraph_meta($opengraph) {
    $ci = & get_instance();

    $return = '<meta property="fb:admins" content="' . $ci->config->item('facebook_admins') . '" />';
    $return .= "\n";
    $return .= '<meta property="fb:app_id" content="' . $ci->config->item('facebook_app_id') . '" />';
    $return .= "\n";
    $return .= '<meta property="og:site_name" content="' . $ci->config->item('facebook_site_name') . '" />';
    $return .= "\n";

    foreach ($opengraph as $key => $value) {
        $return .= '<meta property="og:' . $key . '" content="' . $value . '" />';
        $return .= "\n";
    }

    return $return;
}

function parse_signed_request($signed_request, $secret) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }

    // check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }

    return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

function facebook_app_token() {
    $CI = &get_instance();
    $CI->load->library('facebook');
    $token_url = "https://graph.facebook.com/oauth/access_token?" .
            "client_id=" . $CI->config->item('facebook_app_id') .
            "&client_secret=" . $CI->config->item('facebook_api_secret') .
            "&grant_type=client_credentials";
    return trim(file_get_contents($token_url));
}