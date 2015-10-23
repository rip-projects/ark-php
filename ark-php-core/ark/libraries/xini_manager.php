<?php

/**
 * Xini_manager.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class Xini_manager {
    var $path = '';
    var $head = '';
    var $data = null;

    function  __construct($params = array()) {
        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    function read($needle = null) {

        if ($this->data === null) {

            $er = error_reporting(E_ALL ^ E_DEPRECATED);
            $this->data = parse_ini_file($this->path, TRUE, INI_SCANNER_RAW);

            error_reporting($er);
        }

        if ($needle === null) {
            return $this->data;
        }

        $result = array();
        foreach ($this->data as $key => $value) {
            $match = preg_match($needle, $key);
            if ($match) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
?>
