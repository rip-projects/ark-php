<?php

/**
 * Notif.php
 *
 * @package     ark-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2012/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (yyyy/mm/dd hh:mm:ss) (author)
 * 2012/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class Notif extends CI_Driver_Library {

    protected $valid_drivers    = array(
        '1' => 'notif_system',
        '2' => 'notif_email',
        '4' => 'notif_sms',
    );

    var $notification = array();
    var $system_timeout = 3000;
    var $system_fetch_uri = 'notification/fetch';
    var $system_all_uri = 'notification/all';
    var $default_use = 3;

    var $ci;
    var $db;

    function __construct($params = array()) {
        $this->ci = &get_instance();
        $this->db = &$this->ci->db;
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

        foreach($this->valid_drivers as $key => $driver) {
            $dname = substr($driver, 6);
            if (method_exists($this->$dname, '_initialize')) {
                $this->$dname->_initialize($params);
            }
        }
    }

    function cron() {
        foreach($this->valid_drivers as $key => $driver) {
            $dname = substr($driver, 6);
            if (method_exists($this->$dname, '_cron')) {
                $this->$dname->_cron($key);
            }
        }
    }

    function notify($data = '', $users = '', $use = null) {
        if (empty($use)) {
            $use = $this->default_use;
        }
        $users = (empty($users)) ? array($this->ci->auth->get_user_object()->id) : $users;
        foreach($this->valid_drivers as $key => $driver) {
            if ($use & $key) {
                foreach ($users as $user) {
                    $dname = substr($driver, 6);
                    $data['type'] = $use & $key;
                    $data['user'] = $user;

                    $this->$dname->notify($data);
                }
            }
        }
    }
}