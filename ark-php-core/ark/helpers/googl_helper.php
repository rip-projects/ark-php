<?php

/**
 * A helper to create easy Goo.gl short urls
 * 
 * @package     CodeIgniter
 * @subpackage  Googl_Helper
 * @license     GPLv3 <http://www.gnu.org/licenses/gpl-3.0.txt>
 * @link                http://bitbucket.org/ipalaus/codeigniter-googl-helper/
 * @link                http://ggl-shortener.appspot.com/instructions/
 * @version     1.0
 * @author              Isern Palaus <http://blog.ipalaus.es>
 * @copyright   Copyright (c) 2010, Isern Palaus <http://blog.ipalaus.es>
 */
if (!function_exists('googl_url')) {

    function googl_url($long_url, $secure = TRUE) {
        $return = FALSE;

        $curl = curl_init('http://ggl-shortener.appspot.com/?url=' . rawurlencode($long_url));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($response) {
            $json_response = json_decode($response);

            if (!isset($json_response->error_message))
                $return = $json_response->short_url;

            else {
                if ($secure)
                    $return = $long_url;
            }

            curl_close($curl);
        }

        else {
            if ($secure)
                $return = $long_url;
        }

        return $return;
    }

}