<?php

/**
 * recaptcha_helper.php
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

if (!function_exists('recaptcha_get_html')) {

    function recaptcha_get_html() {
        $CI = &get_instance();
        $CI->load->library('recaptcha');
        $CI->lang->load('recaptcha');
        return $CI->recaptcha->get_html();
    }

}
