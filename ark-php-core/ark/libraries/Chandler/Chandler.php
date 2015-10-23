<?php

/**
 * Chandler.php
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

class chandler extends CI_Driver_Library {

    protected $valid_drivers    = array(
        'chandler_json',
        'chandler_cli',
        'chandler_default',
    );

    var $find_rules;

    var $ci;
    var $_adapter;

    function __construct($params = array()) {
        $this->ci = &get_instance();
        $this->initialize($params);

        $this->_find();
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        if (!isset($this->find_rules)) {
            $this->find_rules = array(
                function() {
                    if (is_cli_request()) {
                        return 'cli';
                    }
                },
                'ext',
            );
        }
    }

    function on_restricted() {
        $this->_adapter->on_restricted();
    }

    function display_output() {
        $this->_adapter->display_output();
    }

    function _find() {
        foreach($this->find_rules as $rule) {
            if (is_callable($rule)) {
                $result = $rule();
                if (!empty($result)) {
                    $this->_adapter = $this->$result;
                    return;
                }
            } elseif (is_string($rule)) {
                $fn = '_find_'.$rule;
                $result = $this->$fn();
                if (!empty($result)) {
                    $this->_adapter = $this->$result;
                    return;
                }
            }
        }
        $this->_adapter = $this->default;
    }

    function _find_ext() {
        if (empty($this->ci->uri->extension)) {
            return;
        }
        $driver = 'chandler_'.$this->ci->uri->extension;
        if (in_array($driver, array_map('strtolower', $this->valid_drivers)))
            return $this->ci->uri->extension;
    }

}