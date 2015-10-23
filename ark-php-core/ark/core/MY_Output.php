<?php


/**
 * MY_Output.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class MY_Output extends CI_Output {
    function __construct(){
        $this->_zlib_oc = @ini_get('zlib.output_compression');

        include ARCHPATH.'config/mimes.php';

        $this->mime_types = $mimes;

        log_message('debug', "Output Class Initialized");
    }

    function get_mime_type($extension) {
        $mime = (!empty($this->mime_types[$extension])) ? $this->mime_types[$extension] : '';
        if (is_array($mime)) {
            $mime = $mime[0];
        }

        if (empty($mime)) {
            $mime = 'text/html';
        }
        return $mime;
    }
}