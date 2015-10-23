<?php

/**
 * recaptcha_lang.php
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

//These will only show up in the logs
$lang['recaptcha_class_initialized'] = 'reCaptcha Library Initialized';
$lang['recaptcha_no_private_key'] = 'You did not supply an API key for Recaptcha';
$lang['recaptcha_no_remoteip'] = 'For security reasons, you must pass the remote ip to reCAPTCHA';
$lang['recaptcha_socket_fail'] = 'Could not open socket';


$lang['recaptcha_incorrect_response'] = 'Incorrect Security Image Response';
$lang['recaptcha_field_name'] = 'Security Image';
$lang['recaptcha_html_error'] = 'Error loading security image.  Please try again later';